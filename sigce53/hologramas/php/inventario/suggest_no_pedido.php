<?php
/**
 * suggest_no_pedido.php — PHP 8.3
 * Autocomplete de No. de Pedido sobre h_pedidos.
 * Devuelve JSON [no_pedido, no_pedido, ...].
 *
 * Cambios vs 5.6:
 *  - BUG CORREGIDO: no_pedido es una columna entera; mysqli la entrega
 *    como int nativo de PHP, así que json_encode() la mandaba sin
 *    comillas (ej. [310] en vez de ["310"]). jQuery UI Autocomplete
 *    solo auto-normaliza items que sean STRING; con un número puro,
 *    la sugerencia se crea vacía (el cuadro crecía pero no se veían
 *    los valores). Se castea a (string) antes de devolver, sin tocar
 *    qué se busca ni qué se filtra.
 *  - SQL concatenado ($busca directo) → sentencia preparada
 *  - include → require_once con __DIR__
 *  - try/catch con error_log
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../common/conexion.php';


if (!isset($_GET['term'])) {
    echo json_encode([]);
    exit;
}

$busca = '%' . (string)$_GET['term'] . '%';
$return_arr = [];

try {
    $stmt = $conexion->prepare(
        "SELECT DISTINCT(no_pedido) FROM h_pedidos WHERE no_pedido LIKE ? LIMIT 10"
    );
    $stmt->bind_param('s', $busca);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $return_arr[] = (string)$row['no_pedido'];
    }
    $stmt->close();

    echo json_encode($return_arr);
} catch (mysqli_sql_exception $e) {
    error_log('[suggest_no_pedido.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error al consultar pedidos']);
} finally {
    $conexion->close();
}