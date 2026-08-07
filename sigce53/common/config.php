<?php
declare(strict_types=1);

/**
 * config.php — Configuración centralizada de rutas del proyecto.
 *
 * 
 * Configuración general del proyecto.
 *
 * Define la ruta base de la aplicación para evitar valores
 * fijos en distintos archivos y facilitar cambios de despliegue.
 *
 * Reemplaza y unifica al antiguo cfg_server.php.
 */

// Ruta base de la aplicación bajo el document root (sin barra final).
if (!defined('APP_BASE_PATH')) {
    define('APP_BASE_PATH', '/sigce53');
}

// Host del servidor tal como lo ve el navegador (dominio:puerto).
$svr_dir = ($_SERVER['HTTP_HOST'] ?? 'localhost') . APP_BASE_PATH;

// Protocolo actual (http/https) detectado de forma robusta.
$protocolo_actual = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['SERVER_PORT'] ?? '') == 443)
) ? 'https:' : 'http:';
