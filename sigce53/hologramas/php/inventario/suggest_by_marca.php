<?php
/**
 * suggest_by_marca.php — PHP 8.3
 * Autocomplete de marca sobre h_pedidos (solicitados a proveedor).
 * Devuelve JSON [marca, marca, ...].
 *
 * Cambios vs 5.6:
 *  - SQL concatenado ($busca directo) → sentencia preparada (inyección SQL real)
 *  - include → require_once con __DIR__
 *  - mysqli_set_charset("utf8") eliminado (conexion.php ya usa utf8mb4)
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
        "SELECT DISTINCT(marcas.marca) marca
         FROM h_pedidos
         INNER JOIN marcas ON marcas.no_cliente = h_pedidos.no_cliente
                           AND marcas.cve_marca = h_pedidos.marca
         WHERE marcas.marca LIKE ?
         GROUP BY h_pedidos.no_cliente, h_pedidos.marca
         LIMIT 10"
    );
    $stmt->bind_param('s', $busca);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $return_arr[] = (string)$row['marca'];
    }
    $stmt->close();

    echo json_encode($return_arr);
} catch (mysqli_sql_exception $e) {
    error_log('[suggest_by_marca.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error al consultar marcas']);
} finally {
    $conexion->close();
}