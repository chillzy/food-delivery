<?php

namespace App\Http\Requests\V1;

use App\Repositories\Order\OrderRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property int $limit
 * @property int $offset
 * @property null|array $statuses
 */
class ListOrdersRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'limit' => ['required', 'int', 'min:1', 'max:'.OrderRepositoryInterface::DEFAULT_LIST_LIMIT],
            'offset' => ['required', 'int', 'min:'.OrderRepositoryInterface::DEFAULT_LIST_OFFSET],
            'statuses' => [
                'nullable',
                'array',
                Rule::in(OrderRepositoryInterface::DEFAULT_LIST_STATUSES),
            ],
        ];
    }
}
