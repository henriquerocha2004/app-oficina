# 🚀 Navegação Rápida - Documentação

## 📦 Sistema de Estoque

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
