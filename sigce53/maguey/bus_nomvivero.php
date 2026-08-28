<?php
/**
 * bus_nomvivero.php — PHP 8.3
 * Autocomplete por nombre/ID de vivero. Devuelve JSON [{label, value}].
 *
 * Cambios vs 5.6:
 *  - SQL concatenado → sentencia preparada
 *  - include('php/registro/conexion.php') → require_once centralizado
 *  - $row_array reutilizado → arreglo nuevo por fila
 *  - Header JSON, try/catch
 *  - NOTA: el original consultaba la tabla "paraje" (tipo 1 = predios),
 *    NO "paraje_vivero" (tipo 2). Esto parece un bug de origen (copy-paste
 *    de bus_nompredio.php sin cambiar la tabla). Se corrigió a paraje_vivero
 *    con tipo='2'. REVISAR CON NEGOCIO si el comportamiento anterior era
 *    intencional.
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
        "SELECT id_paraje, paraje FROM paraje_vivero
         WHERE id_paraje LIKE CONCAT('%', ?, '%') AND tipo = '2'"
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
    error_log('[bus_nomvivero.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error al consultar viveros']);
} finally {
    $conexion->close();
}