<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * The static marketing pages (index.html, airport-taxi-goa.html, …). They are
 * kept out of public/ and served from here, so every request passes
 * PlatformSuspensionGate: while Seema's service is suspended they show the
 * "temporarily unavailable" page straight away, and come back the moment the
 * invoice is paid. No session, so no cookies and they stay cacheable.
 */
class MarketingPageController extends Controller
{
    /** Browser / CDN cache: short, so a suspension shows within minutes. */
    private const MAX_AGE = 300;

    public function show(string $page = 'index'): BinaryFileResponse
    {
        abort_unless(preg_match('/^[a-z0-9][a-z0-9-]{0,80}$/', $page) === 1, 404);
        $file = self::directory() . DIRECTORY_SEPARATOR . $page . '.html';
        abort_unless(is_file($file), 404);

        return response()->file($file, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Cache-Control' => 'public, max-age=' . self::MAX_AGE,
        ]);
    }

    /** Is the marketing site available (Docker image, or the repository root in local dev)? */
    public static function available(): bool
    {
        return is_file(self::directory() . DIRECTORY_SEPARATOR . 'index.html');
    }

    /** resources/marketing in the Docker image; the repository root (taxigo/..) in local dev. */
    private static function directory(): string
    {
        $built = resource_path('marketing');

        return is_dir($built) ? $built : dirname(base_path());
    }
}
