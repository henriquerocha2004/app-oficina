# 🚗 App Oficina - Sistema Multi-Tenant para Gestão de Oficinas Mecânicas

Sistema SaaS completo para gestão de oficinas mecânicas com arquitetura multi-tenant, desenvolvido com Laravel 12 e Vue.js 3.

## 📋 Índice

- [Visão Geral](#visão-geral)
- [Arquitetura](#arquitetura)
- [Stack Tecnológica](#stack-tecnológica)
- [Funcionalidades](#funcionalidades)
- [Documentação](#documentação)
- [Estrutura do Projeto](#estrutura-do-projeto)
- [Instalação](#instalação)
- [Multi-Tenancy](#multi-tenancy)
- [Testes](#testes)
- [Scripts Disponíveis](#scripts-disponíveis)

## 🎯 Visão Geral

O **App Oficina** é uma aplicação SaaS (Software as a Service) que permite que múltiplas oficinas mecânicas utilizem o mesmo sistema de forma isolada e segura através de uma arquitetura multi-tenant. Cada oficina possui:

- **Banco de dados isolado** - Dados completamente segregados
- **Subdomínio próprio** - `{oficina}.seudominio.com`
- **Planos de assinatura** - Free, Basic e Premium
- **Usuários independentes** - Autenticação por tenant
- **Gestão completa** - Clientes, veículos e serviços

### Características Principais

- ✅ **Multi-Tenancy por Subdomínio** - Cada oficina em seu próprio subdomínio
- ✅ **Isolamento Total de Dados** - Bancos MySQL separados por tenant
- ✅ **Painel Administrativo** - Gerenciamento centralizado de tenants e planos
- ✅ **Sistema de Assinaturas** - Planos configuráveis com limites e features
- ✅ **SSR Ready** - Suporte a Server-Side Rendering com Inertia.js
- ✅ **Docker** - Ambiente de desenvolvimento containerizado
- ✅ **Testes Automatizados** - Pest PHP para testes Feature e Unit

## 🏗️ Arquitetura

### Arquitetura Multi-Tenant

```
┌─────────────────────────────────────────────────────────┐
│                   DOMÍNIO CENTRAL                       │
│              (app-oficina.local)                        │
│  - Landing Page                                          │
│  - Registro de Tenants                                   │
│  - Painel Administrativo                                 │
│  - Gestão de Planos                                      │
└─────────────────────────────────────────────────────────┘
                            │
                            ├─── Banco Central (MySQL)
                            │    - tenants
                            │    - domains
                            │    - subscription_plans
                            │    - admin_users
                            │
┌───────────────────────────┴─────────────────────────────┐
│                    TENANTS                               │
├──────────────────────────────────────────────────────────┤
│  oficina1.app.com  │  oficina2.app.com  │  demo.app.com │
│  ┌──────────────┐  │  ┌──────────────┐  │  ┌──────────┐│
│  │ Banco Tenant │  │  │ Banco Tenant │  │  │  Banco   ││
│  │   - users    │  │  │   - users    │  │  │  Tenant  ││
│  │   - clients  │  │  │   - clients  │  │  │  - ...   ││
│  │   - vehicles │  │  │   - vehicles │  │  │          ││
│  │   - services │  │  │   - services │  │  │          ││
│  └──────────────┘  │  └──────────────┘  │  └──────────┘│
└──────────────────────────────────────────────────────────┘
```

### Camadas da Aplicação

```
┌─────────────────────────────────────────────────────┐
│                   FRONTEND (Vue 3)                  │
│  - Inertia.js (SSR Ready)                           │
│  - TypeScript + Vite                                │
│  - Tailwind CSS v4 + Reka UI                        │
│  - Zod + Vee-Validate (Validação)                   │
│  - TanStack Table (Data Tables)                     │
└─────────────────────────────────────────────────────┘
                        ↓ HTTP/JSON
┌─────────────────────────────────────────────────────┐
│              CONTROLLERS (Laravel)                  │
│  - ClientController                                 │
│  - VehiclesController                               │
│  - ServicesController                               │
│  - Admin/AdminTenantsController                     │
└─────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│          REQUESTS (Validação Backend)               │
│  - ClientRequest                                    │
│  - VehicleRequest                                   │
│  - ServiceRequest                                   │
└─────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│            DTOs (Data Transfer Objects)             │
│  - ClientInputDTO / ClientOutputDTO                 │
│  - SearchDTO                                        │
│  - CreateClientResultDTO                            │
└─────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│              SERVICES (Lógica de Negócio)           │
│  - ClientService                                    │
│  - VehicleService                                   │
│  - ServiceService                                   │
└─────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│                MODELS (Eloquent ORM)                │
│  - Client (HasUlids, SoftDeletes)                   │
│  - Vehicle                                          │
│  - Service                                          │
│  - Tenant (Multi-Tenancy)                           │
│  - SubscriptionPlan                                 │
└─────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│                   DATABASE (MySQL)                  │
│  - Banco Central (tenants, plans, domains)          │
│  - Bancos de Tenants (users, clients, vehicles...)  │
└─────────────────────────────────────────────────────┘
```

## 🛠️ Stack Tecnológica

### Backend

| Tecnologia | Versão | Descrição |
|------------|--------|-----------|
| **PHP** | 8.2+ | Linguagem base |
| **Laravel** | 12.0 | Framework PHP |
| **MySQL** | 8.0 | Banco de dados |
| **Tenancy for Laravel** | 3.9 | Multi-tenancy (stancl/tenancy) |
| **Inertia.js** | 2.0 | SSR Stack |
| **Pest PHP** | 4.0 | Framework de testes |

### Frontend

| Tecnologia | Versão | Descrição |
|------------|--------|-----------|
| **Vue.js** | 3.5 | Framework JavaScript |
| **TypeScript** | 5.2 | Superset JavaScript |
| **Vite** | 7.0 | Build tool |
| **Tailwind CSS** | 4.1 | Framework CSS |
| **Reka UI** | 2.6 | Componentes UI (Radix para Vue) |
| **TanStack Table** | 8.21 | Tabelas de dados |
| **Zod** | 3.25 | Schema validation |
| **Vee-Validate** | 4.15 | Form validation |

### DevOps & Ferramentas

- **Docker** - Containerização (PHP, Nginx, MySQL, Mailpit)
- **Dev Containers** - VS Code Remote Development
- **Laravel Pint** - Code style (PSR-12)
- **PHP CodeSniffer** - Linting PHP
- **ESLint** - Linting JavaScript/TypeScript
- **Prettier** - Code formatting
- **Vitest** - Testes frontend
- **Xdebug** - Debugging

## 📚 Documentação

### Documentação Técnica Completa

Acesse a **[documentação completa](./docs/README.md)** para informações detalhadas sobre:

- 📦 **[Sistema de Gestão de Estoque](./docs/inventory-system.md)**
  - Cadastro de produtos com categorias e unidades
  - Controle de estoque em tempo real
  - Histórico completo de movimentações
  - Gestão de fornecedores
  - Alertas de estoque baixo
  - API endpoints e testes

### Documentação de Setup

- 🏗️ **[Multi-Tenancy Setup](./MULTI_TENANCY_SETUP.md)** - Configuração multi-tenant
- 🧪 **[Testing Multi-Tenancy](./TESTING_MULTI_TENANCY.md)** - Testes em ambiente multi-tenant
- 🚀 **[Quick Start](./QUICK_START.md)** - Guia rápido de início
- 🐳 **[Dev Container](./DEV_CONTAINER_README.md)** - Ambiente containerizado

## ✨ Funcionalidades

### Para Administradores do Sistema

- ✅ Dashboard administrativo
- ✅ Gestão de tenants (oficinas)
  - Criar, editar, excluir tenants
  - Ativar/desativar tenants
  - Gerenciar assinaturas
- ✅ Gestão de planos de assinatura
  - Configurar limites por recurso
  - Definir features disponíveis
  - Preços e trial

### Para Oficinas (Tenants)

- ✅ **Gestão de Clientes**
  - CRUD completo
  - Busca e filtros
  - Soft deletes
  - CPF/CNPJ validado

- ✅ **Gestão de Veículos**
  - Vincular a clientes
  - Histórico de serviços
  - Informações completas (marca, modelo, placa, ano)

- ✅ **Gestão de Produtos e Estoque** 📦
  - Cadastro de produtos com categorias
  - Controle de estoque em tempo real
  - Movimentações de entrada e saída
  - Alertas de estoque baixo
  - Histórico completo de movimentações
  - Rastreabilidade de transações
  - **[Ver documentação completa →](./docs/inventory-system.md)**

- ✅ **Gestão de Fornecedores** 🏢
  - Cadastro completo com dados de contato
  - Controle de fornecedores ativos/inativos
  - Busca e filtros avançados
  - **[Ver documentação completa →](./docs/inventory-system.md#fornecedores)**

- ✅ **Gestão de Serviços**
  - Catálogo de serviços
  - Preços e descrições
  - Associação com veículos

- ✅ **Autenticação Multi-Tenant**
  - Login/Registro por tenant
  - Verificação de email
  - Reset de senha
  - Middleware de tenant

## 📁 Estrutura do Projeto

```
app-oficina/
├── app/
│   ├── DTOs/                   # Data Transfer Objects
│   │   ├── ClientInputDTO.php
│   │   ├── ClientOutputDTO.php
│   │   └── SearchDTO.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # Controllers do painel admin
│   │   │   ├── Auth/           # Autenticação
│   │   │   ├── Settings/       # Configurações
│   │   │   ├── ClientController.php
│   │   │   ├── VehiclesController.php
│   │   │   └── ServicesController.php
│   │   ├── Middleware/
│   │   ├── Requests/           # Form Requests
│   │   └── Resources/          # API Resources
│   ├── Models/
│   │   ├── Tenant.php          # Model principal multi-tenancy
│   │   ├── SubscriptionPlan.php
│   │   ├── AdminUser.php
│   │   ├── Client.php
│   │   ├── Vehicle.php
│   │   └── Service.php
│   ├── Services/               # Lógica de negócio
│   │   ├── ClientService.php
│   │   ├── VehicleService.php
│   │   └── ServiceService.php
│   └── Providers/
│
├── database/
│   ├── migrations/             # Migrations do banco central
│   │   ├── *_create_tenants_table.php
│   │   ├── *_create_subscription_plans_table.php
│   │   └── *_create_domains_table.php
│   ├── migrations/tenant/      # Migrations dos tenants
│   │   ├── *_create_users_table.php
│   │   ├── *_create_clients_table.php
│   │   ├── *_create_vehicles_table.php
│   │   └── *_create_services_table.php
│   ├── factories/
│   └── seeders/
│       ├── SubscriptionPlanSeeder.php
│       └── TenantSeeder.php
│
├── resources/
│   ├── js/
│   │   ├── components/         # Componentes reutilizáveis
│   │   │   └── ui/             # Reka UI components
│   │   ├── composables/        # Vue composables
│   │   ├── layouts/            # Layouts do app
│   │   ├── pages/              # Páginas Inertia
│   │   │   ├── admin/          # Páginas admin
│   │   │   ├── clients/        # CRUD clientes
│   │   │   ├── vehicles/       # CRUD veículos
│   │   │   ├── services/       # CRUD serviços
│   │   │   └── auth/           # Autenticação
│   │   ├── types/              # TypeScript types
│   │   └── app.ts              # Entry point
│   ├── css/
│   └── views/
│
├── routes/
│   ├── web.php                 # Rotas centrais (admin, landing)
│   ├── tenant.php              # Rotas dos tenants
│   ├── auth.php                # Rotas de autenticação
│   ├── clients.php             # Rotas de clientes
│   ├── vehicles.php            # Rotas de veículos
│   └── services.php            # Rotas de serviços
│
├── tests/
│   ├── Feature/
│   │   ├── Auth/
│   │   ├── Clients/
│   │   └── Vehicles/
│   └── Unit/
│
├── config/
│   └── tenancy.php             # Configuração multi-tenancy
│
├── docker-compose.yml          # Orquestração containers
├── Dockerfile                  # Imagem PHP customizada
├── nginx/                      # Configuração Nginx
└── .devcontainer/              # Dev Container config
```

## 🚀 Instalação

### Requisitos

- Docker & Docker Compose
- VS Code (recomendado) + Extension Dev Containers

### Método 1: Dev Container (Recomendado)

1. **Clone o repositório:**
```bash
git clone <repo-url>
cd app-oficina
```

2. **Abra no VS Code:**
```bash
code .
```

3. **Reabra no Container:**
- `Ctrl+Shift+P` → "Dev Containers: Reopen in Container"
- Aguarde 2-3 minutos na primeira execução

4. **Configure o ambiente:**
```bash
# Dentro do container
cp .env.multi-tenant .env
php artisan key:generate
```

5. **Inicie o MySQL:**
```bash
# No terminal do HOST (fora do container)
docker compose up -d oficina-db
```

6. **Configure o banco de dados:**
```bash
# Conecte no MySQL
docker exec -it oficina-db mysql -uroot -proot

# Execute:
CREATE DATABASE app_oficina_central CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

7. **Execute migrations e seeds:**
```bash
# Dentro do container
php artisan config:clear
php artisan migrate --database=central
php artisan db:seed --class=SubscriptionPlanSeeder
php artisan db:seed --class=TenantSeeder
```

8. **Configure o /etc/hosts:**
```bash
# Linux/Mac
sudo nano /etc/hosts

# Adicione:
127.0.0.1 demo.localhost
127.0.0.1 app-oficina.local
```

9. **Inicie a aplicação:**
```bash
# Instale dependências frontend
npm install

# Inicie servidor + assets
composer dev
# OU separadamente:
php artisan serve --host=0.0.0.0
npm run dev
```

10. **Acesse:**
- Tenant Demo: http://demo.localhost:4500
- Admin Central: http://app-oficina.local:4500/admin/login
- Mailpit: http://localhost:4503

### Método 2: Docker Compose Tradicional

```bash
docker compose up -d
docker exec -it oficina-app bash
# Siga os passos 4-10 acima
```

## 🏢 Multi-Tenancy

### Como Funciona

O sistema utiliza o pacote **stancl/tenancy** com identificação por **subdomínio**:

1. **Banco Central** (`app_oficina_central`):
   - Armazena: tenants, domains, subscription_plans, admin_users
   - Gerenciado pelo admin do sistema

2. **Banco por Tenant** (`tenant{id}`):
   - Um banco MySQL separado para cada oficina
   - Armazena: users, clients, vehicles, services
   - Isolamento total de dados

### Middleware

```php
// routes/tenant.php
Route::middleware([
    'web',
    InitializeTenancyByDomain::class,        // Identifica tenant pelo subdomínio
    PreventAccessFromCentralDomains::class,  // Bloqueia acesso de domínios centrais
])->group(function () {
    // Rotas do tenant...
});
```

### Planos de Assinatura

| Plano | Clientes | Veículos | Serviços | Preço |
|-------|----------|----------|----------|-------|
| **Free** | 50 | 100 | 20 | R$ 0,00 |
| **Basic** | 200 | 500 | 50 | R$ 49,90 |
| **Premium** | Ilimitado | Ilimitado | Ilimitado | R$ 99,90 |

### Criar um Novo Tenant

```bash
php artisan tinker

$tenant = \App\Models\Tenant::create([
    'id' => 'minha-oficina',
    'name' => 'Minha Oficina Ltda',
    'email' => 'contato@minhaoficina.com',
    'phone' => '11987654321',
    'subscription_plan_id' => 1, // Free
    'subscription_status' => 'trial',
    'trial_ends_at' => now()->addDays(14),
]);

$tenant->domains()->create(['domain' => 'minha-oficina.localhost']);
```

## 🧪 Testes

### Executar Testes

```bash
# Todos os testes
composer test

# Com coverage
php artisan test --coverage

# Filtrar por feature
php artisan test --filter ClientsController
```

### Estrutura de Testes

- **Feature**: Testa fluxos completos (controllers, requests, services)
- **Unit**: Testa classes isoladas (services, DTOs)

```php
// Exemplo: tests/Feature/Clients/ClientsControllerTest.php
it('can create a client', function () {
    $response = $this->postJson('/clients', [
        'name' => 'João Silva',
        'email' => 'joao@example.com',
        'document_number' => '12345678900',
        'phone' => '11987654321',
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('clients', ['email' => 'joao@example.com']);
});
```

## 📜 Scripts Disponíveis

### Backend (Composer)

```bash
composer dev          # Inicia servidor + queue + logs + vite
composer dev:ssr      # Modo SSR
composer test         # Executa testes
composer phpcs        # Verifica code style
composer phpcbf       # Corrige code style automaticamente
```

### Frontend (NPM)

```bash
npm run dev           # Vite dev server
npm run build         # Build production
npm run build:ssr     # Build com SSR
npm run lint          # ESLint
npm run format        # Prettier
npm test              # Vitest
npm run test:ui       # Vitest UI
npm run test:cov      # Coverage
```

## 🔒 Segurança

- ✅ **Validação em múltiplas camadas** (Frontend + Backend)
- ✅ **CSRF Protection** (Laravel)
- ✅ **SQL Injection Prevention** (Eloquent ORM)
- ✅ **XSS Protection** (Vue.js escaping)
- ✅ **Soft Deletes** (Dados não são perdidos)
- ✅ **ULIDs** em vez de IDs incrementais (segurança por obscuridade)

## 📈 Próximos Passos

- [ ] Sistema de registro self-service de tenants
- [ ] Integração de pagamentos (Stripe/Mercado Pago)
- [ ] Dashboard com métricas e gráficos
- [ ] Sistema de ordens de serviço (OS)
- [ ] Calendário/Agendamento
- [ ] Notificações por email/SMS
- [ ] App mobile (React Native)
- [ ] API REST pública
- [ ] Webhooks

## 📚 Documentação Adicional

- [MULTI_TENANCY_SETUP.md](./MULTI_TENANCY_SETUP.md) - Guia completo de multi-tenancy
- [QUICK_START.md](./QUICK_START.md) - Quick start dev container
- [TESTING_MULTI_TENANCY.md](./TESTING_MULTI_TENANCY.md) - Testes multi-tenant
- [DEV_CONTAINER_README.md](./DEV_CONTAINER_README.md) - Dev Container detalhado

## 📄 Licença

MIT

---

**Desenvolvido com ❤️ para oficinas mecânicas**
