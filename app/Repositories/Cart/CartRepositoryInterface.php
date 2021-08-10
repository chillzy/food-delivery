<?php

namespace App\Repositories\Cart;

use App\Exceptions\Repository\ModelAlreadyExistsException;
use App\Exceptions\Repository\ModelNotFoundException;
use App\Models\Cart;
use App\Models\CartMeal;
use App\Models\User;

interface CartRepositoryInterface
{
    public function create(User $user): Cart;

    /**
     * @param User $user
     * @return Cart
     * @throws ModelNotFoundException
     */
    public function get(User $user): Cart;

    public function remove(User $user): void;

    public function getMeal(Cart $cart, int $mealId): ?CartMeal;

    /**
     * @param Cart $cart
     * @param CartMealDTO $cartMealDTO
     * @return CartMeal
     * @throws ModelAlreadyExistsException
     */
    public function addMeal(Cart $cart, CartMealDTO $cartMealDTO): CartMeal;

    /**
     * @param Cart $cart
     * @param CartMealDTO $cartMealDTO
     * @return CartMeal
     * @throws ModelNotFoundException
     */
    public function updateMeal(Cart $cart, CartMealDTO $cartMealDTO): CartMeal;

    /**
     * @param Cart $cart
     * @param int $mealId
     * @throws ModelNotFoundException
     */
    public function removeMeal(Cart $cart, int $mealId): void;
}
