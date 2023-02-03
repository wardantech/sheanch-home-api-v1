<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeedDetailsResource extends JsonResource
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
            'name' => $this->tenant->name,
            'mobile' => $this->tenant->mobile,
            'email' => $this->tenant->email,
            'nid' => $this->tenant->information->nid,
            'image' => $this->tenant->information->image,
            'post' => $this->tenant->information->postal_address,
            'present_address' => $this->tenant->information->residential_address,
            'description' => $this->tenant->information->description,
            'division' => $this->tenant->information->division->name,
            'district' => $this->tenant->information->district->name,
            'thana' => $this->tenant->information->thana->name,
        ];
    }
}
