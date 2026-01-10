# 📊 Diagramas do Sistema de Estoque

Diagramas visuais para entender o fluxo e a arquitetura do sistema de gestão de estoque.

## 🗂️ Modelo de Dados

### Relacionamentos entre Entidades

```mermaid
erDiagram
    PRODUCT ||--o{ STOCK_MOVEMENT : has
    USER ||--o{ STOCK_MOVEMENT : creates
    SUPPLIER ||--o{ PRODUCT : supplies
    
    PRODUCT {
        string id PK
        string name
        string sku UK
        enum category
        enum unit
        int stock_quantity
        int min_stock_level
        decimal unit_price
        boolean is_active
        timestamp deleted_at
        timestamp created_at
        timestamp updated_at
    }
    
    STOCK_MOVEMENT {
        string id PK
        string product_id FK
        string user_id FK
        enum movement_type
        int quantity
        int balance_after
        enum reason
        string notes
        string reference_type
        string reference_id
        timestamp deleted_at
        timestamp created_at
        timestamp updated_at
    }
    
    SUPPLIER {
        string id PK
        string name
        string document_number
        string email
        string phone
        string address
        string city
        string state
        string zip_code
        string notes
        boolean is_active
        timestamp deleted_at
        timestamp created_at
        timestamp updated_at
    }
    
    USER {
        string id PK
        string name
        string email
        timestamp created_at
    }
```

## 🔄 Fluxos de Processo

### 1. Fluxo de Criação de Produto

```mermaid
sequenceDiagram
    participant U as Usuário
    participant F as Frontend (Vue)
    participant C as Controller
    participant R as Request Validator
    participant S as Service
    participant M as Model (Product)
    participant DB as Database
    
    U->>F: Preenche formulário
    F->>F: Valida frontend (Zod)
    F->>C: POST /products
    C->>R: Valida dados
    R->>R: Verifica campos obrigatórios
    R->>R: Valida SKU único
    alt Validação falha
        R-->>C: Erro 422
        C-->>F: JSON com erros
        F-->>U: Exibe mensagens
    else Validação OK
        R->>S: ProductInputDTO
        S->>M: create()
        M->>DB: INSERT
        DB-->>M: Product criado
        M-->>S: Product
        S-->>C: Product
        C-->>F: JSON 201 Created
        F-->>U: Sucesso + Redirect
    end
```

### 2. Fluxo de Ajuste de Estoque

```mermaid
sequenceDiagram
    participant U as Usuário
    participant F as Frontend (Vue)
    participant C as ProductsController
    participant S as ProductService
    participant P as Product Model
    participant SM as StockMovement Model
    participant DB as Database
    
    U->>F: Solicita ajuste (in/out)
    F->>C: POST /products/{id}/adjust-stock
    C->>C: Valida request
    C->>S: adjustStock()
    S->>P: findOrFail(id)
    DB-->>P: Product
    
    alt Movimento OUT
        S->>S: Valida estoque suficiente
        alt Estoque insuficiente
            S-->>C: InsufficientStockException
            C-->>F: 422 Unprocessable
            F-->>U: Erro: Estoque insuficiente
        end
    end
    
    S->>DB: BEGIN TRANSACTION
    
    alt Movimento IN
        S->>P: increment('stock_quantity')
    else Movimento OUT
        S->>P: decrement('stock_quantity')
    end
    
    S->>SM: create(movimento)
    SM->>DB: INSERT stock_movement
    
    S->>DB: COMMIT
    DB-->>S: Success
    S-->>C: StockMovement
    C-->>F: 200 OK + movimento
    F-->>U: Sucesso + Atualiza UI
```

### 3. Fluxo de Recálculo de Estoque

