# 🐳 App Oficina - Dev Container

Este projeto está configurado para usar **Dev Containers** do VS Code, que fornece um ambiente de desenvolvimento consistente e isolado.

## 🚀 Como usar

### 1. Pré-requisitos
- [Visual Studio Code](https://code.visualstudio.com/)
- [Docker Desktop](https://www.docker.com/products/docker-desktop/)
- Extensão [Dev Containers](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers)

### 2. Abrir no Dev Container

#### Opção A - Comando VS Code:
1. Abra o VS Code
2. `Ctrl+Shift+P` → "Dev Containers: Reopen in Container"
3. Aguarde a configuração automática

#### Opção B - Clone direto no container:
1. `Ctrl+Shift+P` → "Dev Containers: Clone Repository in Container Volume"
2. Cole a URL do repositório
3. Aguarde a configuração

### 3. O que acontece automaticamente:
- ✅ Container PHP 8.4 com todas as extensões
- ✅ Composer e dependências PHP instaladas
- ✅ Node.js 22 e npm instalado
- ✅ Banco SQLite criado e migrações executadas
- ✅ PHP CodeSniffer configurado e funcionando
- ✅ Extensões VS Code instaladas automaticamente

## 🔧 Configurações incluídas

### Extensões instaladas:
- **PHP Intelephense** - IntelliSense para PHP
- **PHP Debug** - Debug com Xdebug
- **PHP CodeSniffer** - Linting automático
- **Laravel Blade** - Suporte para templates Blade
- **Vue/TypeScript** - Suporte frontend
- **GitLens** - Git aprimorado
- **Prettier** - Formatação de código

### Configurações PHP:
- PHP 8.4 com Xdebug habilitado
- PHP CodeSniffer com PSR-12
- Formatação automática ao salvar
- IntelliSense completo para Laravel

## 🌐 Portas expostas:
- **4500** - Nginx (App principal)
- **5173** - Vite Dev Server
- **4502** - Mailpit SMTP
- **4503** - Mailpit Web UI

## 📝 Comandos úteis:

```bash
# Verificar código
composer phpcs:check

# Corrigir código automaticamente
composer phpcbf

# Iniciar desenvolvimento frontend
npm run dev

# Executar testes
php artisan test

# Acessar banco
php artisan tinker
```

## 🐛 Troubleshooting

### Container não inicia:
```bash
# Parar containers existentes
docker-compose down

# Limpar volumes se necessário
docker-compose down -v

# Recriar containers
docker-compose build --no-cache
```

### PHP CodeSniffer não funciona:
- O PHPCS está pré-configurado dentro do container
- Todas as configurações são automáticas
- Reinicie o VS Code se necessário

### Mudanças no devcontainer.json:
1. `Ctrl+Shift+P` → "Dev Containers: Rebuild Container"
2. Aguarde a reconstrução

## 💡 Vantagens do Dev Container:

✅ **Ambiente consistente** - Todos da equipe têm o mesmo setup  
✅ **Zero configuração** - Tudo funciona automaticamente  
✅ **PHP CodeSniffer nativo** - Sem workarounds com Docker  
✅ **Debugging completo** - Xdebug configurado  
✅ **Extensões automáticas** - VS Code configurado perfeitamente  
✅ **Isolamento** - Não interfere no sistema host  

## 🔄 Workflow recomendado:

1. Abra o projeto no Dev Container
2. Aguarde a configuração automática
3. Comece a desenvolver - tudo já está funcionando!
4. Use `composer phpcs:check` para verificar o código
5. Use `composer phpcbf` para corrigir automaticamente

**O PHP CodeSniffer funcionará nativamente dentro do container, sem necessidade de configurações extras!**