<?php

namespace App\Http\Resources;

use App\Service\TransactionsService;
use Illuminate\Http\Resources\Json\JsonResource;

class MobileBankAccountResource extends JsonResource
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
            'name' => $this->mobileBank->name,
            'account_no' => $this->account_number,
            'amount' => TransactionsService::totalAmountInMobileBank($this->id)
        ];
    }
}
