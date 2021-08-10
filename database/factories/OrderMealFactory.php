<?php

namespace Database\Factories;

use App\Models\OrderMeal;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderMealFactory extends Factory
{
    protected $model = OrderMeal::class;

    public function definition(): array
    {
        return [
            'meal_quantity' => $this->faker->numberBetween(1, 5),
        ];
    }
}
