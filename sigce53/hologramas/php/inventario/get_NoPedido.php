<?php
/**
 * get_NoPedido.php — PHP 8.3
 * Calcula el siguiente No. de Pedido: si hay uno en cola (h_tmp_pedido) lo
 * usa, si no, calcula el siguiente sobre h_pedidos.
 * Devuelve JSON {status, tmp, no_pedido} o {status, n_rcbo}.
 *
 * Cambios vs 5.6:
 *  - include → require_once con __DIR__
 *  - Sin entrada de usuario, no hay riesgo de inyección; se mantiene query() directa
 *  - try/catch con error_log
 *  - Se agrega header JSON explícito
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../common/conexion.php';


try {
    $res_tmp = $conexion->query(
        "SELECT IF(MAX(no_pedido) IS NULL, 0, MAX(no_pedido)) FROM h_tmp_pedido"
    );

    if ($res_tmp->num_rows === 1) {
        $row_tmp = $res_tmp->fetch_row();
        $no_tmp = $row_tmp[0];

        if ($no_tmp != 0) {
            echo json_encode(['status' => 'correcto', 'tmp' => 'si', 'no_pedido' => $no_tmp]);
        } else {
            $result = $conexion->query(
                "SELECT IF(MAX(no_pedido) IS NULL, 0, MAX(no_pedido)) FROM h_pedidos"
            );

            if ($result->num_rows === 1) {
                $row = $result->fetch_row();
                $no_pedido = $row[0] + 1;
                echo json_encode(['status' => 'correcto', 'tmp' => 'no', 'no_pedido' => $no_pedido]);
            } else {
                echo json_encode(['status' => 'error', 'n_rcbo' => 'na']);
            }
        }
    } else {
        echo json_encode(['status' => 'error', 'n_rcbo' => 'na']);
    }
} catch (mysqli_sql_exception $e) {
    error_log('[get_NoPedido.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msj' => 'Disculpe ha ocurrido un error, intente mas tarde']);
} finally {
    $conexion->close();
}
