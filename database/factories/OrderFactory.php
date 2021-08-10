<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderMeal;
use App\Models\States\Order\OrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid,
            'price' => 0,
            'status' => OrderStatus::NEW,
            'payment_type' => $this->faker->randomElement(Order::PAYMENT_TYPES),
        ];
    }

    public function withNewStatus(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => OrderStatus::NEW,
            ];
        });
    }

    public function cooking(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => OrderStatus::COOKING,
            ];
        });
    }

    public function done(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => OrderStatus::DONE,
            ];
        });
    }

    public function cancelled(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => OrderStatus::CANCELLED,
                'cancel_reason' => $this->faker->text(255),
            ];
        });
    }

    public function configure(): self
    {
        return $this->afterCreating(function (Order $order) {
            /** @var Category $category */
            $category = CategoryFactory::new()->create();

            $price = 0;
            $orderMeals = [];
            foreach ($category->meals as $meal) {
                /** @var OrderMeal $orderMeal */
                $orderMeal = OrderMealFactory::new()->create([
                    'order_id' => $order->id,
                    'meal_id' => $meal->id,
                ]);

                $price += $orderMeal->meal_quantity * $meal->price;

                $orderMeal->setRelation('meal', $meal);

                $orderMeals[] = $orderMeal;
            }

            $order->price = $price;
            $order->save();

            $order->setRelation('meals', $orderMeals);
        });
    }
}
