<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facility extends Model
{
    use HasFactory, SoftDeletes;

    public function facilityCategory(): BelongsTo
    {
        return $this->belongsTo(FacilityCategory::class, 'facility_category_id', 'id')->withTrashed();
    }
}
