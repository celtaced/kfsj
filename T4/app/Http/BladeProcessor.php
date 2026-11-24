<?php

namespace App\Http;

/**
 * Simple Blade Template Processor
 * Procesa sintaxis básica de Blade sin Laravel completo
 */
class BladeProcessor
{
    private static $yields = [];
    private static $sections = [];
    private static $currentSection = null;
    private static $data = [];

    public static function render($viewPath, $data = [])
    {
        self::$data = $data;
        self::$yields = [];
        self::$sections = [];

        $file = RESOURCES_PATH . '/views/' . str_replace('.', '/', $viewPath) . '.blade.php';

        if (!file_exists($file)) {
            throw new Exception("Vista no encontrada: $file");
        }

        // Leer el archivo
        $content = file_get_contents($file);

        // Procesar @extends
        if (preg_match("/@extends\(['\"]([^'\"]+)['\"]\)/", $content, $matches)) {
            $layoutPath = $matches[1];
            $content = preg_replace("/@extends\(['\"]([^'\"]+)['\"]\)/", '', $content);

            // Procesar secciones
            preg_match_all("/@section\(['\"]([^'\"]+)['\"]\)(.*?)@endsection/s", $content, $sections);

            for ($i = 0; $i < count($sections[1]); $i++) {
                self::$sections[$sections[1][$i]] = trim($sections[2][$i]);
            }

            // Renderizar layout
            return self::processLayout($layoutPath);
        } else {
            // Renderizar como vista simple
            return self::processView($content);
        }
    }

    private static function processLayout($layoutPath)
    {
        $file = RESOURCES_PATH . '/views/' . str_replace('.', '/', $layoutPath) . '.blade.php';
        $content = file_get_contents($file);

        // Reemplazar @yield con contenido de secciones
        $content = preg_replace_callback(
            "/@yield\(['\"]([^'\"]+)['\"](.*?)\)/",
            function ($matches) {
                $sectionName = $matches[1];
                return isset(self::$sections[$sectionName]) ? self::processView(self::$sections[$sectionName]) : '';
            },
            $content
        );

        return self::processView($content);
    }

    private static function processView($content)
    {
        // Procesar @if, @else, @endif
        $content = preg_replace_callback(
            "/@if\s*\((.*?)\)(.*?)(?:@else(.*?))?@endif/s",
            function ($matches) {
                $condition = $matches[1];
                $bodyTrue = $matches[2];
                $bodyFalse = isset($matches[3]) ? $matches[3] : '';

                // Evaluar condición simple
                if (self::evaluateCondition($condition)) {
                    return $bodyTrue;
                }
                return $bodyFalse;
            },
            $content
        );

        // Procesar @foreach
        $content = preg_replace_callback(
            "/@foreach\s*\((.*?)\s+as\s+\$(\w+)\)(.*?)@endforeach/s",
            function ($matches) {
                $arrayExpr = trim($matches[1]);
                $varName = $matches[2];
                $body = $matches[3];

                // Obtener array
                $array = self::getVariable($arrayExpr);

                if (!is_array($array)) {
                    return '';
                }

                $output = '';
                foreach ($array as $item) {
                    $html = $body;
                    
                    // Procesar la variable en el loop
                    if (is_array($item)) {
                        // Reemplazar {{ $var['key'] }} en el body
                        foreach ($item as $key => $value) {
                            $html = str_replace(
                                "{{ \$" . $varName . "['" . $key . "'] }}",
                                htmlspecialchars((string)$value),
                                $html
                            );
                        }
                        // Mantener en contexto para route() y otros helpers
                        self::$data['loop_item'] = $item;
                        self::$data[$varName] = $item;
                    } else {
                        $html = str_replace('{{ $' . $varName . ' }}', htmlspecialchars((string)$item), $html);
                    }
                    
                    $output .= $html;
                }

                return $output;
            },
            $content
        );

        // Procesar route() helper ANTES de procesar {{ }} variables
        $content = preg_replace_callback(
            "/route\('([^']+)'(?:\s*,\s*\[(.*?)\])?\)/",
            function ($matches) {
                $routeName = $matches[1];
                $params = isset($matches[2]) && !empty($matches[2]) ? $matches[2] : '';

                if (!empty($params)) {
                    // Limpiar y procesar parámetros
                    if (preg_match("/\$(\w+)\['id'\]/", $params, $m)) {
                        $value = self::getVariable($m[1] . "['id']");
                        return self::generateRoute($routeName, $value);
                    } elseif (preg_match("/(\d+)/", $params, $m)) {
                        return self::generateRoute($routeName, $m[1]);
                    }
                }

                return self::generateRoute($routeName);
            },
            $content
        );

        // Procesar {{ }} variables
        $content = preg_replace_callback(
            "/\{\{\s*\\\$([^\}]+)\s*\}\}/",
            function ($matches) {
                $expr = $matches[1];
                $value = self::getVariable($expr);
                return htmlspecialchars((string)$value);
            },
            $content
        );

        // Procesar session() helper
        if (preg_match("/session\('success'\)/", $content)) {
            $success = isset($_SESSION['success']) ? htmlspecialchars($_SESSION['success']) : '';
            $content = str_replace("session('success')", $success, $content);
            // Limpiar sesión después de leerla
            if (!empty($_SESSION['success'])) {
                unset($_SESSION['success']);
            }
        }

        return $content;
    }

    private static function getVariable($expr)
    {
        $expr = trim($expr);

        // Variables simples: $var
        if (preg_match("/^\\\?\\$(\w+)$/", $expr, $m)) {
            return self::$data[$m[1]] ?? '';
        }

        // Array access: $var['key'] o $var['key']['subkey']
        if (preg_match("/^\\\?\\\$(\w+)\['(\w+)'\]$/", $expr, $m)) {
            $arr = self::$data[$m[1]] ?? [];
            return $arr[$m[2]] ?? '';
        }

        return '';
    }

    private static function evaluateCondition($condition)
    {
        $condition = trim($condition);

        // Evaluar condiciones simples: empty($var), isset($var), etc.
        if (preg_match("/^empty\(\\\?\\\$(\w+)\)$/", $condition, $m)) {
            return empty(self::$data[$m[1]] ?? '');
        }

        if (preg_match("/^!empty\(\\\?\\\$(\w+)\)$/", $condition, $m)) {
            return !empty(self::$data[$m[1]] ?? '');
        }

        return false;
    }

    private static function generateRoute($name, $param = null)
    {
        $routes = [
            'destinos.index' => '/',
            'destinos.show' => '/destinos/{id}',
            'contacto.formulario' => '/contacto',
            'contacto.enviar' => '/contacto',
        ];

        $url = $routes[$name] ?? '/';

        if ($param !== null && $param !== '') {
            $url = str_replace('{id}', (int)$param, $url);
        }

        return $url;
    }
}
