<?php

namespace App\Http\Resources\V1;

use App\Models\CartMeal;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property CartMeal $resource
 */
class CartMealResource extends JsonResource
{
    public function toArray($request): array
    {
        $meal = MealResource::make($this->resource->meal)->toArray($request);

        return array_merge(['quantity' => $this->resource->quantity], $meal);
    }
}
