<?php

namespace App\Http;

/**
 * Simple View Renderer para trabajar sin Blade Engine
 */
class ViewRenderer
{
    private static $data = [];

    public static function render($viewPath, $data = [])
    {
        self::$data = $data;

        if ($viewPath === 'destinos.index') {
            return self::renderIndex($data);
        } elseif ($viewPath === 'destinos.show') {
            return self::renderShow($data);
        } elseif ($viewPath === 'destinos.contacto') {
            return self::renderContacto($data);
        } else {
            throw new \Exception("Vista no encontrada: $viewPath");
        }
    }

    private static function renderIndex($data)
    {
        $destinos = $data['destinos'] ?? [];
        $contenido = '<h2>🗺️ Nuestros Destinos</h2>';

        if (empty($destinos)) {
            $contenido .= '<p>No hay destinos disponibles en este momento.</p>';
        } else {
            $contenido .= '<div class="grid">';
            foreach ($destinos as $destino) {
                $id = htmlspecialchars($destino['id']);
                $nombre = htmlspecialchars($destino['nombre']);
                $departamento = htmlspecialchars($destino['departamento']);
                $categoria = htmlspecialchars($destino['categoria']);
                $precio = htmlspecialchars($destino['precio']);

                $contenido .= <<<HTML
                <div class="card">
                    <div class="card-image">🌟</div>
                    <div class="card-body">
                        <div class="card-title">$nombre</div>
                        <div class="card-info">
                            <strong>Departamento:</strong> $departamento
                        </div>
                        <div class="card-info">
                            <strong>Categoría:</strong> $categoria
                        </div>
                        <div class="card-price">$precio</div>
                        <a href="/destinos/$id" class="btn">Ver Detalles</a>
                    </div>
                </div>
                HTML;
            }
            $contenido .= '</div>';
        }

        return self::renderLayout($contenido, 'Destinos Turísticos');
    }

    private static function renderShow($data)
    {
        $destino = $data['destino'] ?? [];

        if (empty($destino)) {
            return '<p>Destino no encontrado</p>';
        }

        $nombre = htmlspecialchars($destino['nombre']);
        $departamento = htmlspecialchars($destino['departamento']);
        $categoria = htmlspecialchars($destino['categoria']);
        $precio = htmlspecialchars($destino['precio']);
        $descripcion = htmlspecialchars($destino['descripcion']);
        $mejoresMeses = htmlspecialchars($destino['mejores_meses']);

        $contenido = <<<HTML
        <div class="detail-container">
            <h2>$nombre</h2>
            
            <div class="detail-item">
                <span class="detail-label">Departamento:</span>
                <div class="detail-value">$departamento</div>
            </div>
            
            <div class="detail-item">
                <span class="detail-label">Categoría:</span>
                <div class="detail-value">$categoria</div>
            </div>
            
            <div class="detail-item">
                <span class="detail-label">Precio:</span>
                <div class="detail-value">$precio</div>
            </div>
            
            <div class="detail-item">
                <span class="detail-label">Descripción:</span>
                <div class="detail-value">$descripcion</div>
            </div>
            
            <div class="detail-item">
                <span class="detail-label">Mejores meses para visitar:</span>
                <div class="detail-value">$mejoresMeses</div>
            </div>
            
            <div class="detail-item">
                <span class="detail-label">Características:</span>
                <ul class="caracteristicas-list">
        HTML;

        foreach ($destino['caracteristicas'] as $caracteristica) {
            $carac = htmlspecialchars($caracteristica);
            $contenido .= "<li>$carac</li>";
        }

        $contenido .= <<<HTML
                </ul>
            </div>
            
            <a href="/" class="btn-back">Volver a Destinos</a>
        </div>
        HTML;

        return self::renderLayout($contenido, $nombre);
    }

