<?php

namespace App\Models\Property;

use App\Models\Landlord;
use App\Models\Settings\District;
use App\Models\Settings\Division;
use App\Models\Settings\PropertyType;
use App\Models\Settings\Thana;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    public function landloard(): BelongsTo
    {
        return $this->belongsTo(Landlord::class, 'landloard_id', 'id')->withTrashed();
    }

    public function thana()
    {
        return $this->belongsTo(Thana::class, 'thana_id', 'id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id', 'id');
    }

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class, 'property_type_id', 'id');
    }

    public function landlord()
    {
        return $this->belongsTo(Landlord::class, 'landlord_id', 'id');
    }
}
