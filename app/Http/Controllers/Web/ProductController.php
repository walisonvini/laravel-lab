<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\ProductService;

use App\Traits\ApiResponse;

class ProductController extends Controller
{
    use ApiResponse;

    public function __construct(
        private ProductService $productService
    ) {}

    public function index(Request $request)
    {
        $products = $this->productService->paginate($this->perPage($request));

        return $this->successResponse($products, 'Products retrieved successfully.', 200);
    }

    public function show(string $product)
    {
        $product = $this->productService->findById($product);

        if (!$product) {
            return $this->errorResponse('Product not found.', 404);
        }

        return $this->successResponse($product, 'Product retrieved successfully.', 200);
    }
}
