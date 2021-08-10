<?php

namespace App\Models\States\Order;

class DoneOrderStatus extends OrderStatus
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
        return self::DONE;
    }
}
