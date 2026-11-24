# 🔄 Análisis del Ciclo de Vida de una Petición HTTP en Laravel MVC

## Descripción General

En Laravel, una petición HTTP atraviesa un flujo bien definido donde cada componente del patrón MVC cumple un rol específico:

```
Navegador (HTTP Request)
    ↓
1️⃣  Punto de Entrada (index.php)
    ↓
2️⃣  Bootstrap de la Aplicación
    ↓
3️⃣  Enrutador (Router) - routes/web.php
    ↓
4️⃣  Controlador (Controller) - app/Http/Controllers/
    ↓
5️⃣  Modelo/Datos (Model/JSON)
    ↓
6️⃣  Vista (View) - resources/views/
    ↓
7️⃣  Respuesta HTML
    ↓
Navegador (HTTP Response)
```

---

## Paso a Paso: Petición a `/destinos`

### **1️⃣ Punto de Entrada (index.php en public/)**

```php
// El navegador hace una petición GET a http://localhost:8000/destinos
// Laravel recibe la petición en el archivo public/index.php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
```

**Función:** Captura la petición HTTP y la envía al kernel de la aplicación.

---

### **2️⃣ Bootstrap de la Aplicación (bootstrap/app.php)**

```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
    )
    ->withMiddleware(...)
    ->withExceptions(...)
    ->create();
```

**Función:** Inicializa la aplicación, carga configuraciones y prepara el enrutador.

---

### **3️⃣ Enrutador (routes/web.php) - CAPA DE RUTEO**

```php
// routes/web.php
Route::get('/', [DestinoController::class, 'index'])->name('destinos.index');
Route::get('/destinos/{id}', [DestinoController::class, 'show'])->name('destinos.show');
Route::get('/contacto', [DestinoController::class, 'formulario'])->name('contacto.formulario');
Route::post('/contacto', [DestinoController::class, 'enviarContacto'])->name('contacto.enviar');
```

**Petición:** `GET /destinos/1`

**Lo que pasa:**
- Laravel busca coincidencia en las rutas
- Encuentra: `Route::get('/destinos/{id}', [DestinoController::class, 'show'])`
- Extrae el parámetro `{id}` = `1`
- Resuelve a: `DestinoController@show(1)`

---

### **4️⃣ Controlador (app/Http/Controllers/DestinoController.php) - CAPA DE LÓGICA**

```php
namespace App\Http\Controllers;

class DestinoController extends Controller
{
    protected $rutaJson;

    public function __construct()
    {
        // Ruta del archivo JSON con los datos
        $this->rutaJson = public_path('data/destinos.json');
    }

    // Acción para listar destinos
    public function index()
    {
        // 1. Obtiene datos desde el modelo/JSON
        $destinos = $this->obtenerDestinos();
        
        // 2. Pasa datos a la vista
        return view('destinos.index', ['destinos' => $destinos]);
    }

    // Acción para mostrar detalle
    public function show($id)
    {
        // 1. Obtiene datos del JSON
        $destinos = $this->obtenerDestinos();
        
        // 2. Busca el destino específico
        $destino = collect($destinos)->firstWhere('id', (int)$id);

        // 3. Validación (no encontrado)
        if (!$destino) {
            abort(404, 'Destino no encontrado');
        }

        // 4. Pasa datos a la vista
        return view('destinos.show', ['destino' => $destino]);
    }

    // Acción para formulario de contacto
    public function formulario()
    {
        return view('destinos.contacto');
    }

    // Acción para procesar envío de contacto
    public function enviarContacto(Request $request)
    {
        // 1. Valida datos
        $validado = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email',
            'destino' => 'required|string',
            'mensaje' => 'required|string|max:1000',
        ]);

        // 2. Aquí se guardaría en BD o enviaría email
        // Por ahora guardamos en log o sesión

        // 3. Redirige con mensaje de éxito
        return redirect()->route('destinos.index')
                        ->with('success', 'Tu mensaje ha sido enviado correctamente.');
    }

    // Método privado para obtener datos
    private function obtenerDestinos()
    {
        $contenido = file_get_contents($this->rutaJson);
        return json_decode($contenido, true) ?? [];
    }
}
```