    private static function renderContacto($data)
    {
        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        
        $contenido = '<h2>Formulario de Contacto</h2>';
        $contenido .= '<p>¿Tiene preguntas o comentarios? Nos gustaría conocer su opinión.</p>';

        $contenido .= '<form method="POST" action="/contacto" style="max-width: 500px; margin: 20px auto;">';

        // Campo Nombre
        $nombreValue = htmlspecialchars($old['nombre'] ?? '');
        $nombreError = isset($errors['nombre']) ? '<div class="error">' . htmlspecialchars($errors['nombre']) . '</div>' : '';
        $contenido .= <<<HTML
        <div class="form-group">
            <label for="nombre">Nombre *</label>
            <input type="text" id="nombre" name="nombre" value="$nombreValue" required>
            $nombreError
        </div>
        HTML;

        // Campo Email
        $emailValue = htmlspecialchars($old['email'] ?? '');
        $emailError = isset($errors['email']) ? '<div class="error">' . htmlspecialchars($errors['email']) . '</div>' : '';
        $contenido .= <<<HTML
        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" value="$emailValue" required>
            $emailError
        </div>
        HTML;

        // Campo Destino
        $destinoValue = htmlspecialchars($old['destino'] ?? '');
        $destinoError = isset($errors['destino']) ? '<div class="error">' . htmlspecialchars($errors['destino']) . '</div>' : '';
        
        $option1 = ('Suchitoto' === ($old['destino'] ?? '') ? 'selected' : '');
        $option2 = ('Playa El Tunco' === ($old['destino'] ?? '') ? 'selected' : '');
        $option3 = ('Volcán de Santa Ana' === ($old['destino'] ?? '') ? 'selected' : '');
        $option4 = ('Parque Nacional Montecristo' === ($old['destino'] ?? '') ? 'selected' : '');
        $option5 = ('Playa El Espino' === ($old['destino'] ?? '') ? 'selected' : '');
        $option6 = ('Joya de Cerén' === ($old['destino'] ?? '') ? 'selected' : '');
        
        $contenido .= <<<HTML
        <div class="form-group">
            <label for="destino">Destino de Interés *</label>
            <select id="destino" name="destino" required>
                <option value="">Selecciona un destino</option>
                <option value="Suchitoto" $option1>Suchitoto</option>
                <option value="Playa El Tunco" $option2>Playa El Tunco</option>
                <option value="Volcán de Santa Ana" $option3>Volcán de Santa Ana</option>
                <option value="Parque Nacional Montecristo" $option4>Parque Nacional Montecristo</option>
                <option value="Playa El Espino" $option5>Playa El Espino</option>
                <option value="Joya de Cerén" $option6>Joya de Cerén</option>
            </select>
            $destinoError
        </div>
        HTML;

        // Campo Mensaje
        $mensajeValue = htmlspecialchars($old['mensaje'] ?? '');
        $mensajeError = isset($errors['mensaje']) ? '<div class="error">' . htmlspecialchars($errors['mensaje']) . '</div>' : '';
        $contenido .= <<<HTML
        <div class="form-group">
            <label for="mensaje">Mensaje *</label>
            <textarea id="mensaje" name="mensaje" rows="5" required>$mensajeValue</textarea>
            $mensajeError
        </div>
        
        <button type="submit" class="form-btn">Enviar Mensaje</button>
        </form>
        HTML;

        // Limpiar sesión de errores
        unset($_SESSION['errors']);
        unset($_SESSION['old']);

        return self::renderLayout($contenido, 'Contacto');
    }

    private static function renderLayout($contenido, $titulo = 'Destinos Turísticos')
    {
        $htmlTitulo = htmlspecialchars($titulo);
        
        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>$htmlTitulo</title>
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
        
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .card-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3em;
            color: white;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .card-title {
            font-size: 1.3em;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        
        .card-info {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 8px;
        }
        
        .card-price {
            font-size: 1.1em;
            color: #667eea;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
            font-size: 0.95em;
            border: none;
            cursor: pointer;
        }
        
        .btn:hover {
            background: #764ba2;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }

        .btn-back {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn-back:hover {
            background: #764ba2;
        }

        .detail-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .detail-item {
            margin: 15px 0;
            font-size: 1.05em;
        }

        .detail-label {
            font-weight: bold;
            color: #667eea;
        }

        .detail-value {
            color: #333;
            margin-top: 5px;
            padding-left: 15px;
            border-left: 3px solid #667eea;
        }

        .caracteristicas-list {
            list-style: none;
            padding-left: 0;
        }

        .caracteristicas-list li {
            padding: 8px 0;
            padding-left: 25px;
            position: relative;
            color: #333;
        }

        .caracteristicas-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #667eea;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: inherit;
            font-size: 1em;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }

        .error {
            color: #dc3545;
            font-size: 0.9em;
            margin-top: 5px;
        }

        .form-btn {
            background: #667eea;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
            transition: background 0.3s;
        }

        .form-btn:hover {
            background: #764ba2;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        p {
            line-height: 1.6;
            color: #333;
        }

        form p {
            margin-bottom: 20px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🌴 Destinos Turísticos de El Salvador 🌴</h1>
            <p>Explora la belleza natural e histórica de nuestro país</p>
        </header>
        
        <nav>
            <a href="/">Inicio</a>
            <a href="/contacto">Contacto</a>
        </nav>
        
        <div class="content">
            $contenido
        </div>
        
        <footer>
            <p>&copy; 2024 Destinos Turísticos de El Salvador. Desarrollado con Laravel MVC.</p>
        </footer>
    </div>
</body>
</html>
HTML;
    }
}
