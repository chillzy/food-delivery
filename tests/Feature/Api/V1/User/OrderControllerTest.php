<?php

namespace Tests\Feature\Api\V1\User;

use App\Models\CartMeal;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderMeal;
use App\Models\States\Order\OrderStatus;
use App\Models\User;
use Database\Factories\CartFactory;
use Database\Factories\CartMealFactory;
use Database\Factories\CategoryFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

    private User $user;

    /** @var CartMeal[] */
    private array $cartMeals = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = UserFactory::new()->create();

        /** @var Category $category */
        $category = CategoryFactory::new()->create();

        $cart = CartFactory::new();
        foreach ($category->meals as $meal) {
            $mealQuantity = $this->faker->numberBetween(1, 5);

            $this->cartMeals[$meal->id]['meal'] = $meal;
            $this->cartMeals[$meal->id]['quantity'] = $mealQuantity;

            $cart->has(CartMealFactory::new()->state([
                'meal_id' => $meal->id,
                'quantity' => $mealQuantity,
            ]), 'meals');
        }

        $cart->create(['user_id' => $this->user->id]);
    }

    public function testOrderSuccessfullyCreated(): void
    {
        $request = [
            'paymentType' => $this->faker->randomElement(Order::PAYMENT_TYPES),
            'meals' => [],
        ];

        $expectedResponse = [
            'id' => 0,
            'paymentType' => $request['paymentType'],
            'status' => OrderStatus::NEW,
            'userId' => $this->user->id,
            'price' => 0,
            'createdAt' => (new \DateTime())->format('Y-m-d H:i'),
            'meals' => [],
        ];

        foreach ($this->cartMeals as $cartMeal) {
            $meal = $cartMeal['meal'];
            $mealQuantity = $cartMeal['quantity'];

            $expectedResponse['price'] += $meal->price * $mealQuantity;

            $request['meals'][] = [
                'mealId' => $meal->id,
                'quantity' => $mealQuantity,
            ];

            $expectedResponse['meals'][] = [
                'quantity' => $mealQuantity,
                'id' => $meal->id,
                'price' => $meal->price,
                'categoryId' => $meal->category_id,
                'name' => $meal->name,
                'isVegan' => $meal->is_vegan,
                'isSpicy' => $meal->is_spicy,
            ];
        }

        $response = $this->loginAs($this->user)->postJson($this->makeCreateUrl(), $request);
        $response->assertCreated();

        $orderId = $response['id'];
        $expectedResponse['id'] = $orderId;
        $response->assertExactJson($expectedResponse);

        foreach ($this->cartMeals as $cartMeal) {
            $this->assertDatabaseHas(OrderMeal::class, [
                'order_id' => $orderId,
                'meal_id' => $cartMeal['meal']->id,
                'meal_quantity' => $cartMeal['quantity'],
            ]);
        }

        $this->assertDatabaseHas(Order::class, [
            'id' => $orderId,
            'user_id' => $this->user->id,
            'price' => $expectedResponse['price'],
            'status' => OrderStatus::NEW,
            'payment_type' => $request['paymentType'],
        ]);
    }

    public function testOrderCreationFailed(): void
    {
        $withNotExistingPaymentType = [
            'paymentType' => $this->faker->word,
            'meals' => [],
        ];

        $this->postJson($this->makeCreateUrl(), $withNotExistingPaymentType)->assertUnauthorized();

        $this->loginAs($this->user)
            ->postJson($this->makeCreateUrl(), $withNotExistingPaymentType)
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertDatabaseMissing(Order::class, [
            'user_id' => $this->user->id,
            'status' => OrderStatus::NEW,
            'payment_type' => $withNotExistingPaymentType['paymentType'],
        ]);

        $withEmptyMeals = [
            'paymentType' => $this->faker->randomElement(Order::PAYMENT_TYPES),
            'meals' => [],
        ];

        $this->loginAs($this->user)
            ->postJson($this->makeCreateUrl(), $withEmptyMeals)
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertDatabaseMissing(Order::class, [
            'user_id' => $this->user->id,
            'status' => OrderStatus::NEW,
            'payment_type' => $withNotExistingPaymentType['paymentType'],
        ]);

        $withNotExistingMeal = [
            'paymentType' => $this->faker->randomElement(Order::PAYMENT_TYPES),
            'meals' => [
                [
                    'id' => $this->faker->randomNumber(),
                    'quantity' => $this->faker->numberBetween(1, 5),
                ],
            ],
        ];

        $this->loginAs($this->user)
            ->postJson($this->makeCreateUrl(), $withNotExistingMeal)
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertDatabaseMissing(Order::class, [
            'user_id' => $this->user->id,
            'status' => OrderStatus::NEW,
            'payment_type' => $withNotExistingPaymentType['paymentType'],
        ]);
    }

    private function makeCreateUrl(): string
    {
        return URL::route('v1.user.order.create');
    }
}
