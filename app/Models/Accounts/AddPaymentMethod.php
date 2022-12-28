<?php

namespace App\Models\Accounts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AddPaymentMethod extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'bank_id', 'mobile_banking_id', 'account_number'];

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id', 'id');
    }
}
