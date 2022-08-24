<?php

namespace App\Models;

use App\Models\Property\PropertyAd;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wishlist extends Model
{
    use HasFactory;

    public function propertyAd(): BelongsTo
    {
        return $this->belongsTo(PropertyAd::class, 'property_ad_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
}
