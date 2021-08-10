<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Repositories\Cart\CartRepository;

class CartFactory extends RedisFactory
{
    protected string $model = Cart::class;

    public function definition(): array
    {
        return [
            'user_id' => $this->faker->randomNumber(),
        ];
    }

    protected function getKey(): string
    {
        return CartRepository::CART_KEY;
    }

    /**
     * {@inheritDoc}
     */
    protected function getIdentifier()
    {
        return 'user_id';
    }
}
