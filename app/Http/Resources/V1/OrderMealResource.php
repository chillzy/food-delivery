<?php

namespace App\Http\Resources\V1;

use App\Models\OrderMeal;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property OrderMeal $resource
 */
class OrderMealResource extends JsonResource
{
    public function toArray($request): array
    {
        $meal = MealResource::make($this->resource->meal)->toArray($request);

        return array_merge(['quantity' => $this->resource->meal_quantity], $meal);
    }
}
