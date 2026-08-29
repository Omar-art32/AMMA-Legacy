<?php
declare(strict_types=1);

/**
 * cfg_server.php — Puente de compatibilidad.
 *
 * Este archivo se conserva SOLO para no romper el código legado que todavía
 * hace require_once('common/cfg_server.php') (por ejemplo, el index.php de la
 * raíz y otros módulos aún sin migrar).
 *
 * La configuración real vive ahora en config.php, que centraliza la ruta base
 * en APP_BASE_PATH y define $svr_dir. Aquí solo se reenvía a ese archivo.
 *
 * Cuando TODOS los archivos que usaban cfg_server.php se hayan migrado para
 * incluir config.php directamente, este puente puede eliminarse.
 */

require_once __DIR__ . '/config.php';

// config.php ya deja definidas las variables que el código legado espera:
//   $svr_dir          = HTTP_HOST . APP_BASE_PATH   (p.ej. "localhost/sigce53")
//   $protocolo_actual = "http:" | "https:"


/*
 * Verificamos que config.php haya definido correctamente
 * las variables que necesita el código legado.
 */
if (!isset($svr_dir) || trim($svr_dir) === '') {
    $svr_dir = ($_SERVER['HTTP_HOST'] ?? 'localhost') . APP_BASE_PATH;
}

if (!isset($protocolo_actual) || trim($protocolo_actual) === '') {
    $protocolo_actual = 'http:';
}