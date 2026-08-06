<?php
declare(strict_types=1);

/**
 * cerrar.php — Cierre de sesión.
 *
 * CORRECCIÓN DE RUTA: el original redirigía a '/sigce/acceso/login.php'
 * (carpeta inexistente en este despliegue, que vive en /sigce53), por lo que
 * cada logout terminaba en un 404. Ahora se usa una ruta RELATIVA a login.php:
 * como cerrar.php y login.php están en la misma carpeta, el redirect funciona
 * sin importar cómo se llame la carpeta del proyecto ni dónde se despliegue.
 */

session_start();

$d_s = $_GET['d_s'] ?? '';

// Elimina solo el namespace de sesión de este usuario y destruye la sesión.
if ($d_s !== '' && isset($_SESSION[$d_s])) {
    unset($_SESSION[$d_s]);
}
session_destroy();

// Ruta relativa: resuelve a la carpeta actual (acceso/), sin hardcodear.
header('Location: login.php');
exit;
