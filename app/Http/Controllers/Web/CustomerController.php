<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

use Illuminate\Http\JsonResponse;

use App\Http\Requests\Customer\CustomerStoreRequest;

use App\Services\CustomerService;

use App\Traits\ApiResponse;

class CustomerController extends Controller
{
    use ApiResponse;

    public function __construct(
        private CustomerService $customerService
    ) {}

    public function store(CustomerStoreRequest $request): JsonResponse
    {
        $customer = $this->customerService->create($request->validated());

        return $this->successResponse($customer, 'Customer created successfully.', 201);
    }
}
