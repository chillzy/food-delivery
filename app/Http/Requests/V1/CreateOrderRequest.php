<?php

namespace App\Http\Requests\V1;

use App\Models\Meal;
use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string $paymentType
 * @property array $meals
 */
class CreateOrderRequest extends FormRequest
{
    public const MAX_MEALS_COUNT_IN_CART = 50;

    public function rules(): array
    {
        return [
            'paymentType' => ['required', 'string', Rule::in(Order::PAYMENT_TYPES)],
            'meals' => ['required', 'array'],
            'meals.*.mealId' => ['required', 'int', 'exists:'.Meal::class.',id'],
            'meals.*.quantity' => ['required', 'int', 'min:1', 'max:'.self::MAX_MEALS_COUNT_IN_CART],
        ];
    }
}
