<?php

namespace App\Repositories\Order;

use App\Exceptions\Repository\ModelNotFoundException;
use App\Models\Order;
use App\Models\OrderMeal;
use App\Models\States\Order\OrderStatus;
use App\Models\User;
use Illuminate\Support\Str;

class OrderRepository implements OrderRepositoryInterface
{
    public function add(User $user, OrderDTO $dto): Order
    {
        $order = new Order();
        $order->id = Str::uuid()->toString();
        $order->user_id = $user->id;
        $order->status = OrderStatus::NEW;
        $order->payment_type = $dto->paymentType;

        $orderMeals = [];
        $orderPrice = 0;
        foreach ($dto->meals as $orderMealDTO) {
            $meal = $orderMealDTO->meal;

            $orderPrice += $meal->price * $orderMealDTO->quantity;

            $orderMeal = new OrderMeal();
            $orderMeal->order_id = $order->id;
            $orderMeal->meal_id = $meal->id;
            $orderMeal->meal_quantity = $orderMealDTO->quantity;

            $orderMeals[] = $orderMeal;
        }

        $order->price = $orderPrice;
        $order->save();
        $order->meals()->saveMany($orderMeals);
        $order->load('meals.meal');

        return $order;
    }

    public function get(string $id): Order
    {
        $order = Order::find($id);

        if (is_null($order)) {
            throw new ModelNotFoundException();
        }

        return $order;
    }

    public function updateStatus(Order $order, OrderStatus $status): Order
    {
        $order->status = $status->toString();
        $order->save();

        return $order;
    }

    public function cancel(Order $order, string $message): Order
    {
        $order->status = OrderStatus::CANCELLED;
        $order->cancel_reason = $message;
        $order->save();

        return $order;
    }

    /**
     * {@inheritDoc}
     */
    public function list(ListOrdersDTO $dto)
    {
        $query = Order::query();

        $statuses = self::DEFAULT_LIST_STATUSES;
        if (!is_null($dto->statuses)) {
            $statuses = $dto->statuses;
        }

        return $query->whereIn('status', $statuses)
            ->limit($dto->limit)
            ->offset($dto->offset)
            ->get();
    }
}
