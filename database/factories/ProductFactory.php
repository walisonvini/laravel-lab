<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        [$name, $category] = fake()->randomElement([
            ['Wireless Mouse', 'peripherals'],
            ['Mechanical Keyboard', 'peripherals'],
            ['Gaming Mouse Pad', 'peripherals'],
            ['Webcam Full HD', 'peripherals'],
            ['Bluetooth Headphones', 'audio'],
            ['USB-C Hub', 'accessories'],
            ['Laptop Stand', 'accessories'],
            ['Power Bank 20000mAh', 'accessories'],
            ['Portable SSD 1TB', 'storage'],
            ['Smartwatch', 'wearables'],
        ]);

        return [
            'sku' => fake()->unique()->bothify('SKU-####-???'),
            'name' => $name,
            'category' => $category,
            'description' => fake()->sentence(12),
            'price' => fake()->randomFloat(2, 19.90, 1999.90),
            'stock_quantity' => fake()->numberBetween(0, 200),
            'height_cm' => fake()->numberBetween(2, 40),
            'width_cm' => fake()->numberBetween(5, 60),
            'length_cm' => fake()->numberBetween(5, 60),
            'weight_grams' => fake()->numberBetween(50, 5000),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the product is not available for sale.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the product has no units in stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => 0,
        ]);
    }
}
