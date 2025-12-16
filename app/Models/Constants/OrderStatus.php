<?php

namespace App\Models\Constants;

class OrderStatus
{
    public const PENDING = 'pending';
    public const ACCEPTED = 'accepted';
    public const REJECTED = 'rejected';

    public static function all(): array
    {
        return [
            self::PENDING => 'Pending',
            self::ACCEPTED => 'Accepted',
            self::REJECTED => 'Rejected',
        ];
    }
}

