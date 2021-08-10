<?php

namespace App\Actions;

use App\Exceptions\Repository\ModelAlreadyExistsException;
use App\Exceptions\Repository\ModelNotFoundException;
use App\Models\Cart as CartModel;
use App\Models\CartMeal;
use App\Models\User;
use App\Repositories\Cart\CartMealDTO;
use App\Repositories\Cart\CartRepositoryInterface;
use App\Repositories\Meal\MealRepositoryInterface;

class Cart
{
    private CartRepositoryInterface $cartRepository;
    private MealRepositoryInterface $mealRepository;

    public function __construct(
        CartRepositoryInterface $cartRepository,
        MealRepositoryInterface $mealRepository
    ) {
        $this->cartRepository = $cartRepository;
        $this->mealRepository = $mealRepository;
    }

    /**
     * @param User $user
     * @return CartModel
     * @throws ModelNotFoundException
     */
    public function get(User $user): CartModel
    {
        $cart = $this->cartRepository->get($user);

        $mealsIds = [];
        foreach ($cart->meals as $cartMeal) {
            $mealsIds[] = $cartMeal->meal_id;
        }

        $meals = $this->mealRepository->getByIds($mealsIds, 'id');

        foreach ($cart->meals as $cartMeal) {
            $cartMeal->meal = $meals[$cartMeal->meal_id];
        }

        return $cart;
    }

    public function create(User $user): CartModel
    {
        return $this->cartRepository->create($user);
    }

    /**
     * @param User $user
     * @param CartMealDTO $cartMealDTO
     * @return CartMeal
     * @throws ModelNotFoundException
     * @throws ModelAlreadyExistsException
     */
    public function addMeal(User $user, CartMealDTO $cartMealDTO): CartMeal
    {
        $cart = $this->cartRepository->get($user);

        $existingCartMeal = $this->cartRepository->getMeal($cart, $cartMealDTO->mealId);
        if (!is_null($existingCartMeal)) {
            throw new ModelAlreadyExistsException();
        }

        $meal = $this->mealRepository->get($cartMealDTO->mealId);

        $addedMeal = $this->cartRepository->addMeal($cart, $cartMealDTO);
        $addedMeal->meal = $meal;

        return $addedMeal;
    }

    /**
     * @param User $user
     * @param CartMealDTO $cartMealDTO
     * @return CartMeal
     * @throws ModelNotFoundException
     */
    public function updateMeal(User $user, CartMealDTO $cartMealDTO): CartMeal
    {
        $cart = $this->cartRepository->get($user);

        $meal = $this->mealRepository->get($cartMealDTO->mealId);

        $updatedCartMeal = $this->cartRepository->updateMeal($cart, $cartMealDTO);
        $updatedCartMeal->meal = $meal;

        return $updatedCartMeal;
    }

    /**
     * @param User $user
     * @param int $mealId
     * @throws ModelNotFoundException
     */
    public function removeMeal(User $user, int $mealId): void
    {
        $cart = $this->cartRepository->get($user);

        $this->cartRepository->removeMeal($cart, $mealId);
    }

    /**
     * @param User $user
     * @throws ModelNotFoundException
     */
    public function clear(User $user): void
    {
        $this->cartRepository->get($user);

        $this->cartRepository->remove($user);
    }
}
