<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResourse extends JsonResource
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
            'name' => $this->name,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'nid' => $this->information->nid ?? '',
            'image' => $this->information->image ?? '',
            'thana_id' => $this->information->thana_id ?? '',
            'district_id' => $this->information->district_id ?? '',
            'division_id' => $this->information->division_id ?? '',
            'postal_address' => $this->information->postal_address ?? '',
            'residential_address' => $this->information->residential_address ?? ''
        ];
    }
}
