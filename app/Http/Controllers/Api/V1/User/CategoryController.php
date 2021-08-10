<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ListCategoriesRequest;
use App\Http\Resources\V1\CategoryResource;
use App\Repositories\Category\CategoryRepositoryInterface;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoryController extends Controller
{
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function list(ListCategoriesRequest $request): ResourceCollection
    {
        return CategoryResource::collection($this->categoryRepository->list($request->limit, $request->offset));
    }
}
