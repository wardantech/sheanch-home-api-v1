<?php

namespace App\Models\Property;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyAd extends Model
{
    use HasFactory, SoftDeletes;

    public $fillable = [
        'status',
        'user_id',
        'start_date',
        'end_date',
        'property_id',
        'property_category',
        'property_type_id',
        'sale_type',
        'security_money',
        'rent_amount',
        'division_id',
        'district_id',
        'thana_id',
        'created_by',
        'updated_by',
        'description'
    ];

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withTrashed();
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id', 'id')->withTrashed();
    }

}
