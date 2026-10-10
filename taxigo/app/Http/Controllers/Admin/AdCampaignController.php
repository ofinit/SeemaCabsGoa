<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Type;
use App\Http\Controllers\Controller;
use App\Models\AdAdvertiser;
use App\Models\AdCampaign;
use App\Models\AdAgency;
use App\Models\AdBundle;
use App\Models\AdCategory;
use App\Models\AdCoupon;
use App\Models\AdLicence;
use App\Models\AdPlacement;
use App\Models\AdPricingRule;
use App\Models\AdPushSend;
use App\Models\AdQrCard;
use App\Models\Cab;
use App\Models\AdReport;
use App\Models\Advertisement;
use App\Models\SettingChange;
use App\Services\Ads\AdAvailability;
use App\Services\Ads\AdCampaignService;
use App\Services\Ads\AdPaymentService;
use App\Services\Ads\AdPushService;
use App\Services\Ads\AdReporting;
use App\Services\Ads\AdSettings;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Admin → Advertisements: self-serve review queue with the approval
 * checklist (plan §11), campaign actions, placements & pricing, advertisers.
 */
class AdCampaignController extends Controller
{
    /** Admin approval checklist (plan §11). Every item must pass to approve. */
    public const CHECKLIST = [
        'A. Advertiser & payment' => [
            'payment' => 'Payment received and verified with the gateway.',
            'identity' => 'Business identity is real; GSTIN valid and matches the legal name (if given).',
            'tier' => 'Category and tier are correct.',
            'licence' => 'Required licence uploaded, legible, in the advertiser\'s name, valid through the end date.',
            'standing' => 'Advertiser isn\'t blocked and has no unresolved reports.',
        ],
        'B. Creative' => [
            'render' => 'Renders correctly in every booked placement on a small phone (crop, legibility, nothing cut off).',
            'prohibited' => 'Nothing from the prohibited list, including surrogate liquor or gambling imagery.',
            'category_rules' => 'Category rules met: casino 21+ and "Play responsibly"; RERA number; no liquor offers for pubs / clubs.',
            'claims' => 'Claims are truthful and provable; offers show validity; prices include taxes.',
            'competitor' => 'No competing taxi / cab service; no impersonation; no third-party brands without rights.',
            'family' => 'Not offensive for a family-friendly cab app.',
        ],
        'C. Link' => [
            'link' => 'Opens over HTTPS, loads on mobile, matches the ad, no unexpected redirects or downloads.',
            'numbers' => 'Phone / WhatsApp numbers belong to the advertiser.',
        ],
        'D. Schedule' => [
            'schedule' => 'Dates, slots and exclusivity have no conflicts; discounts were applied correctly.',
        ],
    ];

    public const TABS = [
        'review' => 'In review',
        'changes' => 'Changes requested',
        'live' => 'Approved',
        'paused' => 'Paused',
        'payment' => 'Awaiting payment',
        'closed' => 'Expired / rejected / cancelled',
    ];

    public function __construct(private readonly AdCampaignService $ads)
    {
    }

    private function authorizeAdmin(): void
    {
        abort_unless(Auth::user() && (int) Auth::user()->type === Type::ADMIN, 403);
    }

    public function index(Request $request)
    {
        $this->authorizeAdmin();
        $tab = array_key_exists($request->input('tab'), self::TABS) ? $request->input('tab') : 'review';
        $statuses = [
            'review' => [AdCampaign::IN_REVIEW],
            'changes' => [AdCampaign::CHANGES_REQUESTED],
            'live' => [AdCampaign::APPROVED],
            'paused' => [AdCampaign::PAUSED],
            'payment' => [AdCampaign::PENDING_PAYMENT],
            'closed' => [AdCampaign::EXPIRED, AdCampaign::REJECTED, AdCampaign::CANCELLED],
        ];
        $query = AdCampaign::with('advertiser.category', 'items.placement')->whereIn('status', $statuses[$tab]);
        if ($tab === 'closed') {
            $query->whereNotNull('paid_at');
        }
        if ($search = trim((string) $request->input('q'))) {
            $query->where(fn ($q) => $q->where('reference', 'like', "%{$search}%")
                ->orWhereHas('advertiser', fn ($a) => $a->where('business_name', 'like', "%{$search}%")));
        }
        $tab === 'review' ? $query->orderBy('submitted_at') : $query->latest('id');

        $counts = AdCampaign::selectRaw('status, COUNT(*) as n')->groupBy('status')->pluck('n', 'status');

        return view('advertisements.self-serve.index', [
            'campaigns' => $query->paginate(30)->withQueryString(),
            'tab' => $tab,
            'counts' => $counts,
        ]);
    }

