<?php

namespace App\Models\Pages;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyFaq extends Model
{
    use HasFactory, SoftDeletes;

    public $fillable = ['title', 'description', 'status'];
}
