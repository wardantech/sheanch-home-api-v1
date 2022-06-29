<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Utility extends Model
{
    use HasFactory, SoftDeletes;

    public function utilityCategory()
    {
        return $this->belongsTo(UtilityCategory::class, 'utility_category_id', 'id');
    }
}
