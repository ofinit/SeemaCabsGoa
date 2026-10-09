<?php

namespace Tests\Unit;

use App\Support\Gstin;
use PHPUnit\Framework\TestCase;

class GstinTest extends TestCase
{
    public function test_checksum_is_computed_over_the_first_14_characters(): void
    {
        $first14 = '30AAECO0806H1Z';
        $gstin = $first14 . Gstin::checksum($first14);

        $this->assertTrue(Gstin::isValid($gstin));
        $this->assertSame('30', Gstin::stateCode($gstin));
    }

    public function test_known_valid_gstins(): void
    {
        $this->assertTrue(Gstin::isValid('27AAPFU0939F1ZV'));
        $this->assertTrue(Gstin::isValid(' 27aapfu0939f1zv '));
    }

    public function test_rejects_typos_and_bad_formats(): void
    {
        $this->assertFalse(Gstin::isValid('27AAPFU0939F1ZW'));   // wrong checksum
        $this->assertFalse(Gstin::isValid('27AAPFU0939F1Z'));    // too short
        $this->assertFalse(Gstin::isValid('00AAPFU0939F1ZV'));   // state 00
        $this->assertFalse(Gstin::isValid(''));
        $this->assertFalse(Gstin::isValid(null));
    }
}
