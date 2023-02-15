<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyAdRequest extends FormRequest
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
            'user_id' => 'required',
            'status' => 'required|integer',
            'rent_amount' => 'required',
            'start_date' => 'required',
            'end_date' => 'nullable',
            'property_id' => 'required',
            'description' => 'nullable',
            'district_id' => 'required|integer',
            'division_id' => 'required|integer',
            'property_category' => 'required',
            'property_category_id' => 'required|integer',
            'property_id' => 'required|integer',
            'property_type_id' => 'required|integer',
            'sale_type' => 'required|integer',
            'security_money' => 'required',
            'status' => 'required',
            'status' => 'required',
            'thana_id' => 'required|integer',
            'user_id' => 'required|integer'
        ];
    }
}
