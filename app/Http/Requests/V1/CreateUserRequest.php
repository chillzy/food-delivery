<?php

namespace App\Http\Requests\V1;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * @property string $name
 * @property string $email
 * @property string $password
 */
class CreateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $passwordRule = Password::min(6)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols();

        return [
            'name' => ['required', 'max:50'],
            'email' => ['required', 'email', 'unique:'.User::class.',email', 'max:255'],
            'password' => ['required', $passwordRule, 'max:255'],
        ];
    }
}
