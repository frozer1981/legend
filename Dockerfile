 FROM php:8.3-fpm

# Инсталиране на нужните пакети
RUN apt-get update && apt-get install -y \
    libxml2-dev \
    zip unzip git curl \
    && docker-php-ext-install pdo pdo_mysql simplexml

# Инсталиране на Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

CMD ["php-fpm"]
