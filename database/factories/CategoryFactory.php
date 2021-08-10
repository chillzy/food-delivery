<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
        ];
    }

    public function configure(): self
    {
        return $this->afterCreating(function (Category $category) {
            MealFactory::new()->count(3)->create(['category_id' => $category->id]);
        });
    }
}
