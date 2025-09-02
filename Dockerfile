# Dockerfile
FROM php:8.3-apache

# Evita errores por falta de TTY y reduce preguntas interactivas
ENV DEBIAN_FRONTEND=noninteractive

# Instala dependencias necesarias y extensiones PHP
RUN apt-get update && apt-get install -y \
    git curl unzip zip libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libzip-dev libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Habilita mod_rewrite de Apache
RUN a2enmod rewrite

# Copia el archivo del virtual host
COPY .docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf

# Establece directorio de trabajo
WORKDIR /var/www/html

# Copia el código fuente (esto lo puedes mejorar luego con bind mounts)
COPY . /var/www/html

# Instala Composer (desde imagen oficial para evitar curl manual)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Ajusta permisos (esto puedes adaptarlo si usas Laravel Herd)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html
