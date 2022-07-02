<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class UtilityCategory extends Model
{
    use HasFactory, SoftDeletes;

    public function utilities(): HasMany
    {
        return $this->hasMany(Utility::class, 'utility_category_id', 'id')->withTrashed();
    }
}
