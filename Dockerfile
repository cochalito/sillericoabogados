# Usamos una imagen oficial de PHP con Apache
FROM php:8.2-apache

# Instalar dependencias del sistema y extensiones de PHP necesarias para Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl

# Limpiar caché
RUN apt-get clean && rm -rf /var/lib/lists/*

# Instalar extensiones de PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Habilitar el módulo rewrite de Apache para las rutas de Laravel
RUN a2enmod rewrite

# Cambiar el DocumentRoot de Apache para que apunte a /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar Node.js y npm desde la imagen oficial de Node de forma segura
COPY --from=node:20 /usr/local/bin/node /usr/local/bin/node
COPY --from=node:20 /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

# Copiar el código del proyecto al contenedor
WORKDIR /var/www/html
COPY . .

# Instalar dependencias de Composer (Backend)
RUN composer install --no-interaction --optimize-autoloader --no-dev --ignore-platform-reqs

# Instalar dependencias de npm y construir assets para producción, luego eliminar node_modules
RUN npm ci && npm run build && rm -rf node_modules

# Permisos para las carpetas de almacenamiento de Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Puerto expuesto por Render
EXPOSE 80
