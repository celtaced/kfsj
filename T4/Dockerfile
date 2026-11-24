FROM php:8.2-apache

WORKDIR /var/www/html

# Instalar extensiones PHP necesarias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Habilitar mod_rewrite para Laravel
RUN a2enmod rewrite

# Copiar archivos del proyecto
COPY . /var/www/html/

# Copiar configuración de Apache
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Desactivar el sitio por defecto y activar el nuestro
RUN a2dissite 000-default && a2ensite 000-default

# Crear directorios necesarios
RUN mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/public/data

# Crear .htaccess en public si no existe
RUN echo '<IfModule mod_rewrite.c>\n    RewriteEngine On\n    RewriteBase /\n    RewriteCond %{REQUEST_FILENAME} !-f\n    RewriteCond %{REQUEST_FILENAME} !-d\n    RewriteRule ^(.*)$ index.php?/$1 [L]\n</IfModule>' > /var/www/html/public/.htaccess

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html/storage && \
    chmod -R 755 /var/www/html/bootstrap && \
    chmod -R 755 /var/www/html/public

EXPOSE 80

CMD ["apache2-foreground"]

