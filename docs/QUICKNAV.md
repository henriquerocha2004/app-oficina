# 🚀 Navegação Rápida - Documentação

## � Sistema de Gerenciamento de Usuários

### Por Perfil de Usuário

#### 👨‍💼 Owner/Proprietário do Tenant
- **Como convidar novos usuários?** → [Guia de Convites](./QUICK_START_USER_MANAGEMENT.md#1-convidar-usuário)
- **Como gerenciar roles?** → [Gerenciar Roles](./USER_MANAGEMENT.md#como-gerenciar-roles)
- **Quais são as roles padrão?** → [Roles Predefinidas](./USER_MANAGEMENT.md#2-roles-predefinidas)
- **Como customizar permissões?** → [Customizar Permissions](./USER_MANAGEMENT.md#customizar-permissions-de-uma-role)

#### 🛠️ Super Admin
- **Como usar impersonation?** → [Guia de Impersonation](./USER_MANAGEMENT.md#como-usar-impersonation-admin)
- **Como ver logs de impersonation?** → [Audit Logs](./USER_MANAGEMENT.md#impersonation-admin-routes)
- **Estrutura de segurança?** → [Segurança](./USER_MANAGEMENT.md#segurança)

#### 👨‍💻 Desenvolvedor
- **Arquitetura do sistema?** → [Arquitetura](./USER_MANAGEMENT.md#arquitetura-do-sistema)
- **Fluxos principais?** → [Fluxos](./USER_MANAGEMENT.md#fluxos-principais)
- **API Reference?** → [API Reference](./USER_MANAGEMENT.md#api-reference)
- **Como testar?** → [Testes](./QUICK_START_USER_MANAGEMENT.md#-testando-localmente)
- **DTOs e Services?** → [Componentes](./USER_MANAGEMENT.md#componentes-do-sistema)

#### 🧪 QA/Tester
- **Testes disponíveis?** → [Tests Backend](./QUICK_START_USER_MANAGEMENT.md#tests-)
- **Como testar fluxo de convite?** → [Testar Convite](./QUICK_START_USER_MANAGEMENT.md#1-testar-fluxo-de-convite)
- **Como testar impersonation?** → [Testar Impersonation](./QUICK_START_USER_MANAGEMENT.md#2-testar-impersonation)

### Por Funcionalidade

#### 👥 Usuários
- ✅ [Sistema de Convites por Email](./USER_MANAGEMENT.md#1-fluxo-de-convite-de-usuário)
- ✅ [CRUD de Usuários](./USER_MANAGEMENT.md#users)
- ✅ [Alteração de Roles](./USER_MANAGEMENT.md#como-gerenciar-roles)
- ✅ [Proteção do Owner](./USER_MANAGEMENT.md#1-proteção-do-owner)
- ✅ [Validação de Limites do Plano](./USER_MANAGEMENT.md#validação-de-plano)

#### 🔐 Roles e Permissões
- ✅ [5 Roles Predefinidas](./USER_MANAGEMENT.md#2-roles-predefinidas)
- ✅ [Roles Customizáveis](./USER_MANAGEMENT.md#criar-nova-role)
- ✅ [~35 Permissões por Módulo](./USER_MANAGEMENT.md#3-módulos-de-permissões)
- ✅ [Gerenciamento Visual](./USER_MANAGEMENT.md#pages)
- ✅ [Sincronização de Permissões](./USER_MANAGEMENT.md#roles)

#### 📧 Convites
- ✅ [Criar e Enviar Convites](./QUICK_START_USER_MANAGEMENT.md#2-convidar-usuário)
- ✅ [Reenviar Convites](./USER_MANAGEMENT.md#invitations)
- ✅ [Cancelar Convites](./USER_MANAGEMENT.md#invitations)
- ✅ [Aceitar Convite (Público)](./QUICK_START_USER_MANAGEMENT.md#3-aceitar-convite)
- ✅ [Validação de Expiração](./USER_MANAGEMENT.md#2-validação-de-convites)

#### 🎭 Impersonation (Admin)
- ✅ [Sessões de Impersonation](./USER_MANAGEMENT.md#2-fluxo-de-impersonation)
- ✅ [Audit Logs Centralizados](./USER_MANAGEMENT.md#central-database)
- ✅ [Banner de Impersonation](./USER_MANAGEMENT.md#components)
- ✅ [Segurança e Rastreamento](./USER_MANAGEMENT.md#3-impersonation-security)

### Por Tecnologia

#### Backend (Laravel/PHP)
```bash
# Models:
app/Models/User.php                    # Enhanced com HasRoleAndPermissions trait
app/Models/Role.php
app/Models/Permission.php
app/Models/UserInvitation.php
app/Models/ImpersonationLog.php

# Services:
app/Services/UserService.php
app/Services/InvitationService.php
app/Services/RoleService.php
app/Services/Admin/ImpersonationService.php

# Controllers:
app/Http/Controllers/UsersController.php
app/Http/Controllers/InvitationsController.php
app/Http/Controllers/RolesController.php
app/Http/Controllers/PermissionsController.php
app/Http/Controllers/Admin/ImpersonationController.php

# DTOs:
app/DTOs/UserInputDTO.php
app/DTOs/InvitationInputDTO.php
app/DTOs/RoleInputDTO.php
# ... (8 DTOs no total)

# Middleware:
app/Http/Middleware/ShareImpersonationState.php
```

#### Frontend (Vue 3 + TypeScript)
```bash
# Pages:
resources/js/pages/users/Index.vue
resources/js/pages/users/Invite.vue
resources/js/pages/users/ChangeRole.vue
resources/js/pages/roles/Index.vue
resources/js/pages/roles/Create.vue
resources/js/pages/roles/ManagePermissions.vue
resources/js/pages/invitations/Index.vue
resources/js/pages/invitations/Accept.vue
resources/js/pages/admin/impersonation-logs/Index.vue

# Components:
resources/js/components/ImpersonationBanner.vue
resources/js/components/admin/ImpersonateUserDialog.vue

# Types:
resources/js/types/user-management.ts
```

#### Testes
```bash
# Unit Tests:
tests/Unit/Services/InvitationServiceTest.php
tests/Unit/Services/UserServiceTest.php
tests/Unit/Services/ImpersonationServiceTest.php
tests/Unit/Models/UserTest.php

# Feature Tests:
tests/Feature/InvitationsControllerTest.php
tests/Feature/UsersControllerTest.php
tests/Feature/RolesControllerTest.php
```

#### Database
```bash
# Tenant Migrations:
database/migrations/2026_01_04_184435_create_roles_table.php
database/migrations/2026_01_04_184456_create_permissions_table.php
database/migrations/2026_01_04_184459_create_permission_role_table.php
database/migrations/2026_01_04_184501_add_role_fields_to_users_table.php
database/migrations/2026_01_04_184824_create_user_invitations_table.php

# Central Migration:
database/migrations/central/2026_01_04_190012_create_impersonation_logs_table.php

# Seeders:
database/seeders/RoleSeeder.php         # 5 roles + ~35 permissions
```

### Recursos Visuais

- 🔄 [Fluxo de Convite](./USER_MANAGEMENT.md#1-fluxo-de-convite-de-usuário)
- 🎭 [Fluxo de Impersonation](./USER_MANAGEMENT.md#2-fluxo-de-impersonation)
- 🏗️ [Fluxo de Criação de Tenant](./USER_MANAGEMENT.md#3-fluxo-de-criação-de-tenant)
- 💾 [Database Schema](./USER_MANAGEMENT.md#1-database-schema)
- 🔐 [Arquitetura de Segurança](./USER_MANAGEMENT.md#segurança)

---

## �📦 Sistema de Estoque

### Por Perfil de Usuário

#### 👨‍💼 Gerente/Administrador da Oficina
- **Preciso configurar produtos?** → [Cadastro de Produtos](./inventory-system.md#produtos)
- **Como controlar o estoque?** → [Movimentações de Estoque](./inventory-system.md#movimentações-de-estoque)
- **Preciso cadastrar fornecedores?** → [Gestão de Fornecedores](./inventory-system.md#fornecedores)
- **Como vejo produtos em falta?** → [Produtos com Estoque Baixo](./inventory-system.md#4-produtos-com-estoque-baixo)

#### 👨‍💻 Desenvolvedor
- **Estrutura de dados?** → [Diagramas - Modelo de Dados](./diagrams.md#modelo-de-dados)
- **Fluxo de criação de produto?** → [Diagrama de Sequência](./diagrams.md#1-fluxo-de-criação-de-produto)
- **Como funciona o ajuste de estoque?** → [Fluxo de Ajuste](./diagrams.md#2-fluxo-de-ajuste-de-estoque)
- **API endpoints disponíveis?** → [Referência API](./inventory-system.md#api-endpoints)
- **Executar testes?** → [Guia de Testes](./inventory-system.md#testes)
- **Arquitetura do sistema?** → [Arquitetura de Camadas](./diagrams.md#arquitetura-de-camadas)

#### 🧪 QA/Tester
- **Cobertura de testes?** → [Pirâmide de Testes](./diagrams.md#cobertura-de-testes)
- **Como testar produtos?** → [ProductsControllerTest](./inventory-system.md#backend-phppest)
- **Validações do sistema?** → [Fluxo de Validação](./diagrams.md#fluxo-de-validação)

### Por Funcionalidade

#### 📦 Produtos
- ✅ [Estrutura de Dados](./inventory-system.md#estrutura-de-dados)
- ✅ [Categorias Disponíveis](./inventory-system.md#categorias-disponíveis)
- ✅ [Unidades de Medida](./inventory-system.md#unidades-de-medida)
- ✅ [Regras de Negócio](./inventory-system.md#regras-de-negócio)
- ✅ [Criar/Atualizar/Listar](./inventory-system.md#funcionalidades)

#### 📊 Movimentações
- ✅ [Tipos de Movimentação](./inventory-system.md#tipos-de-movimentação)
- ✅ [Motivos de Movimentação](./inventory-system.md#motivos-de-movimentação)
- ✅ [Ajustar Estoque](./inventory-system.md#1-ajustar-estoque)
- ✅ [Histórico](./inventory-system.md#2-histórico-de-movimentações)
- ✅ [Recalcular Estoque](./inventory-system.md#3-recalcular-estoque)

#### 🏢 Fornecedores
- ✅ [Cadastro Completo](./inventory-system.md#fornecedores)
- ✅ [Ativar/Desativar](./inventory-system.md#4-desativar-fornecedor)
- ✅ [Busca e Filtros](./inventory-system.md#3-listar-fornecedores)

### Por Tecnologia

#### Backend (Laravel/PHP)
```bash
# Código relevante:
app/Models/Product.php
app/Models/StockMovement.php
app/Models/Supplier.php
app/Services/ProductService.php
app/Http/Controllers/ProductsController.php
app/Http/Controllers/StockMovementsController.php
app/Http/Controllers/SuppliersController.php
```

#### Frontend (Vue.js)
```bash
# Código relevante:
resources/js/pages/products/
resources/js/pages/stock-movements/
resources/js/pages/suppliers/
resources/js/api/Products.ts
resources/js/api/Suppliers.ts
```

#### Testes
```bash
# Backend:
tests/Feature/ProductServiceTest.php
tests/Feature/ProductsControllerTest.php
tests/Feature/StockMovementsControllerTest.php
tests/Feature/SuppliersControllerTest.php

# Frontend:
resources/js/tests/products/
```

### Recursos Visuais

- 📊 [Modelo de Dados ER](./diagrams.md#modelo-de-dados)
- 🔄 [Fluxo de Criação de Produto](./diagrams.md#1-fluxo-de-criação-de-produto)
- 📈 [Fluxo de Ajuste de Estoque](./diagrams.md#2-fluxo-de-ajuste-de-estoque)
- 🔁 [Fluxo de Recálculo](./diagrams.md#3-fluxo-de-recálculo-de-estoque)
- 🏗️ [Arquitetura de Camadas](./diagrams.md#arquitetura-de-camadas)
- 📱 [Componentes Frontend](./diagrams.md#componentes-frontend)
- 🔐 [Fluxo de Validação](./diagrams.md#fluxo-de-validação)
- 📊 [Estados de Estoque](./diagrams.md#estados-de-estoque)
- 🧪 [Pirâmide de Testes](./diagrams.md#cobertura-de-testes)

## 🆘 Resolução de Problemas

| Problema | Solução |
|----------|---------|
| Estoque ficou negativo | [Troubleshooting - Estoque Negativo](./inventory-system.md#estoque-ficou-negativo) |
| Movimentações inconsistentes | [Troubleshooting - Movimentações](./inventory-system.md#movimentações-inconsistentes) |
| Produto não aparece em estoque baixo | [Troubleshooting - Estoque Baixo](./inventory-system.md#produto-não-aparece-em-estoque-baixo) |

## 📚 Índice Completo

1. **[README Principal](./README.md)** - Visão geral da documentação (201 linhas)
2. **[Sistema de Estoque](./inventory-system.md)** - Documentação completa (602 linhas)
3. **[Diagramas](./diagrams.md)** - Fluxos e arquitetura visual (399 linhas)

**Total**: 1.202 linhas de documentação técnica

---

## 🔗 Links Externos

- [Laravel Documentation](https://laravel.com/docs)
- [Vue.js 3 Guide](https://vuejs.org/guide/)
- [Inertia.js](https://inertiajs.com)
- [Pest PHP Testing](https://pestphp.com)
- [Vitest Documentation](https://vitest.dev)
- [TanStack Table](https://tanstack.com/table/latest)
- [shadcn-vue](https://www.shadcn-vue.com)

---

**Dica**: Use `Ctrl+F` ou `Cmd+F` para buscar termos específicos na documentação!
