<?php

namespace App\Http\Requests;

use App\Rules\BeforeMonthRule;
use App\Rules\UniqueDeedDateRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRentCollectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'cash_in' => 'required',
            'bank_id' => 'nullable',
            'remark' => 'nullable|string',
            'user_id' => 'required|integer',
            'mobile_banking_id' => 'nullable',
            'property_id' => 'required|integer',
            'payment_method' => 'required|integer',
            'property_deed_id' => 'required|integer',
            'transaction_id' => 'nullable|string',
            'date' => [
                'required',
                new BeforeMonthRule,
                new UniqueDeedDateRule($this->property_deed_id, $this->user_id)
            ]
        ];
    }
}
