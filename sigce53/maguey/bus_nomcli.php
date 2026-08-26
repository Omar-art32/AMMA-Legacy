<?php
/**
 * bus_nomcli.php — PHP 8.3
 * Autocomplete del campo "Nombre" (jQuery UI). Devuelve JSON [{label, value}].
 *
 * DETECTADO COMO FALTANTE en la primera ronda de migración: seguía con SQL
 * concatenado (inyectable). Migrado al mover el módulo a buscar_predio/.
 *
 * Comportamiento preservado 1:1 del original, incluidas dos particularidades
 * a revisar con negocio:
 *  - Busca el término en la columna no_cliente (aunque el campo es "Nombre").
 *  - NO aplica el filtro de conflicto de intereses que sí usa bus_clientes.php.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../common/conexion.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['term'])) {
    echo json_encode([]);
    exit;
}

$busca = (string)$_GET['term'];
$return_arr = [];

try {
    $stmt = $conexion->prepare(
        "SELECT no_cliente, nombre
         FROM clientes
         WHERE no_cliente LIKE CONCAT('%', ?, '%')"
    );

    $stmt->bind_param('s', $busca);
    $stmt->execute();

    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $return_arr[] = [
            'label' => $row['no_cliente'],
            'value' => $row['nombre'],
        ];
    }

    $stmt->close();

    echo json_encode($return_arr);

} catch (mysqli_sql_exception $e) {

    error_log('[bus_nomcli.php] ' . $e->getMessage());

    http_response_code(500);

    echo json_encode([
        'error' => 'Error al consultar clientes'
    ]);

} finally {
    $conexion->close();
}