    public function show(AdCampaign $campaign)
    {
        $this->authorizeAdmin();
        $campaign->load('advertiser.category', 'advertiser.licences', 'advertiser.user', 'advertiser.agency', 'items.placement', 'items.advertisement', 'creatives', 'reviews.reviewer', 'invoices', 'reports', 'qrCards', 'pushSends');
        $placements = $campaign->items->pluck('placement');
        $conflicts = $campaign->start_date
            ? AdAvailability::conflicts($placements, $campaign->start_date->toDateString(), $campaign->end_date->toDateString(), $campaign->id)
            : [];
        $otherCampaigns = AdCampaign::where('advertiser_id', $campaign->advertiser_id)->where('id', '!=', $campaign->id)->latest('id')->limit(10)->get();

        return view('advertisements.self-serve.show', [
            'campaign' => $campaign,
            'checklist' => self::CHECKLIST,
            'reasons' => AdCampaign::REASON_CODES,
            'conflicts' => $conflicts,
            'otherCampaigns' => $otherCampaigns,
            'stats' => app(AdReporting::class)->stats($campaign),
        ]);
    }

    public function export(AdCampaign $campaign)
    {
        $this->authorizeAdmin();

        return response(app(AdReporting::class)->csv($campaign), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="ad-' . $campaign->reference . '-results.csv"',
        ]);
    }

    public function dashboard(Request $request)
    {
        $this->authorizeAdmin();
        $month = $request->filled('month') && preg_match('/^\d{4}-\d{2}$/', $request->input('month'))
            ? Carbon::createFromFormat('Y-m', $request->input('month'), 'Asia/Kolkata')->startOfMonth()
            : now('Asia/Kolkata')->startOfMonth();

        return view('advertisements.self-serve.dashboard', ['month' => $month] + app(AdReporting::class)->dashboard($month));
    }

    // --------------------------------------------------------- QR cards (P18)

    /** Printable A6 cards (one per cab) with cab assignment. */
    public function qrCards(AdCampaign $campaign)
    {
        $this->authorizeAdmin();
        $campaign->load('qrCards.cab', 'creatives', 'advertiser', 'items.placement');
        $p18 = $campaign->items->first(fn ($i) => $i->placement->billing === AdPlacement::PER_MONTH);
        abort_unless($p18, 404);

        return view('advertisements.self-serve.qr-cards', [
            'campaign' => $campaign,
            'creative' => $campaign->creatives->firstWhere('shape', $p18->placement->shapeKey()),
            'cabs' => Cab::orderBy('number')->get(['id', 'number']),
        ]);
    }

    public function updateQrCard(Request $request, AdQrCard $card)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'cab_id' => 'nullable|integer|exists:cabs,id',
            'placed_on' => 'nullable|date',
            'removed_on' => 'nullable|date',
        ]);
        $card->fill($data)->save();

        return back()->with('success', "Card {$card->code} updated.");
    }

    // --------------------------------------------------- sponsored push (P11)

    public function sendPush(AdPushSend $push, AdPushService $pushes)
    {
        $this->authorizeAdmin();
        if ($push->status !== AdPushSend::SCHEDULED || $push->campaign?->status !== AdCampaign::APPROVED) {
            return back()->with('error', 'Only scheduled pushes of approved ads can be sent.');
        }
        $ok = $pushes->send($push);
        $push->refresh();

        return back()->with($ok ? 'success' : 'error', $ok ? "Push sent to {$push->recipients} customers ({$push->delivered} delivered)." : 'Push failed: ' . $push->error);
    }

    // --------------------------------------------------------------- agencies

    public function updateAgency(Request $request, AdAgency $agency)
    {
        $this->authorizeAdmin();
        $data = $request->validate(['status' => 'required|in:pending,approved,blocked', 'status_note' => 'nullable|string|max:255']);
        SettingChange::record("ad_agency.{$agency->id}.status", $agency->status, $data['status']);
        $agency->fill($data)->save();

        return back()->with('success', "Agency {$agency->name} is now {$agency->status}.");
    }

    // ----------------------------------------------------------------- reports

    public function reports(Request $request)
    {
        $this->authorizeAdmin();
        $status = in_array($request->input('status'), [AdReport::OPEN, AdReport::DISMISSED, AdReport::ACTIONED], true) ? $request->input('status') : AdReport::OPEN;
        $groups = AdReport::with('advertisement', 'campaign.advertiser')->where('status', $status)->latest('id')->limit(500)->get()
            ->groupBy('advertisement_id');

        return view('advertisements.self-serve.reports', ['groups' => $groups, 'status' => $status, 'reasons' => AdReport::REASONS]);
    }

    /** Dismiss (ad is fine — resume it if reports paused it) or uphold (keep it paused). */
    public function resolveReports(Request $request, Advertisement $advertisement)
    {
        $this->authorizeAdmin();
        $action = $request->input('action') === 'uphold' ? AdReport::ACTIONED : AdReport::DISMISSED;
        AdReport::where('advertisement_id', $advertisement->id)->where('status', AdReport::OPEN)
            ->update(['status' => $action, 'resolved_by' => Auth::id(), 'resolved_at' => now()]);

        $campaign = $advertisement->ad_campaign_id ? AdCampaign::find($advertisement->ad_campaign_id) : null;
        if ($action === AdReport::DISMISSED) {
            if ($campaign && $campaign->status === AdCampaign::PAUSED && $campaign->paused_reason === 'reports') {
                $this->ads->pause($campaign, Auth::id(), false, 'Reports reviewed — ad is fine');
            } elseif (!$campaign && $advertisement->approval_status === Advertisement::PAUSED && str_contains((string) $advertisement->status_note, 'user reports')) {
                $advertisement->forceFill(['approval_status' => Advertisement::APPROVED, 'status_changed_by' => Auth::id(), 'status_changed_at' => now(), 'status_note' => 'Reports dismissed'])->save();
            }

            return back()->with('success', 'Reports dismissed' . ($campaign ? " and {$campaign->reference} resumed if it was paused by reports." : '.'));
        }
        if ($campaign && $campaign->status === AdCampaign::APPROVED) {
            $this->ads->pause($campaign, Auth::id(), true, 'Reports upheld by our team', 'reports');
        } elseif (!$campaign && $advertisement->approval_status === Advertisement::APPROVED) {
            $advertisement->forceFill(['approval_status' => Advertisement::PAUSED, 'status_changed_by' => Auth::id(), 'status_changed_at' => now(), 'status_note' => 'Paused: user reports upheld'])->save();
        }

        return back()->with('success', 'Reports upheld; the ad stays paused.' . ($campaign ? ' Open the ad to cancel and refund it if needed.' : ''));
    }

    public function decide(Request $request, AdCampaign $campaign)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'decision' => 'required|in:approve,changes,reject',
            'checks' => 'array',
            'checks.*' => 'in:pass,fail',
            'reasons' => 'array',
            'reasons.*' => 'in:' . implode(',', array_keys(AdCampaign::REASON_CODES)),
            'note' => 'nullable|string|max:1000',
        ]);
        $checks = $data['checks'] ?? [];
        $keys = collect(self::CHECKLIST)->flatMap(fn ($items) => array_keys($items))->all();
        $reasons = $data['reasons'] ?? [];

        try {
            if ($data['decision'] === 'approve') {
                $failed = array_filter($keys, fn ($k) => ($checks[$k] ?? null) !== 'pass');
                if ($failed) {
                    return back()->with('error', 'Mark every checklist item as Pass to approve (or request changes / reject).')->withInput();
                }
                $this->ads->approve($campaign, Auth::id(), $checks, $data['note'] ?? null);
                $message = $campaign->status === AdCampaign::APPROVED
                    ? "Ad {$campaign->reference} approved."
                    : "First approval recorded for {$campaign->reference}. A second admin must approve it.";
            } else {
                if (!$reasons) {
                    return back()->with('error', 'Choose at least one reason code.')->withInput();
                }
                if (in_array('R12', $reasons, true) && blank($data['note'] ?? null)) {
                    return back()->with('error', 'Add a note for reason R12 (Other).')->withInput();
                }
                if ($data['decision'] === 'changes') {
                    $this->ads->requestChanges($campaign, Auth::id(), $checks, $reasons, $data['note'] ?? null);
                    $message = "Changes requested on {$campaign->reference}.";
                } else {
                    $this->ads->reject($campaign, Auth::id(), $checks, $reasons, $data['note'] ?? null);
                    $campaign->refresh();
                    $message = "Ad {$campaign->reference} rejected." . ($campaign->refunded_at
                        ? ' Refund of ' . AdCampaign::rupees($campaign->refund_amount) . ' started.'
                        : ($campaign->refund_error ? ' Refund failed: ' . $campaign->refund_error : ''));
                }
            }
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()->route('admin.advertisements.selfServe.show', $campaign)->with('success', $message);
    }

    public function pause(Request $request, AdCampaign $campaign)
    {
        $this->authorizeAdmin();
        try {
            $pause = $campaign->status === AdCampaign::APPROVED;
            $this->ads->pause($campaign, Auth::id(), $pause, $request->input('note'));
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', $pause ? 'Ad paused.' : 'Ad resumed.');
    }

    public function cancel(Request $request, AdCampaign $campaign)
    {
        $this->authorizeAdmin();
        $data = $request->validate(['refund' => 'required|numeric|min:0', 'note' => 'required|string|max:1000']);
        $refund = (int) round(((float) $data['refund']) * 100);
        if ($refund > (int) $campaign->total_amount - (int) $campaign->refund_amount) {
            return back()->with('error', 'The refund is more than the amount left to refund.');
        }
        try {
            $this->ads->cancel($campaign, Auth::id(), $refund, $data['note']);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
        $campaign->refresh();

        return back()->with($campaign->refund_error ? 'error' : 'success', "Ad {$campaign->reference} cancelled."
            . ($refund > 0 ? ($campaign->refund_error ? ' Refund failed: ' . $campaign->refund_error : ' Refund of ' . AdCampaign::rupees($refund) . ' started.') : ''));
    }

    /** Pay out a refund that failed earlier (also retried hourly by ads:maintain). */
    public function retryRefund(AdCampaign $campaign)
    {
        $this->authorizeAdmin();
        if ((int) $campaign->refund_due <= 0) {
            return back()->with('error', 'No refund is owed on this ad.');
        }
        $refunded = $this->ads->settleRefund($campaign);
        $campaign->refresh();

        return back()->with($refunded ? 'success' : 'error', $refunded
            ? 'Refunded ' . AdCampaign::rupees($refunded) . '.'
            : 'Refund failed again: ' . ($campaign->refund_error ?: 'unknown error'));
    }

    public function retryTransfer(AdCampaign $campaign, AdPaymentService $payments)
    {
        $this->authorizeAdmin();
        $result = $payments->transfer($campaign, true);

        return back()->with($result['ok'] ? 'success' : 'error', $result['message']);
    }

    public function licence(AdLicence $licence)
    {
        $this->authorizeAdmin();
        abort_unless(Storage::disk('local')->exists($licence->file_path), 404);

        return Storage::disk('local')->response($licence->file_path, $licence->original_name, ['Cache-Control' => 'private, no-store']);
    }

    public function verifyLicence(Request $request, AdLicence $licence)
    {
        $this->authorizeAdmin();
        $status = $request->input('status') === AdLicence::REJECTED ? AdLicence::REJECTED : AdLicence::VERIFIED;
        $licence->forceFill(['status' => $status, 'verified_by' => Auth::id(), 'verified_at' => now()])->save();

        return back()->with('success', 'Licence marked as ' . $status . '.');
    }

    // ------------------------------------------------------- placements & pricing

    public function placements()
    {
        $this->authorizeAdmin();

        return view('advertisements.self-serve.placements', [
            'placements' => AdPlacement::orderBy('sort')->get(),
            'categories' => AdCategory::orderBy('sort')->get(),
            'bundles' => AdBundle::orderBy('id')->get(),
            'peaks' => AdPricingRule::orderBy('start_date')->get(),
            'coupons' => AdCoupon::latest('id')->get(),
            'settings' => AdSettings::load(),
            'labels' => AdSettings::LABELS,
        ]);
    }

    public function savePlacements(Request $request)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'placements' => 'required|array',
            'placements.*.price_standard' => 'required|numeric|min:0|max:1000000',
            'placements.*.price_premium' => 'required|numeric|min:0|max:1000000',
            'placements.*.slots' => 'required|integer|min:1|max:20',
            'placements.*.active' => 'nullable|boolean',
        ]);
        foreach ($data['placements'] as $id => $row) {
            $placement = AdPlacement::find($id);
            if (!$placement) {
                continue;
            }
            $new = [
                'price_standard' => (int) round($row['price_standard'] * 100),
                'price_premium' => (int) round($row['price_premium'] * 100),
                'slots' => $placement->exclusive ? 1 : (int) $row['slots'],
                'active' => !empty($row['active']),
            ];
            foreach ($new as $key => $value) {
                SettingChange::record("ad_placement.{$placement->code}.{$key}", $placement->{$key} === true ? 1 : ($placement->{$key} === false ? 0 : $placement->{$key}), $value === true ? 1 : ($value === false ? 0 : $value));
            }
            $placement->fill($new)->save();
        }

        return back()->with('success', 'Placements and prices saved. New prices apply to ads not yet paid.');
    }

    public function saveSettings(Request $request)
    {
        $this->authorizeAdmin();
        $rules = [
            AdSettings::ENABLED => 'required|in:0,1',
            AdSettings::SECOND_APPROVAL => 'required|in:0,1',
            AdSettings::EXCLUSIVITY_PERCENT => 'required|numeric|min:0|max:300',
            AdSettings::LAUNCH_PERCENT => 'required|numeric|min:0|max:100',
            AdSettings::LAUNCH_UNTIL => 'nullable|date_format:Y-m-d',
            AdSettings::REPORT_THRESHOLD => 'required|integer|min:1|max:50',
            AdSettings::WEEKLY_REPORTS => 'required|in:0,1',
            AdSettings::SEEMA_PERCENT => 'required|numeric|min:0|max:100',
            AdSettings::GST_RATE => 'required|numeric|min:0|max:28',
            AdSettings::SAC_SEEMA => 'nullable|digits_between:4,8',
            AdSettings::SAC_OFINIT => 'nullable|digits_between:4,8',
            AdSettings::MIN_DAYS => 'required|integer|min:1|max:90',
            AdSettings::MAX_DAYS => 'required|integer|min:7|max:366',
            AdSettings::DISCOUNT_14 => 'required|numeric|min:0|max:90',
            AdSettings::DISCOUNT_30 => 'required|numeric|min:0|max:90',
            AdSettings::HOLD_MINUTES => 'required|integer|min:5|max:120',
        ];
        $data = $request->validate($rules);
        foreach ($rules as $key => $rule) {
            SettingChange::setEnvironment($key, (string) ($data[$key] ?? ''));
        }

        return back()->with('success', 'Ad settings saved.');
    }

    public function saveBundles(Request $request)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'bundles' => 'required|array',
            'bundles.*.price_standard' => 'required|numeric|min:0|max:1000000',
            'bundles.*.price_premium' => 'required|numeric|min:0|max:1000000',
            'bundles.*.active' => 'nullable|boolean',
        ]);
        foreach ($data['bundles'] as $id => $row) {
            if ($bundle = AdBundle::find($id)) {
                SettingChange::record("ad_bundle.{$bundle->code}.price_standard", $bundle->price_standard, (int) round($row['price_standard'] * 100));
                SettingChange::record("ad_bundle.{$bundle->code}.price_premium", $bundle->price_premium, (int) round($row['price_premium'] * 100));
                $bundle->fill([
                    'price_standard' => (int) round($row['price_standard'] * 100),
                    'price_premium' => (int) round($row['price_premium'] * 100),
                    'active' => !empty($row['active']),
                ])->save();
            }
        }

        return back()->with('success', 'Bundles saved.');
    }

    public function addPeak(Request $request)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'name' => 'required|string|max:80',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'multiplier' => 'required|numeric|min:1|max:5',
        ]);
        $rule = AdPricingRule::create($data + ['active' => true]);
        SettingChange::record("ad_peak.{$rule->id}", null, "{$rule->name} {$data['start_date']}→{$data['end_date']} ×{$data['multiplier']}");

        return back()->with('success', 'Peak window added. It applies to ads not yet paid.');
    }

    public function deletePeak(AdPricingRule $rule)
    {
        $this->authorizeAdmin();
        SettingChange::record("ad_peak.{$rule->id}", "{$rule->name} ×{$rule->multiplier}", null);
        $rule->delete();

        return back()->with('success', 'Peak window removed.');
    }

    public function addCoupon(Request $request)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'code' => 'required|alpha_num:ascii|max:30|unique:ad_coupons,code',
            'description' => 'nullable|string|max:150',
            'type' => 'required|in:percent,flat',
            'value' => 'required|numeric|min:1',
            'max_uses' => 'nullable|integer|min:1',
            'per_advertiser' => 'required|integer|min:1|max:100',
            'min_amount' => 'nullable|numeric|min:0',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'first_booking_only' => 'nullable|boolean',
        ]);
        if ($data['type'] === 'percent' && $data['value'] > 100) {
            return back()->with('error', 'A percentage coupon can be at most 100%.')->withInput();
        }
        $coupon = AdCoupon::create([
            'code' => strtoupper($data['code']),
            'description' => $data['description'] ?? null,
            'type' => $data['type'],
            'value' => $data['type'] === 'flat' ? (int) round($data['value'] * 100) : (int) $data['value'],
            'max_uses' => $data['max_uses'] ?? null,
            'per_advertiser' => $data['per_advertiser'],
            'min_amount' => (int) round(((float) ($data['min_amount'] ?? 0)) * 100),
            'valid_from' => $data['valid_from'] ?? null,
            'valid_until' => $data['valid_until'] ?? null,
            'first_booking_only' => $request->boolean('first_booking_only'),
            'active' => true,
        ]);
        SettingChange::record("ad_coupon.{$coupon->code}", null, $coupon->label());

        return back()->with('success', "Coupon {$coupon->code} created.");
    }

    public function toggleCoupon(AdCoupon $coupon)
    {
        $this->authorizeAdmin();
        $coupon->forceFill(['active' => !$coupon->active])->save();
        SettingChange::record("ad_coupon.{$coupon->code}.active", (int) !$coupon->active, (int) $coupon->active);

        return back()->with('success', "Coupon {$coupon->code} " . ($coupon->active ? 'enabled.' : 'disabled.'));
    }

    public function saveCategories(Request $request)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'categories' => 'required|array',
            'categories.*.tier' => 'required|in:standard,premium',
            'categories.*.licence_required' => 'nullable|boolean',
            'categories.*.second_approval' => 'nullable|boolean',
            'categories.*.active' => 'nullable|boolean',
        ]);
        foreach ($data['categories'] as $id => $row) {
            $category = AdCategory::find($id);
            if (!$category) {
                continue;
            }
            SettingChange::record("ad_category.{$category->id}.tier", $category->tier, $row['tier']);
            $category->fill([
                'tier' => $row['tier'],
                'licence_required' => !empty($row['licence_required']),
                'second_approval' => !empty($row['second_approval']),
                'active' => !empty($row['active']),
            ])->save();
        }

        return back()->with('success', 'Categories saved.');
    }

    // ----------------------------------------------------------- advertisers

    public function advertisers(Request $request)
    {
        $this->authorizeAdmin();
        $query = AdAdvertiser::with('category', 'user', 'licences')->withCount('campaigns')->latest('id');
        if ($search = trim((string) $request->input('q'))) {
            $query->where(fn ($q) => $q->where('business_name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
        }

        return view('advertisements.self-serve.advertisers', [
            'advertisers' => $query->paginate(30)->withQueryString(),
            'categories' => AdCategory::orderBy('sort')->get(),
            'agencies' => AdAgency::with('user')->withCount('clients')->orderByRaw("FIELD(status, 'pending', 'approved', 'blocked')")->latest('id')->get(),
        ]);
    }

    public function updateAdvertiser(Request $request, AdAdvertiser $advertiser)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'status' => 'required|in:active,blocked',
            'category_id' => 'required|exists:ad_categories,id',
            'status_note' => 'nullable|string|max:255',
        ]);
        SettingChange::record("ad_advertiser.{$advertiser->id}.status", $advertiser->status, $data['status']);
        SettingChange::record("ad_advertiser.{$advertiser->id}.category", $advertiser->category_id, $data['category_id']);
        $advertiser->fill($data)->save();

        return back()->with('success', 'Advertiser updated. A category change applies to ads not yet paid.');
    }
}
