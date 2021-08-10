<?php

namespace Tests\Feature\Api\V1\User;

use App\Models\Cart;
use App\Models\CartMeal;
use App\Models\Category;
use App\Models\Meal;
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

class CartControllerTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

    private User $userWithoutCart;
    private User $userWithEmptyCart;
    private User $userWithFilledCart;

    /** @var CartMeal[] */
    private array $cartMeals = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->userWithoutCart = UserFactory::new()->create();

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

        $this->userWithFilledCart = UserFactory::new()->create();
        $cart->create(['user_id' => $this->userWithFilledCart->id]);

        $this->userWithEmptyCart = UserFactory::new()->create();
        CartFactory::new()->create(['user_id' => $this->userWithEmptyCart->id]);
    }

    public function testCartSuccessfullyFetched(): void
    {
        $expectedResponse = [];
        foreach ($this->cartMeals as $cartMeal) {
            /** @var Meal $meal */
            $meal = $cartMeal['meal'];

            $expectedResponse[] = [
                'quantity' => $cartMeal['quantity'],
                'id' => $meal->id,
                'price' => $meal->price,
                'categoryId' => $meal->category_id,
                'name' => $meal->name,
                'isVegan' => $meal->is_vegan,
                'isSpicy' => $meal->is_spicy,
            ];
        }

        $this->loginAs($this->userWithFilledCart)
            ->getJson($this->makeGetUrl())
            ->assertOk()
            ->assertExactJson($expectedResponse);
    }

    public function testCartFetchingFailed(): void
    {
        $this->getJson($this->makeGetUrl())->assertUnauthorized();

        $this->loginAs($this->userWithoutCart)->getJson($this->makeGetUrl())->assertNotFound();
    }

    public function testCartSuccessfullyCreated(): void
    {
        $this->loginAs($this->userWithoutCart)
            ->postJson($this->makeCreateUrl())
            ->assertCreated()
            ->assertExactJson([]);

        $createdCart = new Cart();
        $createdCart->user_id = $this->userWithoutCart->id;

        $this->loginAs($this->userWithoutCart)
            ->getJson($this->makeGetUrl())
            ->assertOk();
    }

    public function testCartCreationFailed(): void
    {
        $this->postJson($this->makeCreateUrl())->assertUnauthorized();

        $this->loginAs($this->userWithEmptyCart)
            ->postJson($this->makeCreateUrl())
            ->assertStatus(JsonResponse::HTTP_CONFLICT);
    }

    public function testCartSuccessfullyCleared(): void
    {
        $this->loginAs($this->userWithFilledCart)
            ->deleteJson($this->makeClearUrl())
            ->assertNoContent();

        $this->loginAs($this->userWithFilledCart)
            ->getJson($this->makeGetUrl())
            ->assertNotFound();
    }

    public function testCartClearingFailed(): void
    {
        $this->deleteJson($this->makeClearUrl())->assertUnauthorized();
    }

    private function makeGetUrl(): string
    {
        return URL::route('v1.cart.get');
    }

    private function makeCreateUrl(): string
    {
        return URL::route('v1.cart.create');
    }

    private function makeClearUrl(): string
    {
        return URL::route('v1.cart.clear');
    }
}
