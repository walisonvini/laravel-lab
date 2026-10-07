<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;

class OrderService
{
    /**
     * Create an order for the customer, reserving the stock of each product.
     *
     * @param  array<int, array{product_id: int, quantity: int}>  $items
     *
     * @throws ValidationException
     */
    public function create(Customer $customer, array $items): Order
    {
        $products = Product::query()
            ->whereKey(array_column($items, 'product_id'))
            ->get()
            ->keyBy('id');

        $orderItems = [];
        $subtotal = 0;

        foreach ($items as $index => $item) {
            $product = $products->get($item['product_id']);
            $quantity = (int) $item['quantity'];

            if (!$product || !$product->is_active) {
                throw ValidationException::withMessages([
                    "items.{$index}.product_id" => 'The selected product is not available.',
                ]);
            }

            if ($product->stock_quantity < $quantity) {
                throw ValidationException::withMessages([
                    "items.{$index}.quantity" => 'Insufficient stock for the selected product.',
                ]);
            }

            $total = $product->price * $quantity;
            $subtotal += $total;

            $orderItems[] = [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $product->price,
                'total' => $total,
            ];
        }

        $order = DB::transaction(function () use ($customer, $products, $orderItems, $subtotal): Order {
            foreach ($orderItems as $orderItem) {
                $products->get($orderItem['product_id'])->decrement('stock_quantity', $orderItem['quantity']);
            }

            $order = Order::create([
                'customer_id' => $customer->id,
                'subtotal' => $subtotal,
                'shipping_cost' => 0,
                'total' => $subtotal,
            ]);

            $order->items()->createMany($orderItems);

            return $order;
        });

        return $order->refresh()->load('items');
    }
}
