<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Actions\Cart;
use App\Exceptions\Repository\ModelAlreadyExistsException;
use App\Exceptions\Repository\ModelNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\CreateCartMealRequest;
use App\Http\Requests\V1\UpdateCartMealRequest;
use App\Http\Resources\V1\CartMealResource;
use App\Models\User;
use App\Repositories\Cart\CartMealDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CartMealController extends Controller
{
    private Cart $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    public function create(CreateCartMealRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        try {
            $createdCartMeal = $this->cart->addMeal($user, new CartMealDTO($request->mealId, $request->quantity));
        } catch (ModelNotFoundException $exception) {
            throw new NotFoundHttpException();
        } catch (ModelAlreadyExistsException $exception) {
            throw new ConflictHttpException('The meal is already added');
        }

        return (new CartMealResource($createdCartMeal))->response()->setStatusCode(JsonResponse::HTTP_CREATED);
    }

    public function update(int $mealId, UpdateCartMealRequest $request): CartMealResource
    {
        /** @var User $user */
        $user = $request->user();

        try {
            $updatedCartMeal = $this->cart->updateMeal($user, new CartMealDTO($mealId, $request->quantity));
        } catch (ModelNotFoundException $exception) {
            throw new NotFoundHttpException();
        }

        return new CartMealResource($updatedCartMeal);
    }

    public function remove(int $mealId, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        try {
            $this->cart->removeMeal($user, $mealId);
        } catch (ModelNotFoundException $exception) {
            throw new NotFoundHttpException('Meal not found in cart');
        }

        return new JsonResponse('', JsonResponse::HTTP_NO_CONTENT);
    }
}
