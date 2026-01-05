# Guia Rápido: Sistema de Usuários Multi-Tenant

## 🚀 Quick Start (5 minutos)

### 1. Executar Migrações

```bash
# Tenant migrations (roles, permissions, invitations)
php artisan migrate

# Central migration (impersonation logs)
php artisan migrate --database=central --path=database/migrations/central
```

### 2. Popular Roles e Permissões

```bash
# Será executado automaticamente ao criar novo tenant
# Mas você pode executar manualmente:
php artisan db:seed --class=RoleSeeder
```

### 3. Configurar Email (opcional, para produção)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@appoficina.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 4. Testar o Sistema

```bash
# Executar todos os testes
php artisan test

# Testes específicos
php artisan test --filter=InvitationServiceTest
php artisan test --filter=UsersControllerTest
```

---

## 📝 Checklist de Implementação

### Backend ✅

- [x] Migrations (6 arquivos)
- [x] Models (5 models + 1 trait)
- [x] DTOs (8 DTOs)
- [x] Requests (7 FormRequests)
- [x] Services (5 services)
- [x] Controllers (6 controllers)
- [x] Routes (4 arquivos de rotas)
- [x] Middleware (ShareImpersonationState)
- [x] Email (Mailable + template Blade)
- [x] Seeder (RoleSeeder completo)

### Frontend ✅

- [x] Types (user-management.ts)
- [x] Components (ImpersonationBanner, ImpersonateUserDialog)
- [x] Pages de Usuários (Index, Invite, ChangeRole)
- [x] Pages de Roles (Index, Create, ManagePermissions)
- [x] Pages de Convites (Index, Accept)
- [x] Pages Admin (ImpersonationLogs)
- [x] Layout (GuestLayout)
- [x] Integração com AppSidebarLayout

### Tests ✅

- [x] Unit Tests (4 arquivos)
  - InvitationServiceTest
  - UserServiceTest
  - UserModelTest (HasRoleAndPermissions trait)
  - ImpersonationServiceTest
- [x] Feature Tests (3 arquivos)
  - InvitationsControllerTest
  - UsersControllerTest
  - RolesControllerTest

### Documentation ✅

- [x] USER_MANAGEMENT.md (documentação técnica completa)
- [x] QUICK_START_USER_MANAGEMENT.md (este arquivo)
- [ ] API documentation (Swagger/OpenAPI) - *opcional*
- [ ] Diagramas adicionais - *opcional*

---

## 🎯 Casos de Uso Principais

### 1. Criar Novo Tenant

```php
// TenantService já faz tudo automaticamente:
$tenant = $tenantService->create([
    'name' => 'Oficina ABC',
    'domain' => 'oficina-abc',
]);

// Resultado:
// ✅ 5 roles criadas
// ✅ ~35 permissions criadas
// ✅ Owner user criado com is_owner=true
// ✅ Pronto para convidar mais usuários
```

### 2. Convidar Usuário

```php
// Frontend: pages/users/Invite.vue
// Backend: InvitationsController@store

$invitationService->invite(new InvitationInputDTO(
    email: 'mecanico@example.com',
    role_id: $mechanicRole->id,
    invited_by_user_id: auth()->id()
));

// ✅ Email enviado com token único
// ✅ Link válido por 7 dias
// ✅ Validação de limites do plano
```

### 3. Aceitar Convite

```php
// Frontend: pages/invitations/Accept.vue (pública, sem auth)
// Backend: InvitationsController@acceptStore

$invitationService->accept(new AcceptInvitationDTO(
    token: $request->token,
    name: 'João Silva',
    password: 'SenhaSegura123!'
));

// ✅ User criado com role definida
// ✅ Convite marcado como aceito
// ✅ Login automático
```

### 4. Customizar Permissions de uma Role

```php
// Frontend: pages/roles/ManagePermissions.vue
// Backend: RolesController@updatePermissions

$roleService->syncPermissions($role->id, [
    $viewClientsPermission->id,
    $createClientsPermission->id,
    $viewVehiclesPermission->id,
]);

// ✅ Permissões atualizadas
// ✅ Afeta todos os usuários com essa role imediatamente
```

### 5. Impersonate (Admin)

```php
// Frontend: components/admin/ImpersonateUserDialog.vue
// Backend: Admin/ImpersonationController@impersonate

$impersonationService->impersonate($tenant, $user);

// ✅ Admin logout
// ✅ ImpersonationLog criado no central DB
// ✅ Tenant inicializado
// ✅ User login no guard web
// ✅ Session data armazenada
// ✅ Redirect para tenant dashboard
```

---

## 🔧 Customização

### Adicionar Nova Permission

```php
// database/seeders/RoleSeeder.php

Permission::create([
    'id' => Str::ulid(),
    'name' => 'Export Reports',
    'slug' => 'reports.export',
    'description' => 'Exportar relatórios em PDF/Excel',
    'module' => 'reports',
]);
```

### Criar Nova Role

```php
// Via interface (pages/roles/Create.vue) ou programaticamente:

$role = Role::create([
    'id' => Str::ulid(),
    'name' => 'Coordenador',
    'slug' => 'coordenador',
    'description' => 'Supervisiona equipe de mecânicos',
    'is_system' => false, // Permite exclusão
]);

// Depois atribua permissões via interface ManagePermissions
```