```mermaid
flowchart TD
    A[Iniciar Recálculo] --> B[Buscar Produto]
    B --> C{Produto existe?}
    C -->|Não| D[Erro: Not Found]
    C -->|Sim| E[Buscar todas movimentações]
    E --> F[Ordenar por created_at ASC]
    F --> G[Inicializar saldo = 0]
    G --> H{Mais movimentos?}
    H -->|Não| M[Atualizar product.stock_quantity]
    H -->|Sim| I[Próximo movimento]
    I --> J{Tipo de movimento}
    J -->|IN| K[saldo += quantidade]
    J -->|OUT| L[saldo -= quantidade]
    K --> H
    L --> H
    M --> N[Salvar no banco]
    N --> O[Retornar saldo calculado]
    O --> P[Fim]
```

## 🏗️ Arquitetura de Camadas

### Estrutura de Componentes

```mermaid
graph TB
    subgraph Frontend["🎨 Frontend Layer"]
        Pages["Pages (Inertia)<br/>products/Index.vue<br/>stock-movements/Index.vue"]
        Composables["Composables<br/>useProductsTable<br/>useStockMovements"]
        API["API Clients<br/>ProductsApi.ts<br/>SuppliersApi.ts"]
        Components["UI Components<br/>Form, Table, Dialog"]
    end
    
    subgraph Backend["⚙️ Backend Layer"]
        Routes["Routes<br/>web.php"]
        Controllers["Controllers<br/>ProductsController<br/>StockMovementsController"]
        Requests["Form Requests<br/>CreateProductRequest<br/>UpdateProductRequest"]
        Services["Services<br/>ProductService"]
        DTOs["DTOs<br/>ProductInputDTO<br/>ProductOutputDTO"]
        Models["Models<br/>Product<br/>StockMovement"]
    end
    
    subgraph Database["💾 Database Layer"]
        MySQL[(MySQL<br/>Tenant DB)]
    end
    
    Pages --> Composables
    Composables --> API
    Components --> Pages
    
    API -->|HTTP/JSON| Routes
    Routes --> Controllers
    Controllers --> Requests
    Requests --> Controllers
    Controllers --> Services
    Services --> DTOs
    Services --> Models
    Models --> MySQL
    
    style Frontend fill:#e3f2fd
    style Backend fill:#fff3e0
    style Database fill:#f1f8e9
```

## 📱 Componentes Frontend

### Estrutura de Páginas de Produtos

```mermaid
graph LR
    subgraph ProductsIndex["📄 products/Index.vue"]
        Table["ProductsTable<br/>(TanStack Table)"]
        Filters["Filtros<br/>Busca, Categoria"]
        Actions["Ações<br/>Criar, Editar, Deletar"]
        StockBadge["Badge Estoque<br/>Baixo/Normal/Alto"]
    end
    
    subgraph Composable["🎯 useProductsTable"]
        State["Estado<br/>products, loading, page"]
        Methods["Métodos<br/>fetchProducts<br/>goToNextPage"]
        Watchers["Watchers<br/>searchTerm (debounced)"]
    end
    
    subgraph APILayer["🌐 ProductsApi"]
        Search["search()"]
        AdjustStock["adjustStock()"]
        GetLowStock["getLowStock()"]
        Save["save/update()"]
    end
    
    Table --> Composable
    Filters --> Composable
    Actions --> Composable
    Composable --> APILayer
    
    style ProductsIndex fill:#bbdefb
    style Composable fill:#c8e6c9
    style APILayer fill:#fff9c4
```

## 🔐 Fluxo de Validação

### Validação em Múltiplas Camadas

```mermaid
graph TD
    A[Usuário preenche formulário] --> B[Validação Frontend - Zod]
    B --> C{Válido?}
    C -->|Não| D[Exibe erros inline]
    C -->|Sim| E[Envia para API]
    E --> F[Validação Request - Laravel]
    F --> G{Válido?}
    G -->|Não| H[Retorna 422 + Erros JSON]
    H --> I[Frontend exibe erros]
    G -->|Sim| J[Validação Regras de Negócio]
    J --> K{Regras OK?}
    K -->|Não| L[Exception específica]
    L --> M[Handler converte em HTTP]
    M --> I
    K -->|Sim| N[Processa ação]
    N --> O[Retorna Sucesso]
    
    style B fill:#e1f5fe
    style F fill:#fff3e0
    style J fill:#f1f8e9
```

