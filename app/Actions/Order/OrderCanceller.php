<?php

namespace App\Actions\Order;

use App\Exceptions\Actions\Order\OrderCantBeCancelledException;
use App\Exceptions\Repository\ModelNotFoundException;
use App\Exceptions\State\StateNotExistsException;
use App\Models\Order;
use App\Notifications\Order\OrderCancelledEmail;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Facades\Notification;

class OrderCanceller
{
    private OrderRepositoryInterface $orderRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param Order $order
     * @param string $message
     * @return Order
     * @throws ModelNotFoundException
     * @throws OrderCantBeCancelledException
     * @throws StateNotExistsException
     */
    public function cancel(Order $order, string $message): Order
    {
        $orderStatus = $order->getStatus();

        if (!$orderStatus->canBeCancelled()) {
            throw new OrderCantBeCancelledException();
        }

        $user = $this->userRepository->get($order->user_id);

        $order = $this->orderRepository->cancel($order, $message);

        Notification::send([$user], new OrderCancelledEmail($message));

        return $order;
    }
}
