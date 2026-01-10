<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Jobs\CreateDatabase;
use Stancl\Tenancy\Jobs\MigrateDatabase;

// Check if tenant already exists
$existingTenant = Tenant::find('test-shared');

if ($existingTenant) {
    echo "✓ Tenant 'test-shared' já existe\n";
    $tenant = $existingTenant;
} else {
    echo "→ Criando tenant 'test-shared'...\n";

    $plan = SubscriptionPlan::first();
    if (!$plan) {
        die("✗ Erro: Nenhum plano de assinatura encontrado. Execute: php artisan db:seed\n");
    }

    // Create tenant without triggering events
    $tenant = Tenant::withoutEvents(function () use ($plan) {
        return Tenant::create([
            'id' => 'test-shared',
            'slug' => 'test-shared',
            'name' => 'Test Tenant',
            'email' => 'test@tenant.com',
            'phone' => '(11) 99999-9999',
            'subscription_plan_id' => $plan->id,
            'subscription_status' => 'active',
            'is_active' => true,
        ]);
    });

    echo "✓ Tenant criado com sucesso\n";
}

// Check if database exists
$databaseName = 'tenanttest-shared';
$databases = DB::select("SHOW DATABASES LIKE '$databaseName'");

if (count($databases) > 0) {
    echo "✓ Banco de dados '$databaseName' já existe\n";
} else {
    echo "→ Criando banco de dados '$databaseName'...\n";

    // Create database manually via DatabaseManager
    $databaseManager = app(\Stancl\Tenancy\Database\DatabaseManager::class);
    $createDb = new CreateDatabase($tenant);
    $createDb->handle($databaseManager);

    echo "✓ Banco de dados criado com sucesso\n";

    echo "→ Executando migrações no banco '$databaseName'...\n";

    // Migrate database
    $migrator = app(\Illuminate\Database\Migrations\Migrator::class);
    $migrateDb = new MigrateDatabase($tenant);
    $migrateDb->handle($migrator);

    echo "✓ Migrações executadas com sucesso\n";
}

echo "\n✓ Setup completo! Banco de testes pronto para uso.\n";
