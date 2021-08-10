<?php

namespace App\Models;

class CartMeal
{
    public int $meal_id;
    public int $quantity;
    public ?Meal $meal;
}
