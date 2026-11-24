<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Destinos Turísticos de El Salvador')</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }
        
        header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        header p {
            font-size: 1.1em;
            opacity: 0.9;
        }
        
        nav {
            background: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            gap: 20px;
            justify-content: center;
        }
        
        nav a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        nav a:hover {
            color: #764ba2;
        }
        
        .content {
            padding: 40px 20px;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #dee2e6;
            color: #666;
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="container">
        <header>
            <h1>🌴 Destinos Turísticos de El Salvador 🌴</h1>
            <p>Explora la belleza natural e histórica de nuestro país</p>
        </header>
        
        <nav>
            <a href="{{ route('destinos.index') }}">Inicio</a>
            <a href="{{ route('contacto.formulario') }}">Contacto</a>
        </nav>
        
        <div class="content">
            @if(session('success'))
                <div class="alert">
                    {{ session('success') }}
                </div>
            @endif
            
            @yield('contenido')
        </div>
        
        <footer>
            <p>&copy; 2024 Destinos Turísticos de El Salvador. Desarrollado con Laravel MVC.</p>
        </footer>
    </div>
</body>
</html>
