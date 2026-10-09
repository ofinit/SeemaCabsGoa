<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Type;
use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Models\Environment;
use App\Models\SettingChange;
use App\Services\Pricing\FareBreakdown;
use App\Services\Pricing\FareCalculator;
use App\Services\Pricing\PricingSettings;
use App\Support\Gstin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Super-admin settings for pricing (internal fare markup + OfinIT platform
 * fee), GST, and the two invoicing business profiles. Every change is
 * written to setting_changes.
 */
class BillingSettingsController extends Controller
{
    private function authorizeAdmin(): void
    {
        abort_unless(Auth::user() && (int) Auth::user()->type === Type::ADMIN, 403);
    }

    private function history(array $keys)
    {
        return SettingChange::whereIn('key', $keys)->latest('id')->limit(25)->get()
            ->each(function ($change) {
                $change->user_name = $change->user_id ? optional(\App\Models\User::find($change->user_id))->name : 'System';
            });
    }

    // ---- Pricing: markup + platform fee + advance --------------------------

    private const PRICING_FIELDS = [
        PricingSettings::MARKUP_RIDES => ['label' => 'Fare markup — rides (%)', 'min' => -50, 'max' => 100],
        PricingSettings::MARKUP_PACKAGES => ['label' => 'Fare markup — sightseeing packages (%)', 'min' => -50, 'max' => 100],
        Type::CompanyCommission => ['label' => 'OfinIT platform fee — rides (% of fare incl. markup, excl. GST)', 'min' => 0, 'max' => 100],
        Type::PackageAggregatorCommission => ['label' => 'OfinIT platform fee — packages (% of package price incl. markup, excl. GST)', 'min' => 0, 'max' => 100],
        Type::FleetOperatorCommission => ['label' => 'Fleet operator commission — rides (% of fare incl. markup, excl. GST)', 'min' => 0, 'max' => 100],
        Type::TotalCommission => ['label' => 'Advance paid online — rides (% of total incl. GST)', 'min' => 0, 'max' => 100],
    ];

    public function pricing()
    {
        $this->authorizeAdmin();
        $settings = PricingSettings::load();
        $fields = self::PRICING_FIELDS;
        $example = $this->example($settings);
        $history = $this->history(array_keys($fields));

        return view('settings.pricing', compact('settings', 'fields', 'example', 'history'));
    }

    public function savePricing(Request $request)
    {
        $this->authorizeAdmin();
        $rules = [];
        foreach (self::PRICING_FIELDS as $key => $field) {
            $rules[$key] = "required|numeric|min:{$field['min']}|max:{$field['max']}";
        }
        $data = $request->validate($rules);

        foreach ($data as $key => $value) {
            SettingChange::setEnvironment($key, $this->number($value));
        }

        return redirect()->route('admin.setting.pricing')->with('success', 'Pricing saved. New quotes and bookings use it immediately; existing bookings are unchanged.');
    }

    // ---- GST ---------------------------------------------------------------

    public function gst()
    {
        $this->authorizeAdmin();
        $settings = PricingSettings::load();
        $settings[Type::TdsTitle] = Environment::where('title', Type::TdsTitle)->value('value') ?? '0';
        $supplier = BusinessProfile::supplier();
        $platform = BusinessProfile::platform();
        $example = $this->example($settings);
        $history = $this->history([
            PricingSettings::GST_ENABLED, PricingSettings::GST_START_AT, PricingSettings::GST_RATE_RIDES,
            PricingSettings::GST_RATE_PACKAGES, PricingSettings::GST_SAC_RIDES, PricingSettings::GST_SAC_PACKAGES,
            PricingSettings::GST_RATE_PLATFORM_FEE, PricingSettings::GST_SAC_PLATFORM_FEE, Type::TdsTitle,
        ]);

        return view('settings.gst', compact('settings', 'supplier', 'platform', 'example', 'history'));
    }

