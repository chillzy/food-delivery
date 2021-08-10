<?php

namespace Tests\Feature\Api\V1\Admin;

use App\Models\Admin;
use App\Models\Category;
use Database\Factories\AdminFactory;
use Database\Factories\CategoryFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

    private Admin $admin;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = AdminFactory::new()->create();
        $this->category = CategoryFactory::new()->create();
    }

    public function testCategorySuccessfullyCreated(): void
    {
        $name = $this->faker->word;

        $response = $this->loginAs($this->admin, 'admin')->postJson($this->makeCreateUrl(), ['name' => $name]);
        $response->assertCreated()
            ->assertExactJson([
                'id' => $response['id'],
                'name' => $name,
                'mealsCount' => null,
            ]);

        $this->assertDatabaseHas(Category::class, ['name' => $name]);
    }

    public function testCategoryCreationFailed(): void
    {
        $notValidName = $this->faker->realTextBetween(256, 300);

        $this->postJson($this->makeCreateUrl(), ['name' => $notValidName])->assertUnauthorized();
        $this->assertDatabaseMissing(Category::class, ['name' => $notValidName]);

        $this->loginAs($this->admin, 'admin')
            ->postJson($this->makeCreateUrl(), ['name' => $notValidName])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertDatabaseMissing(Category::class, ['name' => $notValidName]);
    }

    public function testCategorySuccessfullyFetched(): void
    {
        $expectedResponse = [
            'id' => $this->category->id,
            'name' => $this->category->name,
            'meals' => [],
            'mealsCount' => null,
        ];

        foreach ($this->category->meals as $meal) {
            $expectedResponse['meals'][] = [
                'id' => $meal->id,
                'price' => $meal->price,
                'name' => $meal->name,
                'categoryId' => $meal->category_id,
                'isVegan' => $meal->is_vegan,
                'isSpicy' => $meal->is_spicy,
            ];
        }

        $this->loginAs($this->admin, 'admin')
            ->getJson($this->makeGetUrl($this->category->id))
            ->assertOk()
            ->assertExactJson($expectedResponse);
    }

    public function testCategoryFetchingFailed(): void
    {
        $this->getJson($this->makeGetUrl($this->category->id))->assertUnauthorized();

        $notExistingCategoryId = $this->category->id + $this->faker->numberBetween(1, 100);

        $this->loginAs($this->admin, 'admin')
            ->getJson($this->makeGetUrl($notExistingCategoryId))
            ->assertNotFound();
    }

    public function testCategorySuccessfullyDeleted(): void
    {
        $this->loginAs($this->admin, 'admin')
            ->deleteJson($this->makeDeleteUrl($this->category->id))
            ->assertNoContent();

        $this->assertSoftDeleted(Category::class, ['id' => $this->category->id]);
    }

    public function testCategoryDeletionFailed(): void
    {
        $this->deleteJson($this->makeDeleteUrl($this->category->id))->assertUnauthorized();
        $this->assertDatabaseHas(Category::class, ['id' => $this->category->id]);
    }

    public function testCategoriesListSuccessfullyFetched(): void
    {
        /** @var Category[] $categories */
        $categories = array_merge([$this->category], CategoryFactory::new()->count(2)->create()->all());

        $expectedResponse = [];
        foreach ($categories as $category) {
            $expectedResponse[] = [
                'id' => $category->id,
                'name' => $category->name,
                'mealsCount' => $this->category->meals->count(),
            ];
        }

        $request = [
            'limit' => 30,
            'offset' => 0,
        ];

        $this->loginAs($this->admin, 'admin')
            ->getWithParams($this->makeListUrl(), $request)
            ->assertOk()
            ->assertExactJson($expectedResponse);
    }

    public function testCategoriesFetchingFailed(): void
    {
        $this->getJson($this->makeListUrl())->assertUnauthorized();

        $request = [
            'limit' => 10000,
            'offset' => 0,
        ];

        $this->loginAs($this->admin, 'admin')
            ->getWithParams($this->makeListUrl(), $request)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    private function makeCreateUrl(): string
    {
        return URL::route('v1.admin.category.create');
    }

    private function makeGetUrl(int $id): string
    {
        return URL::route('v1.admin.category.get', ['id' => $id]);
    }

    private function makeDeleteUrl(int $id): string
    {
        return URL::route('v1.admin.category.delete', ['id' => $id]);
    }

    private function makeListUrl(): string
    {
        return URL::route('v1.admin.category.list');
    }
}
