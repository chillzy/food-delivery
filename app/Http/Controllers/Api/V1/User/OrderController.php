<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Actions\Order\OrderCreator;
use App\Actions\Order\OrderDTO;
use App\Actions\Order\OrderMealDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\CreateOrderRequest;
use App\Http\Resources\V1\OrderResource;
use App\Models\User;
use Throwable;

class OrderController extends Controller
{
    private OrderCreator $orderCreator;

    public function __construct(OrderCreator $orderCreator)
    {
        $this->orderCreator = $orderCreator;
    }

    /**
     * @param CreateOrderRequest $request
     * @return OrderResource
     * @throws Throwable
     */
    public function create(CreateOrderRequest $request): OrderResource
    {
        /** @var User $user */
        $user = $request->user();

        $orderMealsDTO = [];
        foreach ($request->meals as $meal) {
            $orderMealsDTO[] = new OrderMealDTO($meal['mealId'], $meal['quantity']);
        }

        $order = $this->orderCreator->create($user, new OrderDTO($request->paymentType, $orderMealsDTO));

        return new OrderResource($order);
    }
}
