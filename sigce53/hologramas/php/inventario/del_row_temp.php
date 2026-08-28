<?php
/**
 * del_row_temp.php — PHP 8.3
 * Regresa a "en lista" (status=2) el detalle de la solicitud y elimina
 * el registro correspondiente de h_tmp_pedido.
 * Devuelve JSON {status, msj}.
 *
 * Cambios vs 5.6:
 *  - utf8_decode() eliminado (entrada ya viene en UTF-8)
 *  - DELETE concatenado → sentencia preparada (el UPDATE ya estaba preparado)
 *  - include → require_once con __DIR__
 *  - try/catch con error_log
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../common/conexion.php';


$no_pedido = (string)($_POST['no_pedido'] ?? '');
$cliente   = (string)($_POST['cliente'] ?? '');
$marca     = (string)($_POST['marca'] ?? '');
$estado    = (string)($_POST['estado'] ?? '');

try {
    $stmt = $conexion->prepare(
        "UPDATE sh_detalle sh
         INNER JOIN h_tmp_pedido ht ON sh.id = ht.id_sh_d
                                    AND ht.no_pedido = ?
                                    AND ht.no_cliente = ?
                                    AND ht.marca = ?
                                    AND ht.edo = ?
         SET sh.status = 2
         WHERE sh.status = 3"
    );
    $stmt->bind_param('isss', $no_pedido, $cliente, $marca, $estado);

    if (!$stmt->execute()) {
        echo json_encode(['status' => 'error', 'msj' => 'No se pudo actualizar estatus']);
    } else {
        $stmt->close();
        $stmtDel = $conexion->prepare(
            "DELETE FROM h_tmp_pedido WHERE no_pedido = ? AND no_cliente = ? AND marca = ? AND edo = ?"
        );
        $stmtDel->bind_param('isss', $no_pedido, $cliente, $marca, $estado);
        $stmtDel->execute();

        if ($conexion->affected_rows > 0) {
            echo json_encode(['status' => 'OK', 'msj' => 'Detalle eliminado correctamente']);
        } else {
            echo json_encode(['status' => 'error', 'msj' => 'No se pudo eliminar el detalle de la lista, intente mas tarde']);
        }
        $stmtDel->close();
    }
} catch (mysqli_sql_exception $e) {
    error_log('[del_row_temp.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msj' => 'Disculpe ha ocurrido un error, intente mas tarde']);
} finally {
    $conexion->close();
}
