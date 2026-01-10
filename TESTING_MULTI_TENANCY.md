# Testing Multi-Tenancy

Este guia explica como escrever testes para a aplicação multi-tenant.

## ✅ Configuração Concluída

### Arquivos Criados/Modificados:
- ✅ `tests/Helpers/TenantTestHelper.php` - Trait com métodos helper para testes
- ✅ `tests/TestCase.php` - Base test case com setup/teardown de tenancy
- ✅ `tests/Pest.php` - Configuração do Pest com helpers
- ✅ `tests/Feature/TenantIsolationTest.php` - Testes de isolamento entre tenants
- ✅ `phpunit.xml` - Configuração atualizada para usar MySQL ao invés de SQLite
- ✅ Banco de teste `app_oficina_test` criado e migrations executadas

### Testes Adaptados:
- ✅ `tests/Feature/Clients/ClientsControllerTest.php`
- ✅ `tests/Feature/Vehicles/VehiclesControllerTest.php`
- ✅ `tests/Feature/Services/ServicesControllerTest.php`
- ✅ `tests/Feature/Auth/AuthenticationTest.php`

## Configuração Básica

### 1. TestCase Base

Todos os testes herdam de `Tests\TestCase`, que automaticamente:
- Inicia na conexão `central` 
- Finaliza o contexto de tenancy ao terminar o teste
- Garante isolamento entre testes

### 2. TenantTestHelper Trait

Use o trait `TenantTestHelper` em qualquer teste que precise de contexto de tenant:

```php
use Tests\Helpers\TenantTestHelper;

class MyTest extends TestCase
{
    use RefreshDatabase;
    use TenantTestHelper;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Inicializar tenant
        $this->initializeTenant();
        
        // IMPORTANTE: Desabilitar middlewares de tenancy
        // pois o tenant já foi inicializado manualmente
        $this->withoutMiddleware([
            \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class,
            \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
        ]);
    }
}
```

## Métodos Disponíveis

### `initializeTenant(array $attributes = []): Tenant`

Cria um tenant de teste completo com:
- Banco de dados isolado
- Migrations executadas
- Contexto de tenancy ativado

```php
protected function setUp(): void
{
    parent::setUp();
    
    // Criar tenant com atributos padrão
    $this->initializeTenant();
    
    // OU criar tenant customizado
    $this->initializeTenant([
        'id' => 'my-tenant',
        'name' => 'My Custom Tenant',
        'subscription_plan_id' => 2,
    ]);
}
```

### `actingAsTenant(Tenant $tenant): self`

Alterna para o contexto de um tenant específico:

```php
$tenant1 = $this->initializeTenant(['id' => 'tenant1']);
$this->endTenancy();

$tenant2 = $this->initializeTenant(['id' => 'tenant2']);

// Voltar para tenant1
$this->actingAsTenant($tenant1);
```

### `endTenancy(): self`

Finaliza o contexto de tenancy e retorna para o banco central:

```php
$this->initializeTenant();
// Código no contexto do tenant

$this->endTenancy();
// Código no banco central
```

## Exemplos Práticos

### Teste de CRUD em Tenant

```php
class ClientsControllerTest extends TestCase
{
    use RefreshDatabase;
    use TenantTestHelper;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Inicializar tenant
        $this->initializeTenant();
        
        // Desabilitar middlewares de tenancy
        $this->withoutMiddleware([
            \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class,
            \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
        ]);

        // Criar usuário no tenant
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function testCanCreateClient(): void
    {
        $response = $this->postJson('/clients', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'document_number' => '12345678901',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('clients', [
            'name' => 'John Doe',
        ]);
    }
}
```

### Teste de Isolamento de Dados

```php
public function testTenantDataIsIsolated(): void
{
    // Tenant 1
    $tenant1 = $this->initializeTenant(['id' => 'tenant1']);
    Client::create(['name' => 'Client 1', /* ... */]);
    $this->assertEquals(1, Client::count());
    
    // Tenant 2
    $this->endTenancy();
    $tenant2 = $this->initializeTenant(['id' => 'tenant2']);
    
    // Sem dados do tenant1
    $this->assertEquals(0, Client::count());
    
    Client::create(['name' => 'Client 2', /* ... */]);
    $this->assertEquals(1, Client::count());
    
    // Voltar para tenant1
    $this->actingAsTenant($tenant1);
    $this->assertEquals(1, Client::count());
    $this->assertEquals('Client 1', Client::first()->name);
}
```

