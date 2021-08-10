<?php

namespace App\Repositories\Order;

use App\Models\Meal;

class OrderMealDTO
{
    public Meal $meal;
    public int $quantity;

    public function __construct(Meal $meal, int $quantity)
    {
        $this->meal = $meal;
        $this->quantity = $quantity;
    }
}
