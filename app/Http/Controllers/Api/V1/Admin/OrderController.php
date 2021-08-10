<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Actions\Order\OrderCanceller;
use App\Actions\Order\OrderStatusMover;
use App\Exceptions\Actions\Order\OrderCantBeCancelledException;
use App\Exceptions\Repository\ModelNotFoundException;
use App\Exceptions\Actions\Order\OrderStatusCantBeMovedException;
use App\Exceptions\State\NotificationNotExistsException;
use App\Exceptions\State\StateNotExistsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\CancelOrderRequest;
use App\Http\Requests\V1\ListOrdersRequest;
use App\Http\Requests\V1\MoveOrderStatusRequest;
use App\Http\Resources\V1\OrderResource;
use App\Models\Order;
use App\Repositories\Order\ListOrdersDTO;
use App\Repositories\Order\OrderRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class OrderController extends Controller
{
    private OrderRepositoryInterface $orderRepository;
    private OrderStatusMover $orderStatusMover;
    private OrderCanceller $orderCanceller;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderStatusMover $orderStatusMover,
        OrderCanceller $orderCanceller
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderStatusMover = $orderStatusMover;
        $this->orderCanceller = $orderCanceller;
    }

    public function list(ListOrdersRequest $request): ResourceCollection
    {
        $listOrdersDTO = new ListOrdersDTO(
            $request->limit,
            $request->offset,
            $request->statuses
        );

        $orders = $this->orderRepository->list($listOrdersDTO);

        return OrderResource::collection($orders);
    }

    public function moveStatus(string $id, MoveOrderStatusRequest $request): JsonResponse
    {
        $order = $this->getOrder($id);

        try {
            $this->orderStatusMover->moveStatus($order);
        } catch (OrderStatusCantBeMovedException $exception) {
            throw new UnprocessableEntityHttpException('Order status cannot be moved');
        } catch (StateNotExistsException | NotificationNotExistsException $exception) {
            throw new UnprocessableEntityHttpException();
        } catch (ModelNotFoundException $exception) {
            throw new NotFoundHttpException();
        }

        return new JsonResponse('', JsonResponse::HTTP_NO_CONTENT);
    }

    public function cancel(string $id, CancelOrderRequest $request): JsonResponse
    {
        $order = $this->getOrder($id);

        try {
            $this->orderCanceller->cancel($order, $request->reason);
        } catch (OrderCantBeCancelledException $exception) {
            throw new UnprocessableEntityHttpException('Order cannot be cancelled');
        } catch (StateNotExistsException $exception) {
            throw new UnprocessableEntityHttpException();
        } catch (ModelNotFoundException $exception) {
            throw new NotFoundHttpException();
        }

        return new JsonResponse('', JsonResponse::HTTP_NO_CONTENT);
    }

    private function getOrder(string $id): Order
    {
        try {
            return $this->orderRepository->get($id);
        } catch (ModelNotFoundException $exception) {
            throw new NotFoundHttpException('Order not found');
        }
    }
}
