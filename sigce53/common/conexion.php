<?php
declare(strict_types=1);

/**
 * conexion.php — Conexión MariaDB modernizada para PHP 8.3.
 *
 * Cambios respecto al original:
 *  - mysqli_report explícito: en 8.1+ el constructor de mysqli lanza excepción
 *    al fallar la conexión, así que el antiguo `if ($conexion->connect_errno > 0)`
 *    con die() ya nunca se ejecutaba. Ahora se captura la excepción real.
 *  - Se fija el charset a utf8mb4 una sola vez, aquí (antes cada archivo lo
 *    repetía, o lo omitía).
 *  - Credenciales leídas de variables de entorno con respaldo a los valores
 *    del docker-compose, para no dejar la contraseña incrustada en el código.
 */

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$DB_HOST = getenv('DB_HOST') ?: 'mariadb';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: 'root';
$DB_NAME = getenv('DB_NAME') ?: 'amma';

try {
    $conexion = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    $conexion->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    // No exponer el detalle del error al cliente; registrarlo en el log.
    error_log('Error de conexión a BD: ' . $e->getMessage());
    http_response_code(500);
    die('No fue posible conectar con la base de datos.');
}
