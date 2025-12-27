<?php

namespace Database\Factories;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubscriptionPlan>
 */
class SubscriptionPlanFactory extends Factory
{
    protected $model = SubscriptionPlan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->randomElement(['Básico', 'Premium', 'Enterprise', 'Starter']);
        $priceMonthly = fake()->randomFloat(2, 49.90, 499.90);

        return [
            'id' => (string) Str::ulid(),
            'name' => $name . ' Plan',
            'slug' => Str::slug($name) . '-' . rand(1000, 9999),
            'description' => fake()->sentence(),
            'price_monthly' => $priceMonthly,
            'price_yearly' => round($priceMonthly * 10, 2), // 2 meses grátis
            'limits' => [
                'max_users' => fake()->randomElement([1, 3, 5, 10, 50]),
                'max_clients' => fake()->randomElement([100, 500, 1000, 5000, null]),
                'max_vehicles' => fake()->randomElement([500, 2500, 5000, 10000, null]),
                'max_services_per_month' => fake()->randomElement([50, 250, 500, 1000, null]),
            ],
            'features' => [
                'Gestão de clientes',
                'Controle de veículos',
                'Relatórios básicos',
            ],
            'is_active' => true,
            'is_visible' => true,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    /**
     * Indicate that the plan is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the plan is hidden.
     */
    public function hidden(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_visible' => false,
        ]);
    }

    /**
     * Create a free plan.
     */
    public function free(): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => 'Free Plan',
            'slug' => 'free-' . rand(1000, 9999),
            'price_monthly' => 0,
            'price_yearly' => 0,
            'limits' => [
                'max_users' => 1,
                'max_clients' => 50,
                'max_vehicles' => 100,
                'max_services_per_month' => 20,
            ],
        ]);
    }
}
