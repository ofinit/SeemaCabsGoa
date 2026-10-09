<?php

namespace App\Services\Ads;

use App\Enums\Type;

/**
 * Ad targeting (plan §6). Stored on the campaign and copied to its serving
 * rows (`advertisements.targeting`):
 *
 *   areas        ['north', 'south']         — pickup / drop of the booking
 *   trips        ['airport_pickup', 'airport_drop', 'local', 'sightseeing']
 *   days         [0..6] (0 = Sunday)        — IST
 *   hours        ['from' => 'HH:MM', 'to' => 'HH:MM'] (overnight allowed)
 *   package_id   P14: the sightseeing package the stop is on
 *   page_groups  P9: website page groups
 *
 * Area and trip type narrow an ad only where the booking is known (trip
 * screens); elsewhere the ad shows to everyone. Day and hour apply
 * everywhere. Package and page group are strict.
 */
class AdTargeting
{
    public const AREAS = ['north' => 'North Goa', 'south' => 'South Goa'];

    public const TRIPS = [
        'airport_pickup' => 'Airport pickups',
        'airport_drop' => 'Airport drops',
        'local' => 'Local rides',
        'sightseeing' => 'Sightseeing tours',
    ];

    public const DAYS = [1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat', 0 => 'Sun'];

    /** Website page groups for P9 (file names of the marketing pages). */
    public const PAGE_GROUPS = [
        'airport' => ['label' => 'Airport transfers', 'pages' => ['airport-taxi-goa', 'dabolim-airport-to-calangute-taxi', 'dabolim-airport-to-colva-taxi', 'mopa-airport-to-baga-taxi', 'mopa-airport-to-calangute-taxi']],
        'nightlife' => ['label' => 'Nightclub & party drops', 'pages' => ['goa-nightclub-taxi-drops']],
        'north' => ['label' => 'North Goa routes (Baga, Calangute, Vagator…)', 'pages' => ['baga-to-anjuna-taxi', 'calangute-to-candolim-taxi', 'panjim-to-baga-taxi', 'panjim-to-calangute-taxi', 'taxi-service-in-morjim', 'taxi-service-in-vagator', 'thivim-railway-station-to-baga-taxi']],
        'south' => ['label' => 'South Goa routes (Colva, Palolem…)', 'pages' => ['madgaon-railway-station-to-colva-taxi', 'madgaon-railway-station-to-palolem-taxi']],
        'sightseeing' => ['label' => 'Sightseeing tours', 'pages' => ['goa-sightseeing-tour', 'taxi-from-calangute-to-dudhsagar']],
        'general' => ['label' => 'Home page & city transfers', 'pages' => ['index', 'in-city-transfers']],
    ];

    private const NORTH = ['panaji', 'panjim', 'mapusa', 'calangute', 'candolim', 'baga', 'anjuna', 'vagator', 'arambol', 'morjim', 'mandrem', 'ashwem',
        'siolim', 'sinquerim', 'porvorim', 'old goa', 'bicholim', 'pernem', 'assagao', 'chapora', 'dona paula', 'miramar', 'mopa', 'arpora', 'nerul',
        'saligao', 'sangolda', 'parra', 'valpoi', 'thivim', 'ponda', 'reis magos', 'aldona', 'GOX'];

    private const SOUTH = ['margao', 'madgaon', 'vasco', 'colva', 'benaulim', 'palolem', 'agonda', 'canacona', 'varca', 'cavelossim', 'betalbatim',
        'majorda', 'utorda', 'bogmalo', 'dabolim', 'cansaulim', 'quepem', 'sanguem', 'cola', 'galgibaga', 'patnem', 'mobor', 'velsao', 'arossim',
        'verna', 'cortalim', 'curchorem', 'GOI'];

    /** Clean, validated targeting from user input (null when nothing is set). */
    public static function normalize(array $input): ?array
    {
        $out = [];
        $areas = array_values(array_intersect((array) ($input['areas'] ?? []), array_keys(self::AREAS)));
        if ($areas && count($areas) < count(self::AREAS)) {
            $out['areas'] = $areas;
        }
        $trips = array_values(array_intersect((array) ($input['trips'] ?? []), array_keys(self::TRIPS)));
        if ($trips && count($trips) < count(self::TRIPS)) {
            $out['trips'] = $trips;
        }
        $days = array_values(array_unique(array_map('intval', array_intersect(array_map('strval', (array) ($input['days'] ?? [])), array_map('strval', array_keys(self::DAYS))))));
        if ($days && count($days) < 7) {
            sort($days);
            $out['days'] = $days;
        }
        $from = (string) ($input['hours']['from'] ?? '');
        $to = (string) ($input['hours']['to'] ?? '');
        if (preg_match('/^\d{2}:\d{2}$/', $from) && preg_match('/^\d{2}:\d{2}$/', $to) && $from !== $to) {
            $out['hours'] = ['from' => $from, 'to' => $to];
        }
        if (!empty($input['package_id'])) {
            $out['package_id'] = (int) $input['package_id'];
        }
        $groups = array_values(array_intersect((array) ($input['page_groups'] ?? []), array_keys(self::PAGE_GROUPS)));
        if ($groups) {
            $out['page_groups'] = $groups;
        }

        return $out ?: null;
    }

    /**
     * @param  array{area?: ?string, trip?: ?string, package_id?: ?int, page_group?: ?string}  $context
     */
    public static function matches(?array $targeting, array $context = [], ?\DateTimeInterface $now = null): bool
    {
        if (!$targeting) {
            return true;
        }
        $now = $now ? \Carbon\Carbon::instance($now)->timezone('Asia/Kolkata') : now('Asia/Kolkata');

        if (!empty($targeting['days']) && !in_array((int) $now->dayOfWeek, array_map('intval', $targeting['days']), true)) {
            return false;
        }
        if (!empty($targeting['hours'])) {
            $t = $now->format('H:i');
            [$from, $to] = [$targeting['hours']['from'], $targeting['hours']['to']];
            $inside = $from < $to ? ($t >= $from && $t < $to) : ($t >= $from || $t < $to);
            if (!$inside) {
                return false;
            }
        }
        if (!empty($targeting['areas']) && !empty($context['area']) && !in_array($context['area'], $targeting['areas'], true)) {
            return false;
        }
        if (!empty($targeting['trips']) && !empty($context['trip']) && !in_array($context['trip'], $targeting['trips'], true)) {
            return false;
        }
        if (!empty($targeting['package_id']) && (int) ($context['package_id'] ?? 0) !== (int) $targeting['package_id']) {
            return false;
        }
        if (!empty($targeting['page_groups']) && !in_array($context['page_group'] ?? null, $targeting['page_groups'], true)) {
            return false;
        }

        return true;
    }

    /** Context for a booking: area from pickup / drop names, trip type. */
    public static function contextForBooking(object|array|null $booking): array
    {
        if (!$booking) {
            return [];
        }
        $get = fn ($key) => is_array($booking) ? ($booking[$key] ?? null) : ($booking->{$key} ?? null);
        $trip = match (true) {
            !empty($get('sight_seeing_package_id')) => 'sightseeing',
            (int) $get('trip_type') === Type::AIRPORT_PICKUP => 'airport_pickup',
            (int) $get('trip_type') === Type::AIRPORT_DROP => 'airport_drop',
            default => 'local',
        };

        return [
            'trip' => $trip,
            'area' => self::areaOf($get('drop_to'), $get('drop_of_address')) ?? self::areaOf($get('pickup_from'), $get('pickup_address')),
            'package_id' => $get('sight_seeing_package_id') ? (int) $get('sight_seeing_package_id') : null,
        ];
    }

    /** 'north' / 'south' from place names, or null if unknown. */
    public static function areaOf(?string ...$texts): ?string
    {
        $text = ' ' . mb_strtolower(implode(' ', array_filter($texts))) . ' ';
        foreach (['north' => self::NORTH, 'south' => self::SOUTH] as $area => $places) {
            foreach ($places as $place) {
                if (preg_match('/\b' . preg_quote(mb_strtolower($place), '/') . '\b/u', $text)) {
                    return $area;
                }
            }
        }

        return null;
    }

    public static function pageGroupOf(string $page): ?string
    {
        $page = preg_replace('/\.html$/', '', basename($page)) ?: 'index';
        foreach (self::PAGE_GROUPS as $group => $info) {
            if (in_array($page, $info['pages'], true)) {
                return $group;
            }
        }

        return null;
    }

    /** One-line summary for people. */
    public static function describe(?array $targeting): string
    {
        if (!$targeting) {
            return 'Everyone';
        }
        $parts = [];
        if (!empty($targeting['areas'])) {
            $parts[] = implode(' & ', array_map(fn ($a) => self::AREAS[$a] ?? $a, $targeting['areas']));
        }
        if (!empty($targeting['trips'])) {
            $parts[] = implode(', ', array_map(fn ($t) => self::TRIPS[$t] ?? $t, $targeting['trips']));
        }
        if (!empty($targeting['days'])) {
            $parts[] = implode(' ', array_map(fn ($d) => self::DAYS[$d] ?? $d, $targeting['days']));
        }
        if (!empty($targeting['hours'])) {
            $parts[] = $targeting['hours']['from'] . '–' . $targeting['hours']['to'];
        }
        if (!empty($targeting['page_groups'])) {
            $parts[] = 'Pages: ' . implode(', ', array_map(fn ($g) => self::PAGE_GROUPS[$g]['label'] ?? $g, $targeting['page_groups']));
        }
        if (!empty($targeting['package_id'])) {
            $parts[] = 'Package #' . $targeting['package_id'];
        }

        return implode(' · ', $parts) ?: 'Everyone';
    }
}
