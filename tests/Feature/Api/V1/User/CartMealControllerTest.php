<?php

namespace Tests\Feature\Api\V1\User;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Meal;
use App\Models\User;
use Database\Factories\CartMealFactory;
use Database\Factories\CategoryFactory;
use Database\Factories\CartFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class CartMealControllerTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

    private User $userWithEmptyCart;
    private User $userWithFilledCart;
    private Cart $filledCart;

    /** @var Meal[] */
    private array $mealsInCart = [];

    protected function setUp(): void
    {
        parent::setUp();

        /** @var Category $category */
        $category = CategoryFactory::new()->create();

        $cart = CartFactory::new();
        foreach ($category->meals as $meal) {
            $this->mealsInCart[$meal->id] = $meal;
            $cart->has(CartMealFactory::new()->state(['meal_id' => $meal->id]), 'meals');
        }

        $this->userWithFilledCart = UserFactory::new()->create();
        $cart->create(['user_id' => $this->userWithFilledCart->id]);

        $this->userWithEmptyCart = UserFactory::new()->create();
        CartFactory::new()->create(['user_id' => $this->userWithEmptyCart->id]);
    }

    public function testCartMealSuccessfullyCreated(): void
    {
        $mealToBeCreated = reset($this->mealsInCart);
        $quantity = $this->faker->numberBetween(1, 5);

        $expectedResponse = [
            'quantity' => $quantity,
            'id' => $mealToBeCreated->id,
            'price' => $mealToBeCreated->price,
            'categoryId' => $mealToBeCreated->category_id,
            'name' => $mealToBeCreated->name,
            'isVegan' => $mealToBeCreated->is_vegan,
            'isSpicy' => $mealToBeCreated->is_spicy,
        ];

        $this->loginAs($this->userWithEmptyCart)
            ->postJson($this->makeCreateUrl(), [
                'mealId' => $mealToBeCreated->id,
                'quantity' => $quantity,
            ])->assertCreated()
            ->assertExactJson($expectedResponse);
    }

    public function testCartMealCreationFailed(): void
    {
        $mealToBeCreated = reset($this->mealsInCart);
        $quantity = $this->faker->numberBetween(1, 5);

        $this->postJson($this->makeCreateUrl(), [
            'mealId' => $mealToBeCreated->id,
            'quantity' => $quantity,
        ])->assertUnauthorized();

        $this->loginAs($this->userWithEmptyCart)
            ->postJson($this->makeCreateUrl(), [
                'mealId' => $mealToBeCreated->id,
                'quantity' => $quantity,
            ])->assertCreated();

        $this->loginAs($this->userWithEmptyCart)
            ->postJson($this->makeCreateUrl(), [
                'mealId' => $mealToBeCreated->id,
                'quantity' => $quantity,
            ])->assertStatus(JsonResponse::HTTP_CONFLICT);

        $this->loginAs($this->userWithEmptyCart)
            ->postJson($this->makeCreateUrl(), [
                'mealId' => $mealToBeCreated->id + $this->faker->numberBetween(1000, 5000),
                'quantity' => $quantity,
            ])->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY);

        $this->loginAs($this->userWithEmptyCart)
            ->postJson($this->makeCreateUrl(), [
                'mealId' => $mealToBeCreated->id,
                'quantity' => -5,
            ])->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCartMealSuccessfullyUpdated(): void
    {
        $mealToBeUpdated = reset($this->mealsInCart);
        $updatedQuantity = $this->faker->numberBetween(1, 5);

        $expectedResponse = [
            'quantity' => $updatedQuantity,
            'id' => $mealToBeUpdated->id,
            'price' => $mealToBeUpdated->price,
            'categoryId' => $mealToBeUpdated->category_id,
            'name' => $mealToBeUpdated->name,
            'isVegan' => $mealToBeUpdated->is_vegan,
            'isSpicy' => $mealToBeUpdated->is_spicy,
        ];

        $this->loginAs($this->userWithFilledCart)
            ->putJson($this->makeUpdateUrl($mealToBeUpdated->id), ['quantity' => $updatedQuantity])
            ->assertOk()
            ->assertExactJson($expectedResponse);
    }

    public function testCartMealUpdatingFailed(): void
    {
        $this->putJson($this->makeUpdateUrl(1))->assertUnauthorized();

        $meal = reset($this->mealsInCart);

        $notExistingMealId = $meal->id + $this->faker->numberBetween(1000, 5000);
        $request = [
            'quantity' => $this->faker->numberBetween(1, 5),
        ];

        $this->loginAs($this->userWithEmptyCart)
            ->putJson($this->makeUpdateUrl($notExistingMealId), $request)
            ->assertNotFound();

        $requestWithInvalidQuantity = [
            'quantity' => 0,
        ];

        $this->loginAs($this->userWithEmptyCart)
            ->putJson($this->makeUpdateUrl($meal->id), $requestWithInvalidQuantity)
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCartMealSuccessfullyRemoved(): void
    {
        $mealIdToBeRemoved = reset($this->mealsInCart);

        $this->loginAs($this->userWithFilledCart)
            ->deleteJson($this->makeRemoveMealUrl($mealIdToBeRemoved->id))
            ->assertNoContent();
    }

    public function testCartMealRemovingFailed(): void
    {
        $this->deleteJson($this->makeRemoveMealUrl(1))->assertUnauthorized();

        $mealIdToBeRemoved = reset($this->mealsInCart);

        $this->loginAs($this->userWithFilledCart)
            ->deleteJson($this->makeRemoveMealUrl($mealIdToBeRemoved->id))
            ->assertNoContent();

        $this->loginAs($this->userWithFilledCart)
            ->deleteJson($this->makeRemoveMealUrl($mealIdToBeRemoved->id))
            ->assertNotFound();
    }

    private function makeCreateUrl(): string
    {
        return URL::route('v1.cart.meal.create');
    }

    private function makeUpdateUrl(int $mealId): string
    {
        return URL::route('v1.cart.meal.update', ['id' => $mealId]);
    }

    private function makeRemoveMealUrl(int $mealId): string
    {
        return URL::route('v1.cart.meal.remove', ['id' => $mealId]);
    }
}
