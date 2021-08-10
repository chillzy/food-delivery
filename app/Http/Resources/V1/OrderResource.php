<?php

namespace App\Http\Resources\V1;

use App\Models\Order;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Order $resource
 */
class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'paymentType' => $this->resource->payment_type,
            'status' => $this->resource->status,
            'userId' => $this->resource->user_id,
            'price' => $this->resource->price,
            'createdAt' => $this->resource->created_at->format('Y-m-d H:i'),
            'meals' => OrderMealResource::collection($this->resource->meals),
        ];
    }
}
