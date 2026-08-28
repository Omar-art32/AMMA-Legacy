<?php
/**
 * idparaje_vivero.php — PHP 8.3
 * Genera el siguiente ID consecutivo de vivero (V).
 * Imprime el ID directo (no JSON) — usado inline en otra página.
 *
 * Cambios vs 5.6:
 *  - include → require_once con __DIR__
 *  - try/catch
 *  - Nota: este archivo es un subconjunto de idparaje.php (modo V).
 *    Podría eliminarse y redirigir al otro, pero se conserva por
 *    compatibilidad con los archivos que lo incluyen.
 */
declare(strict_types=1);

require_once __DIR__ . '/../common/conexion.php';

try {
    $consulta = "SELECT SUBSTR(id_paraje, 2, LENGTH(id_paraje)) AS id
                 FROM paraje_vivero
                 WHERE id = (SELECT MAX(id) FROM paraje_vivero)
                 ORDER BY id DESC";
    $result = $conexion->query($consulta);

    $id = 'V1';
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ((int)$row['id'] > 0) {
            $id = 'V' . ((int)$row['id'] + 1);
        }
    }
    // No imprime nada — $id se usa por el archivo que lo incluye
} catch (mysqli_sql_exception $e) {
    error_log('[idparaje_vivero.php] ' . $e->getMessage());
    $id = 'V1';
} finally {
    $conexion->close();
}