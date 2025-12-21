<?php

namespace Tests\Helpers;

use App\Models\SubscriptionPlan;
use App\Models\Tenant;

trait TenantTestHelper
{
    protected Tenant $tenant;
    protected static ?Tenant $sharedTenant = null;

    /**
     * Initialize a test tenant WITHOUT creating physical database
     * Reuses the same tenant across tests for performance
     */
    protected function initializeTenant(array $attributes = []): Tenant
    {
        // Reuse existing tenant if available
        if (self::$sharedTenant && empty($attributes)) {
            $this->tenant = self::$sharedTenant;
            tenancy()->initialize($this->tenant);
            return $this->tenant;
        }

        // Create subscription plan if needed
        $plan = SubscriptionPlan::firstOrCreate(
            ['slug' => 'test-plan'],
            [
                'name' => 'Test Plan',
                'price_monthly' => 0,
                'price_yearly' => 0,
                'limits' => ['users' => 10, 'clients' => 1000],
                'features' => ['basic_features'],
            ]
        );

        // Create tenant record
        $tenantId = $attributes['id'] ?? 'test-' . uniqid();

        $this->tenant = Tenant::create(array_merge([
            'id' => $tenantId,
            'slug' => $attributes['slug'] ?? $tenantId,
            'name' => 'Test Tenant',
            'email' => 'test@tenant.com',
            'phone' => '(11) 99999-9999',
            'subscription_plan_id' => $plan->id,
            'subscription_status' => 'active',
            'is_active' => true,
        ], $attributes));

        // Create domain for tenant
        $this->tenant->domains()->create([
            'domain' => "{$tenantId}.localhost",
        ]);

        // Initialize tenancy context
        tenancy()->initialize($this->tenant);

        // Cache this tenant for reuse if no custom attributes
        if (empty($attributes)) {
            self::$sharedTenant = $this->tenant;
        }

        return $this->tenant;
    }

    protected function actingAsTenant(?Tenant $tenant = null): self
    {
        $tenant = $tenant ?? $this->tenant;

        if (!$tenant) {
            throw new \RuntimeException('No tenant initialized. Call initializeTenant() first.');
        }

        tenancy()->initialize($tenant);

        return $this;
    }

    protected function endTenancy(): self
    {
        tenancy()->end();

        return $this;
    }

    protected function createAndInitializeTenant(array $attributes = []): Tenant
    {
        return $this->initializeTenant($attributes);
    }
}
