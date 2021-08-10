<?php

namespace App\Http\Resources\V1;

use App\Models\Category;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Category $resource
 */
class CategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        $meals = MealResource::collection($this->whenLoaded('meals'));

        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'meals' => $meals,
            'mealsCount' => $this->resource->meals_count,
        ];
    }
}
