<?php

namespace App\Models\Property;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeedInformation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_deed_id',
        'image',
        'email',
        'tenant_name',
        'fathers_name',
        'date_of_birth',
        'marital_status',
        'present_address',
        'occupation',
        'office_address',
        'religion',
        'edu_qualif',
        'phone',
        'nid',
        'passport',
        'emergency_contact',
        'family_members',
        'home_servant',
        'driver',
        'previus_landlord',
        'leaving_home',
        'issue_date'
    ];
}
