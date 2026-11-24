<?php

namespace App\Http\Controllers;

use App\Http\ViewRenderer;

/**
 * Controlador simplificado de Destinos para funcionar sin Laravel completo
 * Versión para Docker - Sin Composer
 */
class DestinoController
{
    protected $rutaJson;

    public function __construct()
    {
        // Obtener ruta del archivo JSON usando ruta absoluta
        $this->rutaJson = '/var/www/html/resources/data/destinos.json';
    }

    /**
     * Obtiene todos los destinos desde el JSON
     */
    private function obtenerDestinos()
    {
        if (!file_exists($this->rutaJson)) {
            error_log("Archivo no encontrado: " . $this->rutaJson);
            return [];
        }
        $contenido = file_get_contents($this->rutaJson);
        $destinos = json_decode($contenido, true) ?? [];
        error_log("Destinos cargados: " . count($destinos));
        return $destinos;
    }

    /**
     * Muestra lista de todos los destinos
     */
    public function index()
    {
        $destinos = $this->obtenerDestinos();
        $html = ViewRenderer::render('destinos.index', ['destinos' => $destinos]);
        echo $html;
    }

    /**
     * Muestra detalles de un destino específico
     */
    public function show($id)
    {
        error_log("show() called with ID: $id");
        $destinos = $this->obtenerDestinos();
        error_log("Destinos count: " . count($destinos));
        
        $destino = null;

        foreach ($destinos as $d) {
            if ((int)$d['id'] === (int)$id) {
                $destino = $d;
                break;
            }
        }

        if (!$destino) {
            http_response_code(404);
            echo "404 - Destino no encontrado";
            exit;
        }

        $html = ViewRenderer::render('destinos.show', ['destino' => $destino]);
        echo $html;
    }

    /**
     * Muestra formulario de contacto
     */
    public function formulario()
    {
        $html = ViewRenderer::render('destinos.contacto', []);
        echo $html;
    }

    /**
     * Procesa el envío del formulario de contacto
     */
    public function enviarContacto($datos)
    {
        // Validación simple
        $errores = [];

        if (empty($datos['nombre'])) {
            $errores['nombre'] = 'El nombre es requerido';
        }
        if (empty($datos['email']) || !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores['email'] = 'El email es requerido y debe ser válido';
        }
        if (empty($datos['destino'])) {
            $errores['destino'] = 'El destino es requerido';
        }
        if (empty($datos['mensaje'])) {
            $errores['mensaje'] = 'El mensaje es requerido';
        }

        // Si hay errores, mostrar formulario con errores
        if (!empty($errores)) {
            $_SESSION['errors'] = $errores;
            $_SESSION['old'] = $datos;
            $html = ViewRenderer::render('destinos.contacto', [
                'errors' => $errores,
                'old' => $datos
            ]);
            echo $html;
            return;
        }

        // Guardar mensaje (aquí se podría guardar en BD o enviar email)
        $_SESSION['success'] = 'Tu mensaje ha sido enviado correctamente. Nos contactaremos pronto.';
        
        // Redirigir a inicio
        header('Location: /');
        exit;
    }
}
