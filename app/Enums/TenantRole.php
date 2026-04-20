<?php

namespace App\Enums;

/**
 * The tenant role holds the possible roles allowed to a user whom has been assigned
 * a store
*/
enum TenantRole: string
{
    case ASSISTANT = 'assistant';
    case CASHIER = 'cashier';
    case MANAGER = 'manager';
}