### Alterar Tempo de Expiração de Convites

```php
// app/Services/InvitationService.php

private function generateToken(): array
{
    return [
        'token' => Str::random(64),
        'expires_at' => now()->addDays(14), // Altere de 7 para 14 dias
    ];
}
```

### Adicionar Permissão Check em Controllers

```php
// app/Http/Controllers/ClientsController.php

public function index()
{
    // Verificar se usuário tem permissão
    if (!auth()->user()->can('clients.view')) {
        abort(403, 'Você não tem permissão para visualizar clientes.');
    }
    
    // Ou use middleware (recomendado):
    // Route::get('/clients', [ClientsController::class, 'index'])
    //     ->middleware('can:clients.view');
    
    return Inertia::render('clients/Index');
}
```

---

## 🧪 Testando Localmente

### 1. Testar Fluxo de Convite

```bash
# 1. Iniciar servidor
php artisan serve

# 2. Acessar interface de usuários
# http://localhost:8000/users

# 3. Clicar em "Convidar Usuário"
# 4. Preencher email e role
# 5. Email ficará em storage/logs/laravel.log
# 6. Copiar URL do email e acessar
# 7. Preencher formulário de cadastro
```

### 2. Testar Impersonation

```bash
# 1. Logar como super admin
# http://localhost:8000/admin/login

# 2. Acessar tenant desejado
# http://localhost:8000/admin/tenants

# 3. Clicar em "Impersonate User"
# 4. Selecionar usuário
# 5. Verificar banner amarelo no topo
# 6. Operar como usuário
# 7. Clicar "Sair do Impersonate"
```

### 3. Verificar Logs de Impersonation

```bash
# Acessar no admin panel:
# http://localhost:8000/admin/impersonation-logs

# Ou via banco de dados:
mysql> SELECT * FROM impersonation_logs ORDER BY started_at DESC LIMIT 5;
```

---

## 🐛 Debug Common Issues

### Convite não envia email

```bash
# Verificar config de email
php artisan config:clear
php artisan config:cache

# Em desenvolvimento, emails ficam no log
tail -f storage/logs/laravel.log | grep -i "invitation"
```

### Erro "Role não encontrada"

```bash
# Re-executar seeder
php artisan db:seed --class=RoleSeeder --force

# Verificar se roles existem
php artisan tinker
>>> App\Models\Role::count(); // Deve retornar 5
```

### Impersonation não funciona

```bash
# Verificar se middleware está registrado
grep -r "ShareImpersonationState" bootstrap/app.php

# Verificar sessão
php artisan tinker
>>> session()->all();

# Limpar cache
php artisan cache:clear
php artisan view:clear
```

### Permissões não aparecem

```bash
# Verificar relacionamento
php artisan tinker
>>> $role = App\Models\Role::first();
>>> $role->permissions; // Deve retornar collection

# Re-seed se necessário
php artisan db:seed --class=RoleSeeder --force
```

---

## 📊 Monitoramento

### Queries Úteis

```sql
-- Usuários por role
SELECT r.name, COUNT(u.id) as total
FROM roles r
LEFT JOIN users u ON u.role_id = r.id
GROUP BY r.id, r.name
ORDER BY total DESC;

-- Convites pendentes
SELECT email, r.name as role, expires_at
FROM user_invitations ui
JOIN roles r ON r.id = ui.role_id
WHERE accepted_at IS NULL
  AND expires_at > NOW()
ORDER BY created_at DESC;

-- Últimas impersonations
SELECT admin_name, tenant_name, user_name, started_at, ended_at
FROM impersonation_logs
ORDER BY started_at DESC
LIMIT 10;

-- Permissões por role
SELECT r.name as role, COUNT(pr.permission_id) as permissions_count
FROM roles r
LEFT JOIN permission_role pr ON pr.role_id = r.id
GROUP BY r.id, r.name
ORDER BY permissions_count DESC;
```

---

## ⚡ Performance Tips

1. **Cache de Permissões**
   ```php
   // Adicionar cache em HasRoleAndPermissions trait
   public function getPermissionSlugs(): array
   {
       return Cache::remember(
           "user.{$this->id}.permissions",
           now()->addHour(),
           fn() => $this->role->permissions->pluck('slug')->toArray()
       );
   }
   ```

2. **Eager Loading**
   ```php
   // Em UsersController
   $users = User::with('role.permissions')->get();
   ```

3. **Index em Queries Frequentes**
   ```sql
   -- Já criados nas migrations:
   INDEX idx_token ON user_invitations(token)
   INDEX idx_email ON user_invitations(email)
   ```

---

## 🎓 Recursos Adicionais

- [Documentação Completa](./USER_MANAGEMENT.md)
- [Testes Unitários](/tests/Unit/Services/)
- [Testes de Integração](/tests/Feature/)
- [Componentes Vue](/resources/js/pages/users/)

---

**Última atualização:** Janeiro 2026  
**Tempo estimado de leitura:** 10 minutos
