<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacilityCategory extends Model
{
    use HasFactory, SoftDeletes;

    public function Facility(): HasMany
    {
        return $this->hasMany(Facility::class, 'utility_category_id', 'id');
    }
}
