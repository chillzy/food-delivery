<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Actions\Cart;
use App\Exceptions\Repository\ModelNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CartMealResource;
use App\Models\Cart as CartModel;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CartController extends Controller
{
    private Cart $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    public function get(Request $request): ResourceCollection
    {
        /** @var User $user */
        $user = $request->user();

        $cart = $this->getCart($user);

        return CartMealResource::collection($cart->meals);
    }

    public function create(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        try {
            $this->getCart($user);

            throw new ConflictHttpException('Cart already exists');
        } catch (NotFoundHttpException $exception) {
            $this->cart->create($user);
        }

        return new JsonResponse(null, JsonResponse::HTTP_CREATED);
    }

    public function clear(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        try {
            $this->cart->clear($user);
        } catch (ModelNotFoundException $exception) {
            throw new NotFoundHttpException();
        }

        return new JsonResponse('', JsonResponse::HTTP_NO_CONTENT);
    }

    private function getCart(User $user): CartModel
    {
        try {
            $cart = $this->cart->get($user);
        } catch (ModelNotFoundException $exception) {
            throw new NotFoundHttpException();
        }

        return $cart;
    }
}