### Teste de Dados Centrais vs Tenant

```php
public function testCentralDataAccessibleFromAllTenants(): void
{
    // No banco central
    $this->endTenancy();
    $planCount = SubscriptionPlan::count();
    
    // Tenant 1 pode ver os planos
    $tenant1 = $this->initializeTenant();
    $this->endTenancy();
    $this->assertEquals($planCount, SubscriptionPlan::on('central')->count());
    
    // Tenant 2 também vê os mesmos planos
    $tenant2 = $this->initializeTenant();
    $this->endTenancy();
    $this->assertEquals($planCount, SubscriptionPlan::on('central')->count());
}
```

## Pest PHP

Para testes usando Pest, use a função helper `tenant()`:

```php
use function Tests\tenant;

test('can create client in tenant', function () {
    tenant(); // Inicializa tenant automaticamente
    
    $user = User::factory()->create();
    $this->actingAs($user);
    
    $response = $this->postJson('/clients', [
        'name' => 'Test Client',
        'email' => 'test@example.com',
        'document_number' => '12345678901',
    ]);
    
    $response->assertStatus(201);
});
```

## Boas Práticas

1. **Sempre use `initializeTenant()` no setUp()** de testes que precisam de tenant
2. **Desabilite middlewares de tenancy** com `withoutMiddleware()` quando inicializar tenant manualmente
3. **Use `endTenancy()`** antes de criar novo tenant no mesmo teste
4. **RefreshDatabase** funciona normalmente, limpando tanto central quanto tenant
5. **Factories** funcionam no contexto do tenant atual
6. **Migrations** são executadas automaticamente ao criar tenant
7. **Limpeza automática**: Tenants de teste são deletados após cada teste

## Middlewares de Tenancy

Ao inicializar um tenant manualmente nos testes, é **ESSENCIAL** desabilitar os middlewares:

```php
$this->withoutMiddleware([
    \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class,
    \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
]);
```

Caso contrário, você receberá erro 404 nas rotas de tenant.

## Estrutura de Banco de Dados

- **Central**: `app_oficina_central` (tenants, subscription_plans, domains)
- **Tenant**: `tenant{id}` (users, clients, vehicles, services)

## Comandos Úteis

```bash
# Rodar todos os testes
php artisan test

# Rodar testes específicos
php artisan test --filter ClientsControllerTest

# Rodar com coverage
php artisan test --coverage

# Rodar testes de um diretório
php artisan test tests/Feature/Clients
```

## Troubleshooting

### "Table not found" em testes
- Verifique se `initializeTenant()` foi chamado no setUp()
- Certifique-se que `RefreshDatabase` está sendo usado

### 404 em rotas de tenant
- **SOLUÇÃO**: Adicione `withoutMiddleware()` no setUp() para desabilitar InitializeTenancyByDomain e PreventAccessFromCentralDomains
- Esses middlewares tentam inicializar o tenant pelo domínio, mas nos testes inicializamos manualmente

### Dados persistindo entre testes
- Use `RefreshDatabase` trait
- Verifique se `tearDown()` está sendo chamado corretamente

### Erro de conexão com banco
- Confirme que a configuração de teste está correta em `phpunit.xml`
- Verifique se o MySQL está rodando
- Verifique se o banco `app_oficina_test` foi criado

### Testes muito lentos
- Multi-tenancy com MySQL é mais lento que SQLite in-memory
- Considere usar grupos de testes: `@group tenant` e `@group unit`
- Execute testes específicos: `php artisan test --filter=ClientsControllerTest`

## Performance dos Testes

Cada teste que usa `initializeTenant()`:
1. Cria SubscriptionPlan (se não existir)
2. Cria registro de Tenant na tabela central
3. Cria Domain para o tenant
4. Cria banco de dados MySQL físico (`test_tenant_{id}`)
5. Executa todas as migrations no banco do tenant
6. Ao finalizar, deleta o banco de dados

**Tempo médio por teste**: 3-5 segundos

**Recomendações**:
- Agrupe múltiplos testes relacionados em uma mesma classe
- Use `@depends` para compartilhar setup entre testes relacionados
- Considere testes unitários (sem banco) para lógica de negócio
