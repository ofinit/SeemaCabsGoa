<?php

namespace App\Services\Ads;

use Illuminate\Support\Facades\Http;

/**
 * Opens an advertiser's link from the server without letting it reach our
 * own network (SSRF): every hop, including each redirect, must be http(s)
 * on a host that resolves only to public IP addresses, and the request is
 * pinned to the address that was checked (no DNS-rebinding window).
 */
class AdLinkChecker
{
    private const MAX_REDIRECTS = 5;

    /** HTTP status of the final response, or null if the link is unsafe or unreachable. */
    public static function status(?string $url, int $timeout = 8): ?int
    {
        for ($hop = 0; $url && $hop <= self::MAX_REDIRECTS; $hop++) {
            $parts = parse_url($url);
            $scheme = strtolower($parts['scheme'] ?? '');
            $host = strtolower(trim((string) ($parts['host'] ?? ''), '[]'));
            if (!in_array($scheme, ['http', 'https'], true) || $host === '' || isset($parts['user'])) {
                return null;
            }
            $ip = self::publicAddress($host);
            if (!$ip) {
                return null;
            }
            $port = (int) ($parts['port'] ?? ($scheme === 'https' ? 443 : 80));
            try {
                $response = Http::timeout($timeout)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0 (SeemaCabsGoa link check)'])
                    ->withOptions([
                        'allow_redirects' => false,
                        'curl' => [CURLOPT_RESOLVE => ["{$host}:{$port}:{$ip}"]],
                    ])
                    ->get($url);
            } catch (\Throwable $e) {
                return null;
            }
            if (!in_array($response->status(), [301, 302, 303, 307, 308], true)) {
                return $response->status();
            }
            $url = self::resolve($url, (string) $response->header('Location'));
        }

        return null;
    }

    /** The host's first IPv4 address if every address it resolves to is public. */
    public static function publicAddress(string $host): ?string
    {
        if ($host === 'localhost' || str_ends_with($host, '.localhost') || str_ends_with($host, '.internal') || str_ends_with($host, '.local')) {
            return null;
        }
        $ips = filter_var($host, FILTER_VALIDATE_IP) ? [$host] : (gethostbynamel($host) ?: []);
        if (!$ips) {
            return null;
        }
        foreach ($ips as $ip) {
            if (!self::isPublicIp($ip)) {
                return null;
            }
        }

        return $ips[0];
    }

    public static function isPublicIp(string $ip): bool
    {
        return (bool) filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)
            && !str_starts_with($ip, '100.64.') && !str_starts_with($ip, '0.');
    }

    private static function resolve(string $base, string $location): ?string
    {
        if ($location === '') {
            return null;
        }
        if (preg_match('#^https?://#i', $location)) {
            return $location;
        }
        $b = parse_url($base);
        $origin = $b['scheme'] . '://' . $b['host'] . (isset($b['port']) ? ':' . $b['port'] : '');
        if (str_starts_with($location, '//')) {
            return $b['scheme'] . ':' . $location;
        }
        if (str_starts_with($location, '/')) {
            return $origin . $location;
        }
        $dir = rtrim(str_contains($b['path'] ?? '/', '/') ? substr($b['path'] ?? '/', 0, strrpos($b['path'] ?? '/', '/') + 1) : '/', '/') . '/';

        return $origin . $dir . $location;
    }
}
