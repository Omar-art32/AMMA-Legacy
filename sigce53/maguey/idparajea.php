<?php
/**
 * idparajea.php — PHP 8.3
 * Genera el siguiente ID de predio. Deja $id definido para el archivo
 * que lo incluya (usa return en vez de echo).
 *
 * Cambios vs 5.6:
 *  - $_POST['tipo'] con ?? (no se usa en la consulta pero existía)
 *  - include → require_once con __DIR__
 *  - try/catch
 *  - Nota: es un subconjunto de idparaje.php (modo P sin JSON).
 *    Podría unificarse, pero se conserva por compatibilidad.
 */
declare(strict_types=1);

require_once __DIR__ . '/../common/conexion.php';

$tipo = (string)($_POST['tipo'] ?? '');

try {
    $consulta = "SELECT SUBSTR(id_paraje, 2, LENGTH(id_paraje)) AS id
                 FROM paraje
                 WHERE id = (SELECT MAX(id) FROM paraje)
                 ORDER BY id DESC";
    $result = $conexion->query($consulta);

    $id = 'P1';
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ((int)$row['id'] > 0) {
            $id = 'P' . ((int)$row['id'] + 1);
        }
    }
} catch (mysqli_sql_exception $e) {
    error_log('[idparajea.php] ' . $e->getMessage());
    $id = 'P1';
} finally {
    $conexion->close();
}

return $id;