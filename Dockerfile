FROM php:8.4-fpm

# Instala extensões necessárias do Laravel
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    git \
    netcat-openbsd \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instala e configura Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug
    
COPY docker-php-ext-xdebug.ini "${PHP_INI_DIR}/conf.d"

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instala Node.js e npm
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs

# Define o diretório padrão
WORKDIR /var/www

# Dev Container irá gerenciar usuários automaticamente