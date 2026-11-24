#!/bin/bash
set -e

CONTAINER_NAME="laravel-app"

echo "🚀 Iniciando aplicación Laravel con Docker..."
echo ""

# Verificar que Docker está disponible
if ! command -v docker &> /dev/null; then
    echo "❌ Docker no está instalado o no está en el PATH"
    exit 1
fi

echo "📦 Construyendo imagen Docker..."
docker-compose up -d --build

echo ""
echo "⏳ Esperando a que el contenedor esté listo..."
sleep 10

echo "📥 Instalando dependencias de Composer..."
docker-compose exec -T app composer install --no-interaction --prefer-dist 2>/dev/null || true

echo "🔑 Generando clave de aplicación..."
docker-compose exec -T app php artisan key:generate --force 2>/dev/null || true

echo ""
echo "✅ ¡Aplicación iniciada correctamente!"
echo ""
echo "🌐 URL de acceso: http://localhost:8000"
echo ""
echo "📝 Comandos útiles:"
echo "   docker-compose logs app -f          # Ver logs del servidor"
echo "   docker-compose exec app php artisan # Ejecutar comandos artisan"
echo "   docker-compose down                 # Detener contenedores"
echo ""
