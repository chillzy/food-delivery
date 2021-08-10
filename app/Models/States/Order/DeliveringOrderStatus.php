<?php

namespace App\Models\States\Order;

class DeliveringOrderStatus extends OrderStatus
{
    public function getNext(): ?OrderStatus
    {
        return new DoneOrderStatus($this->order);
    }

    public function canBeCancelled(): bool
    {
        return true;
    }

    public function toString(): string
    {
        return self::DELIVERING;
    }
}
