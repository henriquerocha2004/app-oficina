<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Basic plan
        $basicPlan = SubscriptionPlan::where('slug', 'basic')->first();

        // Create demo tenant
        $tenant = Tenant::create([
            'id' => 'demo',
            'name' => 'Oficina Demo',
            'slug' => 'demo',
            'email' => 'demo@app-oficina.com',
            'phone' => '(11) 98765-4321',
            'subscription_plan_id' => $basicPlan->id,
            'subscription_status' => 'active',
            'trial_ends_at' => null,
            'is_active' => true,
            'settings' => [
                'theme' => 'light',
                'logo' => null,
            ],
        ]);

        // Create domain for this tenant
        $tenant->domains()->create([
            'domain' => 'demo.localhost',
        ]);

        $this->command->info('Tenant "demo" created successfully!');
        $this->command->info('Access at: http://demo.localhost:8000');
    }
}
