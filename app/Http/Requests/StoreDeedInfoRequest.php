<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeedInfoRequest extends FormRequest
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
            'property_deed_id' => 'required',
            'tenant_name' => 'required|string',
            'fathers_name' => 'required|string',
            'date_of_birth' => 'required|string',
            'marital_status' => 'required',
            'occupation' => 'required|string',
            'office_address' => 'required|string',
            'present_address' => 'required|string',
            'religion' => 'required|string',
            'edu_qualif' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'nid' => 'required|string',
            'passport' => 'nullable|string',
            'emergency_contact.*' => 'required',
            'family_members.*' => 'required',
            'home_servant.*' => 'nullable',
            'driver.*' => 'nullable',
            'previus_landlord.*' => 'nullable',
            'leaving_home' => 'nullable|string',
            'issue_date' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'tenant_name.required' => 'The name field is required',
            'fathers_name.required' => 'The Father\'s name field is required',
            'edu_qualif.required' => 'The education qualification field is required',
            'nid.required' => 'The national id field is required',
            'present_address.required' => 'The present address field is required',
            'office_address.required' => 'The office address field is required',
            'emergency_contact.name.required' => 'The emergency contact name field is required',
            'emergency_contact.relation.required' => 'The emergency contact relation field is required',
            'emergency_contact.address.required' => 'The emergency contact address field is required',
            'emergency_contact.phone.required' => 'The emergency contact phone field is required',
        ];
    }
}
