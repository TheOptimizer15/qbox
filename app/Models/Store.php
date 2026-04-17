<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'owner_id'])]
class Store extends Model
{
    use HasFactory, HasUuids;
    public function user(){
        return $this->belongsTo(User::class, 'owner_id');
    }
}
