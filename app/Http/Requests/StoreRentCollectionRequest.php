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
            'due_amount' => 'nullable',
            'remark' => 'nullable|string',
            'bank_account_id' => 'nullable',
            'user_id' => 'required|integer',
            'property_id' => 'required|integer',
            'transaction_id' => 'nullable|string',
            'mobile_bank_account_id' => 'nullable',
            'payment_method' => 'required|integer',
            'property_deed_id' => 'required|integer',
            'date' => [
                'required',
                new BeforeMonthRule,
                new UniqueDeedDateRule($this->property_deed_id, $this->user_id)
            ]
        ];
    }
}
