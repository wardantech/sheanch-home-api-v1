<?php

namespace App\Http\Resources;

use DateTime;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyShowResource extends JsonResource
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
            'landlord' => $this->landlord->name,
            'thana' => $this->thana->name,
            'district' => $this->district->name,
            'division' => $this->division->name,
            'property_type' => $this->propertyType->name,
            'property_category' => $this->property_category,
            'sale_type' => $this->sale_type,
            'bed_rooms' => $this->bed_rooms,
            'balcony' => $this->balcony,
            'floor' => $this->floor,
            'bath_rooms' => $this->bath_rooms,
            'holding_number' => $this->holding_number,
            'road_number' => $this->road_number,
            'zip_code' => $this->zip_code,
            'address' => $this->address,
            'rent_amount' => $this->rent_amount,
            'security_money' => $this->security_money,
            'total_amount' => $this->total_amount,
            'area_size' => $this->area_size,
            'video_link' => $this->video_link,
            'utilities' => json_decode($this->utilities),
            'facilitie_ids' => json_decode($this->facilitie_ids),
            'google_map_location' => $this->google_map_location,
            'description' => $this->description,
            'status' => $this->status,
            'created_at' => (new DateTime($this->created_at))->format('Y-m-d H:i:s')
        ];
    }
}
