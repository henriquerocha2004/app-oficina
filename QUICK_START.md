# 🎯 QUICK START - Dev Container (Simplificado)

## 1. ⚡ Início Rápido

```bash
# 1. Instalar Dev Containers extension no VS Code
# 2. Abrir pasta do projeto
# 3. Ctrl+Shift+P → "Dev Containers: Reopen in Container"
# 4. Aguardar configuração automática (2-3 minutos na primeira vez)
```

## 2. ✅ Verificação após container iniciar

```bash
# PHP CodeSniffer funcionará automaticamente
composer phpcs:check

# Versão do PHP
php --version

# Laravel
php artisan --version
```

## 3. 🔧 Desenvolvimento

- **PHPCS funciona automaticamente** - Abra qualquer arquivo .php
- **Debug Xdebug** - F5 ou "Listen for Xdebug"  
- **Tasks** - Ctrl+Shift+P → "Tasks: Run Task"

## 4. 🌐 Acessos

- App: http://localhost:4500
- Vite: http://localhost:5173
- Mail: http://localhost:4503

## 5. 🚨 Se algo der errado

```bash
# Recriar container
Ctrl+Shift+P → "Dev Containers: Rebuild Container"
```

## 🎉 **Simplificações feitas:**

- ❌ **Removido:** Configurações UID/GID (não precisamos mais!)
- ❌ **Removido:** Mapeamento manual de usuários
- ✅ **Adicionado:** Gerenciamento automático pelo Dev Container
- ✅ **Otimizado:** PHPCS nativo sem workarounds

**✨ Agora é muito mais simples! O Dev Container gerencia tudo automaticamente!**