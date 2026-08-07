<?php
declare(strict_types=1);
/**
 * Cierre de sesión del sistema.
 *
 * Se actualizó la redirección al finalizar la sesión para utilizar una ruta
 * relativa, evitando dependencias con el nombre de la carpeta del proyecto
 * y mejorando la compatibilidad entre distintos entornos de despliegue.
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