**Función del Controlador:**
- Recibe la petición y parámetros del router
- Realiza la lógica de negocio
- Llama al Modelo para obtener datos
- Prepara datos para la Vista
- Retorna una respuesta (vista, JSON, redirección, etc.)

---

### **5️⃣ Modelo/Datos - CAPA DE DATOS**

#### **Archivo: public/data/destinos.json**

```json
[
  {
    "id": 1,
    "nombre": "Monumento Nacional El Rosario",
    "departamento": "San Salvador",
    "categoria": "Monumento Histórico",
    "descripcion": "Centro comercial y cultural ubicado en el corazón de San Salvador...",
    "precio": "$25.00",
    "horario": "9:00 AM - 9:00 PM",
    "telefono": "+503 2211-5000",
    "email": "info@rosario.com.sv"
  },
  {
    "id": 2,
    "nombre": "Volcán de Izalco",
    "departamento": "Sonsonate",
    ...
  }
]
```

**En DestinoController:**

```php
// Leer datos desde JSON
$contenido = file_get_contents($this->rutaJson);
$destinos = json_decode($contenido, true);

// Buscar un destino específico
$destino = collect($destinos)->firstWhere('id', 1);

// Resultado:
// [
//   "id" => 1,
//   "nombre" => "Monumento Nacional El Rosario",
//   "departamento" => "San Salvador",
//   ...
// ]
```

---

### **6️⃣ Vista (resources/views/destinos/) - CAPA DE PRESENTACIÓN**

#### **resources/views/layouts/app.blade.php (Layout Base)**

```blade
<!DOCTYPE html>
<html lang="es">
<head>
    <title>@yield('titulo')</title>
</head>
<body>
    <header>
        <h1>🌴 Destinos Turísticos de El Salvador 🌴</h1>
    </header>
    
    <nav>
        <a href="{{ route('destinos.index') }}">Inicio</a>
        <a href="{{ route('contacto.formulario') }}">Contacto</a>
    </nav>
    
    <div class="content">
        @yield('contenido')
    </div>
    
    <footer>
        <p>&copy; 2024 Destinos Turísticos de El Salvador</p>
    </footer>
</body>
</html>
```

#### **resources/views/destinos/index.blade.php (Listar Destinos)**

```blade
@extends('layouts.app')

@section('titulo', 'Destinos Turísticos')

@section('contenido')
    <h2>🗺️ Nuestros Destinos</h2>
    
    <div class="grid">
        @foreach($destinos as $destino)
            <div class="card">
                <div class="card-title">{{ $destino['nombre'] }}</div>
                <div class="card-info">
                    <strong>Departamento:</strong> {{ $destino['departamento'] }}
                </div>
                <div class="card-price">{{ $destino['precio'] }}</div>
                <a href="{{ route('destinos.show', $destino['id']) }}" class="btn">
                    Ver Detalles
                </a>
            </div>
        @endforeach
    </div>
@endsection
```

**Funciones de las Vistas:**
- Recibe datos desde el Controlador (`$destinos`, `$destino`)
- Renderiza HTML dinámico
- Accede a rutas con `{{ route() }}`
- Genera URLs: `route('destinos.show', $destino['id'])` → `/destinos/1`
- Itera sobre datos: `@foreach($destinos as $destino)`
- Muestra contenido: `{{ $destino['nombre'] }}`

#### **resources/views/destinos/show.blade.php (Detalle de Destino)**

