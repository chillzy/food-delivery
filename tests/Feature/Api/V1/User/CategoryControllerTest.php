<?php

namespace Tests\Feature\Api\V1\User;

use App\Models\Category;
use App\Models\User;
use Database\Factories\CategoryFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

    private User $user;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = UserFactory::new()->create();
        $this->category = CategoryFactory::new()->create();
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

        $this->loginAs($this->user)
            ->getWithParams($this->makeListUrl(), $request)
            ->assertOk()
            ->assertExactJson($expectedResponse);
    }

    public function testCategoriesFetchingFailed(): void
    {
        $this->getJson($this->makeListUrl())->assertUnauthorized();
    }

    private function makeListUrl(): string
    {
        return URL::route('v1.category.list');
    }
}
