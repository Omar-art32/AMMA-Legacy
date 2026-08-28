<?php
/**
 * bus_nompredio.php — PHP 8.3
 * Autocomplete por nombre/ID de predio. Devuelve JSON [{label, value}].
 *
 * Cambios vs 5.6:
 *  - SQL concatenado → sentencia preparada
 *  - include → require_once con __DIR__
 *  - $row_array reutilizado → arreglo nuevo por fila
 *  - Header JSON explícito, try/catch
 */
declare(strict_types=1);

require_once __DIR__ . '/../common/conexion.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['pred'])) {
    echo json_encode([]);
    exit;
}

$busca = (string)$_GET['pred'];
$return_arr = [];

try {
    $stmt = $conexion->prepare(
        "SELECT id_paraje, paraje FROM paraje
         WHERE id_paraje LIKE CONCAT('%', ?, '%')"
    );
    $stmt->bind_param('s', $busca);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $return_arr[] = [
            'label' => $row['id_paraje'],
            'value' => $row['paraje'],
        ];
    }
    $stmt->close();

    echo json_encode($return_arr);
} catch (mysqli_sql_exception $e) {
    error_log('[bus_nompredio.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error al consultar predios']);
} finally {
    $conexion->close();
}