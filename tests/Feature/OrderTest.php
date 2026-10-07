<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_order_and_decrements_the_stock(): void
    {
        $customer = Customer::factory()->create();
        $keyboard = Product::factory()->create(['price' => 19.90, 'stock_quantity' => 10]);
        $cable = Product::factory()->create(['price' => 0.10, 'stock_quantity' => 3]);

        $response = $this->actingAs($customer->user)->postJson('/api/orders', [
            'items' => [
                ['product_id' => $keyboard->id, 'quantity' => 3],
                ['product_id' => $cable->id, 'quantity' => 3],
            ],
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('result.customer_id', $customer->id)
            ->assertJsonPath('result.status', 'pending')
            ->assertJsonPath('result.subtotal', '60.00')
            ->assertJsonPath('result.total', '60.00')
            ->assertJsonCount(2, 'result.items');

        $this->assertDatabaseHas('order_items', [
            'order_id' => $response->json('result.id'),
            'product_id' => $keyboard->id,
            'quantity' => 3,
            'unit_price' => 19.90,
            'total' => 59.70,
        ]);
        $this->assertSame(7, $keyboard->refresh()->stock_quantity);
        $this->assertSame(0, $cable->refresh()->stock_quantity);
    }

    public function test_it_requires_authentication(): void
    {
        $product = Product::factory()->create();

        $response = $this->postJson('/api/orders', [
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ]);

        $response->assertUnauthorized();
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_it_rejects_a_user_without_a_customer_profile(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/orders', [
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ]);

        $response
            ->assertForbidden()
            ->assertJsonPath('success', false);

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_it_rejects_the_whole_order_when_an_item_has_insufficient_stock(): void
    {
        $customer = Customer::factory()->create();
        $available = Product::factory()->create(['stock_quantity' => 10]);
        $scarce = Product::factory()->create(['stock_quantity' => 1]);

        $response = $this->actingAs($customer->user)->postJson('/api/orders', [
            'items' => [
                ['product_id' => $available->id, 'quantity' => 2],
                ['product_id' => $scarce->id, 'quantity' => 2],
            ],
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['items.1.quantity']);

        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(10, $available->refresh()->stock_quantity);
        $this->assertSame(1, $scarce->refresh()->stock_quantity);
    }

    public function test_it_rejects_an_unavailable_product(): void
    {
        $customer = Customer::factory()->create();
        $inactive = Product::factory()->inactive()->create();

        $response = $this->actingAs($customer->user)->postJson('/api/orders', [
            'items' => [
                ['product_id' => $inactive->id, 'quantity' => 1],
                ['product_id' => 999999, 'quantity' => 1],
            ],
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['items.0.product_id']);

        $this->assertDatabaseCount('orders', 0);
    }
}
