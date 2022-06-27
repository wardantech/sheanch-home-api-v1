<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Landlord extends Model
{
    use HasFactory, SoftDeletes;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'id','landlord_id');
    }

}
