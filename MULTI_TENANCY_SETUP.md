# 🚀 Guia de Configuração Multi-Tenancy

## 📋 O que foi implementado:

### ✅ Pacotes Instalados
- `stancl/tenancy` v3.9.1 - Sistema completo de multi-tenancy

### ✅ Estrutura de Banco de Dados

#### Banco Central (`app_oficina_central`)
Armazena informações compartilhadas:
- `tenants` - Oficinas cadastradas
- `subscription_plans` - Planos de assinatura  
- `domains` - Subdomínios dos tenants
- `cache`, `jobs`, `sessions` - Tabelas do Laravel

#### Bancos dos Tenants (um para cada oficina)
Cada tenant terá seu próprio banco MySQL isolado:
- `tenant{id}` - Ex: `tenantdemo`, `tenantoficina-joao`
- Contém: `users`, `clients`, `vehicles`, `services` (e futuramente `orders`)

### ✅ Models Criados
- `App\Models\Tenant` - Extende `Stancl\Tenancy\Database\Models\Tenant`
  - Métodos: `isTrial()`, `isActiveSubscription()`, `hasFeature()`, `withinLimit()`
- `App\Models\SubscriptionPlan`
  - 3 planos: Free, Basic, Premium
  - Limites configuráveis por plano

### ✅ Migrations
**Central** (`/database/migrations/`):
- `2019_09_15_000005_create_subscription_plans_table.php`
- `2019_09_15_000010_create_tenants_table.php`
- `2019_09_15_000020_create_domains_table.php`

**Tenant** (`/database/migrations/tenant/`):
- `0001_01_01_000000_create_users_table.php`
- `2025_09_28_150755_create_clients_table.php`
- `2025_10_27_144856_update_cars_to_vehicles_table.php`
- `2025_11_22_000000_create_services_table.php`

### ✅ Seeders
- `SubscriptionPlanSeeder` - Cria 3 planos
- `TenantSeeder` - Cria tenant "demo" para desenvolvimento

---

## 🔧 Próximos Passos (VOCÊ PRECISA FAZER):

### 1️⃣ Subir o Container MySQL

```bash
# No seu terminal LOCAL (fora do container), execute:
cd /caminho/para/app-oficina
docker compose up -d oficina-db

# Aguarde ~30 segundos para o MySQL iniciar
docker compose ps  # Verifique se está running
```

### 2️⃣ Atualizar o .env

```bash
# Ainda no terminal local, atualize o .env:
cp .env .env.backup  # Backup do .env atual
cat .env.multi-tenant > .env

# Ou edite manualmente adicionando:
DB_CONNECTION=central
DB_HOST=oficina-db
DB_PORT=3306
DB_DATABASE=app_oficina_central
DB_USERNAME=root
DB_PASSWORD=root
```

### 3️⃣ Criar o Banco Central no MySQL

```bash
# Conectar no container MySQL:
docker exec -it oficina-db mysql -uroot -proot

# Dentro do MySQL, execute:
CREATE DATABASE app_oficina_central CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SHOW DATABASES;  # Confirme que foi criado
EXIT;
```

### 4️⃣ Rodar Migrations e Seeds

```bash
# Volte para o container da aplicação:
docker exec -it oficina-app bash

# Ou se já está dentro:
cd /var/www

# Limpar cache de configuração:
php artisan config:clear
php artisan cache:clear

# Rodar migrations do banco CENTRAL:
php artisan migrate --database=central --path=database/migrations

# Rodar seeders:
php artisan db:seed --class=SubscriptionPlanSeeder
php artisan db:seed --class=TenantSeeder
```

O comando `TenantSeeder` vai:
- Criar o tenant "demo"
- Criar um banco MySQL chamado `tenantdemo`
- Rodar as migrations do tenant automaticamente
- Criar o domínio `demo.localhost`

### 5️⃣ Configurar /etc/hosts (no seu computador)

Para acessar os subdomínios localmente, adicione ao seu `/etc/hosts`:

**Linux/Mac:**
```bash
sudo nano /etc/hosts

# Adicione:
127.0.0.1 demo.localhost
127.0.0.1 app-oficina.local
```

**Windows:**
```
# Edite: C:\Windows\System32\drivers\etc\hosts

127.0.0.1 demo.localhost
127.0.0.1 app-oficina.local
```

### 6️⃣ Testar o Acesso

```bash
# Inicie o servidor Laravel:
php artisan serve --host=0.0.0.0 --port=8000

# Acesse no navegador:
http://demo.localhost:4500  # Tenant demo (via nginx)
# OU
http://demo.localhost:8000  # Tenant demo (via artisan serve)

http://app-oficina.local:4500  # Domínio central
```

---

## 🎯 Próximas Implementações Necessárias:

### ⏳ Pendente - Tarefa 6: Configurar Middleware
- Adicionar `InitializeTenancyBySubdomain` nas rotas
- Separar rotas tenant vs central

### ⏳ Pendente - Tarefa 8: Sistema de Registro
- Página pública para registrar novas oficinas
- Controller, Request, Service
- Email de boas-vindas

### ⏳ Pendente - Tarefa 9: Refatorar Testes
- Ajustar todos os testes para multi-tenancy
- Criar/destruir bancos de tenant nos testes

### ⏳ Pendente - Super Admin Panel (Futuro)
- Dashboard para gerenciar todos os tenants
- Métricas globais (MRR, churn, etc)
- Impersonate feature

---

## 🐛 Troubleshooting

### Erro: "SQLSTATE[HY000] [2002] Connection refused"
**Solução:** MySQL não está rodando. Execute `docker compose up -d oficina-db`

### Erro: "Access denied for user 'root'@'...' "
**Solução:** Senha incorreta. Verifique que está usando `DB_PASSWORD=root`

### Erro: "Base table or column not found"
**Solução:** Migrations não rodaram. Execute:
```bash
php artisan migrate:fresh --database=central --seed
```

### Subdomínio não funciona
**Solução:** 
1. Verifique `/etc/hosts`
2. Limpe cache do navegador
3. Tente `demo.localhost:8000` em vez de `:4500`

---

## 📚 Referências

- [Tenancy for Laravel Docs](https://tenancyforlaravel.com/docs/v3)
- [Stancl/Tenancy GitHub](https://github.com/archtechx/tenancy)

---

## ✅ Status Atual

- [x] Pacote instalado
- [x] Migrations criadas
- [x] Models criados
- [x] Seeders criados
- [x] Docker Compose configurado
- [ ] **MySQL rodando** ← VOCÊ PRECISA FAZER
- [ ] **Migrations executadas** ← VOCÊ PRECISA FAZER
- [ ] **Seeds executados** ← VOCÊ PRECISA FAZER
- [ ] Middleware configurado
- [ ] Registro self-service
- [ ] Testes refatorados

**Continue a partir do passo 1️⃣ acima!** 🚀
