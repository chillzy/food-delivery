<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $street
 * @property int $house
 * @property int|null $building
 * @property int|null $entrance
 * @property int|null $floor
 * @property int|null $apartment
 * @property string|null $intercom
 * @property string|null $comment
 */
class CreateOrUpdateUserAddressRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'street' => ['required', 'string', 'max:255'],
            'house' => ['required', 'int', 'min:1', 'max:32767'],
            'building' => ['present', 'nullable', 'int', 'min:1', 'max:32767'],
            'entrance' => ['present', 'nullable', 'int', 'min:1', 'max:32767'],
            'floor' => ['present', 'nullable', 'int', 'min:1', 'max:32767'],
            'apartment' => ['present', 'nullable', 'int', 'min:1', 'max:32767'],
            'intercom' => ['present', 'nullable', 'string', 'max:50'],
            'comment' => ['present', 'nullable', 'string', 'max:255'],
        ];
    }
}
