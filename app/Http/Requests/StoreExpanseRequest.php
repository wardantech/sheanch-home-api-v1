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
            'user_id' => 'required|integer',
            'mobile_banking_id' => 'nullable',
            'transaction_id' => 'nullable',
            'property_id' => 'required|integer',
            'payment_method' => 'required|integer',
            'expanse_item_id' => 'required|integer'
        ];
    }
}
