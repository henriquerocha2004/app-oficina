# 📚 Documentação do App Oficina

Bem-vindo à documentação técnica do App Oficina - Sistema Multi-Tenant para Gestão de Oficinas Mecânicas.

> 🚀 **Navegação Rápida**: Não sabe por onde começar? Veja o [Guia de Navegação Rápida](./QUICKNAV.md)

## 📖 Documentos Disponíveis

### 🏗️ Arquitetura e Setup

- **[README.md](../README.md)** - Visão geral do projeto, instalação e configuração
- **[Multi-Tenancy Setup](../MULTI_TENANCY_SETUP.md)** - Configuração da arquitetura multi-tenant
- **[Testing Multi-Tenancy](../TESTING_MULTI_TENANCY.md)** - Testes em ambiente multi-tenant
- **[Quick Start](../QUICK_START.md)** - Guia rápido de início
- **[Dev Container](../DEV_CONTAINER_README.md)** - Ambiente de desenvolvimento containerizado

### 📝 Guias de Contribuição

- **[Style Guide](./STYLE-GUIDE.md)** - Convenções de estilo para documentação
- **[Changelog](./CHANGELOG.md)** - Histórico de atualizações da documentação

### 📦 Módulos do Sistema

- **[Sistema de Gestão de Estoque](./inventory-system.md)** - Produtos, Movimentações e Fornecedores
  - Cadastro de produtos com categorias
  - Controle de estoque em tempo real
  - Histórico de movimentações
  - Gestão de fornecedores
  - Alertas de estoque baixo
  - API endpoints e testes
  - **[Ver diagramas →](./diagrams.md)**

### 🚗 Módulos em Documentação

> Documentação em desenvolvimento para os seguintes módulos:

- **Sistema de Clientes** - Gestão de clientes e contatos
- **Sistema de Veículos** - Cadastro e histórico de veículos
- **Sistema de Serviços** - Ordens de serviço e agendamentos
- **Sistema de Autenticação** - Login, registro e permissões
- **Painel Administrativo** - Gestão de tenants e planos

---

## 🎯 Guias por Funcionalidade

### Para Desenvolvedores

1. **Começando**
   - Leia o [README.md](../README.md) principal
   - Configure o ambiente com [Quick Start](../QUICK_START.md)
   - Entenda a arquitetura em [Multi-Tenancy Setup](../MULTI_TENANCY_SETUP.md)

2. **Desenvolvendo Features**
   - Consulte a documentação do módulo específico
   - Siga os padrões de código do projeto
   - Escreva testes para suas features

3. **Testando**
   - Backend: `php artisan test`
   - Frontend: `npm test`
   - Veja [Testing Notes](../TESTING_NOTES.md)

### Para Administradores de Sistema

1. **Deploy**
   - Configure variáveis de ambiente (.env)
   - Execute migrações: `php artisan migrate`
   - Configure banco central e tenants

2. **Manutenção**
   - Monitore logs em `storage/logs/`
   - Execute backups regulares
   - Acompanhe métricas de uso

---

## 📝 Convenções de Código

### Backend (PHP/Laravel)

```php
// Services seguem pattern Service Layer
class ProductService
{
    public function create(ProductInputDTO $dto): Product
    {
        // Lógica de negócio
    }
}

// Controllers delegam para Services
class ProductsController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}
}

// DTOs para transferência de dados
class ProductInputDTO
{
    public static function fromArray(array $data): self
    {
        // Validação e transformação
    }
}
```

### Frontend (Vue.js 3 + TypeScript)

```typescript
// Composables para lógica reutilizável
export function useProductsTable() {
    const productsData = ref<ProductInterface[]>([]);
    // Lógica do composable
    return { productsData, ... };
}

// TypeScript para type safety
interface ProductInterface {
    id: string;
    name: string;
    // ...
}

// Inertia.js para SSR
import { router } from '@inertiajs/vue3';
```

### Testes

```php
// Pest PHP para backend
test('adjust stock validates insufficient stock', function () {
    $product = Product::factory()->create(['stock_quantity' => 5]);
    
    $response = $this->postJson("/products/{$product->id}/adjust-stock", [
        'movement_type' => 'out',
        'quantity' => 10,
    ]);
    
    $response->assertStatus(422);
});
```

```typescript
// Vitest para frontend
it('fetches products on mount', async () => {
    const mockProducts = [...];
    vi.mocked(ProductsApi.search).mockResolvedValue({
        products: { items: mockProducts, total_items: 1 }
    });
    
    const [{ productsData }] = withSetup(() => useProductsTable());
    await vi.waitFor(() => expect(ProductsApi.search).toHaveBeenCalled());
    
    expect(productsData.value).toEqual(mockProducts);
});
```

---

## 🔧 Stack Tecnológica

### Backend
- **Framework**: Laravel 12
- **PHP**: 8.3+
- **Database**: MySQL 8.0
- **Testing**: Pest PHP 4.0
- **Multi-Tenancy**: stancl/tenancy 3.9

### Frontend
- **Framework**: Vue.js 3
- **SSR**: Inertia.js
- **Build**: Vite 7
- **Styling**: Tailwind CSS 4
- **UI Components**: shadcn-vue
- **Testing**: Vitest 3.2
- **Type Safety**: TypeScript 5.2

### DevOps
- **Container**: Docker
- **Server**: Nginx
- **Cache**: Redis (opcional)
- **Queue**: Laravel Queue (opcional)

---

## 🤝 Contribuindo

1. Sempre atualize a documentação ao adicionar features
2. Mantenha os testes em 100% de cobertura
3. Siga os padrões de código estabelecidos
4. Documente breaking changes no CHANGELOG

---

## 📞 Suporte

- **Issues**: Abra uma issue no repositório
- **Discussões**: Use GitHub Discussions
- **Email**: [seu-email@exemplo.com]

---

**Última atualização**: 01/01/2026  
**Versão do Sistema**: 1.0.0
