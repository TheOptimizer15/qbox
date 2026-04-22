<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'owner_id'])]
class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasUuids, HasFactory;

    /**
     * Get the user that owns the company.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the stores that belong to the company.
     */
    public function stores()
    {
        return $this->hasMany(Store::class, 'company_id');
    }
}