```blade
@extends('layouts.app')

@section('titulo', $destino['nombre'])

@section('contenido')
    <div class="detalle-container">
        <div class="detalle-titulo">{{ $destino['nombre'] }}</div>
        
        <div class="info-item">
            <div class="info-label">📍 Departamento</div>
            <div class="info-value">{{ $destino['departamento'] }}</div>
        </div>
        
        <div class="info-item">
            <div class="info-label">💰 Precio</div>
            <div class="info-value">{{ $destino['precio'] }}</div>
        </div>
        
        <div class="info-item">
            <div class="info-label">⏰ Horario</div>
            <div class="info-value">{{ $destino['horario'] }}</div>
        </div>
        
        <div class="descripcion">{{ $destino['descripcion'] }}</div>
        
        <a href="{{ route('destinos.index') }}" class="btn">← Volver</a>
    </div>
@endsection
```

---

## 🔄 Ejemplo Completo: Petición a `/destinos/1`

### **Usuario hace clic en "Ver Detalles" de Volcán de Izalco**

```
Usuario → Click en botón → Navegador envía GET /destinos/2
```

### **1. Router recibe la petición**
```
Petición: GET /destinos/2
Ruta coincidente: Route::get('/destinos/{id}', [DestinoController::class, 'show'])
Parámetro extraído: id = 2
Acción: DestinoController@show(2)
```

### **2. Controlador procesa**
```php
public function show($id)  // $id = 2
{
    // Obtiene datos
    $destinos = $this->obtenerDestinos();
    // [
    //   {id: 1, nombre: "El Rosario", ...},
    //   {id: 2, nombre: "Volcán de Izalco", ...},
    //   ...
    // ]
    
    // Busca el destino con id=2
    $destino = collect($destinos)->firstWhere('id', 2);
    // {
    //   "id": 2,
    //   "nombre": "Volcán de Izalco",
    //   "departamento": "Sonsonate",
    //   "categoria": "Volcán",
    //   ...
    // }
    
    // Envía datos a la vista
    return view('destinos.show', ['destino' => $destino]);
}
```

### **3. Vista renderiza**
```blade
<div class="detalle-titulo">
    {{ $destino['nombre'] }}  <!-- "Volcán de Izalco" -->
</div>

<div class="info-value">
    {{ $destino['departamento'] }}  <!-- "Sonsonate" -->
</div>

<div class="info-value">
    {{ $destino['precio'] }}  <!-- "$40.00" -->
</div>
```

### **4. Respuesta al navegador**
```html
<!DOCTYPE html>
<html>
<head>
    <title>Volcán de Izalco</title>
</head>
<body>
    <h1>🌴 Destinos Turísticos de El Salvador 🌴</h1>
    <div class="detalle-titulo">Volcán de Izalco</div>
    <div class="info-value">Sonsonate</div>
    <div class="info-value">$40.00</div>
    ...
</body>
</html>
```

---

## 📊 Responsabilidades de Cada Capa

| Capa | Archivo | Responsabilidad | Ejemplo |
|------|---------|-----------------|---------|
| **Route** | `routes/web.php` | Mapear URLs a Controladores | `Route::get('/destinos/{id}', [...])` |
| **Controller** | `DestinoController.php` | Lógica de negocio | Obtener datos, validar, procesar |
| **Model/Datos** | `destinos.json` | Almacenar información | Array de objetos destino |
| **View** | `index.blade.php` | Renderizar HTML | Mostrar destinos en tarjetas |

---

## ✨ Ventajas del Patrón MVC en Laravel

1. **Separación de responsabilidades:** Cada componente tiene una función clara
2. **Reutilización:** Las vistas se reutilizan con diferentes datos
3. **Testabilidad:** Cada capa se puede probar independientemente
4. **Mantenibilidad:** Cambios aislados sin afectar otras capas
5. **Escalabilidad:** Fácil agregar nuevas funcionalidades

---

## 🔐 Flujo de Seguridad

```
Petición → Validación de Ruta
         → Middleware (CSRF, Auth, Rate Limit)
         → Validación en Controlador
         → Procesamiento de Datos
         → Respuesta Segura
```

---

**Compilado por:** Carlos Emanuel Dubón Cornejo  
**Bootcamp:** Full Stack Junior - Módulo 3  
**Fecha:** Noviembre 2024
