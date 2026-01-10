#!/bin/bash
set -e

echo "🚀 Configurando ambiente de desenvolvimento App Oficina..."

# Só faz chown se for root
if [ "$(id -u)" = "0" ]; then
  echo "🔐 Ajustando permissões..."
  chown -R www-data:www-data /var/www
  chmod -R 755 /var/www
else
  echo "⚠️ Ignorando chown (usuário não-root)"
fi

# PHP deps
if [ ! -d "vendor" ]; then
  echo "📦 Instalando dependências PHP..."
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Node deps
if [ ! -d "node_modules" ]; then
  echo "📦 Instalando dependências Node..."
  npm install --no-audit --no-fund
fi

# .env
if [ ! -f ".env" ]; then
  echo "⚙️ Criando .env..."
  cp .env.example .env
  php artisan key:generate --no-interaction
fi

# Storage
echo "📁 Configurando storage..."
php artisan storage:link --quiet || true

# Esperar banco antes de migrar
echo "⏳ Aguardando banco de dados..."
for i in {1..15}; do
  php artisan migrate --force --no-interaction && break
  echo "🔄 Banco ainda não disponível, tentando novamente..."
  sleep 3
done

# PHPCS
if [ -f "vendor/bin/phpcs" ]; then
  echo "✅ PHPCS disponível"
  vendor/bin/phpcs --version || true
fi

echo "✨ Dev Container configurado com sucesso!"

