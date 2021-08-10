<?php

namespace App\Actions\Order;

class OrderMealDTO
{
    public int $mealId;
    public int $quantity;

    public function __construct(int $mealId, int $quantity)
    {
        $this->mealId = $mealId;
        $this->quantity = $quantity;
    }
}
