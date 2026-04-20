<?php

namespace App\Enums;

enum InvitationStatus: string
{
    case PENDING = 'pending';
    case DENIED = 'denied';
    case ACCEPTED = 'accepted';
    case CANCELLED = 'cancelled';
}
