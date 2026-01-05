<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    /**
     * Ensures test database exists before running tests
     */
    protected static bool $testDatabaseCreated = false;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        // Create test database BEFORE parent::setUp() which triggers migrations
        if (!self::$testDatabaseCreated) {
            $this->createTestDatabase();
            self::$testDatabaseCreated = true;
        }

        parent::setUp();

        // Run central migrations after RefreshDatabase runs migrate:fresh
        // This ensures admin_users and impersonation_logs tables exist
        if (!self::$centralMigrationsRun) {
            echo "\n[TestCase] Running central migrations...\n";
            $this->artisan('migrate', [
                '--database' => 'central',
                '--path' => 'database/migrations/central',
                '--force' => true,
            ]);
            self::$centralMigrationsRun = true;
            echo "[TestCase] Central migrations completed\n";
        }

        // Disable tenancy middlewares globally for tests
        $this->withoutMiddleware([
            \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class,
            \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
        ]);
    }

    /**
     * Create the test database if it doesn't exist
     */
    protected function createTestDatabase(): void
    {
        try {
            // Use phpunit.xml env vars directly
            $database = 'app_oficina_central_test';
            $host = 'oficina-db';
            $username = 'root';
            $password = 'root';

            // Connect without database to create it
            $pdo = new \PDO(
                "mysql:host={$host}",
                $username,
                $password
            );

            // Create database if not exists
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        } catch (\Exception $e) {
            // If database can't be created, the error will be caught later
        }
    }

    /**
     * Track if central migrations have been run.
     * This ensures central migrations run once per test suite.
     * 
     * Note: Due to RefreshDatabase trait limitations, central migrations
     * must be run manually before tests. See docs/TESTING_CENTRAL_MIGRATIONS.md
     */
    protected static bool $centralMigrationsRun = false;

    /**
     * Reset central migrations flag (called before test suite runs).
     */
    public static function resetCentralMigrations(): void
    {
        self::$centralMigrationsRun = false;
    }

    /**
     * Override migrateDatabases to add central subfolder migrations.
     * 
     * Note: This method is NOT called due to RefreshDatabaseState::$migrated flag.
     * Central migrations must be run manually. See docs/TESTING_CENTRAL_MIGRATIONS.md
     */
    protected function migrateDatabases()
    {
        // Run default migrations (clears all tables including central)
        $this->artisan('migrate:fresh', $this->migrateFreshUsing());
        
        // Run central subfolder migrations after fresh
        // (admin_users, impersonation_logs, etc.)
        if (!self::$centralMigrationsRun) {
            $this->artisan('migrate', [
                '--database' => 'central',
                '--path' => 'database/migrations/central',
                '--force' => true,
            ]);
            self::$centralMigrationsRun = true;
        }
    }

    /**
     * Hook called after database has been refreshed.
     * 
     * Note: This method is NOT called due to RefreshDatabaseState::$migrated flag.
     * Central migrations must be run manually. See docs/TESTING_CENTRAL_MIGRATIONS.md
     */
    protected function afterRefreshingDatabase()
    {
        // Run central subfolder migrations after migrate:fresh
        // (admin_users, impersonation_logs, etc.)
        if (!self::$centralMigrationsRun) {
            $this->artisan('migrate', [
                '--database' => 'central',
                '--path' => 'database/migrations/central',
                '--force' => true,
            ]);
            self::$centralMigrationsRun = true;
        }
    }

    /**
     * Tear down the test environment.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        if (isset($this->tenant) && tenancy()->initialized) {
            try {
                DB::connection('tenant')->table('user_invitations')->delete();
                DB::connection('tenant')->table('permission_role')->delete();
                DB::connection('tenant')->table('users')->delete();
                DB::connection('tenant')->table('roles')->delete();
                DB::connection('tenant')->table('permissions')->delete();
                DB::connection('tenant')->table('clients')->delete();
                DB::connection('tenant')->table('vehicles')->delete();
                DB::connection('tenant')->table('services')->delete();
            } catch (\Exception $e) {
                // Ignore cleanup errors
            }
        }

        // End tenancy if active
        if (tenancy()->initialized) {
            tenancy()->end();
        }

        parent::tearDown();
    }
}
