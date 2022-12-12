<?php

namespace App\Models\Accounts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'user_id',
        'payment_method',
        'property_id',
        'account_id',
        'mobile_banking_id',
        'property_deed_id',
        'due_id',
        'transaction_purpose',
        'cash_in',
        'cash_out',
        'remark',
        'date'
    ];
}
