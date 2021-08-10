<?php

namespace App\Models\States\Order;

class NewOrderStatus extends OrderStatus
{
    public function getNext(): ?OrderStatus
    {
        return new CookingOrderStatus($this->order);
    }

    public function canBeCancelled(): bool
    {
        return true;
    }

    public function toString(): string
    {
        return self::NEW;
    }
}
