<?php

namespace App\Models\Accounts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpanseItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'created_by', 'updated_by'];
}
