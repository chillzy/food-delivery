<?php

namespace App\Repositories\Cart;

class CartMealDTO
{
    public int $mealId;
    public int $quantity;

    public function __construct(int $mealId, int $quantity)
    {
        $this->mealId = $mealId;
        $this->quantity = $quantity;
    }
}
