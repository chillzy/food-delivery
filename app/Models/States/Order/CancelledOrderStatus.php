<?php

namespace App\Models\States\Order;

class CancelledOrderStatus extends OrderStatus
{
    public function getNext(): ?OrderStatus
    {
        return null;
    }

    public function canBeCancelled(): bool
    {
        return false;
    }

    public function toString(): string
    {
        return self::CANCELLED;
    }
}
