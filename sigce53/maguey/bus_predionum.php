<?php
/**
 * bus_predionum.php — PHP 8.3
 * Autocomplete de predios con datos extendidos: nombre, cliente,
 * superficie, registro de maguey y si tiene polígono.
 * Devuelve JSON [{value, nombrepre, clientep, superficie, maguey_con_registro, poligono}].
 *
 * Cambios vs 5.6:
 *  - SQL concatenado → sentencia preparada
 *  - include → require_once con __DIR__
 *  - set_charset("utf8") eliminado (conexion.php ya usa utf8mb4)
 *  - SELECT * → columnas explícitas (ya estaban en el original)
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
        "SELECT id_paraje, paraje, id_cliente, superficie, maguey_con_registro,
                IF(poligono IS NULL, 0, 1) AS poligono
         FROM paraje
         WHERE id_paraje LIKE CONCAT('%', ?, '%')
           AND paraje != ' '
           AND tipo = '1'"
    );
    $stmt->bind_param('s', $busca);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $return_arr[] = [
            'value'                => $row['id_paraje'],
            'nombrepre'            => $row['paraje'],
            'clientep'             => $row['id_cliente'],
            'superficie'           => $row['superficie'],
            'maguey_con_registro'  => $row['maguey_con_registro'],
            'poligono'             => $row['poligono'],
        ];
    }
    $stmt->close();

    echo json_encode($return_arr);
} catch (mysqli_sql_exception $e) {
    error_log('[bus_predionum.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error al consultar predios']);
} finally {
    $conexion->close();
}