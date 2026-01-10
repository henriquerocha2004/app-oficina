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
     * Tear down the test environment.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        if (isset($this->tenant) && tenancy()->initialized) {
            try {
                DB::connection('tenant')->table('users')->delete();
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
