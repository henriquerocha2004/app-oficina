<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = Product::getCategories();
        $units = Product::getUnits();

        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->optional()->sentence(10),
            'sku' => $this->faker->optional()->regexify('[A-Z]{3}-[0-9]{4}'),
            'barcode' => $this->faker->optional()->ean13(),
            'manufacturer' => $this->faker->optional()->company(),
            'category' => $this->faker->randomElement($categories),
            'stock_quantity' => $this->faker->numberBetween(0, 100),
            'min_stock_level' => $this->faker->numberBetween(5, 20),
            'unit' => $this->faker->randomElement($units),
            'unit_price' => $this->faker->randomFloat(2, 10, 500),
            'suggested_price' => $this->faker->optional()->randomFloat(2, 15, 600),
            'is_active' => $this->faker->boolean(95),
        ];
    }

    /**
     * Indicate that the product has low stock
     */
    public function lowStock(): static
    {
        return $this->state(function (array $attributes) {
            $minStock = $attributes['min_stock_level'] ?? 10;
            return [
                'stock_quantity' => $this->faker->numberBetween(0, $minStock - 1),
            ];
        });
    }

    /**
     * Indicate that the product is out of stock
     */
    public function outOfStock(): static
    {
        return $this->state(fn(array $attributes) => [
            'stock_quantity' => 0,
        ]);
    }

    /**
     * Indicate that the product is inactive
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }
}
