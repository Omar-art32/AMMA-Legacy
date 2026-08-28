<?php
/**
 * bus_viveronum.php — PHP 8.3
 * Autocomplete de viveros con nombre y cliente.
 * Devuelve JSON [{value, nombrepre, clientep}].
 *
 * Cambios vs 5.6:
 *  - SQL concatenado → sentencia preparada
 *  - include('php/registro/conexion.php') → require_once centralizado
 *  - set_charset("utf8") eliminado (conexion.php ya usa utf8mb4)
 *  - $row_array reutilizado → arreglo nuevo por fila
 *  - Header JSON, try/catch
 */
declare(strict_types=1);

require_once __DIR__ . '/../common/conexion.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['term'])) {
    echo json_encode([]);
    exit;
}

$busca = (string)$_GET['term'];
$return_arr = [];

try {
    $stmt = $conexion->prepare(
        "SELECT id_paraje, paraje, id_cliente
         FROM paraje_vivero
         WHERE id_paraje LIKE CONCAT('%', ?, '%')
           AND paraje != ' '
           AND tipo = '2'"
    );
    $stmt->bind_param('s', $busca);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $return_arr[] = [
            'value'     => $row['id_paraje'],
            'nombrepre' => $row['paraje'],
            'clientep'  => $row['id_cliente'],
        ];
    }
    $stmt->close();

    echo json_encode($return_arr);
} catch (mysqli_sql_exception $e) {
    error_log('[bus_viveronum.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error al consultar viveros']);
} finally {
    $conexion->close();
}