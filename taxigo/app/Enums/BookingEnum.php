<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class BookingEnum extends Enum
{
    const COMPLETE = 4;
    const CANCEL = 3;
    // Marked by an admin after pickup time when the customer never showed up;
    // the online advance is forfeited and invoiced.
    const NO_SHOW = 5;
}
