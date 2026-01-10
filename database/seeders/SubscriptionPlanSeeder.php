<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Gratuito',
                'slug' => 'free',
                'description' => 'Plano gratuito para experimentar o sistema',
                'price_monthly' => 0,
                'price_yearly' => 0,
                'limits' => [
                    'max_users' => 1,
                    'max_clients' => 50,
                    'max_vehicles' => 100,
                    'max_orders_per_month' => 20,
                ],
                'features' => [
                    'basic_reports',
                    'email_support',
                ],
                'is_active' => true,
                'is_visible' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Básico',
                'slug' => 'basic',
                'description' => 'Ideal para oficinas pequenas',
                'price_monthly' => 49.90,
                'price_yearly' => 499.00, // ~2 meses grátis
                'limits' => [
                    'max_users' => 3,
                    'max_clients' => null, // unlimited
                    'max_vehicles' => null,
                    'max_orders_per_month' => null,
                ],
                'features' => [
                    'basic_reports',
                    'advanced_reports',
                    'email_support',
                    'chat_support',
                    'custom_branding',
                ],
                'is_active' => true,
                'is_visible' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'Para oficinas que querem o máximo',
                'price_monthly' => 99.90,
                'price_yearly' => 999.00, // ~2 meses grátis
                'limits' => [
                    'max_users' => 10,
                    'max_clients' => null,
                    'max_vehicles' => null,
                    'max_orders_per_month' => null,
                ],
                'features' => [
                    'basic_reports',
                    'advanced_reports',
                    'business_intelligence',
                    'email_support',
                    'chat_support',
                    'priority_support',
                    'phone_support',
                    'custom_branding',
                    'api_access',
                    'webhooks',
                    'advanced_integrations',
                ],
                'is_active' => true,
                'is_visible' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::create($plan);
        }
    }
}
