<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\AdAdvertiser;
use App\Models\AdCampaign;
use App\Models\AdCategory;
use App\Models\AdLicence;
use App\Models\AdPlacement;
use App\Models\AdReview;
use App\Models\Invoice;
use App\Services\Ads\AdAvailability;
use App\Services\Ads\AdCampaignService;
use App\Services\Ads\AdCreativeProcessor;
use App\Services\Ads\AdPaymentService;
use App\Services\Ads\AdPricing;
use App\Services\Ads\AdServer;
use App\Services\Ads\AdSettings;
use App\Support\Gstin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Self-serve advertising in the customer PWA (plan §7): business profile,
 * the create-an-ad wizard, checkout on Seema Holidays' gateway, and "My ads"
 * with results, invoices and renewal.
 */
class AdvertiseController extends Controller
{
    /** Advertiser checklist (plan §10). Every item must be ticked. */
    public const CHECKLIST = [
        'business' => 'The business name and category are correct (the category sets the price tier).',
        'licence' => "I've uploaded the licence required for my category, valid for the whole ad period (if my category needs one).",
        'contacts' => 'The phone, WhatsApp and website in the ad belong to my business.',
        'preview' => 'I checked the preview of every placement; the text is readable on a small phone.',
        'prices' => 'Prices in the ad include all taxes, and any offer shows its validity dates.',
        'rights' => 'I own or have rights to every image, logo and photo, and people shown have consented.',
        'alcohol' => 'No alcohol brands, drink images or liquor offers.',
        'gambling' => 'No betting, gambling, "win money" or jackpot claims.',
        'notices' => 'Casinos: the ad shows 21+ and "Play responsibly". Real estate: the RERA number is visible.',
        'content' => "No adult, offensive, political or misleading content, and no claims I can't prove.",
        'link' => "The link opens on a phone over HTTPS, matches the ad, and doesn't auto-download anything.",
        'terms' => 'I accept the Advertising Terms, Content Policy and Refund Policy, and understand the ad runs only after approval.',
    ];

    public function __construct(private readonly AdCampaignService $ads)
    {
    }

    // ----------------------------------------------------------------- pages

    public function index()
    {
        $user = Auth::guard('customer')->user();
        $advertiser = AdAdvertiser::where('user_id', $user->id)->first();
        $campaigns = $advertiser
            ? AdCampaign::with('items.placement')->where('advertiser_id', $advertiser->id)
                ->where(fn ($q) => $q->where('status', '!=', AdCampaign::CANCELLED)->orWhereNotNull('paid_at'))
                ->latest('id')->limit(50)->get()
            : collect();

        // Showcase of what's live right now.
        $ads = AdServer::live()->orderByDesc('id')->limit(10)->get()
            ->map(fn ($ad) => AdServer::payload($ad, null, 'pwa'));

        return view('customer.advertise', [
            'ads' => $ads,
            'campaigns' => $campaigns,
            'enabled' => AdSettings::enabled(),
        ]);
    }

    public function create()
    {
        return $this->wizard(null);
    }

    public function edit(AdCampaign $campaign)
    {
        $this->own($campaign);
        if (!$campaign->isEditable() && $campaign->status !== AdCampaign::PENDING_PAYMENT) {
            return redirect()->route('customer.ads.show', $campaign);
        }

        return $this->wizard($campaign);
    }

    private function wizard(?AdCampaign $campaign)
    {
        if (!AdSettings::enabled() && !$campaign) {
            return redirect()->route('customer.advertise');
        }
        $user = Auth::guard('customer')->user();
        $advertiser = AdAdvertiser::with('licences', 'category')->where('user_id', $user->id)->first();
        $settings = AdSettings::load();

        return view('customer.ads.wizard', [
            'campaign' => $campaign ? $this->campaignJson($campaign->load('items.placement', 'creatives')) : null,
            'profile' => $advertiser ? $this->profileJson($advertiser) : [
                'business_name' => '', 'category_id' => null, 'contact_name' => $user->name, 'phone' => $user->phone_number,
                'email' => $user->email, 'gstin' => $user->gstin, 'legal_name' => $user->gst_legal_name, 'billing_address' => $user->gst_billing_address,
                'licences' => [], 'tier' => null,
            ],
            'hasProfile' => (bool) $advertiser,
            'categories' => AdCategory::where('active', true)->orderBy('sort')->get(['id', 'name', 'tier', 'licence_label', 'licence_required', 'rules']),
            'placements' => AdPlacement::active()->get()->map(fn (AdPlacement $p) => [
                'id' => $p->id, 'code' => $p->code, 'name' => $p->name, 'description' => $p->description,
                'shape' => $p->shape, 'width' => $p->width, 'height' => $p->height, 'key' => $p->shapeKey(),
                'slots' => $p->slots, 'exclusive' => $p->exclusive,
                'price_standard' => $p->price_standard, 'price_premium' => $p->price_premium,
            ]),
            'rules' => [
                'min_days' => (int) $settings[AdSettings::MIN_DAYS],
                'max_days' => (int) $settings[AdSettings::MAX_DAYS],
                'discount_14' => (float) $settings[AdSettings::DISCOUNT_14],
                'discount_30' => (float) $settings[AdSettings::DISCOUNT_30],
                'gst_rate' => (float) $settings[AdSettings::GST_RATE],
            ],
            'checklist' => self::CHECKLIST,
        ]);
    }

