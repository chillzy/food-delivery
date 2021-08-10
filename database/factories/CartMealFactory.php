<?php

namespace Database\Factories;

use App\Models\CartMeal;

class CartMealFactory extends RedisFactory
{
    protected string $model = CartMeal::class;

    public function definition(): array
    {
        return [
            'meal_id' => $this->faker->randomNumber(),
            'quantity' => $this->faker->numberBetween(1, 5),
        ];
    }

    protected function getKey(): string
    {
        return $this->faker->uuid;
    }
}
