<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int $limit
 * @property int $offset
 * @property null|int $categoryId
 * @property null|bool $isVegan
 * @property null|bool $isSpicy
 */
class ListMealsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'limit' => ['required', 'int', 'min:1', 'max:50'],
            'offset' => ['required', 'int', 'min:0'],
            'categoryId' => ['nullable', 'int'],
            'isVegan' => ['nullable', 'boolean'],
            'isSpicy' => ['nullable', 'boolean'],
        ];
    }
}
