<?php

namespace App\Http\Resources\V1;

use App\Models\Meal;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Meal $resource
 */
class MealResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $meal = $this->resource;

        return [
            'id' => $meal->id,
            'price' => $meal->price,
            'categoryId' => $meal->category_id,
            'name' => $meal->name,
            'isVegan' => $meal->is_vegan,
            'isSpicy' => $meal->is_spicy,
        ];
    }
}
