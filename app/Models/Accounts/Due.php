<?php

namespace App\Models\Accounts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Due extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'property_id', 'property_deed_id', 'amount', 'date'];
}
