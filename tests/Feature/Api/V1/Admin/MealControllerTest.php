<?php

namespace Tests\Feature\Api\V1\Admin;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Meal;
use Database\Factories\AdminFactory;
use Database\Factories\CategoryFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class MealControllerTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

    private Admin $admin;
    private Category $category;
    private Meal $meal;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = AdminFactory::new()->create();
        $this->category = CategoryFactory::new()->create();
        $this->meal = $this->category->meals->first();
    }

    public function testMealSuccessfullyCreated(): void
    {
        $data = [
            'price' => $this->faker->numberBetween(100, 10000),
            'categoryId' => $this->category->id,
            'name' => $this->faker->word,
            'isVegan' => $this->faker->boolean,
            'isSpicy' => $this->faker->boolean,
        ];

        $response = $this->loginAs($this->admin, 'admin')
            ->postJson($this->makeCreateUrl(), $data);

        $response->assertCreated()
            ->assertExactJson([
                'id' => $response['id'],
                'price' => $data['price'],
                'categoryId' => $data['categoryId'],
                'name' => $data['name'],
                'isVegan' => $data['isVegan'],
                'isSpicy' => $data['isSpicy'],
            ]);

        $this->assertDatabaseHas(Meal::class, [
            'id' => $response['id'],
            'price' => $data['price'],
            'category_id' => $data['categoryId'],
            'name' => $data['name'],
            'is_vegan' => $data['isVegan'],
            'is_spicy' => $data['isSpicy'],
        ]);
    }

    public function testMealCreationFailed(): void
    {
        $validName = $this->faker->word;
        $validIsVegan = $this->faker->boolean;
        $validIsSpicy = $this->faker->boolean;

        $data = [
            'price' => $this->faker->numberBetween(100, 10000),
            'categoryId' => $this->category->id,
            'name' => $validName,
            'isVegan' => $validIsVegan,
            'isSpicy' => $validIsSpicy,
        ];

        $this->postJson($this->makeCreateUrl(), $data)->assertUnauthorized();

        $withInvalidName = array_merge($data, ['name' => $this->faker->realTextBetween(300, 400)]);
        $this->loginAs($this->admin, 'admin')
            ->postJson($this->makeCreateUrl(), $withInvalidName)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertDatabaseMissing(Meal::class, [
            'price' => $withInvalidName['price'],
            'category_id' => $withInvalidName['categoryId'],
            'name' => $withInvalidName['name'],
            'is_vegan' => $withInvalidName['isVegan'],
            'is_spicy' => $withInvalidName['isSpicy'],
        ]);

        $withInvalidIsVegan = array_merge($data, ['isVegan' => 'false']);
        $this->loginAs($this->admin, 'admin')
            ->postJson($this->makeCreateUrl(), $withInvalidIsVegan)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertDatabaseMissing(Meal::class, [
            'price' => $withInvalidIsVegan['price'],
            'category_id' => $withInvalidIsVegan['categoryId'],
            'name' => $withInvalidIsVegan['name'],
            'is_vegan' => $withInvalidIsVegan['isVegan'],
            'is_spicy' => $withInvalidIsVegan['isSpicy'],
        ]);

        $withNotExistingCategoryId = array_merge($data, [
            'categoryId' => $this->category->id + $this->faker->numberBetween(100, 200),
        ]);
        $this->loginAs($this->admin, 'admin')
            ->postJson($this->makeCreateUrl(), $withNotExistingCategoryId)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertDatabaseMissing(Meal::class, [
            'price' => $withNotExistingCategoryId['price'],
            'category_id' => $withNotExistingCategoryId['categoryId'],
            'name' => $withNotExistingCategoryId['name'],
            'is_vegan' => $withNotExistingCategoryId['isVegan'],
            'is_spicy' => $withNotExistingCategoryId['isSpicy'],
        ]);
    }

    public function testMealSuccessfullyFetched(): void
    {
        $this->loginAs($this->admin, 'admin')
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
        $this->loginAs($this->admin, 'admin')
            ->getJson($this->makeGetUrl($notExistingMealId))
            ->assertNotFound();
    }

    public function testMealSuccessfullyUpdated(): void
    {
        $data = [
            'price' => $this->meal->price,
            'categoryId' => $this->meal->category_id,
            'name' => $this->faker->word,
            'isVegan' => $this->meal->is_vegan,
            'isSpicy' => $this->meal->is_spicy,
        ];

        $response = $this->loginAs($this->admin, 'admin')
            ->putJson($this->makeUpdateUrl($this->meal->id), $data)
            ->assertOk();

        $this->assertDatabaseHas(Meal::class, [
            'id' => $this->meal->id,
            'price' => $data['price'],
            'category_id' => $data['categoryId'],
            'name' => $data['name'],
            'is_vegan' => $data['isVegan'],
            'is_spicy' => $data['isSpicy'],
        ]);

        $response->assertExactJson([
            'id' => $this->meal->id,
            'price' => $data['price'],
            'categoryId' => $data['categoryId'],
            'name' => $data['name'],
            'isVegan' => $data['isVegan'],
            'isSpicy' => $data['isSpicy'],
        ]);
    }

    public function testMealUpdatingFailed(): void
    {
        $this->putJson($this->makeUpdateUrl($this->meal->id), [
            'price' => $this->meal->price,
            'categoryId' => $this->meal->category_id,
            'name' => 'Бургер',
            'isVegan' => false,
            'isSpicy' => false,
        ])->assertUnauthorized();
    }

    public function testMealSuccessfullyDeleted(): void
    {
        $this->loginAs($this->admin, 'admin')
            ->deleteJson($this->makeDeleteUrl($this->meal->id))
            ->assertNoContent();

        $this->assertSoftDeleted(Meal::class, ['id' => $this->meal->id]);
    }

    public function testMealDeletionFailed(): void
    {
        $this->deleteJson($this->makeDeleteUrl($this->meal->id))->assertUnauthorized();
        $this->assertDatabaseHas(Meal::class, ['id' => $this->meal->id]);
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

        $this->loginAs($this->admin, 'admin')
            ->getWithParams($this->makeListUrl(), $request)
            ->assertOk()
            ->assertExactJson($expectedResponse);
    }

    public function testMealsFetchingFailed(): void
    {
        $this->getWithParams($this->makeListUrl())->assertUnauthorized();

        $this->loginAs($this->admin, 'admin')
            ->getWithParams($this->makeListUrl(), ['limit' => 1000, 'offset' => 0])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    private function makeCreateUrl(): string
    {
        return URL::route('v1.admin.meal.create');
    }

    private function makeGetUrl(int $id): string
    {
        return URL::route('v1.admin.meal.get', ['id' => $id]);
    }

    private function makeUpdateUrl(int $id): string
    {
        return URL::route('v1.admin.meal.update', ['id' => $id]);
    }

    private function makeDeleteUrl(int $id): string
    {
        return URL::route('v1.admin.meal.delete', ['id' => $id]);
    }

    private function makeListUrl(): string
    {
        return URL::route('v1.admin.meal.list');
    }
}
