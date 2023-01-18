<?php

namespace App\Models\Property;

use App\Models\Accounts\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyDeed extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['landlord_id', 'tenant_id', 'property_id', 'property_ad_id', 'status', 'start_date'];

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id', 'id')->withTrashed();
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id', 'id')->withTrashed();
    }

    public function propertyAd(): BelongsTo
    {
        return $this->belongsTo(PropertyAd::class, 'property_ad_id', 'id')->withTrashed();
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id', 'id')->withTrashed();
    }

    public function deedInfo()
    {
        return $this->hasOne(DeedInformation::class, 'property_deed_id', 'id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'property_deed_id', 'id');
    }
}
