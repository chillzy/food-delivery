<?php

namespace App\Http\Requests\V1;

use App\Models\States\Order\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string $reason
 */
class MoveOrderStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(OrderStatus::CAN_BE_MOVED_TO_LIST)],
        ];
    }
}
