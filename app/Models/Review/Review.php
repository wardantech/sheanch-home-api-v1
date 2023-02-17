<?php

namespace App\Models\Review;

use App\Models\Landlord;
use App\Models\Property\Property;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_type_id', 'id')->withTrashed();
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'review_type_id', 'id')->withTrashed();
    }

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_type_id', 'id')->withTrashed();
    }

    public function store($value)
    {
        $this->review = $value->review;
        $this->reviewer_type = $value->reviewer_type;
        $this->review_type = $value->review_type;
        $this->review_type_id = $value->review_type_id;
        $this->reviewer_type_id = $value->reviewer_type_id;
        $this->rating = $value->rating;
        $this->status = $value->status;
    }
}
