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
        'user_id',
        'payment_method',
        'property_id',
        'bank_id',
        'account_id',
        'mobile_banking_id',
        'property_deed_id',
        'expanse_item_id',
        'due_id',
        'transaction_id',
        'transaction_purpose',
        'cash_in',
        'cash_out',
        'due_amount',
        'remark',
        'date',
        'created_by',
        'updated_by'
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
