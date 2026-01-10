<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧹 Limpando bancos de dados de teste...\n\n";

// Conectar ao banco central
$pdo = DB::connection('central')->getPdo();

// Buscar todos os bancos que começam com tenant
$stmt = $pdo->query("SHOW DATABASES LIKE 'tenant%'");
$databases = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($databases)) {
    echo "✅ Nenhum banco de dados tenant encontrado.\n";
} else {
    echo "📁 Encontrados " . count($databases) . " bancos de tenant:\n";

    foreach ($databases as $database) {
        try {
            // Não dropar o banco do tenant demo e do tenant compartilhado de testes
            if (in_array($database, ['tenantdemo', 'tenanttest-shared'])) {
                echo "  ⊘ Pulando: {$database} (preservado)\n";
                continue;
            }

            $pdo->exec("DROP DATABASE `{$database}`");
            echo "  ✓ Removido: {$database}\n";
        } catch (Exception $e) {
            echo "  ✗ Erro ao remover {$database}: " . $e->getMessage() . "\n";
        }
    }
}

// Diretório onde ficam os bancos SQLite dos tenants (se existir)
$tenancyPath = __DIR__ . '/storage/app/tenancy';

if (!is_dir($tenancyPath)) {
    echo "\n⚠️  Diretório tenancy não encontrado (normal para MySQL): {$tenancyPath}\n";
} else {
    // Encontrar todos os arquivos .sqlite
    $sqliteFiles = glob($tenancyPath . '/*.sqlite');

    if (empty($sqliteFiles)) {
        echo "\n✅ Nenhum arquivo SQLite encontrado para remover.\n";
    } else {
        echo "\n📁 Encontrados " . count($sqliteFiles) . " arquivos SQLite:\n";

        foreach ($sqliteFiles as $file) {
            $filename = basename($file);

            // Remover o arquivo
            if (unlink($file)) {
                echo "  ✓ Removido: {$filename}\n";
            } else {
                echo "  ✗ Erro ao remover: {$filename}\n";
            }
        }
    }
}

echo "\n✅ Limpeza concluída!\n";
