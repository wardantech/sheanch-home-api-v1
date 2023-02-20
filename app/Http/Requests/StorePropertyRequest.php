<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest extends FormRequest
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
            'name' => 'required|string',
            'user_id' => 'required|integer',
            'thana_id' => 'required|integer',
            'district_id' => 'required|integer',
            'division_id' => 'required|integer',
            'area_id' => 'required|integer',
            'property_type_id' => 'required|integer',
            'property_category' => 'required|integer',
            'sale_type' => 'required|integer',
            'bed_rooms' => 'required|integer',
            'balcony' => 'required|integer',
            'floor' => 'required|string',
            'bath_rooms' => 'required|integer',
            'holding_number' => 'required|string',
            'road_number' => 'required|string',
            'zip_code' => 'required|string',
            'address' => 'required|string',
            'rent_amount' => 'required',
            'security_money' => 'nullable',
            'area_size' => 'nullable|integer',
            'video_link' => 'nullable|string',
            'utilities' => 'nullable|array',
            'facilitie_ids' => 'nullable|array',
            'google_map_location' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'nullable|integer',
        ];
    }
}
