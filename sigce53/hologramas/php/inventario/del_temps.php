<?php
/**
 * del_temps.php — PHP 8.3
 * Elimina todos los registros de h_tmp_pedido (cancelar carrito de requisición).
 * Devuelve JSON {status, msj}.
 *
 * Cambios vs 5.6:
 *  - include → require_once con __DIR__
 *  - Sin entrada de usuario, se mantiene query() directa
 *  - try/catch con error_log
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../common/conexion.php';


try {
    $conexion->query("DELETE FROM h_tmp_pedido WHERE 1");

    if ($conexion->affected_rows > 0) {
        echo json_encode(['status' => 'OK', 'msj' => 'Detalle eliminado correctamente']);
    } else {
        echo json_encode(['status' => 'error', 'msj' => 'No hay registros que eliminar']);
    }
} catch (mysqli_sql_exception $e) {
    error_log('[del_temps.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msj' => 'Disculpe ha ocurrido un error, intente mas tarde']);
} finally {
    $conexion->close();
}
