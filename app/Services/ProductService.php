<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

use App\Models\Product;

class ProductService
{
    /**
     * Get a page of products.
     *
     * @return LengthAwarePaginator<int, Product>
     */
    public function paginate(int $perPage): LengthAwarePaginator
    {
        return Product::query()->orderBy('id')->paginate($perPage);
    }

    /**
     * Find a product by its id, or return null when it does not exist.
     */
    public function findById(string $id): ?Product
    {
        return Product::find($id);
    }
}
