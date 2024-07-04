<?php

namespace Modules\SPTransfer\Models\Enums;

use App\Contracts\Enum;

class Status extends Enum
{
    public const PENDING = 0;
    public const ACCEPTED = 1;
    public const REJECTED = 2;

    public static array $labels = [
        self::PENDING   => 'Pending',
        self::ACCEPTED   => 'Accepted',
        self::REJECTED   => 'Rejected',
    ];
}
