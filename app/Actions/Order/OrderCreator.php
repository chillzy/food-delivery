<?php

namespace App\Actions\Order;

use App\Models\Order;
use App\Models\User;
use App\Repositories\Cart\CartRepositoryInterface;
use App\Repositories\Meal\MealRepositoryInterface;
use App\Repositories\Order\OrderDTO as RepoOrderDTO;
use App\Repositories\Order\OrderMealDTO as RepoOrderMealDTO;
use App\Repositories\Order\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Throwable;

class OrderCreator
{
    private CartRepositoryInterface $cartRepository;
    private OrderRepositoryInterface $orderRepository;
    private MealRepositoryInterface $mealRepository;

    public function __construct(
        CartRepositoryInterface $cartRepository,
        OrderRepositoryInterface $orderRepository,
        MealRepositoryInterface $mealRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->cartRepository = $cartRepository;
        $this->mealRepository = $mealRepository;
    }

    /**
     * @param User $user
     * @param OrderDTO $orderDTO
     * @return Order
     * @throws Throwable
     */
    public function create(User $user, OrderDTO $orderDTO): Order
    {
        $mealsIds = [];
        foreach ($orderDTO->meals as $orderMealDTO) {
            $mealsIds[] = $orderMealDTO->mealId;
        }

        $meals = $this->mealRepository->getByIds($mealsIds, 'id');

        $repoOrdersMealsDTO = [];
        foreach ($orderDTO->meals as $orderMealDTO) {
            $repoOrdersMealsDTO[] = new RepoOrderMealDTO($meals[$orderMealDTO->mealId], $orderMealDTO->quantity);
        }

        DB::beginTransaction();
        try {
            $this->cartRepository->remove($user);

            $createdOrder = $this->orderRepository->add(
                $user,
                new RepoOrderDTO($orderDTO->paymentType, $repoOrdersMealsDTO)
            );

            DB::commit();

            return $createdOrder;
        } catch (Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }
    }
}
