<?php
/**
 * bus_clientes.php — PHP 8.3
 * Autocomplete de clientes (jQuery UI). Devuelve JSON.
 *
 * Cambios vs 5.6:
 *  - Sentencia preparada (antes: LIKE '%$_GET[term]%' concatenado → inyección SQL)
 *  - Lista de conflicto de intereses validada antes de interpolarla en el IN()
 *  - ?? en lugar de isset()?:, header JSON explícito
 */
declare(strict_types=1);

require_once __DIR__ . '/../common/conexion.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['term'])) {
    echo json_encode([]);
    exit;
}

$busca = (string)$_GET['term'];
$idus  = (int)($_GET['idus'] ?? 0);

$return_arr = [];

try {
    // -----------------------------------------------------------------
    // Conflicto de intereses: la función de BD regresa 'C9999','C9998'
    // -----------------------------------------------------------------
    $sql_conflicto = '';
    if ($idus > 0) {
        $stmt = $conexion->prepare('SELECT getConflictoIntereses(?)');
        $stmt->bind_param('i', $idus);
        $stmt->execute();
        $stmt->bind_result($clientes_conflicto);
        $stmt->fetch();
        $stmt->close();

        // Validamos el formato antes de interpolar en el IN().
        // Aunque viene de la BD, no lo tratamos como confiable a ciegas.
        if ($clientes_conflicto !== null && $clientes_conflicto !== ''
            && preg_match("/^'[A-Za-z0-9]+'(,'[A-Za-z0-9]+')*$/", $clientes_conflicto)) {
            $sql_conflicto = " AND no_cliente NOT IN ({$clientes_conflicto}) ";
        }
    }

    // -----------------------------------------------------------------
    // Búsqueda de clientes
    // -----------------------------------------------------------------
    $sql = "SELECT no_cliente, nombre, registro_crm
            FROM clientes
            WHERE no_cliente LIKE CONCAT('%', ?, '%')
              AND nombre != '--' {$sql_conflicto}";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('s', $busca);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $return_arr[] = [
            'value'       => $row['no_cliente'],
            'abbrev'      => $row['nombre'],
            'cliente_crm' => $row['registro_crm'],
        ];
    }
    $stmt->close();

    echo json_encode($return_arr);
} catch (mysqli_sql_exception $e) {
    error_log('[bus_clientes.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error al consultar clientes']);
} finally {
    $conexion->close();
}