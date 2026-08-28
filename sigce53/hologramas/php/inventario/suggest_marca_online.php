<?php
/**
 * suggest_marca_online.php — PHP 8.3
 * Autocomplete de marca sobre pedidos online (sh_pedidos / sh_detalle).
 * Devuelve JSON [marca, marca, ...].
 *
 * Cambios vs 5.6:
 *  - SQL concatenado ($busca directo) → sentencia preparada
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
        "SELECT DISTINCT(m.marca) marca
         FROM sh_detalle sh_d
         INNER JOIN sh_pedidos sh_p ON sh_p.id_solicitud = sh_d.id_solicitud
         INNER JOIN marcas m ON m.no_cliente = sh_p.no_cliente AND m.cve_marca = sh_d.marca
         WHERE m.marca LIKE ?
         GROUP BY sh_p.no_cliente, sh_d.marca
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
    error_log('[suggest_marca_online.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error al consultar marcas']);
} finally {
    $conexion->close();
}