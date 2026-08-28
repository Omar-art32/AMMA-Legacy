FROM php:8.3-apache


# Actualizar paquetes e instalar extensiones de PHP
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    wget \
    vim \
    nano \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libicu-dev \
    libxml2-dev \
    libonig-dev \
    libcurl4-openssl-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        mysqli \
        pdo \
        pdo_mysql \
        mbstring \
        intl \
        gd \
        zip \
        opcache \
        soap \
    && a2enmod rewrite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Copiar ejecutable oficial de Composer (Última versión 2.x)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

EXPOSE 80
