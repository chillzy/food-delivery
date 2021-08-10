<?php

namespace Tests\Feature\Api\V1\User;

use App\Models\Category;
use App\Models\Meal;
use App\Models\User;
use Database\Factories\CategoryFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class MealControllerTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

    private User $user;
    private Category $category;
    private Meal $meal;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = UserFactory::new()->create();
        $this->category = CategoryFactory::new()->create();
        $this->meal = $this->category->meals->first();
    }

    public function testMealSuccessfullyFetched(): void
    {
        $this->loginAs($this->user)
            ->getJson($this->makeGetUrl($this->meal->id))
            ->assertOk()
            ->assertExactJson([
                'id' => $this->meal->id,
                'price' => $this->meal->price,
                'categoryId' => $this->meal->category_id,
                'name' => $this->meal->name,
                'isVegan' => $this->meal->is_vegan,
                'isSpicy' => $this->meal->is_spicy,
            ]);
    }

    public function testMealFetchingFailed(): void
    {
        $this->getJson($this->makeGetUrl($this->meal->id))->assertUnauthorized();

        $notExistingMealId = $this->category->meals->pluck('id')->sum();
        $this->loginAs($this->user)
            ->getJson($this->makeGetUrl($notExistingMealId))
            ->assertNotFound();
    }

    public function testMealsSuccessfullyFetched(): void
    {
        $expectedResponse = [];

        foreach ($this->category->meals as $meal) {
            $expectedResponse[] = [
                'id' => $meal->id,
                'price' => $meal->price,
                'categoryId' => $meal->category_id,
                'name' => $meal->name,
                'isVegan' => $meal->is_vegan,
                'isSpicy' => $meal->is_spicy,
            ];
        }

        $request = [
            'limit' => 30,
            'offset' => 0,
            'categoryId' => $this->category->id,
        ];

        $this->loginAs($this->user)
            ->getWithParams($this->makeListUrl(), $request)
            ->assertOk()
            ->assertExactJson($expectedResponse);
    }

    public function testMealsFetchingFailed(): void
    {
        $this->getWithParams($this->makeListUrl())->assertUnauthorized();
    }

    private function makeGetUrl(int $id): string
    {
        return URL::route('v1.user.meal.get', ['id' => $id]);
    }

    private function makeListUrl(): string
    {
        return URL::route('v1.user.meal.list');
    }
}
