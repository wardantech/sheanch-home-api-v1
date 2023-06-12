<?php

namespace App\Models\Accounts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MobileBankAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'mobile_banking_id', 'account_number'];

    public function mobileBank()
    {
        return $this->belongsTo(MobileBanking::class, 'mobile_banking_id', 'id');
    }
}
