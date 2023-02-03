<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeedInformationResource extends JsonResource
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
            'property_name' => $this->property->name,
            'holding' => $this->property->holding_number,
            'road' => $this->property->road_number,
            'zip' => $this->property->zip_code,
            'tenant_name' => $this->tenant->name,
            'mobile' => $this->tenant->mobile,
            'email' => $this->tenant->email,
            'tenant_address' => $this->tenant->information->residential_address,
            'tenant_nid' => $this->tenant->information->nid,
            'status' => $this->status
        ];
    }
}
