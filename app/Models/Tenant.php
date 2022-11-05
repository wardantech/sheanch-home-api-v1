<?php

namespace App\Models;

use App\Models\Review\Review;
use App\Models\Settings\Thana;
use App\Models\Settings\District;
use App\Models\Settings\Division;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'id','landlord_id')->withTrashed();
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

    public function reviews()
    {
        return $this->hasMany(Review::class, 'review_type_id', 'id')
            ->where('review_type', 3);
    }
}
