<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['first_name', 'last_name', 'phone_number', 'password', 'role', 'is_active', 'blocked_reason', 'store_id'])]
#[Hidden(['password', 'remember_token', 'created_at', 'updated_at'])]

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasUuids, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class
        ];
    }

    protected $appends = [
        'joined_at',
    ];

    public function getJoinedAtAttribute()
    {
        return $this->created_at;
    }

    public function company()
    {
        return $this->hasOne(Company::class, 'owner_id');
    }

    // a user belongs to store if the role is cashier
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function invitations(){
        return $this->hasMany(Invitation::class, 'invited_by');
    }

    public function isActive(){
        return $this->is_active && !$this->blocked_reason;
    }
}
