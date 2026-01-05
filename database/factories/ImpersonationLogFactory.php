<?php

namespace Database\Factories;

use App\Models\AdminUser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ImpersonationLog>
 */
class ImpersonationLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'admin_user_id' => AdminUser::factory(),
            'admin_email' => fake()->safeEmail(),
            'tenant_id' => 'tenant-' . fake()->unique()->numberBetween(1000, 9999),
            'tenant_name' => fake()->company(),
            'target_user_id' => fake()->uuid(),
            'target_user_name' => fake()->name(),
            'target_user_email' => fake()->safeEmail(),
            'started_at' => now(),
            'ended_at' => fake()->optional()->dateTime(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }

    /**
     * Indicate that the impersonation is still active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'ended_at' => null,
        ]);
    }

    /**
     * Indicate that the impersonation has ended.
     */
    public function ended(): static
    {
        return $this->state(fn (array $attributes) => [
            'ended_at' => now(),
        ]);
    }
}
