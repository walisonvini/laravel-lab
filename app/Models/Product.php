<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'sku',
    'name',
    'category',
    'description',
    'price',
    'stock_quantity',
    'height_cm',
    'width_cm',
    'length_cm',
    'weight_grams',
    'is_active',
])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'height_cm' => 'integer',
            'width_cm' => 'integer',
            'length_cm' => 'integer',
            'weight_grams' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
