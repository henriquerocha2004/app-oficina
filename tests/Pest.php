<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Tenant Test Helpers
|--------------------------------------------------------------------------
|
| These helpers make it easy to test multi-tenant functionality.
| Use initializeTenant() to create and initialize a test tenant.
|
*/

/**
 * Initialize a test tenant for the current test
 */
function testTenant(array $attributes = []): App\Models\Tenant
{
    return test()->initializeTenant($attributes);
}

/**
 * End the current tenancy context
 */
function endTestTenancy(): void
{
    tenancy()->end();
}

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

/*
|--------------------------------------------------------------------------
| Cleanup Tenant Test Databases
|--------------------------------------------------------------------------
|
| After all tests complete, cleanup any remaining tenant test databases.
| This prevents database pollution from test runs.
|
*/

// Capture database credentials while Laravel is available
// Note: APP_ENV will be 'testing' during actual test execution
$cleanupDbHost = env('DB_HOST', 'oficina-db');
$cleanupDbUser = env('DB_USERNAME', 'root');
$cleanupDbPass = env('DB_PASSWORD', 'root');

register_shutdown_function(function () use ($cleanupDbHost, $cleanupDbUser, $cleanupDbPass) {
    try {
        // Check if running in test context via phpunit.xml env override
        // Using $_ENV here because it's guaranteed to be available during shutdown
        $isTestEnv = isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'testing';

        if (!$isTestEnv) {
            return;
        }

        $pdo = new PDO("mysql:host={$cleanupDbHost}", $cleanupDbUser, $cleanupDbPass);

        // Cleanup tenant test databases
        $stmt = $pdo->query("SHOW DATABASES LIKE 'tenanttest%'");
        $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($databases as $db) {
            $pdo->exec("DROP DATABASE IF EXISTS `{$db}`");
        }

        // Optionally cleanup central test database
        // Uncomment the line below to delete it after each test run
        // WARNING: This makes subsequent test runs 5-10s slower
        // $pdo->exec("DROP DATABASE IF EXISTS `app_oficina_central_test`");
    } catch (\Throwable $e) {
        // Silently fail - cleanup is not critical
    }
});
