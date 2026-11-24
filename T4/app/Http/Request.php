<?php
/**
 * Simple View Helper - Reemplaza las funciones de Blade para Docker
 */

class View {
    private static $data = [];
    private static $layout = null;

    public static function render($viewPath, $data = []) {
        self::$data = $data;
        $file = RESOURCES_PATH . '/views/' . str_replace('.', '/', $viewPath) . '.blade.php';
        
        if (!file_exists($file)) {
            throw new Exception("Vista no encontrada: $file");
        }

        // Extraer variables disponibles en la vista
        extract($data);
        
        // Iniciar buffer de salida
        ob_start();
        require $file;
        $content = ob_get_clean();
        
        return $content;
    }

    public static function get($key, $default = null) {
        return self::$data[$key] ?? $default;
    }

    public static function exists($key) {
        return isset(self::$data[$key]);
    }

    public static function has($key) {
        return isset(self::$data[$key]);
    }
}

// Funciones helper globales
if (!function_exists('view')) {
    function view($name, $data = []) {
        return View::render($name, $data);
    }
}

if (!function_exists('abort')) {
    function abort($code, $message = '') {
        http_response_code($code);
        if ($code == 404) {
            echo "<!DOCTYPE html>
            <html>
            <head><title>404 - No encontrado</title></head>
            <body style='font-family: Arial; text-align: center; margin-top: 50px;'>
                <h1>404 - Página no encontrada</h1>
                <p>$message</p>
            </body>
            </html>";
        }
        exit;
    }
}

if (!function_exists('route')) {
    function route($name, $params = []) {
        $routes = [
            'destinos.index' => '/',
            'destinos.show' => '/destinos/{id}',
            'contacto.formulario' => '/contacto',
            'contacto.enviar' => '/contacto',
        ];

        $url = $routes[$name] ?? '/';

        // Reemplazar parámetros
        foreach ((array)$params as $key => $value) {
            $url = str_replace('{' . $key . '}', $value, $url);
            $url = str_replace('{id}', $value, $url);
        }

        return $url;
    }
}

if (!function_exists('redirect')) {
    function redirect($url) {
        header("Location: $url");
        exit;
    }
}

if (!function_exists('session')) {
    function session($key = null, $default = null) {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        if ($key === null) {
            return $_SESSION;
        }

        return $_SESSION[$key] ?? $default;
    }
}

if (!function_exists('old')) {
    function old($key, $default = '') {
        return $_POST[$key] ?? $default;
    }
}

if (!function_exists('errors')) {
    function errors($key = null) {
        if (!isset($_SESSION['errors'])) {
            return [];
        }

        if ($key === null) {
            return $_SESSION['errors'];
        }

        return $_SESSION['errors'][$key] ?? [];
    }
}
