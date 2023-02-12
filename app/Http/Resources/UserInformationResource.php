<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserInformationResource extends JsonResource
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
            'mobile' => $this->mobile,
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status,
            'division_id' => $this->information->division_id,
            'district_id' => $this->information->district_id,
            'thana_id' => $this->information->thana_id,
            'nid' => $this->information->nid,
            'image' => $this->information->image,
            'postal_address' => $this->information->postal_address,
            'residential_address' => $this->information->residential_address,
            'description' => $this->information->description,
            'division' => $this->information->division->name,
            'district' => $this->information->district->name,
            'thana' => $this->information->thana->name,
        ];
    }
}
