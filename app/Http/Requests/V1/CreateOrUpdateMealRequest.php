<?php

namespace App\Http\Requests\V1;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int $price
 * @property int $categoryId
 * @property string $name
 * @property bool $isVegan
 * @property bool $isSpicy
 */
class CreateOrUpdateMealRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'price' => ['required', 'int'],
            'categoryId' => ['required', 'int', 'exists:'.Category::class.',id'],
            'name' => ['required', 'string', 'max:255'],
            'isVegan' => ['required', 'boolean'],
            'isSpicy' => ['required', 'boolean'],
        ];
    }
}
