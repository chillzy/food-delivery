<?php

namespace Database\Factories;

use App\Models\UserAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserAddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserAddress::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'street' => $this->faker->streetName,
            'house' => $this->faker->numberBetween(1, 1000),
            'building' => $this->faker->numberBetween(1, 1000),
            'entrance' => $this->faker->numberBetween(1, 1000),
        ];
    }
}
