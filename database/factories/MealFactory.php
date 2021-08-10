<?php

namespace Database\Factories;

use App\Models\Meal;
use Illuminate\Database\Eloquent\Factories\Factory;

class MealFactory extends Factory
{
    protected $model = Meal::class;

    public function definition(): array
    {
        return [
            'price' => $this->faker->numberBetween(100, 1000),
            'name' => $this->faker->word,
            'is_vegan' => $this->faker->boolean,
            'is_spicy' => $this->faker->boolean,
        ];
    }
}
