<?php
/**
 * suggest_no_cliente_online.php — PHP 8.3
 * Autocomplete de No. de Control sobre sh_pedidos (pedidos online).
 * Devuelve JSON [no_cliente, no_cliente, ...].
 *
 * Cambios vs 5.6:
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
        "SELECT DISTINCT(no_cliente) FROM sh_pedidos WHERE no_cliente LIKE ? LIMIT 10"
    );
    $stmt->bind_param('s', $busca);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $return_arr[] = (string)$row['no_cliente'];
    }
    $stmt->close();

    echo json_encode($return_arr);
} catch (mysqli_sql_exception $e) {
    error_log('[suggest_no_cliente_online.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error al consultar clientes']);
} finally {
    $conexion->close();
}