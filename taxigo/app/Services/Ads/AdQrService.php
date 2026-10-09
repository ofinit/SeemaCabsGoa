<?php

namespace App\Services\Ads;

use App\Models\AdQrCard;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

/** In-cab QR cards (P18): QR images for the printable cards. */
class AdQrService
{
    public static function svg(AdQrCard $card, int $size = 300): string
    {
        $writer = new Writer(new ImageRenderer(new RendererStyle($size, 1), new SvgImageBackEnd()));
        $svg = $writer->writeString($card->url());

        // Drop the XML prolog so the SVG can be inlined in HTML.
        return preg_replace('/^<\?xml[^>]*>\s*/', '', $svg);
    }
}
