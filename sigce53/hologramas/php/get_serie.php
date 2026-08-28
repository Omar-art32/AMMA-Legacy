<?php
/**
 * get_serie.php — PHP 8.3
 * Devuelve la serie asignada a una marca de un cliente.
 * OJO: responde texto plano (no JSON) — el JS lo asigna directo a un
 * input con $(...).val(response). Se conserva ese contrato.
 *
 * Cambios vs 5.6:
 *  - utf8_decode() eliminado (entrada ya viene en UTF-8)
 *  - SQL concatenado → sentencia preparada
 *  - include → require_once con __DIR__
 *  - try/catch con error_log
 *  - Nota: la rama de "sin resultados" del original hacía echo del SQL
 *    (fuga de información); se sustituye por cadena vacía.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../common/conexion.php';

$client = (string)($_POST['cliente'] ?? '');
$marca  = (string)($_POST['marca'] ?? '');

try {
    $stmt = $conexion->prepare(
        "SELECT serie FROM marcas WHERE no_cliente = ? AND cve_marca = ?"
    );
    $stmt->bind_param('ss', $client, $marca);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_row();
        echo trim((string)$row[0]);
    } else {
        echo '';
    }
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    error_log('[get_serie.php] ' . $e->getMessage());
    http_response_code(500);
    echo '';
} finally {
    $conexion->close();
}
