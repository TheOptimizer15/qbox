<?php

namespace App\Models;

use App\Enums\TenantRole;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

#[Fillable(['store_id', 'name', 'role', 'email', 'phone_number'])]
#[Hidden(['invitation_id'])]
class Invitation extends Model
{
    /**
     * @use HasFactory<InvitationFactory>
     */
    use HasFactory, HasUuids;

    protected function casts(): array
    {
        return [
            'role' => TenantRole::class,
        ];
    }

    protected $appends = [
        'is_expired',
    ];

    public function getIsExpiredAttribute()
    {
        return Carbon::parse($this->expires_at)->isNowOrPast();
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function invitedBy(){
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function isExpired()
    {
        return Carbon::parse($this->expires_at)->isNowOrPast();
    }
}
