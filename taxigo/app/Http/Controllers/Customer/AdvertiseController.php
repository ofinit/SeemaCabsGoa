<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\AdAdvertiser;
use App\Models\AdAgency;
use App\Models\AdCampaign;
use App\Models\AdBundle;
use App\Models\AdCategory;
use App\Models\AdCoupon;
use App\Models\AdLicence;
use App\Models\AdPlacement;
use App\Models\AdReview;
use App\Models\Invoice;
use App\Models\SightSeeingPackages;
use App\Services\Ads\AdAvailability;
use App\Services\Ads\AdCampaignService;
use App\Services\Ads\AdCreativeProcessor;
use App\Services\Ads\AdPaymentService;
use App\Services\Ads\AdPricing;
use App\Services\Ads\AdPushService;
use App\Services\Ads\AdReporting;
use App\Services\Ads\AdServer;
use App\Services\Ads\AdSettings;
use App\Services\Ads\AdTargeting;
use App\Support\Gstin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
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
        // Agencies see every client's ads; everyone else their own.
        $campaigns = AdCampaign::with('items.placement', 'advertiser')->where('user_id', $user->id)
            ->where(fn ($q) => $q->where('status', '!=', AdCampaign::CANCELLED)->orWhereNotNull('paid_at'))
            ->latest('id')->limit(100)->get();

        // Showcase of what's live right now.
        $ads = AdServer::live()->orderByDesc('id')->limit(10)->get()
            ->map(fn ($ad) => AdServer::payload($ad, null, 'pwa'));

        return view('customer.advertise', [
            'ads' => $ads,
            'campaigns' => $campaigns,
            'enabled' => AdSettings::enabled(),
            'agency' => AdAgency::where('user_id', $user->id)->first(),
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
        if ($campaign) {
            session(['ads_client' => $campaign->advertiser_id]);
        }
        $advertiser = $this->currentAdvertiser();
        $agency = $this->approvedAgency();
        if ($agency && request()->boolean('new_client') && !$campaign) {
            $advertiser = null;
        }
        $settings = AdSettings::load();

        return view('customer.ads.wizard', [
            'campaign' => $campaign ? $this->campaignJson($campaign->load('items.placement', 'creatives')) : null,
            'profile' => $advertiser ? $this->profileJson($advertiser) : [
                'business_name' => '', 'category_id' => null, 'contact_name' => $user->name, 'phone' => $user->phone_number,
                'email' => $user->email, 'gstin' => $user->gstin, 'legal_name' => $user->gst_legal_name, 'billing_address' => $user->gst_billing_address,
                'licences' => [], 'tier' => null,
            ],
            'hasProfile' => (bool) $advertiser,
            'agency' => $agency ? [
                'name' => $agency->name,
                'current' => $advertiser?->id,
                'clients' => AdAdvertiser::where('user_id', $user->id)->orderBy('business_name')->get(['id', 'business_name']),
            ] : null,
            'pushAudience' => AdPushService::audienceSize(),
            'categories' => AdCategory::where('active', true)->orderBy('sort')->get(['id', 'name', 'tier', 'licence_label', 'licence_required', 'rules']),
            'placements' => AdPlacement::active()->get()->map(fn (AdPlacement $p) => [
                'id' => $p->id, 'code' => $p->code, 'name' => $p->name, 'description' => $p->description,
                'shape' => $p->shape, 'width' => $p->width, 'height' => $p->height, 'key' => $p->shapeKey(),
                'slots' => $p->slots, 'exclusive' => $p->exclusive,
                'price_standard' => $p->price_standard, 'price_premium' => $p->price_premium,
                'billing' => $p->billing, 'unit' => $p->priceUnit(),
            ]),
            'rules' => [
                'min_days' => (int) $settings[AdSettings::MIN_DAYS],
                'max_days' => (int) $settings[AdSettings::MAX_DAYS],
                'discount_14' => (float) $settings[AdSettings::DISCOUNT_14],
                'discount_30' => (float) $settings[AdSettings::DISCOUNT_30],
                'gst_rate' => (float) $settings[AdSettings::GST_RATE],
                'exclusivity_percent' => (float) $settings[AdSettings::EXCLUSIVITY_PERCENT],
                'launch_percent' => AdSettings::launchPercent($settings),
                'launch_until' => $settings[AdSettings::LAUNCH_UNTIL],
                'first_booking' => !$advertiser || !AdCampaign::where('advertiser_id', $advertiser->id)->whereNotNull('paid_at')->exists(),
            ],
            'targetingOptions' => [
                'areas' => AdTargeting::AREAS,
                'trips' => AdTargeting::TRIPS,
                'days' => AdTargeting::DAYS,
                'page_groups' => collect(AdTargeting::PAGE_GROUPS)->map(fn ($g) => $g['label']),
            ],
            'packages' => SightSeeingPackages::orderBy('title')->get(['id', 'title'])->map(fn ($p) => ['id' => $p->id, 'title' => $p->title]),
            'bundles' => AdBundle::where('active', true)->get()->map(fn ($b) => [
                'name' => $b->name, 'codes' => $b->placement_codes, 'price_standard' => $b->price_standard, 'price_premium' => $b->price_premium,
            ]),
            'checklist' => self::CHECKLIST,
        ]);
    }

    public function show(AdCampaign $campaign)
    {
        $this->own($campaign);
        $campaign->load('items.placement', 'creatives', 'invoices', 'advertiser');

        return view('customer.ads.show', [
            'campaign' => $campaign,
            'stats' => app(AdReporting::class)->stats($campaign),
            'invoices' => $campaign->invoices->where('status', Invoice::ISSUED)->values(),
            'reasons' => collect($campaign->reason_codes ?? [])->map(fn ($c) => AdCampaign::REASON_CODES[$c] ?? $c)->all(),
        ]);
    }

    /** Turn on conversion tracking: a private token for the advertiser's tag. */
    public function conversionToken(AdCampaign $campaign)
    {
        $this->own($campaign);
        if (!$campaign->conversion_token) {
            $campaign->forceFill(['conversion_token' => Str::random(24)])->save();
        }

        return response()->json(['status' => true, 'data' => ['redirect' => route('customer.ads.show', $campaign) . '#conversions']]);
    }

    /** Agencies: pick the client the next ad is for. */
    public function switchClient(Request $request)
    {
        $id = (int) $request->input('id');
        abort_unless($this->approvedAgency() && AdAdvertiser::where('user_id', Auth::guard('customer')->id())->whereKey($id)->exists(), 404);
        session(['ads_client' => $id]);

        return response()->json(['status' => true]);
    }

    /** Ask to become an agency account (admin approves). */
    public function applyAgency(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:120', 'gstin' => 'nullable|string|max:15']);
        if (!empty($data['gstin']) && !Gstin::isValid(Gstin::normalize($data['gstin']))) {
            return response()->json(['status' => false, 'message' => 'The GSTIN is not valid.'], 422);
        }
        AdAgency::updateOrCreate(
            ['user_id' => Auth::guard('customer')->id()],
            ['name' => $data['name'], 'gstin' => !empty($data['gstin']) ? Gstin::normalize($data['gstin']) : null]
        );

        return response()->json(['status' => true, 'message' => 'Thanks — we will review your agency account shortly.']);
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

        $agency = $this->approvedAgency();
        $advertiser = ($agency && $request->boolean('new_client')) ? null : $this->currentAdvertiser();
        if ($advertiser?->isBlocked()) {
            return response()->json(['status' => false, 'message' => 'Your advertiser account is on hold. Please contact us.'], 403);
        }
        $advertiser = $advertiser
            ? tap($advertiser)->update($data)
            : AdAdvertiser::create($data + ['user_id' => $user->id, 'agency_id' => $agency?->id]);
        session(['ads_client' => $advertiser->id]);

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
        $groups = array_values(array_intersect((array) $request->input('page_groups', []), array_keys(AdTargeting::PAGE_GROUPS)));

        $units = AdCampaignService::unitsFor($placements, ['page_groups' => $groups, 'cabs' => (int) $request->input('cabs', 1)]);

        return response()->json(['status' => true, 'data' => AdAvailability::soldOut($placements, $from, $to, $exclude, $groups, $units)]);
    }

    public function quote(Request $request)
    {
        $advertiser = $this->currentAdvertiser();
        $request->validate([
            'placements' => 'required|array|min:1',
            'days' => 'required|integer|min:1|max:366',
            'start_date' => 'nullable|date_format:Y-m-d',
            'page_groups' => 'nullable|array',
            'cabs' => 'nullable|integer|min:1|max:200',
            'exclusive_category' => 'nullable|boolean',
            'coupon_code' => 'nullable|string|max:30',
            'campaign' => 'nullable|integer',
        ]);
        $placements = AdPlacement::active()->whereIn('id', array_map('intval', $request->input('placements')))->get();
        $coupon = AdCoupon::findCode($request->input('coupon_code'));
        $firstBooking = !$advertiser || !AdCampaign::where('advertiser_id', $advertiser->id)->whereNotNull('paid_at')
            ->when($request->filled('campaign'), fn ($q) => $q->where('id', '!=', (int) $request->input('campaign')))->exists();
        $quote = AdPricing::quote($placements, optional($advertiser?->category)->tier ?? AdCategory::STANDARD, (int) $request->input('days'), $advertiser?->gstin, null, [
            'start_date' => $request->input('start_date'),
            'units' => AdCampaignService::unitsFor($placements, ['page_groups' => (array) $request->input('page_groups', []), 'cabs' => (int) $request->input('cabs', 1)]),
            'exclusive_category' => $request->boolean('exclusive_category'),
            'coupon' => $coupon,
            'first_booking' => $firstBooking,
        ]);
        if ($request->filled('coupon_code')) {
            $quote['coupon_error'] = !$coupon ? 'That coupon code was not found.'
                : ($advertiser ? $coupon->problemFor($advertiser, (int) $quote['list_amount'], $request->filled('campaign') ? (int) $request->input('campaign') : null) : null);
        }

        return response()->json(['status' => true, 'data' => $quote]);
    }

    /** Results by day and placement as CSV. */
    public function export(AdCampaign $campaign)
    {
        $this->own($campaign);

        return response(app(AdReporting::class)->csv($campaign), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="ad-' . $campaign->reference . '-results.csv"',
        ]);
    }

    /** Create (no id) or update a draft. */
    public function saveCampaign(Request $request, ?AdCampaign $campaign = null)
    {
        $advertiser = $this->advertiser();
        if ($campaign) {
            $this->own($campaign);
            $advertiser = $campaign->advertiser()->with('category', 'licences')->first();
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
            'targeting' => 'nullable|array',
            'headline' => 'nullable|string|max:60',
            'push_body' => 'nullable|string|max:160',
            'exclusive_category' => 'nullable|boolean',
            'coupon_code' => 'nullable|string|max:30',
        ]);
        if ($campaign && $campaign->status === AdCampaign::PENDING_PAYMENT && !$campaign->isPaid()) {
            $campaign->forceFill(['status' => AdCampaign::DRAFT, 'hold_expires_at' => null])->save();
        }

        try {
            $campaign = $this->ads->saveDraft($campaign, $advertiser, $data['placements'], $data['start_date'], (int) $data['days'], $data['landing_type'], (string) ($data['landing_value'] ?? ''), [
                'targeting' => (array) ($data['targeting'] ?? []),
                'headline' => $data['headline'] ?? null,
                'push_body' => $data['push_body'] ?? null,
                'exclusive_category' => $request->boolean('exclusive_category'),
                'coupon_code' => $data['coupon_code'] ?? null,
            ]);
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
        // Paid already on an earlier attempt (e.g. the confirmation didn't reach us)? Don't charge again.
        if (!$campaign->isPaid() && ($paid = $payments->reconcile($campaign))) {
            $campaign = $this->ads->paymentReceived($campaign, $paid['gateway'], $paid['transaction_id']);

            return response()->json(['status' => true, 'data' => ['redirect' => route('customer.ads.show', $campaign)]]);
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
        $advertiser = $this->currentAdvertiser();
        if (!$advertiser) {
            abort(response()->json(['status' => false, 'message' => 'Add your business details first.'], 422));
        }
        if ($advertiser->isBlocked()) {
            abort(response()->json(['status' => false, 'message' => 'Your advertiser account is on hold. Please contact us.'], 403));
        }

        return $advertiser;
    }

    private function approvedAgency(): ?AdAgency
    {
        $agency = AdAgency::where('user_id', Auth::guard('customer')->id())->first();

        return $agency && $agency->isApproved() ? $agency : null;
    }

    /** The advertiser profile being used: an agency's selected client, or the user's own. */
    private function currentAdvertiser(): ?AdAdvertiser
    {
        $query = AdAdvertiser::with('category', 'licences')->where('user_id', Auth::guard('customer')->id());
        if ($this->approvedAgency() && ($id = session('ads_client'))) {
            $chosen = (clone $query)->whereKey($id)->first();
            if ($chosen) {
                return $chosen;
            }
        }

        return $query->orderBy('id')->first();
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
            'targeting' => $campaign->targeting ?? (object) [],
            'headline' => $campaign->headline,
            'push_body' => $campaign->push_body,
            'exclusive_category' => (bool) $campaign->exclusive_category,
            'coupon_code' => $campaign->coupon_code,
            'creatives' => $campaign->creatives->mapWithKeys(fn ($c) => [$c->shape => ['url' => $c->url(), 'bytes' => $c->bytes]]),
            'quote' => [
                'days' => (int) $campaign->days,
                'list_amount' => (int) $campaign->list_amount,
                'peak_amount' => (int) $campaign->peak_amount,
                'bundle_discount' => (int) $campaign->bundle_discount,
                'exclusivity_amount' => (int) $campaign->exclusivity_amount,
                'promo_discount' => (int) $campaign->promo_discount,
                'promo_label' => $campaign->promo_label,
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
}
