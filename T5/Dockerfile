FROM composer:2

RUN docker-php-ext-install pdo_mysql
WORKDIR /app

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
