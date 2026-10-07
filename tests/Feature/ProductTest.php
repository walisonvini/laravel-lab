<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_products_paginated(): void
    {
        $products = Product::factory(3)->create();

        $response = $this->getJson('/api/products?per_page=2');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('result.per_page', 2)
            ->assertJsonPath('result.total', 3)
            ->assertJsonCount(2, 'result.data')
            ->assertJsonPath('result.data.0.id', $products[0]->id)
            ->assertJsonPath('result.data.1.id', $products[1]->id);
    }

    public function test_it_limits_the_page_size(): void
    {
        $this->getJson('/api/products?per_page=999')
            ->assertOk()
            ->assertJsonPath('result.per_page', 100);

        $this->getJson('/api/products?per_page=0')
            ->assertOk()
            ->assertJsonPath('result.per_page', 15);
    }

    public function test_it_shows_a_product(): void
    {
        $product = Product::factory()->create(['price' => 19.90]);

        $response = $this->getJson("/api/products/{$product->id}");

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('result.id', $product->id)
            ->assertJsonPath('result.sku', $product->sku)
            ->assertJsonPath('result.price', '19.90');
    }

    public function test_it_returns_not_found_for_a_missing_product(): void
    {
        $response = $this->getJson('/api/products/999999');

        $response
            ->assertNotFound()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Product not found.');
    }
}