    public function show(AdCampaign $campaign)
    {
        $this->own($campaign);
        $campaign->load('items.placement', 'creatives', 'invoices', 'advertiser');

        return view('customer.ads.show', [
            'campaign' => $campaign,
            'stats' => $this->stats($campaign),
            'invoices' => $campaign->invoices->where('status', Invoice::ISSUED)->values(),
            'reasons' => collect($campaign->reason_codes ?? [])->map(fn ($c) => AdCampaign::REASON_CODES[$c] ?? $c)->all(),
        ]);
    }

    /** Cashfree return_url for ad payments: verify with the gateway, then show the ad. */
    public function cashfreeReturn(Request $request)
    {
        $orderId = (string) $request->query('order_id', '');
        $campaign = null;
        if (preg_match('/^' . AdPaymentService::CASHFREE_PREFIX . '(\d+)_\d+$/', $orderId, $m)) {
            $campaign = AdCampaign::where('id', $m[1])->where('user_id', Auth::guard('customer')->id())->first();
        }
        if (!$campaign) {
            return redirect()->route('customer.advertise');
        }
        if (!$campaign->isPaid()) {
            $result = app(AdPaymentService::class)->verify($campaign, 'cashfree', $orderId);
            if ($result['ok']) {
                $this->ads->paymentReceived($campaign, 'cashfree', $result['transaction_id']);
            } else {
                return redirect()->route('customer.ads.show', $campaign)->with('error', $result['message']);
            }
        }

        return redirect()->route('customer.ads.show', $campaign);
    }

    // --------------------------------------------------------------- actions

    public function saveProfile(Request $request)
    {
        $user = Auth::guard('customer')->user();
        $data = $request->validate([
            'business_name' => 'required|string|max:120',
            'category_id' => 'required|integer|exists:ad_categories,id',
            'contact_name' => 'required|string|max:100',
            'phone' => ['required', 'string', 'regex:/^(\+?91)?[6-9]\d{9}$/'],
            'email' => 'required|email|max:150',
            'gstin' => 'nullable|string|max:15',
            'legal_name' => 'nullable|required_with:gstin|string|max:150',
            'billing_address' => 'nullable|required_with:gstin|string|max:500',
        ]);
        if (!empty($data['gstin'])) {
            $data['gstin'] = Gstin::normalize($data['gstin']);
            if (!Gstin::isValid($data['gstin'])) {
                return response()->json(['status' => false, 'message' => 'The GSTIN is not valid. Check it, or leave it empty.'], 422);
            }
        } else {
            $data['gstin'] = $data['legal_name'] = $data['billing_address'] = null;
        }
        $data['phone'] = substr(preg_replace('/\D/', '', $data['phone']), -10);

        $advertiser = AdAdvertiser::where('user_id', $user->id)->first();
        if ($advertiser?->isBlocked()) {
            return response()->json(['status' => false, 'message' => 'Your advertiser account is on hold. Please contact us.'], 403);
        }
        $advertiser = AdAdvertiser::updateOrCreate(['user_id' => $user->id], $data);

        return response()->json(['status' => true, 'data' => $this->profileJson($advertiser->fresh(['licences', 'category']))]);
    }

