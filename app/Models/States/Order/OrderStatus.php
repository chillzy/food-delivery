<?php

namespace App\Models\States\Order;

use App\Exceptions\State\StateNotExistsException;
use App\Models\Order;

abstract class OrderStatus
{
    public const NEW = 'NEW';
    public const COOKING = 'COOKING';
    public const DELIVERING = 'DELIVERING';
    public const DONE = 'DONE';
    public const CANCELLED = 'CANCELLED';

    public const CAN_BE_MOVED_TO_LIST = [
        self::COOKING,
        self::DELIVERING,
        self::DONE,
    ];

    public const ALL = [
        self::NEW,
        self::COOKING,
        self::DELIVERING,
        self::DONE,
        self::CANCELLED,
    ];

    protected Order $order;

    abstract public function getNext(): ?OrderStatus;

    abstract public function canBeCancelled(): bool;

    abstract public function toString(): string;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * @param Order $order
     * @return static
     * @throws StateNotExistsException
     */
    public static function create(Order $order): self
    {
        $orderStatus = $order->status;

        if ($orderStatus === self::NEW) {
            return new NewOrderStatus($order);
        }

        if ($orderStatus === self::COOKING) {
            return new CookingOrderStatus($order);
        }

        if ($orderStatus === self::DELIVERING) {
            return new DeliveringOrderStatus($order);
        }

        if ($orderStatus === self::DONE) {
            return new DoneOrderStatus($order);
        }

        if ($orderStatus === self::CANCELLED) {
            return new CancelledOrderStatus($order);
        }

        throw new StateNotExistsException();
    }
}
