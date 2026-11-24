# Instrucciones de Ejecución - Aplicación Laravel MVC

## Requisitos Previos

✅ **Docker Desktop** instalado en tu sistema  
✅ **Docker Compose** (incluido en Docker Desktop)

## ¿Tienes Docker instalado?

Verifica ejecutando en PowerShell:

```powershell
docker --version
docker-compose --version
```

Si tienes errores, [descarga Docker Desktop](https://www.docker.com/products/docker-desktop).

---

## 🎯 Opción 1: Ejecución en Windows (Recomendada)

### Paso 1: Abre PowerShell o CMD

Navega a la carpeta del proyecto:

```powershell
cd "C:\Users\[TuUsuario]\OneDrive - Alsicorp\Escritorio\K\T4"
```

### Paso 2: Ejecuta el script de inicio

```powershell
.\start-docker.bat
```

Este script automáticamente:
- Construye la imagen Docker
- Levanta los contenedores (PHP y MySQL)
- Instala dependencias de Composer
- Genera la clave de la aplicación
- Inicia el servidor Laravel

### Paso 3: Accede a la aplicación

Abre tu navegador en:

```
http://localhost:8000
```

---

## 🎯 Opción 2: Ejecución Manual

Si el script no funciona, ejecuta manualmente:

```powershell
# 1. Construir e iniciar contenedores
docker-compose up -d --build

# 2. Instalar dependencias
docker-compose exec app composer install

# 3. Generar clave de aplicación
docker-compose exec app php artisan key:generate

# 4. Ver logs (opcional)
docker-compose logs app -f
```

---

## 🌐 Acceso a la Aplicación

Una vez iniciado, accede en tu navegador:

- **Página Principal:** `http://localhost:8000/`
- **Listar Destinos:** `http://localhost:8000/`
- **Ver Destino:** `http://localhost:8000/destinos/1`
- **Formulario de Contacto:** `http://localhost:8000/contacto`

---

## 🛑 Detener la Aplicación

```powershell
docker-compose down
```

Esto detiene y elimina los contenedores (los datos JSON se mantienen).

---

## 📊 Comandos Útiles

### Ver logs del servidor Laravel:
```powershell
docker-compose logs app -f
```

### Ver logs de la base de datos MySQL:
```powershell
docker-compose logs db -f
```

### Ejecutar comando Artisan:
```powershell
docker-compose exec app php artisan [comando]
```

### Acceder a la consola interactiva de Laravel:
```powershell
docker-compose exec app php artisan tinker
```

### Listar rutas disponibles:
```powershell
docker-compose exec app php artisan route:list
```

---

## 🔍 Verificar que todo funciona

### 1. Verifica que los contenedores están corriendo:
```powershell
docker-compose ps
```

Deberías ver algo como:
```
NAME            STATUS      PORTS
laravel-app     Up          0.0.0.0:8000->8000/tcp
laravel-db      Up          0.0.0.0:3306->3306/tcp
```

### 2. Visita `http://localhost:8000` y verifica:
- ✅ Aparece el titulo "Destinos Turísticos de El Salvador"
- ✅ Se muestran 6 destinos en tarjetas
- ✅ Puedes hacer clic en "Ver Detalles"
- ✅ La página de contacto carga correctamente

---

## ❌ Solución de Problemas

### **El puerto 8000 ya está en uso**

```powershell
# Cambiar el puerto en docker-compose.yml
# Reemplaza:  "8000:8000"
# Por:        "8001:8000"

docker-compose up -d --build
```

Luego accede a `http://localhost:8001`

### **El contenedor no inicia**

```powershell
# Ver error detallado
docker-compose logs app

# Reintentar
docker-compose restart app
```

### **Dependencias de Composer no instalan**

```powershell
# Limpiar caché de Docker
docker-compose down -v
docker-compose up -d --build
docker-compose exec app composer install --no-interaction
```

### **MySQL no conecta**

Espera 15-20 segundos después de `docker-compose up` para que MySQL inicie completamente.

```powershell
# Esperar y reintentar
docker-compose restart app
```

---

## 📁 Estructura de Archivos

```
T4/
├── app/Http/Controllers/DestinoController.php    # Lógica
├── routes/web.php                               # Rutas
├── resources/views/                             # Vistas
├── public/data/destinos.json                    # Datos
├── docker-compose.yml                           # Orquestación Docker
├── Dockerfile                                   # Imagen PHP
├── README.md                                    # Documentación principal
├── FLUJO-MVC.md                                # Análisis MVC detallado
├── start-docker.bat                             # Script de inicio (Windows)
└── start-docker.sh                              # Script de inicio (Linux/Mac)
```

---

## 🎓 Conceptos de MVC Aplicados

Mientras la aplicación está corriendo, observa:

1. **Request → Router** (`http://localhost:8000/destinos/1`)
2. **Router → Controlador** (DestinoController@show)
3. **Controlador → Modelo** (Lectura desde destinos.json)
4. **Modelo → Controlador** (Datos procesados)
5. **Controlador → Vista** (Datos al template Blade)
6. **Vista → Respuesta HTML** (Página renderizada)

Lee `FLUJO-MVC.md` para análisis detallado.

---

## 📝 Notas Importantes

- **Sin base de datos relacional:** Usamos JSON para simplicidad educativa
- **Sin autenticación:** No hay login implementado
- **Formulario de contacto:** Muestra mensaje de éxito pero no envía correo
- **Desarrollo local:** Usar solo en desarrollo, no en producción

---

## 🔧 Personalización

### Agregar más destinos:

Edita `public/data/destinos.json`:

```json
{
  "id": 7,
  "nombre": "Mi Nuevo Destino",
  "departamento": "Chalatenango",
  "categoria": "Naturaleza",
  "descripcion": "...",
  "precio": "$50.00",
  "horario": "...",
  "telefono": "+503 ...",
  "email": "..."
}
```

Recarga el navegador. ¡Aparecerá automáticamente!

---

## ✨ Éxito

Si ves la página con destinos, ¡la aplicación está funcionando correctamente!

🎉 **¡Felicidades! Ya tienes Laravel MVC corriendo con Docker.**

---

**Compilado por:** Carlos Emanuel Dubón Cornejo  
**Bootcamp:** Full Stack Junior - Módulo 3  
**Fecha:** Noviembre 2024
