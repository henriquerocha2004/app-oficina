<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed central database with subscription plans
        \App\Models\SubscriptionPlan::create([
            'name' => 'Básico',
            'slug' => 'basico',
            'description' => 'Plano básico para pequenas oficinas',
            'price_monthly' => 99.90,
            'price_yearly' => 999.00,
            'limits' => ['max_users' => 5],
            'is_active' => true,
            'features' => ['Gestão de clientes', 'Gestão de veículos', 'Gestão de serviços'],
            'sort_order' => 1,
        ]);

        \App\Models\SubscriptionPlan::create([
            'name' => 'Profissional',
            'slug' => 'profissional',
            'description' => 'Plano profissional para oficinas médias',
            'price_monthly' => 199.90,
            'price_yearly' => 1999.00,
            'limits' => ['max_users' => 15],
            'is_active' => true,
            'features' => ['Gestão de clientes', 'Gestão de veículos', 'Gestão de serviços', 'Relatórios avançados'],
            'sort_order' => 2,
        ]);

        \App\Models\SubscriptionPlan::create([
            'name' => 'Empresarial',
            'slug' => 'empresarial',
            'description' => 'Plano empresarial para grandes oficinas',
            'price_monthly' => 399.90,
            'price_yearly' => 3999.00,
            'limits' => ['max_users' => null],
            'is_active' => true,
            'features' => ['Gestão de clientes', 'Gestão de veículos', 'Gestão de serviços', 'Relatórios avançados', 'API integrada', 'Suporte prioritário'],
            'sort_order' => 3,
        ]);

        // Tenant seeders should be run separately when inside a tenant context
        // $this->call([
        //     ClientsAndVehiclesSeeder::class,
        // ]);
    }
}
