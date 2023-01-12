<?php

namespace App\Models\Property;

use App\Models\Accounts\Revenue;
use App\Models\Landlord;
use App\Models\Review\Review;
use App\Models\Settings\District;
use App\Models\Settings\Division;
use App\Models\Settings\PropertyType;
use App\Models\Settings\Thana;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Property extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'name',
        'user_id',
        'thana_id',
        'district_id',
        'division_id',
        'property_category',
        'property_type_id',
        'sale_type',
        'bed_rooms',
        'balcony',
        'floor',
        'bath_rooms',
        'holding_number',
        'road_number',
        'zip_code',
        'address',
        'rent_amount',
        'total_amount',
        'security_money',
        'area_size',
        'video_link',
        'utilities',
        'facilitie_ids',
        'google_map_location',
        'description',
        'created_by',
        'updated_by'
    ];

    public function thana(): BelongsTo
    {
        return $this->belongsTo(Thana::class, 'thana_id', 'id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'division_id', 'id');
    }

    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class, 'property_type_id', 'id');
    }

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withTrashed();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'review_type_id', 'id');
    }

    public function revenue(): HasMany
    {
        return $this->hasMany(Revenue::class);
    }

    public function deed(): HasMany
    {
        return $this->hasMany(PropertyDeed::class, 'property_id', 'id');
    }

}