    public function saveGst(Request $request)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'gst_enabled' => 'nullable|in:1',
            'gst_start_at' => 'nullable|date',
            PricingSettings::GST_RATE_RIDES => 'required|numeric|min:0|max:40',
            PricingSettings::GST_RATE_PACKAGES => 'required|numeric|min:0|max:40',
            PricingSettings::GST_SAC_RIDES => 'nullable|regex:/^\d{4,8}$/',
            PricingSettings::GST_SAC_PACKAGES => 'nullable|regex:/^\d{4,8}$/',
            PricingSettings::GST_RATE_PLATFORM_FEE => 'required|numeric|min:0|max:40',
            PricingSettings::GST_SAC_PLATFORM_FEE => 'nullable|regex:/^\d{4,8}$/',
            Type::TdsTitle => 'required|numeric|min:0|max:100',
        ], [
            'regex' => 'SAC codes are 4 to 8 digits.',
        ]);

        $enabled = ($data['gst_enabled'] ?? null) === '1';
        if ($enabled) {
            $missing = [];
            if (blank($data[PricingSettings::GST_SAC_RIDES] ?? null)) {
                $missing[] = 'SAC code for rides';
            }
            if (blank($data[PricingSettings::GST_SAC_PACKAGES] ?? null)) {
                $missing[] = 'SAC code for packages';
            }
            if (blank($data['gst_start_at'] ?? null)) {
                $missing[] = 'GST start date & time';
            }
            $supplier = BusinessProfile::supplier();
            if (!$supplier || !$supplier->isReadyForInvoicing()) {
                $missing[] = 'complete Seema Holidays business profile (' . implode(', ', $supplier?->missingForInvoicing() ?? ['profile']) . ')';
            }
            if ($missing) {
                return back()->withInput()->with('error', 'Before switching GST on, please add: ' . implode('; ', $missing) . '.');
            }
        }

        $startAt = filled($data['gst_start_at'] ?? null)
            ? Carbon::parse($data['gst_start_at'], 'Asia/Kolkata')->format('Y-m-d H:i')
            : '';

        SettingChange::setEnvironment(PricingSettings::GST_ENABLED, $enabled ? '1' : '0');
        SettingChange::setEnvironment(PricingSettings::GST_START_AT, $startAt);
        foreach ([
            PricingSettings::GST_RATE_RIDES, PricingSettings::GST_RATE_PACKAGES, PricingSettings::GST_RATE_PLATFORM_FEE, Type::TdsTitle,
        ] as $key) {
            SettingChange::setEnvironment($key, $this->number($data[$key]));
        }
        foreach ([PricingSettings::GST_SAC_RIDES, PricingSettings::GST_SAC_PACKAGES, PricingSettings::GST_SAC_PLATFORM_FEE] as $key) {
            SettingChange::setEnvironment($key, (string) ($data[$key] ?? ''));
        }

        return redirect()->route('admin.setting.gst')->with('success', $enabled
            ? 'GST settings saved. GST applies to bookings made from ' . Carbon::parse($startAt)->format('d M Y, h:i A') . ' onwards.'
            : 'GST settings saved. GST is switched off.');
    }

    // ---- Business profiles -------------------------------------------------

    public function profiles()
    {
        $this->authorizeAdmin();
        $profiles = [
            BusinessProfile::SUPPLIER => BusinessProfile::supplier(),
            BusinessProfile::PLATFORM => BusinessProfile::platform(),
        ];
        $history = SettingChange::where('key', 'like', 'profile.%')->latest('id')->limit(25)->get();

        return view('settings.business-profiles', compact('profiles', 'history'));
    }

    public function saveProfile(Request $request, string $role)
    {
        $this->authorizeAdmin();
        abort_unless(in_array($role, [BusinessProfile::SUPPLIER, BusinessProfile::PLATFORM], true), 404);

        $data = $request->validate([
            'legal_name' => 'required|string|max:191',
            'trade_name' => 'nullable|string|max:191',
            'gstin' => ['nullable', 'string', function ($attribute, $value, $fail) {
                if (filled($value) && !Gstin::isValid($value)) {
                    $fail('This GSTIN is not valid (check for typos — the last character is a checksum).');
                }
            }],
            'pan' => 'nullable|regex:/^[A-Za-z]{5}[0-9]{4}[A-Za-z]$/',
            'address_line_one' => 'required|string|max:191',
            'address_line_two' => 'nullable|string|max:191',
            'city' => 'nullable|string|max:100',
            'state_name' => 'required|string|max:100',
            'state_code' => 'required|digits:2',
            'pincode' => 'nullable|digits:6',
            'email' => 'nullable|email|max:191',
            'phone' => 'nullable|string|max:20',
            'signatory_name' => 'nullable|string|max:191',
            'bank_details' => 'nullable|string|max:500',
            'invoice_prefix' => 'required|alpha_num|max:12',
        ], [
            'pan.regex' => 'PAN must look like ABCDE1234F.',
        ]);

        $data['gstin'] = filled($data['gstin'] ?? null) ? Gstin::normalize($data['gstin']) : null;
        $data['pan'] = filled($data['pan'] ?? null) ? strtoupper($data['pan']) : null;
        $data['invoice_prefix'] = strtoupper($data['invoice_prefix']);
        if ($data['gstin'] && Gstin::stateCode($data['gstin']) !== $data['state_code']) {
            return back()->withInput()->with('error', 'The state code must match the first two digits of the GSTIN (' . Gstin::stateCode($data['gstin']) . ').');
        }

        $profile = BusinessProfile::where('role', $role)->firstOrFail();
        $otherPrefix = BusinessProfile::where('role', '!=', $role)->value('invoice_prefix');
        if ($otherPrefix && strtoupper($otherPrefix) === $data['invoice_prefix']) {
            return back()->withInput()->with('error', 'Each business needs its own invoice prefix.');
        }

        foreach ($data as $field => $value) {
            SettingChange::record("profile.{$role}.{$field}", $profile->{$field}, $value);
        }
        $profile->fill($data)->save();

        return redirect()->route('admin.setting.businessProfiles')->with('success', $profile->legal_name . ' details saved. New invoices will use them; issued invoices keep the details they were issued with.');
    }

    // ------------------------------------------------------------------------

    /** Worked example shown on the settings pages. */
    private function example(array $settings): array
    {
        $calc = new FareCalculator($settings);
        $ride = $calc->ride(1000, Type::CITY_RIDES, now('Asia/Kolkata'), now());
        $forcedGst = new FareCalculator(array_merge($settings, [PricingSettings::GST_ENABLED => '1', PricingSettings::GST_START_AT => '']));
        $withGst = $forcedGst->ride(1000, Type::CITY_RIDES, now('Asia/Kolkata'), now());

        $row = fn (FareBreakdown $f) => [
            'fare_before_markup' => FareBreakdown::rupees($f->farePreMarkup),
            'markup' => FareBreakdown::rupees($f->markup),
            'fare' => FareBreakdown::rupees($f->fare),
            'gst' => FareBreakdown::rupees($f->gst),
            'total' => FareBreakdown::rupees($f->total),
            'advance' => FareBreakdown::rupees($f->advance),
            'balance' => FareBreakdown::rupees($f->balance),
            'platform_fee' => FareBreakdown::rupees($f->platformFee),
            'operator' => FareBreakdown::rupees($f->fleetOperatorPayment),
            'retained' => FareBreakdown::rupees($f->advance - $f->platformFee - $f->fleetOperatorPayment),
        ];

        return ['now' => $row($ride), 'with_gst' => $row($withGst)];
    }

    private function number($value): string
    {
        $value = (string) (0 + $value);

        return $value === '-0' ? '0' : $value;
    }
}
