<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'location', 'longitude', 'latitude', 'online'])]
class Store extends Model
{
    use HasFactory, HasUuids;

    protected function casts(): array
    {
        return [
            'online' => 'boolean',
            'longitude' => 'float',
            'latitude' => 'float',
        ];
    }

    // a store can have many tenants
    public function tenants()
    {
        return $this->hasMany(User::class, 'store_id');
    }

    // a store belongs to a company
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function inviations(){
        return $this->hasMany(Invitation::class, 'store_id');
    }
}
