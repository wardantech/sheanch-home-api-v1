<?php

namespace App\Models\Review;

use App\Models\Landlord;
use App\Models\Property\Property;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'reviewer_type_id', 'id')->withTrashed();
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'review_type_id', 'id')->withTrashed();
    }

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(Landlord::class, 'reviewer_type_id', 'id')->withTrashed();
    }

}
