<?php

namespace App\Models;

class Cart
{
    public int $user_id;

    /** @var CartMeal[] */
    public array $meals = [];
}
