<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public const DEFAULT_VALID_PASSWORD = '1234%Dsz';

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make(self::DEFAULT_VALID_PASSWORD),
            'email_verified_at' => Date::now(),
        ];
    }

    public function notVerified(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    public function configure(): self
    {
        return $this->afterCreating(function (User $user) {
            EmailVerificationFactory::new()->create(['user_id' => $user->id]);
            UserAddressFactory::new()->count(3)->create(['user_id' => $user->id]);
        });
    }
}
