<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class NotificationEnum extends Enum
{
    const READ = 1;
    const UNREAD = 2;
    const ADMIN_ID = 1;
    const FLEET_OPERATOR_ID = 1;
}
