#!/bin/bash

echo "🚀 Configurando ambiente de desenvolvimento App Oficina..."

# Dar permissões corretas aos arquivos
echo "🔐 Configurando permissões..."
chown -R www-data:www-data /var/www
chmod -R 755 /var/www

# Instalar dependências PHP
if [ ! -d "vendor" ]; then
    echo "📦 Instalando dependências PHP com Composer..."
    composer install --no-interaction --optimize-autoloader
fi

# Instalar dependências Node.js
if [ ! -d "node_modules" ]; then
    echo "📦 Instalando dependências Node.js..."
    npm install
fi

# Configurar arquivo .env se não existir
if [ ! -f ".env" ]; then
    echo "⚙️ Criando arquivo .env..."
    cp .env.example .env
    php artisan key:generate --no-interaction
fi

# Configurar banco de dados
if [ ! -f "database/database.sqlite" ]; then
    echo "🗄️ Criando banco de dados SQLite..."
    touch database/database.sqlite
    chmod 664 database/database.sqlite
fi

# Configurar storage
echo "📁 Configurando diretórios de storage..."
php artisan storage:link --quiet 2>/dev/null || true

# Executar migrações
echo "🔄 Executando migrações..."
php artisan migrate --force --no-interaction

# Verificar PHPCS
echo "🔍 Verificando configuração do PHP CodeSniffer..."
if [ -f "vendor/bin/phpcs" ]; then
    echo "✅ PHPCS instalado e configurado!"
    vendor/bin/phpcs --version
    
    # Testar PHPCS
    echo "🧪 Testando PHPCS..."
    if vendor/bin/phpcs --standard=phpcs.xml --report=summary app/ 2>/dev/null; then
        echo "✅ PHPCS funcionando perfeitamente!"
    else
        echo "⚠️ PHPCS configurado, mas encontrou alguns problemas no código"
    fi
else
    echo "❌ PHPCS não encontrado!"
fi

echo ""
echo "✨ Configuração do Dev Container concluída!"
echo ""
echo "🌐 Serviços disponíveis:"
echo "   - App: http://localhost:4500"
echo "   - Vite: http://localhost:5173" 
echo "   - Mailpit: http://localhost:4503"
echo ""
echo "🔧 Comandos úteis:"
echo "   - composer phpcs:check  # Verificar código"
echo "   - composer phpcbf       # Corrigir código"
echo "   - npm run dev           # Iniciar Vite"
echo "   - php artisan serve     # Servidor Laravel"
echo ""
echo "🎯 PHP CodeSniffer está pronto para usar nativamente!"
echo ""