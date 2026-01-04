<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockMovement>
 */
class StockMovementFactory extends Factory
{
    protected $model = StockMovement::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $movementType = $this->faker->randomElement([StockMovement::TYPE_IN, StockMovement::TYPE_OUT]);
        $quantity = $this->faker->numberBetween(1, 50);

        return [
            'product_id' => Product::factory(),
            'movement_type' => $movementType,
            'quantity' => $quantity,
            'balance_after' => $this->faker->numberBetween(0, 100),
            'reference_type' => $this->faker->optional()->randomElement(['order', 'purchase', 'adjustment']),
            'reference_id' => $this->faker->optional()->uuid(),
            'reason' => $this->faker->randomElement(StockMovement::getReasons()),
            'notes' => $this->faker->optional()->sentence(),
            'user_id' => User::factory(),
        ];
    }

    /**
     * Indicate an inbound movement
     */
    public function in(): static
    {
        return $this->state(fn(array $attributes) => [
            'movement_type' => StockMovement::TYPE_IN,
            'reason' => $this->faker->randomElement([
                StockMovement::REASON_PURCHASE,
                StockMovement::REASON_RETURN,
                StockMovement::REASON_ADJUSTMENT,
            ]),
        ]);
    }

    /**
     * Indicate an outbound movement
     */
    public function out(): static
    {
        return $this->state(fn(array $attributes) => [
            'movement_type' => StockMovement::TYPE_OUT,
            'reason' => $this->faker->randomElement([
                StockMovement::REASON_SALE,
                StockMovement::REASON_LOSS,
                StockMovement::REASON_ADJUSTMENT,
            ]),
        ]);
    }
}
