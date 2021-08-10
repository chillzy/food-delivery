<?php

namespace App\Repositories\Order;

class OrderDTO
{
    public string $paymentType;

    /** @var OrderMealDTO[] */
    public array $meals;

    public function __construct(string $paymentType, array $meals)
    {
        $this->paymentType = $paymentType;
        $this->meals = $meals;
    }
}
