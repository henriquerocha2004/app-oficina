<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Troca de Óleo e Filtro',
                'description' => 'Troca completa de óleo do motor e filtro de óleo',
                'base_price' => 150.00,
                'category' => Service::CATEGORY_MAINTENANCE,
                'estimated_time' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'Alinhamento e Balanceamento',
                'description' => 'Alinhamento de direção e balanceamento das rodas',
                'base_price' => 120.00,
                'category' => Service::CATEGORY_ALIGNMENT,
                'estimated_time' => 90,
                'is_active' => true,
            ],
            [
                'name' => 'Revisão Completa',
                'description' => 'Revisão geral do veículo incluindo todos os fluidos e sistemas',
                'base_price' => 350.00,
                'category' => Service::CATEGORY_MAINTENANCE,
                'estimated_time' => 180,
                'is_active' => true,
            ],
            [
                'name' => 'Troca de Pastilhas de Freio',
                'description' => 'Substituição das pastilhas de freio dianteiras e/ou traseiras',
                'base_price' => 280.00,
                'category' => Service::CATEGORY_REPAIR,
                'estimated_time' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'Troca de Correia Dentada',
                'description' => 'Substituição da correia dentada e tensor',
                'base_price' => 450.00,
                'category' => Service::CATEGORY_REPAIR,
                'estimated_time' => 240,
                'is_active' => true,
            ],
            [
                'name' => 'Diagnóstico Eletrônico',
                'description' => 'Diagnóstico completo dos sistemas eletrônicos do veículo',
                'base_price' => 80.00,
                'category' => Service::CATEGORY_DIAGNOSTIC,
                'estimated_time' => 45,
                'is_active' => true,
            ],
            [
                'name' => 'Troca de Bateria',
                'description' => 'Substituição da bateria do veículo',
                'base_price' => 250.00,
                'category' => Service::CATEGORY_MAINTENANCE,
                'estimated_time' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Limpeza de Bicos Injetores',
                'description' => 'Limpeza e calibração dos bicos injetores',
                'base_price' => 180.00,
                'category' => Service::CATEGORY_MAINTENANCE,
                'estimated_time' => 90,
                'is_active' => true,
            ],
            [
                'name' => 'Troca de Embreagem',
                'description' => 'Substituição do kit de embreagem completo',
                'base_price' => 800.00,
                'category' => Service::CATEGORY_REPAIR,
                'estimated_time' => 360,
                'is_active' => true,
            ],
            [
                'name' => 'Pintura Parcial',
                'description' => 'Pintura parcial de uma ou mais peças do veículo',
                'base_price' => 600.00,
                'category' => Service::CATEGORY_PAINTING,
                'estimated_time' => 480,
                'is_active' => true,
            ],
            [
                'name' => 'Troca de Amortecedores',
                'description' => 'Substituição dos amortecedores dianteiros e/ou traseiros',
                'base_price' => 400.00,
                'category' => Service::CATEGORY_REPAIR,
                'estimated_time' => 150,
                'is_active' => true,
            ],
            [
                'name' => 'Limpeza de Ar Condicionado',
                'description' => 'Limpeza completa do sistema de ar condicionado e higienização',
                'base_price' => 120.00,
                'category' => Service::CATEGORY_MAINTENANCE,
                'estimated_time' => 60,
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
