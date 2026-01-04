<?php

namespace Tests\Helpers;

use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use Illuminate\Support\Facades\Event;
use Stancl\Tenancy\Events;
use Stancl\Tenancy\Jobs;

trait TenantTestHelper
{
    protected Tenant $tenant;
    protected static ?Tenant $sharedTenant = null;
    protected static bool $databaseCreated = false;

    /**
     * Initialize a test tenant reusing the same physical database across tests
     * Creates database only once for maximum performance
     */
    protected function initializeTenant(array $attributes = []): Tenant
    {
        // Reuse existing shared tenant if available and no custom attributes
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

        // Use fixed tenant ID for shared tenant to avoid creating multiple databases
        $tenantId = empty($attributes) ? 'test-shared' : 'test-' . uniqid();

        // Find or create tenant record without firing events
        $existingTenant = Tenant::find($tenantId);

        if ($existingTenant) {
            $this->tenant = $existingTenant;
        } else {
            $this->tenant = Tenant::withoutEvents(function () use ($tenantId, $attributes, $plan) {
                return Tenant::create(array_merge([
                    'id' => $tenantId,
                    'slug' => $attributes['slug'] ?? $tenantId,
                    'name' => 'Test Tenant',
                    'email' => 'test@tenant.com',
                    'phone' => '(11) 99999-9999',
                    'subscription_plan_id' => $plan->id,
                    'subscription_status' => 'active',
                    'is_active' => true,
                ], $attributes));
            });

            // Create domain for tenant
            $this->tenant->domains()->create([
                'domain' => "{$tenantId}.localhost",
            ]);
        }

        // Always ensure physical database exists for shared tenant
        // This auto-creates the database if it doesn't exist yet
        if (!self::$databaseCreated && empty($attributes)) {
            try {
                // Check if database exists
                $databaseName = config('tenancy.database.prefix') . $this->tenant->getTenantKey() . config('tenancy.database.suffix');
                $dbExists = \DB::select("SHOW DATABASES LIKE '{$databaseName}'");

                if (empty($dbExists)) {
                    // Create database and run migrations
                    $databaseManager = app(\Stancl\Tenancy\Database\DatabaseManager::class);
                    $createDb = new Jobs\CreateDatabase($this->tenant);
                    $createDb->handle($databaseManager);

                    $migrator = app(\Illuminate\Database\Migrations\Migrator::class);
                    $migrateDb = new Jobs\MigrateDatabase($this->tenant);
                    $migrateDb->handle($migrator);
                }

                self::$databaseCreated = true;
            } catch (\Exception $e) {
                // If fails, tests will fail anyway when trying to use the database
                throw new \RuntimeException("Failed to create test database: " . $e->getMessage(), 0, $e);
            }
        }

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
