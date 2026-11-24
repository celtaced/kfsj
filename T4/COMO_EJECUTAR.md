# ¿Cómo Ejecuto Este Programa?

### En Windows (PowerShell o CMD):

```bash
cd T4
docker-compose up
```

Luego abre tu navegador en: **http://localhost**

¡Eso es todo! La aplicación está lista.

---

## 📋 Requisitos

- ✅ **Docker Desktop** instalado ([Descargar aquí](https://www.docker.com/products/docker-desktop))
- ✅ Docker Compose (ya viene con Docker Desktop)

Verifica que tengas Docker:
```bash
docker --version
docker-compose --version
```

---

## 🎯 Pasos Detallados

### 1️⃣ Abre PowerShell (Windows) o Terminal (Mac/Linux)

### 2️⃣ Navega a la carpeta T4

### 3️⃣ Construye e Inicia Docker

```bash
docker-compose build
docker-compose up
```

**O en un solo comando:**
```bash
docker-compose up --build
```

### 4️⃣ Espera a que Docker termine de inicializar

Verás mensajes como:
```
✓ Container laravel-app  Running
```

### 5️⃣ Accede a la Aplicación

Abre tu navegador web y ve a:

```
http://localhost
```

o 

```
http://localhost:80
```

---

## URLs de la Aplicación

| URL | Descripción |
|-----|-------------|
| `http://localhost/` | Página de inicio - Listado de destinos |
| `http://localhost/destinos/1` | Detalles de Suchitoto |
| `http://localhost/destinos/2` | Detalles de Playa El Tunco |
| `http://localhost/destinos/3` | Detalles de Volcán de Izalco |
| `http://localhost/destinos/4` | Detalles de Ruta de las Flores |
| `http://localhost/destinos/5` | Detalles de Joya de Cerén |
| `http://localhost/destinos/6` | Detalles de Isla Meanguera |
| `http://localhost/contacto` | Formulario de contacto |

---

## Detener la Aplicación

### Opción 1: En la misma ventana
Presiona: **CTRL + C**

### Opción 2: En otra ventana de PowerShell
```bash
docker-compose down
```

---

## Reiniciar la Aplicación

Si quieres reiniciar desde cero:

```bash
# Detener y eliminar contenedores
docker-compose down

# Reinicar todo
docker-compose up --build
```

---

## Solución de Problemas Rápida

### "Port 80 already in use"
El puerto 80 está ocupado. Cambiar a otro puerto en `docker-compose.yml`:

```yaml
ports:
  - "8080:80"  # Cambiar esto
```

Luego acceder a: `http://localhost:8080`

### "Cannot find docker-compose"
Docker no está instalado. [Descarga aquí](https://www.docker.com/products/docker-desktop)

### "Destino no encontrado" al abrir /destinos/1
Espera unos segundos más a que Docker inicie completamente.

### Ver logs para debuguear
```bash
docker-compose logs laravel-app -f
```

---

## Verificar que todo funciona

```bash
# Ver contenedores corriendo
docker ps

# Ver logs
docker-compose logs

# Acceder a la consola del contenedor
docker-compose exec app bash
```

---

## Información Técnica (Opcional)

- **Lenguaje:** PHP 8.2
- **Framework:** Laravel (versión simplificada)
- **Servidor Web:** Apache
- **Base de Datos:** JSON (sin MySQL)
- **Arquitectura:** MVC (Model-View-Controller)
- **Contenedor:** Docker

---

## Próximos Pasos

Una vez esté funcionando:

1. Explora todos los destinos
2. Revisa el código en la carpeta `app/Http/Controllers/DestinoController.php`
3. Lee el archivo `FLUJO-MVC.md` para entender cómo funciona la aplicación
4. Personaliza los destinos editando `resources/data/destinos.json`
