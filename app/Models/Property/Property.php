<?php

namespace App\Models\Property;

use App\Models\Accounts\Revenue;
use App\Models\Landlord;
use App\Models\Review\Review;
use App\Models\Settings\District;
use App\Models\Settings\Division;
use App\Models\Settings\PropertyType;
use App\Models\Settings\Thana;
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
        return $this->belongsTo(Landlord::class, 'landlord_id', 'id')->withTrashed();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'review_type_id', 'id');
    }

    public function revenue()
    {
        return $this->hasMany(Revenue::class);
    }
}
