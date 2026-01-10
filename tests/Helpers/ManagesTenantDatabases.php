<?php

namespace Tests\Helpers;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait ManagesTenantDatabases
{
    protected array $createdTenants = [];

    /**
     * Register a tenant for cleanup after test.
     */
    protected function registerTenantForCleanup(Tenant $tenant): void
    {
        $this->createdTenants[] = $tenant;
    }

    /**
     * Clean up all tenant databases created during tests.
     */
    protected function cleanupTenantDatabases(): void
    {
        foreach ($this->createdTenants as $tenant) {
            try {
                // Drop tenant database
                $databaseName = $tenant->database()->getName();

                if (Schema::hasTable('tenants') && Tenant::find($tenant->id)) {
                    // Delete tenant record (will cascade delete domains)
                    $tenant->delete();
                }

                // Drop database if exists
                DB::statement("DROP DATABASE IF EXISTS `{$databaseName}`");
            } catch (\Exception $e) {
                // Silently continue if cleanup fails
            }
        }

        $this->createdTenants = [];
    }

    /**
     * Tear down and cleanup.
     */
    protected function tearDown(): void
    {
        $this->cleanupTenantDatabases();
        parent::tearDown();
    }
}
