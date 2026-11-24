#!/bin/bash
set -e

echo "Instalando dependencias de Laravel..."
composer install --no-interaction --prefer-dist

echo "Generando clave de aplicación..."
php artisan key:generate --force

echo "Ejecutando migraciones..."
php artisan migrate --force

echo "Publicando activos..."
php artisan vendor:publish --force

echo "Iniciando servidor..."
php artisan serve --host=0.0.0.0 --port=8000
