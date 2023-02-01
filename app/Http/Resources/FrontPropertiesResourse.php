<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FrontPropertiesResourse extends JsonResource
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
            'id' => $this->id,
            'property_image' => $this->property->getMedia(),
            'sale_type' => $this->property->sale_type,
            'rent_amount' => $this->property->rent_amount,
            'name' => $this->property->name,
            'bed_rooms' => $this->property->bed_rooms,
            'bath_rooms' => $this->property->bath_rooms,
            'area_size' => $this->property->area_size,
            'address' => $this->property->address,
        ];
    }
}
