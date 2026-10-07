<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

use Illuminate\Http\JsonResponse;

use App\Http\Requests\Order\OrderStoreRequest;

use App\Services\OrderService;

use App\Traits\ApiResponse;

class OrderController extends Controller
{
    use ApiResponse;

    public function __construct(
        private OrderService $orderService
    ) {}

    public function store(OrderStoreRequest $request): JsonResponse
    {
        $customer = $request->user()->customer;

        if (!$customer) {
            return $this->errorResponse('Only customers can place orders.', 403);
        }

        $order = $this->orderService->create($customer, $request->validated('items'));

        return $this->successResponse($order, 'Order created successfully.', 201);
    }
}
