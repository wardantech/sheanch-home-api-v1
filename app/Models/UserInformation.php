<?php

namespace App\Models;

use App\Models\Settings\District;
use App\Models\Settings\Division;
use App\Models\Settings\Thana;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'division_id',
        'district_id',
        'thana_id',
        'nid',
        'image',
        'postal_address',
        'residential_address',
        'description'
    ];

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id', 'id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function thana()
    {
        return $this->belongsTo(Thana::class, 'thana_id', 'id');
    }
}
