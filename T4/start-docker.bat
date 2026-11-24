@echo off
REM Script para iniciar la aplicación Laravel con Docker en Windows

echo.
echo 🚀 Iniciando aplicación Laravel con Docker...
echo.

REM Verificar que Docker está disponible
docker --version >nul 2>&1
if errorlevel 1 (
    echo ❌ Docker no está instalado o no está disponible
    pause
    exit /b 1
)

echo 📦 Construyendo imagen Docker...
docker-compose up -d --build
if errorlevel 1 (
    echo ❌ Error construyendo la imagen
    pause
    exit /b 1
)

echo.
echo ⏳ Esperando a que el contenedor esté listo (10 segundos)...
timeout /t 10 /nobreak

echo 📥 Instalando dependencias de Composer...
docker-compose exec -T app composer install --no-interaction --prefer-dist >nul 2>&1

echo 🔑 Generando clave de aplicación...
docker-compose exec -T app php artisan key:generate --force >nul 2>&1

echo.
echo ✅ ¡Aplicación iniciada correctamente!
echo.
echo 🌐 URL de acceso: http://localhost:8000
echo.
echo 📝 Comandos útiles:
echo    docker-compose logs app -f          # Ver logs del servidor
echo    docker-compose down                 # Detener contenedores
echo.
pause
