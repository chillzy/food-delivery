<?php

namespace App\Models\States\Order;

class CookingOrderStatus extends OrderStatus
{
    public function getNext(): ?OrderStatus
    {
        return new DeliveringOrderStatus($this->order);
    }

    public function canBeCancelled(): bool
    {
        return true;
    }

    public function toString(): string
    {
        return self::COOKING;
    }
}
