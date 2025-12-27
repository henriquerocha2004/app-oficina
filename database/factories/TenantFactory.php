<?php

namespace Database\Factories;

use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant>
 */
class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company();

        // Criar subscription_plan diretamente sem usar factory para evitar problemas
        $plan = SubscriptionPlan::factory()->create();

        return [
            'id' => (string) Str::ulid(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->randomNumber(4),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'subscription_plan_id' => $plan->id,
            'subscription_status' => 'active',
            'trial_ends_at' => null,
            'is_active' => true,
            'settings' => null,
        ];
    }

    /**
     * Indicate that the tenant is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the tenant is on trial.
     */
    public function onTrial(): static
    {
        return $this->state(fn(array $attributes) => [
            'trial_ends_at' => now()->addDays(14),
        ]);
    }
}
