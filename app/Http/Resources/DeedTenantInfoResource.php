<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeedTenantInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'status' => $this->status,
            'flatName' => $this->property->name,
            'holding' => $this->property->holding_number,
            'road' => $this->property->road_number,
            'zip' => $this->property->zip_code,
            'image' => $this->deedInfo->image,
            'name' => $this->deedInfo->tenant_name,
            'fathers_name' => $this->deedInfo->fathers_name,
            'dob' => $this->deedInfo->date_of_birth,
            'marital_status' => $this->deedInfo->marital_status,
            'present_address' => $this->deedInfo->present_address,
            'occupation' => $this->deedInfo->occupation,
            'office_address' => $this->deedInfo->office_address,
            'religion' => $this->deedInfo->religion,
            'edu_qualif' => $this->deedInfo->edu_qualif,
            'phone' => $this->deedInfo->phone,
            'email' => $this->deedInfo->email,
            'nid' => $this->deedInfo->nid,
            'passport' => $this->deedInfo->passport,
            'emergency_contact' => json_decode($this->deedInfo->emergency_contact),
            'family_members' => json_decode($this->deedInfo->family_members),
            'home_servant' => json_decode($this->deedInfo->home_servant),
            'driver' => json_decode($this->deedInfo->driver),
            'previus_landlord' => json_decode($this->deedInfo->previus_landlord),
            'leaving_home' => $this->deedInfo->leaving_home,
            'issue_date' => $this->deedInfo->issue_date,
        ];
    }
}
