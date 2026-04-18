<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

#[Fillable(['store_id', 'inivation_id', 'expires_at', 'name'])]
#[Hidden(['inivation_id'])]
class Invitation extends Model
{
    use HasUuids;

    public function store(){
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function isExpired(){
        return Carbon::parse($this->expires_at)->isNowOrPast();
    }

    public function owner(){
        return $this->belongsTo(User::class, 'invited_by');
    }
}
