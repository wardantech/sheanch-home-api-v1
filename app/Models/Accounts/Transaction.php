<?php

namespace App\Models\Accounts;

use App\Models\Property\Property;
use App\Models\Property\PropertyDeed;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory,SoftDeletes;

    protected $dates = ['date'];

    protected $fillable = [
        'date',
        'remark',
        'due_id',
        'cash_in',
        'user_id',
        'cash_out',
        'is_initial',
        'due_amount',
        'created_by',
        'updated_by',
        'account_id',
        'property_id',
        'payment_method',
        'bank_account_id',
        'expanse_item_id',
        'property_deed_id',
        'transaction_id',
        'transaction_purpose',
        'mobile_bank_account_id'
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function deed()
    {
        return $this->belongsTo(PropertyDeed::class, 'property_deed_id', 'id');
    }

    public function mobileBank()
    {
        return $this->belongsTo(MobileBanking::class, 'mobile_banking_id', 'id');
    }
}
