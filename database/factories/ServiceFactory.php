<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $services = [
            [
                'name' => 'Troca de Óleo e Filtro',
                'category' => Service::CATEGORY_MAINTENANCE,
                'price' => 150.00,
                'time' => 60
            ],
            [
                'name' => 'Alinhamento e Balanceamento',
                'category' => Service::CATEGORY_ALIGNMENT,
                'price' => 120.00,
                'time' => 90
            ],
            [
                'name' => 'Revisão Completa',
                'category' => Service::CATEGORY_MAINTENANCE,
                'price' => 350.00,
                'time' => 180
            ],
            [
                'name' => 'Troca de Pastilhas de Freio',
                'category' => Service::CATEGORY_REPAIR,
                'price' => 280.00,
                'time' => 120
            ],
            [
                'name' => 'Troca de Correia Dentada',
                'category' => Service::CATEGORY_REPAIR,
                'price' => 450.00,
                'time' => 240
            ],
            [
                'name' => 'Diagnóstico Eletrônico',
                'category' => Service::CATEGORY_DIAGNOSTIC,
                'price' => 80.00,
                'time' => 45
            ],
            [
                'name' => 'Troca de Bateria',
                'category' => Service::CATEGORY_MAINTENANCE,
                'price' => 250.00,
                'time' => 30
            ],
            [
                'name' => 'Limpeza de Bicos Injetores',
                'category' => Service::CATEGORY_MAINTENANCE,
                'price' => 180.00,
                'time' => 90
            ],
            [
                'name' => 'Troca de Embreagem',
                'category' => Service::CATEGORY_REPAIR,
                'price' => 800.00,
                'time' => 360
            ],
            [
                'name' => 'Pintura Parcial',
                'category' => Service::CATEGORY_PAINTING,
                'price' => 600.00,
                'time' => 480
            ],
        ];

        $service = $this->faker->randomElement($services);

        return [
            'name' => $service['name'],
            'description' => $this->faker->optional()->sentence(10),
            'base_price' => $service['price'],
            'category' => $service['category'],
            'estimated_time' => $service['time'],
            'is_active' => $this->faker->boolean(95),
        ];
    }

    /**
     * Indicate that the service is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the service is a maintenance service.
     */
    public function maintenance(): static
    {
        return $this->state(fn(array $attributes) => [
            'category' => Service::CATEGORY_MAINTENANCE,
        ]);
    }

    /**
     * Indicate that the service is a repair service.
     */
    public function repair(): static
    {
        return $this->state(fn(array $attributes) => [
            'category' => Service::CATEGORY_REPAIR,
        ]);
    }
}
