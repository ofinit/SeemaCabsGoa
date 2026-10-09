<?php

namespace App\Support;

/**
 * GSTIN (Indian GST identification number) helpers.
 *
 * Format: 2-digit state code + 10-char PAN + entity number + 'Z' + checksum,
 * e.g. 30AAECO0806H1Z1. The 15th character is a base-36 checksum over the
 * first 14, so a typo in any position is caught before it reaches an invoice.
 */
class Gstin
{
    private const CHARSET = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    public static function normalize(?string $gstin): string
    {
        return strtoupper(preg_replace('/\s+/', '', (string) $gstin));
    }

    public static function isValid(?string $gstin): bool
    {
        $gstin = self::normalize($gstin);

        if (!preg_match('/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/', $gstin)) {
            return false;
        }

        $state = (int) substr($gstin, 0, 2);
        if ($state < 1 || $state > 38 && $state !== 97 && $state !== 99) {
            return false;
        }

        return self::checksum(substr($gstin, 0, 14)) === $gstin[14];
    }

    public static function stateCode(?string $gstin): ?string
    {
        $gstin = self::normalize($gstin);

        return strlen($gstin) >= 2 ? substr($gstin, 0, 2) : null;
    }

    public static function checksum(string $first14): string
    {
        $sum = 0;
        for ($i = 0; $i < 14; $i++) {
            $value = strpos(self::CHARSET, $first14[$i]);
            $product = $value * (($i % 2) + 1);
            $sum += intdiv($product, 36) + ($product % 36);
        }

        return self::CHARSET[(36 - ($sum % 36)) % 36];
    }
}