    public function uploadLicence(Request $request)
    {
        $advertiser = $this->advertiser();
        $data = $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
            'number' => 'nullable|string|max:100',
            'valid_until' => 'required|date|after_or_equal:today',
        ]);
        $path = $request->file('file')->store('ads/licences', 'local');
        AdLicence::create([
            'advertiser_id' => $advertiser->id,
            'type' => optional($advertiser->category)->licence_label ?? 'Licence',
            'number' => $data['number'] ?? null,
            'file_path' => $path,
            'original_name' => mb_substr($request->file('file')->getClientOriginalName(), 0, 190),
            'valid_until' => $data['valid_until'],
        ]);

        return response()->json(['status' => true, 'data' => $this->profileJson($advertiser->fresh(['licences', 'category']))]);
    }

    /** Sold-out dates for the chosen placements over the next ~4 months. */
    public function availability(Request $request)
    {
        $ids = array_map('intval', (array) $request->input('placements', []));
        $from = now('Asia/Kolkata')->toDateString();
        $to = now('Asia/Kolkata')->addDays(120)->toDateString();
        $placements = AdPlacement::active()->whereIn('id', $ids)->get();
        $exclude = $request->filled('campaign') ? (int) $request->input('campaign') : null;

        return response()->json(['status' => true, 'data' => AdAvailability::soldOut($placements, $from, $to, $exclude)]);
    }

    public function quote(Request $request)
    {
        $advertiser = AdAdvertiser::with('category')->where('user_id', Auth::guard('customer')->id())->first();
        $request->validate(['placements' => 'required|array|min:1', 'days' => 'required|integer|min:1|max:366']);
        $placements = AdPlacement::active()->whereIn('id', array_map('intval', $request->input('placements')))->get();
        $quote = AdPricing::quote($placements, optional($advertiser?->category)->tier ?? AdCategory::STANDARD, (int) $request->input('days'), $advertiser?->gstin);

        return response()->json(['status' => true, 'data' => $quote]);
    }

    /** Create (no id) or update a draft. */
    public function saveCampaign(Request $request, ?AdCampaign $campaign = null)
    {
        $advertiser = $this->advertiser();
        if ($campaign) {
            $this->own($campaign);
        } elseif (!AdSettings::enabled()) {
            return response()->json(['status' => false, 'message' => 'New ad bookings are paused right now. Please contact us.'], 403);
        }
        $data = $request->validate([
            'placements' => 'required|array|min:1',
            'placements.*' => 'integer',
            'start_date' => 'required|date_format:Y-m-d',
            'days' => 'required|integer|min:1|max:366',
            'landing_type' => 'required|in:website,whatsapp,call',
            'landing_value' => 'nullable|string|max:500',
        ]);
        if ($campaign && $campaign->status === AdCampaign::PENDING_PAYMENT && !$campaign->isPaid()) {
            $campaign->forceFill(['status' => AdCampaign::DRAFT, 'hold_expires_at' => null])->save();
        }

        try {
            $campaign = $this->ads->saveDraft($campaign, $advertiser, $data['placements'], $data['start_date'], (int) $data['days'], $data['landing_type'], (string) ($data['landing_value'] ?? ''));
        } catch (RuntimeException $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['status' => true, 'data' => $this->campaignJson($campaign)]);
    }

    public function uploadCreative(Request $request, AdCampaign $campaign, AdCreativeProcessor $processor)
    {
        $this->own($campaign);
        $request->validate([
            'shape' => 'required|string|max:20',
            'file' => 'nullable|file|max:10240',
            'crop' => 'required|json',
        ]);
        $placement = $campaign->items()->with('placement')->get()->pluck('placement')
            ->first(fn ($p) => $p->shapeKey() === $request->input('shape'));
        if (!$placement) {
            return response()->json(['status' => false, 'message' => 'That image size is not part of this ad.'], 422);
        }
        $crop = json_decode($request->input('crop'), true) ?: [];

        try {
            if ($request->hasFile('file')) {
                $processed = $processor->process($request->file('file'), $crop, $placement->width, $placement->height);
            } else {
                // Re-crop the original already uploaded for this ad.
                $existing = $campaign->creatives()->where('shape', $request->input('shape'))->first()
                    ?? $campaign->creatives()->latest('id')->first();
                if (!$existing) {
                    return response()->json(['status' => false, 'message' => 'Choose an image first.'], 422);
                }
                $processed = $processor->recrop($existing->original_path, $crop, $placement->width, $placement->height);
            }
            $this->ads->saveCreative($campaign, $placement->shapeKey(), $processed);
        } catch (RuntimeException $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            Log::error("Ad creative upload failed for {$campaign->reference}: " . $e->getMessage());

            return response()->json(['status' => false, 'message' => 'We could not process this image. Try a different JPEG or PNG.'], 422);
        }

        return response()->json(['status' => true, 'data' => $this->campaignJson($campaign->fresh(['items.placement', 'creatives']))]);
    }

    /** Validate, hold the slots and open the gateway order. */
    public function checkout(Request $request, AdCampaign $campaign, AdPaymentService $payments)
    {
        $this->own($campaign);
        $request->validate(['gateway' => 'required|in:razorpay,cashfree']);
        if ($error = $this->checklistError($request)) {
            return response()->json(['status' => false, 'message' => $error], 422);
        }

        try {
            $campaign = $this->ads->startCheckout($campaign);
            $this->recordChecklist($campaign, $request);
            $order = $request->input('gateway') === 'cashfree'
                ? $payments->createCashfreeOrder($campaign)
                : $payments->createRazorpayOrder($campaign);
        } catch (RuntimeException $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            Log::error("Ad checkout failed for {$campaign->reference}: " . $e->getMessage());

            return response()->json(['status' => false, 'message' => 'Payment could not be started. Please try again.'], 500);
        }

        return response()->json(['status' => true, 'data' => array_merge($order, [
            'reference' => $campaign->reference,
            'total' => $campaign->total_amount,
        ])]);
    }

    public function confirm(Request $request, AdCampaign $campaign, AdPaymentService $payments)
    {
        $this->own($campaign);
        $request->validate(['gateway' => 'required|in:razorpay,cashfree', 'reference' => 'required|string|max:80']);
        if (!$campaign->isPaid()) {
            $result = $payments->verify($campaign, $request->input('gateway'), $request->input('reference'));
            if (!$result['ok']) {
                return response()->json(['status' => false, 'message' => $result['message']], 422);
            }
            $campaign = $this->ads->paymentReceived($campaign, $request->input('gateway'), $result['transaction_id']);
        }

        return response()->json(['status' => true, 'data' => ['redirect' => route('customer.ads.show', $campaign)]]);
    }

    public function resubmit(Request $request, AdCampaign $campaign)
    {
        $this->own($campaign);
        if ($error = $this->checklistError($request)) {
            return response()->json(['status' => false, 'message' => $error], 422);
        }
        try {
            $this->ads->resubmit($campaign);
            $this->recordChecklist($campaign, $request);
        } catch (RuntimeException $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['status' => true, 'data' => ['redirect' => route('customer.ads.show', $campaign)]]);
    }

    public function discard(AdCampaign $campaign)
    {
        $this->own($campaign);
        try {
            $this->ads->discard($campaign);
        } catch (RuntimeException $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['status' => true, 'data' => ['redirect' => route('customer.advertise')]]);
    }

    public function renew(Request $request, AdCampaign $campaign)
    {
        $this->own($campaign);
        try {
            $new = $this->ads->renew($campaign, $request->filled('days') ? (int) $request->input('days') : null);
        } catch (RuntimeException $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['status' => true, 'data' => ['redirect' => route('customer.ads.edit', $new)]]);
    }

    // --------------------------------------------------------------- helpers

    private function own(AdCampaign $campaign): void
    {
        abort_unless((int) $campaign->user_id === (int) Auth::guard('customer')->id(), 404);
    }

    private function advertiser(): AdAdvertiser
    {
        $advertiser = AdAdvertiser::with('category', 'licences')->where('user_id', Auth::guard('customer')->id())->first();
        if (!$advertiser) {
            abort(response()->json(['status' => false, 'message' => 'Add your business details first.'], 422));
        }
        if ($advertiser->isBlocked()) {
            abort(response()->json(['status' => false, 'message' => 'Your advertiser account is on hold. Please contact us.'], 403));
        }

        return $advertiser;
    }

    private function checklistError(Request $request): ?string
    {
        $ticked = array_keys(array_filter((array) $request->input('checklist', [])));
        $missing = array_diff(array_keys(self::CHECKLIST), $ticked);

        return $missing ? 'Please tick every item in the checklist.' : null;
    }

    /** The advertiser's declarations are kept with the review log. */
    private function recordChecklist(AdCampaign $campaign, Request $request): void
    {
        AdReview::create([
            'campaign_id' => $campaign->id,
            'reviewer_id' => $campaign->user_id,
            'stage' => 'advertiser',
            'decision' => 'submit',
            'checklist' => array_fill_keys(array_keys(self::CHECKLIST), true),
        ]);
    }

    private function profileJson(AdAdvertiser $advertiser): array
    {
        return [
            'business_name' => $advertiser->business_name,
            'category_id' => $advertiser->category_id,
            'contact_name' => $advertiser->contact_name,
            'phone' => $advertiser->phone,
            'email' => $advertiser->email,
            'gstin' => $advertiser->gstin,
            'legal_name' => $advertiser->legal_name,
            'billing_address' => $advertiser->billing_address,
            'tier' => optional($advertiser->category)->tier,
            'licences' => $advertiser->licences->map(fn (AdLicence $l) => [
                'id' => $l->id, 'type' => $l->type, 'number' => $l->number, 'status' => $l->status,
                'valid_until' => optional($l->valid_until)->format('Y-m-d'), 'name' => $l->original_name,
            ])->values(),
        ];
    }

    private function campaignJson(AdCampaign $campaign): array
    {
        $landingValue = in_array($campaign->landing_type, ['whatsapp', 'call'], true)
            ? substr(preg_replace('/\D/', '', (string) $campaign->landing_url), -10)
            : $campaign->landing_url;

        return [
            'id' => $campaign->id,
            'reference' => $campaign->reference,
            'status' => $campaign->status,
            'status_label' => $campaign->displayStatus(),
            'paid' => $campaign->isPaid(),
            'placements' => $campaign->items->pluck('placement_id')->values(),
            'start_date' => optional($campaign->start_date)->format('Y-m-d'),
            'end_date' => optional($campaign->end_date)->format('Y-m-d'),
            'days' => (int) $campaign->days,
            'landing_type' => $campaign->landing_type,
            'landing_value' => $landingValue,
            'creatives' => $campaign->creatives->mapWithKeys(fn ($c) => [$c->shape => ['url' => $c->url(), 'bytes' => $c->bytes]]),
            'quote' => [
                'list_amount' => (int) $campaign->list_amount,
                'discount_percent' => (float) $campaign->discount_percent,
                'discount_amount' => (int) $campaign->discount_amount,
                'net_amount' => (int) $campaign->net_amount,
                'gst_rate' => (float) $campaign->gst_rate,
                'inter_state' => (bool) $campaign->inter_state,
                'cgst_amount' => (int) $campaign->cgst_amount,
                'sgst_amount' => (int) $campaign->sgst_amount,
                'igst_amount' => (int) $campaign->igst_amount,
                'total_amount' => (int) $campaign->total_amount,
            ],
            'reasons' => collect($campaign->reason_codes ?? [])->map(fn ($c) => AdCampaign::REASON_CODES[$c] ?? $c)->values(),
            'review_note' => $campaign->status === AdCampaign::CHANGES_REQUESTED ? $campaign->review_note : null,
        ];
    }

    /** Results for "My ads": impressions, clicks, CTR by placement and by day. */
    private function stats(AdCampaign $campaign): array
    {
        $adIds = $campaign->items->pluck('advertisement_id')->filter()->values();
        if ($adIds->isEmpty()) {
            return ['views' => 0, 'clicks' => 0, 'ctr' => null, 'placements' => [], 'days' => []];
        }
        $byAd = $campaign->items->filter(fn ($i) => $i->advertisement_id)->keyBy('advertisement_id');

        $views = DB::table('advertisement_impressions')->whereIn('advertisement_id', $adIds)
            ->groupBy('advertisement_id', 'date')->selectRaw('advertisement_id, date, SUM(views) as views')->get();
        $clicks = DB::table('advertisement_user_clicks')->whereIn('advertisement_id', $adIds)
            ->groupBy('advertisement_id', DB::raw('DATE(created_at)'))->selectRaw('advertisement_id, DATE(created_at) as date, COUNT(*) as clicks')->get();

        $placements = [];
        $days = [];
        foreach ($views as $row) {
            $code = $byAd[$row->advertisement_id]->placement->code ?? '—';
            $placements[$code]['views'] = ($placements[$code]['views'] ?? 0) + $row->views;
            $days[$row->date]['views'] = ($days[$row->date]['views'] ?? 0) + $row->views;
        }
        foreach ($clicks as $row) {
            $code = $byAd[$row->advertisement_id]->placement->code ?? '—';
            $placements[$code]['clicks'] = ($placements[$code]['clicks'] ?? 0) + $row->clicks;
            $days[$row->date]['clicks'] = ($days[$row->date]['clicks'] ?? 0) + $row->clicks;
        }
        krsort($days);
        $totalViews = array_sum(array_column($placements, 'views'));
        $totalClicks = array_sum(array_column($placements, 'clicks'));

        return [
            'views' => $totalViews,
            'clicks' => $totalClicks,
            'ctr' => $totalViews > 0 ? round($totalClicks * 100 / $totalViews, 2) : null,
            'placements' => $placements,
            'days' => array_slice($days, 0, 31, true),
        ];
    }
}
