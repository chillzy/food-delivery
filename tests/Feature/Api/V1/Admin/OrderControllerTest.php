<?php

namespace Tests\Feature\Api\V1\Admin;

use App\Models\Admin;
use App\Models\Order;
use App\Models\States\Order\OrderStatus;
use App\Models\User;
use App\Notifications\Order\OrderCancelledEmail;
use App\Notifications\Order\OrderStatusChangedToCookingEmail;
use Database\Factories\AdminFactory;
use Database\Factories\OrderFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

    private Admin $admin;
    private User $user;
    private User $anotherUser;
    private Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = AdminFactory::new()->create();
        $this->user = UserFactory::new()->create();
        $this->anotherUser = UserFactory::new()->create();
    }

    public function testOrderListSuccessfullyFetched(): void
    {
        /** @var Order $newOrderForUser */
        $newOrderForUser = OrderFactory::new()->for($this->user)->withNewStatus()->create();
        OrderFactory::new()->for($this->user)->done()->create();

        /** @var Order $cookingOrderForAnotherUser */
        $cookingOrderForAnotherUser = OrderFactory::new()->for($this->anotherUser)->cooking()->create();

        $expectedOrdersInResponse = [$newOrderForUser, $cookingOrderForAnotherUser];

        $expectedResponse = [];

        foreach ($expectedOrdersInResponse as $order) {
            $orderMeals = [];

            foreach ($order->meals as $orderMeal) {
                $meal = $orderMeal->meal;

                $orderMeals[] = [
                    'quantity' => $orderMeal->meal_quantity,
                    'id' => $meal->id,
                    'price' => $meal->price,
                    'categoryId' => $meal->category_id,
                    'name' => $meal->name,
                    'isVegan' => $meal->is_vegan,
                    'isSpicy' => $meal->is_spicy,
                ];
            }

            $expectedResponse[] = [
                'id' => $order->id,
                'paymentType' => $order->payment_type,
                'status' => $order->status,
                'userId' => $order->user_id,
                'price' => $order->price,
                'createdAt' => $order->created_at->format('Y-m-d H:i'),
                'meals' => $orderMeals,
            ];
        }

        $request = [
            'limit' => 50,
            'offset' => 0,
        ];

        $this->loginAs($this->admin, 'admin')
            ->getWithParams($this->makeListUrl(), $request)
            ->assertOk()
            ->assertExactJson($expectedResponse);

        $request = [
            'limit' => 50,
            'offset' => 0,
            'statuses' => [OrderStatus::NEW],
        ];

        $expectedResponseForOneStatus = [];
        $orderMealsForConfirmedOrder = [];

        foreach ($newOrderForUser->meals as $orderMeal) {
            $meal = $orderMeal->meal;

            $orderMealsForConfirmedOrder[] = [
                'quantity' => $orderMeal->meal_quantity,
                'id' => $meal->id,
                'price' => $meal->price,
                'categoryId' => $meal->category_id,
                'name' => $meal->name,
                'isVegan' => $meal->is_vegan,
                'isSpicy' => $meal->is_spicy,
            ];
        }

        $expectedResponseForOneStatus[] = [
            'id' => $newOrderForUser->id,
            'paymentType' => $newOrderForUser->payment_type,
            'status' => $newOrderForUser->status,
            'userId' => $newOrderForUser->user_id,
            'price' => $newOrderForUser->price,
            'createdAt' => $newOrderForUser->created_at->format('Y-m-d H:i'),
            'meals' => $orderMealsForConfirmedOrder,
        ];

        $this->loginAs($this->admin, 'admin')
            ->getWithParams($this->makeListUrl(), $request)
            ->assertOk()
            ->assertExactJson($expectedResponseForOneStatus);
    }

    public function testOrderListFetchingFailed(): void
    {
        $request = [
            'limit' => 50,
            'offset' => 0,
        ];

        $this->getWithParams($this->makeListUrl(), $request)->assertUnauthorized();

        $withInvalidLimit = [
            'limit' => $this->faker->numberBetween(51, 100),
            'offset' => 0,
        ];

        $this->loginAs($this->admin, 'admin')
            ->getWithParams($this->makeListUrl(), $withInvalidLimit)
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY);

        $withInvalidOffset = [
            'limit' => $this->faker->numberBetween(51, 100),
            'offset' => -5,
        ];

        $this->loginAs($this->admin, 'admin')
            ->getWithParams($this->makeListUrl(), $withInvalidOffset)
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testOrderStatusSuccessfullyMoved(): void
    {
        Notification::fake();

        /** @var Order $newOrder */
        $newOrder = OrderFactory::new()->for($this->user)->withNewStatus()->create();

        $this->loginAs($this->admin, 'admin')
            ->putJson($this->makeMoveStatusUrl($newOrder->id), ['status' => OrderStatus::COOKING])
            ->assertNoContent();

        $this->assertDatabaseHas(Order::class, [
            'id' => $newOrder->id,
            'user_id' => $this->user->id,
            'price' => $newOrder->price,
            'payment_type' => $newOrder->payment_type,
            'cancel_reason' => null,
            'status' => OrderStatus::COOKING,
        ]);

        Notification::assertSentTo($this->user, OrderStatusChangedToCookingEmail::class);
    }

    public function testOrderStatusMovingFailed(): void
    {
        Notification::fake();

        /** @var Order $newOrder */
        $newOrder = OrderFactory::new()->for($this->user)->withNewStatus()->create();

        $this->putJson($this->makeMoveStatusUrl($newOrder->id))->assertUnauthorized();

        $this->loginAs($this->admin, 'admin')
            ->putJson($this->makeMoveStatusUrl($this->faker->uuid), ['status' => OrderStatus::COOKING])
            ->assertNotFound();

        Notification::assertNotSentTo($this->user, OrderStatusChangedToCookingEmail::class);

        $this->loginAs($this->admin, 'admin')
            ->putJson($this->makeMoveStatusUrl($newOrder->id), ['status' => OrderStatus::CANCELLED])
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY);

        Notification::assertNothingSent();

        $this->loginAs($this->admin, 'admin')
            ->putJson($this->makeMoveStatusUrl($newOrder->id), ['status' => OrderStatus::NEW])
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY);

        Notification::assertNothingSent();

        /** @var Order $doneOrder */
        $doneOrder = OrderFactory::new()->for($this->user)->done()->create();

        $this->loginAs($this->admin, 'admin')
            ->putJson($this->makeMoveStatusUrl($doneOrder->id))
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertDatabaseHas(Order::class, [
            'id' => $doneOrder->id,
            'user_id' => $doneOrder->user_id,
            'price' => $doneOrder->price,
            'payment_type' => $doneOrder->payment_type,
            'cancel_reason' => null,
            'status' => $doneOrder->status,
        ]);

        Notification::assertNothingSent();
    }

    public function testOrderSuccessfullyCancelled(): void
    {
        Notification::fake();

        /** @var Order $newOrder */
        $newOrder = OrderFactory::new()->for($this->user)->withNewStatus()->create();

        $request = [
            'reason' => $this->faker->text(255),
        ];

        $this->loginAs($this->admin, 'admin')
            ->deleteJson($this->makeCancelUrl($newOrder->id), $request)
            ->assertNoContent();

        Notification::assertSentTo($this->user, OrderCancelledEmail::class);

        $this->assertDatabaseHas(Order::class, [
            'id' => $newOrder->id,
            'user_id' => $newOrder->user_id,
            'price' => $newOrder->price,
            'payment_type' => $newOrder->payment_type,
            'cancel_reason' => $request['reason'],
            'status' => OrderStatus::CANCELLED,
        ]);
    }

    public function testOrderCancellingFailed(): void
    {
        Notification::fake();

        /** @var Order $doneOrder */
        $doneOrder = OrderFactory::new()->for($this->user)->done()->create();

        $this->deleteJson($this->makeCancelUrl($doneOrder->id))->assertUnauthorized();

        $this->loginAs($this->admin, 'admin')
            ->deleteJson($this->makeCancelUrl($doneOrder->id))
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY);

        Notification::assertNotSentTo($this->user, OrderCancelledEmail::class);

        $this->assertDatabaseHas(Order::class, [
            'id' => $doneOrder->id,
            'user_id' => $doneOrder->user_id,
            'price' => $doneOrder->price,
            'payment_type' => $doneOrder->payment_type,
            'cancel_reason' => $doneOrder->cancel_reason,
            'status' => $doneOrder->status,
        ]);

        /** @var Order $cancelledOrder */
        $cancelledOrder = OrderFactory::new()->for($this->user)->cancelled()->create();

        $this->loginAs($this->admin, 'admin')
            ->deleteJson($this->makeCancelUrl($cancelledOrder->id))
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY);

        Notification::assertNotSentTo($this->user, OrderCancelledEmail::class);

        $this->assertDatabaseHas(Order::class, [
            'id' => $cancelledOrder->id,
            'user_id' => $cancelledOrder->user_id,
            'price' => $cancelledOrder->price,
            'payment_type' => $cancelledOrder->payment_type,
            'cancel_reason' => $cancelledOrder->cancel_reason,
            'status' => $cancelledOrder->status,
        ]);
    }

    private function makeListUrl(): string
    {
        return URL::route('v1.admin.order.list');
    }

    private function makeMoveStatusUrl(string $id): string
    {
        return URL::route('v1.admin.order.moveStatus', ['id' => $id]);
    }

    private function makeCancelUrl(string $id): string
    {
        return URL::route('v1.admin.order.cancel', ['id' => $id]);
    }
}
