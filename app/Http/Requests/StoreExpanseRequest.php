<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpanseRequest extends FormRequest
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
            'date' => 'required',
            'cash_out' => 'required',
            'remark' => 'nullable|string',
            'transaction_id' => 'nullable',
            'user_id' => 'required|integer',
            'bank_account_id' => 'nullable',
            'property_id' => 'required|integer',
            'payment_method' => 'required|integer',
            'mobile_bank_account_id' => 'nullable',
            'expanse_item_id' => 'required|integer'
        ];
    }
}
