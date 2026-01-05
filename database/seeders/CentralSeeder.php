<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class CentralSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create subscription plans
        SubscriptionPlan::create([
            'name' => 'Básico',
            'slug' => 'basic',
            'description' => 'Plano básico para pequenas oficinas',
            'price_monthly' => 0,
            'price_yearly' => 0,
            'limits' => json_encode(['max_users' => 3]),
            'features' => json_encode(['Até 3 usuários']),
            'is_active' => true,
            'is_visible' => true,
            'sort_order' => 1,
        ]);

        SubscriptionPlan::create([
            'name' => 'Profissional',
            'slug' => 'professional',
            'description' => 'Plano para oficinas em crescimento',
            'price_monthly' => 99.90,
            'price_yearly' => 999.00,
            'limits' => json_encode(['max_users' => 10]),
            'features' => json_encode(['Até 10 usuários', 'Suporte prioritário']),
            'is_active' => true,
            'is_visible' => true,
            'sort_order' => 2,
        ]);

        SubscriptionPlan::create([
            'name' => 'Empresarial',
            'slug' => 'enterprise',
            'description' => 'Plano para grandes oficinas',
            'price_monthly' => 299.90,
            'price_yearly' => 2999.00,
            'limits' => json_encode(['max_users' => -1]), // Ilimitado
            'features' => json_encode(['Usuários ilimitados', 'Suporte 24/7', 'Treinamento']),
            'is_active' => true,
            'is_visible' => true,
            'sort_order' => 3,
        ]);

        // Create super admin user
        AdminUser::create([
            'name' => 'Super Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
        ]);
    }
}