### Exemplo de Validação de Ajuste de Estoque

```mermaid
sequenceDiagram
    participant F as Frontend
    participant R as Request
    participant S as Service
    participant E as Exception
    
    F->>R: { movement_type: "out", quantity: 10 }
    R->>R: Valida campos obrigatórios
    R->>R: Valida enum movement_type
    R->>R: Valida quantity > 0
    
    alt Validação Request falha
        R-->>F: 422 + erros de validação
    else Request OK
        R->>S: adjustStock(id, 10, "out")
        S->>S: Busca produto (stock: 5)
        S->>S: Valida estoque suficiente
        
        alt Estoque insuficiente
            S->>E: InsufficientStockException
            E-->>F: 422 + "Disponível: 5, Solicitado: 10"
        else Estoque OK
            S->>S: Executa movimentação
            S-->>F: 200 + movimento criado
        end
    end
```

## 📊 Estados de Estoque

### Classificação de Níveis

```mermaid
graph TD
    Start[Verificar Estoque] --> Check{Comparar stock_quantity<br/>com min_stock_level}
    
    Check -->|stock <= min_stock| Critical[🔴 CRÍTICO<br/>Estoque Baixo]
    Check -->|stock <= min_stock * 1.5| Warning[🟡 ATENÇÃO<br/>Próximo ao Mínimo]
    Check -->|stock > min_stock * 1.5| Normal[🟢 NORMAL<br/>Estoque OK]
    
    Critical --> Alert1[Exibir Badge Vermelho]
    Warning --> Alert2[Exibir Badge Amarelo]
    Normal --> Alert3[Exibir Badge Cinza]
    
    Critical --> Notify[Notificar na Dashboard]
    
    style Critical fill:#ffcdd2
    style Warning fill:#fff9c4
    style Normal fill:#c8e6c9
```

## 🧪 Cobertura de Testes

### Pirâmide de Testes

```mermaid
graph TD
    subgraph Testes["🧪 Estratégia de Testes"]
        E2E["E2E Tests<br/>(Futuro)"]
        Integration["Integration Tests<br/>Controllers + API"]
        Unit["Unit Tests<br/>Services + Composables"]
    end
    
    E2E -.-> Integration
    Integration --> Unit
    
    subgraph Backend["Backend (175 testes)"]
        Feature["Feature Tests<br/>Controllers + Endpoints"]
        UnitB["Unit Tests<br/>Services"]
    end
    
    subgraph Frontend["Frontend (115 testes)"]
        Component["Component Tests<br/>Vue + Composables"]
        UnitF["Unit Tests<br/>Utils + Helpers"]
    end
    
    Integration --> Feature
    Integration --> Component
    Unit --> UnitB
    Unit --> UnitF
    
    style E2E fill:#f3e5f5
    style Integration fill:#e1bee7
    style Unit fill:#ce93d8
```

---

## 📚 Referências Visuais

### Convenções de Cores

- 🔴 **Vermelho**: Crítico, Erro, Estoque Baixo
- 🟡 **Amarelo**: Atenção, Warning, Próximo ao Limite
- 🟢 **Verde**: Sucesso, Normal, OK
- 🔵 **Azul**: Informação, Links, Navegação
- ⚪ **Cinza**: Neutro, Inativo, Padrão

### Ícones do Sistema

- 📦 Produtos
- 📊 Movimentações
- 🏢 Fornecedores
- ➕ Entrada (IN)
- ➖ Saída (OUT)
- 🔄 Recálculo
- ⚠️ Alerta
- ✅ Sucesso
- ❌ Erro

---

**Última atualização**: 01/01/2026  
**Versão**: 1.0.0
