<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int $quantity
 */
class UpdateCartMealRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'quantity' => ['required', 'int', 'min:1', 'max:'.CreateOrderRequest::MAX_MEALS_COUNT_IN_CART],
        ];
    }
}
