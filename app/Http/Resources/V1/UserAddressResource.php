<?php

namespace App\Http\Resources\V1;

use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property UserAddress $resource
 */
class UserAddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $userAddress = $this->resource;

        return [
            'id' => $userAddress->id,
            'userId' => $userAddress->user_id,
            'street' => $userAddress->street,
            'house' => $userAddress->house,
            'building' => $userAddress->building,
            'entrance' => $userAddress->entrance,
            'floor' => $userAddress->floor,
            'apartment' => $userAddress->apartment,
            'intercom' => $userAddress->intercom,
            'comment' => $userAddress->comment,
        ];
    }
}
