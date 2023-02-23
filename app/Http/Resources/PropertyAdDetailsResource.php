<?php

namespace App\Http\Resources;

use DateTime;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyAdDetailsResource extends JsonResource
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
            'name' => $this->property->name,
            'start_date' => (new DateTime($this->start_date))->format('Y-m-d'),
            'end_date' => (new DateTime($this->end_date))->format('Y-m-d'),
            'property_category' => $this->property_category,
            'sale_type' => $this->sale_type,
            'security_money' => $this->security_money,
            'rent_amount' => $this->rent_amount,
            'description' => $this->description,
        ];
    }
}
