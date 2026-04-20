<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case OWNER = 'owner';
    case ASSISTANT = 'assistant';
    case CASHIER = 'cashier';
    case MANAGER = 'manager';

    // these are the role allowed for the owners
    public static function tenants(): array
    {
        return [
            self::CASHIER->value,
            self::MANAGER->value,
            self::ASSISTANT->value,
        ];
    }
}
