# Seeders Documentation

## ClientsAndCarsSeeder

Este seeder cria 100 clientes, cada um com um carro vinculado.

### Como usar:

```bash
# Executar o seeder específico
php artisan db:seed --class=ClientsAndCarsSeeder

# Executar todos os seeders (incluindo este)
php artisan db:seed

# Executar com migração fresh (limpa e recria tudo)
php artisan migrate:fresh --seed
```

### Características dos dados gerados:

#### Clientes:
- 100 clientes únicos
- Dados brasileiros (CPF, endereços, telefones)
- Names e emails únicos

#### Carros:
- 1 carro por cliente (total: 100 carros)
- Diversidade de tipos: sedan, hatchback, suv, coupe, convertible, wagon, van, pickup
- Variação de transmissão: manual e automatic
- Variação de placas: formato antigo (ABC1234) e Mercosul (ABC1D23)
- Mistura de carros novos e antigos
- Dados opcionais preenchidos com probabilidades realistas

#### Distribuição por padrão:
- **Carros recentes** (< 3 anos): ~25%
- **Carros vintage** (> 25 anos): ~25% 
- **Carros completos** (todos os campos): ~12.5%
- **Carros mínimos** (só obrigatórios): ~12.5%
- **Marcas específicas** (Toyota, BMW): ~25%

### Estatísticas típicas após execução:
- Total de clientes: 100
- Total de carros: 100
- Carros com placas: ~80-85
- Carros com VIN: ~75-80
- Distribuição equilibrada entre tipos de carros
- Proporção realista manual/automático