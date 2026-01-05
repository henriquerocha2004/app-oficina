<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Permission>
 */
class PermissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $module = fake()->randomElement(['clients', 'vehicles', 'products', 'services', 'users']);
        $action = fake()->randomElement(['view', 'create', 'edit', 'delete']);
        $unique = fake()->unique()->randomNumber(5);
        
        return [
            'name' => ucfirst($action) . ' ' . ucfirst($module),
            'slug' => $module . '.' . $action . '.' . $unique,
            'description' => fake()->sentence(),
            'module' => $module,
        ];
    }
}
