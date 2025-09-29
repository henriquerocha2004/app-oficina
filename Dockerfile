FROM php:8.4-fpm

ARG USER_ID=1000
ARG GROUP_ID=1000

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
    build-essential \
    libpng-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd    

RUN groupadd -g ${GROUP_ID} laravel \
    && useradd -u ${USER_ID} -g laravel -m -s /bin/bash laravel

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instala o nodejs e npm
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs

# Define o diretório padrão
WORKDIR /var/www

USER laravel