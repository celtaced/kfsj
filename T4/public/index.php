<?php
/**
 * Laravel Application Entry Point
 * Simplified version without Composer for Docker education purposes
 */

define('LARAVEL_START', microtime(true));

// Set the base path
$basePath = __DIR__ . '/..';

// Initialize application paths
define('BASE_PATH', $basePath);
define('APP_PATH', BASE_PATH . '/app');
define('RESOURCES_PATH', BASE_PATH . '/resources');
define('PUBLIC_PATH', __DIR__);

// Start session
session_start();

// Require helpers
require APP_PATH . '/Http/ViewRenderer.php';
require APP_PATH . '/Http/Request.php';

// Require the controller
require APP_PATH . '/Http/Controllers/DestinoController.php';
require APP_PATH . '/Http/Kernel.php';

use App\Http\Controllers\DestinoController;

// Simple routing system
// Try different server variables depending on Apache configuration
$uri = $_SERVER['REQUEST_URI'] ?? '';
if (empty($uri) || $uri === '/index.php') {
    $uri = $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_PATH'] ?? '';
}

$uri = parse_url($uri, PHP_URL_PATH);
$uri = trim($uri, '/');

// DEBUG: Log the URI being processed
error_log("Processing URI: $uri");

// Route the request
$controller = new DestinoController();
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($uri === '' || $uri === 'destinos') {
        // GET / or GET /destinos
        if ($method === 'GET') {
            $controller->index();
        }
    } elseif (preg_match('/^destinos\/(\d+)$/', $uri, $matches)) {
        // GET /destinos/{id}
        if ($method === 'GET') {
            error_log("Calling show with ID: " . $matches[1]);
            $controller->show($matches[1]);
        }
    } elseif ($uri === 'contacto') {
        // GET /contacto or POST /contacto
        if ($method === 'GET') {
            $controller->formulario();
        } elseif ($method === 'POST') {
            $controller->enviarContacto($_POST);
        }
    } else {
        // 404 Not Found
        header("HTTP/1.0 404 Not Found");
        echo "<!DOCTYPE html>
        <html>
        <head><title>404 - No encontrado</title></head>
        <body style='font-family: Arial; text-align: center; margin-top: 50px;'>
            <h1>404 - Página no encontrada</h1>
            <p>La página que buscas no existe.</p>
            <p style='color: #999; font-size: 0.9em;'>URI: " . htmlspecialchars($uri) . "</p>
            <p style='color: #999; font-size: 0.9em;'>REQUEST_URI: " . htmlspecialchars($_SERVER['REQUEST_URI']) . "</p>
            <a href='/'>Volver al inicio</a>
        </body>
        </html>";
    }
} catch (Exception $e) {
    header("HTTP/1.0 500 Internal Server Error");
    echo "<!DOCTYPE html>
    <html>
    <head><title>Error</title></head>
    <body style='font-family: Arial; text-align: center; margin-top: 50px;'>
        <h1>Error</h1>
        <p>" . htmlspecialchars($e->getMessage()) . "</p>
        <p style='color: #999; font-size: 0.9em;'>" . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>
        <a href='/'>Volver al inicio</a>
    </body>
    </html>";
}


