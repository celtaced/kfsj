# 🌴 Catálogo de Destinos Turísticos de El Salvador con Laravel MVC

Aplicación web desarrollada con **Laravel** que implementa el patrón arquitectónico **MVC** (Model-View-Controller) para gestionar un catálogo de destinos turísticos de El Salvador.

## 📋 Descripción

Esta aplicación demuestra cómo funciona el patrón MVC en Laravel, mostrando:

- **Model (Modelo)**: Gestión de datos desde archivo JSON (`destinos.json`)
- **View (Vista)**: Plantillas Blade que renderizan la información de forma dinámica
- **Controller (Controlador)**: `DestinoController` que orquesta la lógica de negocio y el flujo de datos

Los usuarios pueden:
- ✅ Explorar destinos turísticos disponibles
- ✅ Ver información detallada de cada lugar
- ✅ Enviar formularios de contacto

## 🏗️ Estructura del Proyecto

```
T4/
├── app/
│   └── Http/
│       └── Controllers/
│           └── DestinoController.php          # Controlador principal
├── routes/
│   └── web.php                               # Definición de rutas
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php                 # Plantilla base
│       └── destinos/
│           ├── index.blade.php               # Listado de destinos
│           ├── show.blade.php                # Detalle del destino
│           └── contacto.blade.php            # Formulario de contacto
├── public/
│   └── data/
│       └── destinos.json                     # Base de datos JSON
├── docker-compose.yml                        # Configuración Docker
├── Dockerfile                                # Imagen del contenedor
├── composer.json                             # Dependencias PHP
└── .env                                      # Variables de entorno
```

## 🔄 Ciclo de Vida de una Petición HTTP en Laravel MVC

### 1. **Petición HTTP** → Router
```
http://localhost:8000/destinos/1
```

### 2. **Router (routes/web.php)**
```php
Route::get('/destinos/{id}', [DestinoController::class, 'show'])->name('destinos.show');
```

### 3. **Controller (DestinoController)**
```php
public function show($id) {
    // Obtiene datos del JSON
    $destinos = $this->obtenerDestinos();
    $destino = collect($destinos)->firstWhere('id', (int)$id);
    
    // Pasa datos a la vista
    return view('destinos.show', ['destino' => $destino]);
}
```

### 4. **View (Blade Template)**
```blade
<div class="detalle-titulo">{{ $destino['nombre'] }}</div>
<div class="card-price">{{ $destino['precio'] }}</div>
```

### 5. **Respuesta HTML**
Navegador renderiza la página completa

## 🐳 Requisitos (Docker)

- Docker Desktop instalado
- Docker Compose disponible

## 🚀 Instrucciones de Instalación y Ejecución

### Con Docker (Recomendado)

1. **Navega a la carpeta del proyecto:**
   ```bash
   cd T4
   ```

2. **Construye y levanta los contenedores:**
   ```bash
   docker-compose up -d --build
   ```

3. **Instala dependencias de Composer:**
   ```bash
   docker-compose exec app composer install
   ```

4. **Genera la clave de la aplicación:**
   ```bash
   docker-compose exec app php artisan key:generate
   ```

5. **Accede a la aplicación:**
   ```
   http://localhost:8000
   ```

6. **Para detener los contenedores:**
   ```bash
   docker-compose down
   ```

### Ver logs del servidor:
```bash
docker-compose logs app -f
```

### Ejecutar comandos en el contenedor:
```bash
docker-compose exec app php artisan migrate
docker-compose exec app php artisan tinker
```

## 📊 Datos de Prueba

La aplicación incluye 6 destinos turísticos en `public/data/destinos.json`:

1. **Monumento Nacional El Rosario** - San Salvador (Centro Comercial)
2. **Volcán de Izalco** - Sonsonate (Volcán)
3. **Playa El Tunco** - La Libertad (Playa)
4. **Lago de Coatepeque** - Santa Ana (Lago)
5. **Ruta de las Flores** - Ahuachapán (Ruta Turística)
6. **Joya de Cerén** - La Libertad (Sitio Arqueológico)

## 🛣️ Rutas Disponibles

| Método | Ruta | Controlador | Descripción |
|--------|------|-------------|-------------|
| GET | `/` | `DestinoController@index` | Listado de destinos |
| GET | `/destinos/{id}` | `DestinoController@show` | Detalle del destino |
| GET | `/contacto` | `DestinoController@formulario` | Formulario de contacto |
| POST | `/contacto` | `DestinoController@enviarContacto` | Procesar contacto |

## 📱 Funcionalidades Implementadas

### ✅ Listar Destinos
- Carga datos desde JSON
- Muestra tarjetas con información básica
- Interfaz responsive con CSS Grid

### ✅ Ver Detalles
- Información completa del destino
- Teléfono, email, horarios
- Categoría y departamento

### ✅ Formulario de Contacto
- Validación de datos en servidor
- Selección de destino
- Mensaje personalizado

### ✅ Patrón MVC
- Separación clara de responsabilidades
- Rutas definidas centralizadamente
- Vistas Blade con sintaxis limpia
- Controlador con lógica de negocio

## 🔐 Seguridad

- ✅ Protección CSRF en formularios
- ✅ Validación de datos servidor
- ✅ Sanitización de entrada
- ✅ Variables de entorno segurizadas

## 📦 Dependencias Principales

- Laravel 10.x
- PHP 8.2 FPM
- MySQL 8.0
- Composer

## 📸 Capturas de Pantalla

### Página Principal
La página de inicio muestra un grid de destinos con tarjetas interactivas.

### Detalle de Destino
Vista completa con información de contacto, horarios y precios.

### Formulario de Contacto
Formulario validado con selección de destino y campo de mensaje.

## 🧪 Testing

Para probar las funcionalidades:

1. Acceder a `http://localhost:8000` → Ver listado de destinos
2. Hacer clic en "Ver Detalles" → Ir a página de destino específico
3. Hacer clic en "Contactar" → Ir a formulario de contacto
4. Completar formulario → Recibir confirmación

## 📚 Concepto de Flujo MVC

```
Usuario (HTTP Request)
    ↓
    Rutas (Router)
    ↓
    Controlador (Lógica)
    ↓
    Modelo (Datos JSON)
    ↓
    Controlador (Procesa)
    ↓
    Vista (Blade Template)
    ↓
Usuario (HTML Response)
```

## 🤝 Contribuciones

Este proyecto es parte del Bootcamp Full Stack Junior de Código (Módulo 3).

## 📄 Licencia

MIT License - Libre para uso educativo

## 👨‍💻 Autor

**Carlos Emanuel Dubón Cornejo**
Bootcamp: Full Stack Junior
Módulo 3: Desarrollo Web Avanzado

---

**Última actualización:** Noviembre 2024
