<?php

namespace App\Actions\Order;

use App\Exceptions\Repository\ModelNotFoundException;
use App\Exceptions\Actions\Order\OrderStatusCantBeMovedException;
use App\Exceptions\State\NotificationNotExistsException;
use App\Exceptions\State\StateNotExistsException;
use App\Models\Order;
use App\Models\States\Order\CookingOrderStatus;
use App\Models\States\Order\DeliveringOrderStatus;
use App\Models\States\Order\DoneOrderStatus;
use App\Notifications\Order\OrderStatusChangedToCookingEmail;
use App\Notifications\Order\OrderStatusChangedToDeliveringEmail;
use App\Notifications\Order\OrderStatusChangedToDoneEmail;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Models\States\Order\OrderStatus;
use Illuminate\Notifications\Notification as NotificationEmail;
use Illuminate\Support\Facades\Notification;

class OrderStatusMover
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
     * @return Order
     * @throws ModelNotFoundException
     * @throws NotificationNotExistsException
     * @throws OrderStatusCantBeMovedException
     * @throws StateNotExistsException
     */
    public function moveStatus(Order $order): Order
    {
        $orderStatus = $order->getStatus();

        $nextOrderStatus = $orderStatus->getNext();
        if (is_null($nextOrderStatus)) {
            throw new OrderStatusCantBeMovedException();
        }

        $notification = $this->getNotificationForNewStatus($nextOrderStatus);

        $user = $this->userRepository->get($order->user_id);

        $order = $this->orderRepository->updateStatus($order, $nextOrderStatus);

        Notification::send([$user], $notification);

        return $order;
    }

    /**
     * @param OrderStatus $newStatus
     * @return NotificationEmail
     * @throws NotificationNotExistsException
     */
    private function getNotificationForNewStatus(OrderStatus $newStatus): NotificationEmail
    {
        if ($newStatus instanceof CookingOrderStatus) {
            return new OrderStatusChangedToCookingEmail();
        }

        if ($newStatus instanceof DeliveringOrderStatus) {
            return new OrderStatusChangedToDeliveringEmail();
        }

        if ($newStatus instanceof DoneOrderStatus) {
            return new OrderStatusChangedToDoneEmail();
        }

        throw new NotificationNotExistsException();
    }
}
