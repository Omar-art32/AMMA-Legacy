<?php
declare(strict_types=1);

/**
 * conexion.php — Conexión MariaDB modernizada para PHP 8.3.
 *
 * /**
 * Conexión a la base de datos.
 *
 * Adaptada para PHP 8.3 utilizando el manejo de excepciones de mysqli,
 * configuración del juego de caracteres UTF-8 y credenciales obtenidas
 * desde la configuración del entorno.
 *
 */

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$DB_HOST = getenv('DB_HOST') ?: 'mariadb';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: 'root';
$DB_NAME = getenv('DB_NAME') ?: 'amma';

try {
    // la siguiente linea comentada ya estaba
    //$conexion = new mysqli("localhost","root","","amma");
    $conexion = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    $conexion->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    // No exponer el detalle del error al cliente; registrarlo en el log.
    error_log('Error de conexión a BD: ' . $e->getMessage());
    http_response_code(500);
    die('No fue posible conectar con la base de datos.');
}
