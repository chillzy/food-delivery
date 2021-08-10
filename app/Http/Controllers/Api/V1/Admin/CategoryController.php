<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Exceptions\Repository\ModelNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\CreateCategoryRequest;
use App\Http\Requests\V1\ListCategoriesRequest;
use App\Http\Resources\V1\CategoryResource;
use App\Models\Category;
use App\Repositories\Category\CategoryRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryController extends Controller
{
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function create(CreateCategoryRequest $request): CategoryResource
    {
        $createdCategory = $this->categoryRepository->add($request->name);

        return new CategoryResource($createdCategory);
    }

    public function get(int $id): CategoryResource
    {
        return new CategoryResource($this->getCategory($id, true));
    }

    public function delete(int $id): JsonResponse
    {
        $category = $this->getCategory($id, false);

        $this->categoryRepository->remove($category);

        return new JsonResponse('', JsonResponse::HTTP_NO_CONTENT);
    }

    public function list(ListCategoriesRequest $request): ResourceCollection
    {
        return CategoryResource::collection(
            $this->categoryRepository->list($request->limit, $request->offset)
        );
    }

    public function getCategory(int $id, bool $mustMealsBeLoaded): Category
    {
        try {
            if ($mustMealsBeLoaded) {
                return $this->categoryRepository->getWithMeals($id);
            }

            return $this->categoryRepository->get($id);
        } catch (ModelNotFoundException $exception) {
            throw new NotFoundHttpException();
        }
    }
}
