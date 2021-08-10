<?php

namespace App\Repositories\Order;

use App\Exceptions\Repository\ModelNotFoundException;
use App\Models\Order;
use App\Models\States\Order\OrderStatus;
use App\Models\User;
use Illuminate\Support\Collection;

interface OrderRepositoryInterface
{
    public const DEFAULT_LIST_LIMIT = 50;
    public const DEFAULT_LIST_OFFSET = 0;
    public const DEFAULT_LIST_STATUSES = [
        OrderStatus::NEW,
        OrderStatus::COOKING,
        OrderStatus::DELIVERING,
    ];

    public function add(User $user, OrderDTO $dto): Order;

    /**
     * @param string $id
     * @return Order
     * @throws ModelNotFoundException
     */
    public function get(string $id): Order;

    public function updateStatus(Order $order, OrderStatus $status): Order;

    public function cancel(Order $order, string $message): Order;

    /**
     * @param ListOrdersDTO $dto
     * @return Collection|Order[]
     */
    public function list(ListOrdersDTO $dto);
}
