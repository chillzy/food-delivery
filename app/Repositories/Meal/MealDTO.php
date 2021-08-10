<?php

namespace App\Repositories\Meal;

class MealDTO
{
    public int $categoryId;
    public string $name;
    public bool $isVegan;
    public bool $isSpicy;
    public int $price;

    public function __construct(int $price, int $categoryId, string $name, bool $isVegan, bool $isSpicy)
    {
        $this->categoryId = $categoryId;
        $this->name = $name;
        $this->isVegan = $isVegan;
        $this->isSpicy = $isSpicy;
        $this->price = $price;
    }
}
