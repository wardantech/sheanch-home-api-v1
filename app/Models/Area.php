<?php

namespace App\Models;

use App\Models\Settings\Thana;
use App\Models\Settings\District;
use App\Models\Settings\Division;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'division_id',
        'district_id',
        'thana_id',
        'name',
        'bn_name',
        'created_at',
    ];

    public function thanas(): BelongsTo
    {
        return $this->belongsTo(Thana::class, 'thana_id', 'id');
    }

    public function districts(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function divisions(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'division_id', 'id');
    }
}
