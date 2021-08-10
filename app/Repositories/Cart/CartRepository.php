<?php

namespace App\Repositories\Cart;

use App\Exceptions\Repository\ModelNotFoundException;
use App\Models\Cart;
use App\Models\CartMeal;
use App\Models\User;
use Illuminate\Cache\RedisStore;

class CartRepository implements CartRepositoryInterface
{
    public const DEFAULT_CART_TTL = 24 * 60 * 60;
    public const CART_KEY = 'cart_';

    private RedisStore $cacheStore;

    public function __construct(RedisStore $store)
    {
        $this->cacheStore = $store;
    }

    public function create(User $user): Cart
    {
        $cart = (new Cart());
        $cart->user_id = $user->id;

        $this->cacheStore->put($this->makeCartKey($cart->user_id), $cart, self::DEFAULT_CART_TTL);

        return $cart;
    }

    /**
     * {@inheritDoc}
     */
    public function get(User $user): Cart
    {
        $cart = $this->cacheStore->get($this->makeCartKey($user->id));

        if (is_null($cart)) {
            throw new ModelNotFoundException();
        }

        return $cart;
    }

    public function remove(User $user): void
    {
        $this->cacheStore->forget($this->makeCartKey($user->id));
    }

    public function getMeal(Cart $cart, int $mealId): ?CartMeal
    {
        foreach ($cart->meals as $meal) {
            if ($meal->meal_id === $mealId) {
                return $meal;
            }
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function addMeal(Cart $cart, CartMealDTO $cartMealDTO): CartMeal
    {
        $cartMeal = $this->makeCartMealModel($cartMealDTO);
        $cart->meals[] = $cartMeal;

        $this->cacheStore->put($this->makeCartKey($cart->user_id), $cart, self::DEFAULT_CART_TTL);

        return $cartMeal;
    }

    /**
     * {@inheritDoc}
     */
    public function updateMeal(Cart $cart, CartMealDTO $cartMealDTO): CartMeal
    {
        $cartMeal = $this->getMeal($cart, $cartMealDTO->mealId);
        $cartMeal->quantity = $cartMealDTO->quantity;

        $this->cacheStore->put($this->makeCartKey($cart->user_id), $cart, self::DEFAULT_CART_TTL);

        return $cartMeal;
    }

    /**
     * {@inheritDoc}
     */
    public function removeMeal(Cart $cart, int $mealId): void
    {
        $cartMealToBeRemoved = $this->getMeal($cart, $mealId);

        if (is_null($cartMealToBeRemoved)) {
            throw new ModelNotFoundException();
        }

        foreach ($cart->meals as $index => $meal) {
            if ($meal->meal_id === $cartMealToBeRemoved->meal_id) {
                unset($cart->meals[$index]);

                break;
            }
        }

        $this->cacheStore->put($this->makeCartKey($cart->user_id), $cart, self::DEFAULT_CART_TTL);
    }

    private function makeCartKey(int $userId): string
    {
        return self::CART_KEY.$userId;
    }

    private function makeCartMealModel(CartMealDTO $cartMealDTO): CartMeal
    {
        $cartMeal = new CartMeal();
        $cartMeal->meal_id = $cartMealDTO->mealId;
        $cartMeal->quantity = $cartMealDTO->quantity;

        return $cartMeal;
    }
}
