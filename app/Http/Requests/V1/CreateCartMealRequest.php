<?php

namespace App\Http\Requests\V1;

use App\Models\Meal;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int $mealId
 * @property int $quantity
 */
class CreateCartMealRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'mealId' => ['required', 'int', 'exists:'.Meal::class.',id'],
            'quantity' => ['required', 'int', 'min:1', 'max:'.CreateOrderRequest::MAX_MEALS_COUNT_IN_CART],
        ];
    }
}
