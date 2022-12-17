<?php

namespace App\Http\Resources;

use DateTime;
use Illuminate\Http\Resources\Json\JsonResource;

class UserExpanseResourse extends JsonResource
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
            'user_id' => $this->user_id,
            'property_id' => $this->property_id,
            'account_id' => $this->account_id,
            'mobile_banking_id' => $this->mobile_banking_id,
            'property_deed_id' => $this->property_deed_id,
            'expanse_item_id' => $this->expanse_item_id,
            'transaction_purpose' => $this->transaction_purpose,
            'cash_out' => $this->cash_out,
            'remark' => $this->remark,
            'payment_method' => $this->payment_method,
            'date' => (new DateTime($this->date))->format('Y-m-d H:i:s')
        ];
    }
}
