<?php
/**
 * conf_pago.php — PHP 8.3
 * Marca como pagado (pagado=1) un pedido en cola de h_tmp_pedido.
 * Devuelve JSON {status, msj}.
 *
 * Cambios vs 5.6:
 *  - utf8_decode() eliminado (entrada ya viene en UTF-8)
 *  - SQL concatenado → sentencia preparada
 *  - include → require_once con __DIR__
 *  - try/catch con error_log
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../common/conexion.php';


$no_pedido = (string)($_POST['no_pedido'] ?? '');
$cliente   = (string)($_POST['no_cliente'] ?? '');
$marca     = (string)($_POST['marca'] ?? '');
$estado    = (string)($_POST['estado'] ?? '');

try {
    $stmt = $conexion->prepare(
        "UPDATE h_tmp_pedido SET pagado = 1 WHERE no_pedido = ? AND no_cliente = ? AND marca = ? AND edo = ?"
    );
    $stmt->bind_param('isss', $no_pedido, $cliente, $marca, $estado);
    $stmt->execute();

    if ($conexion->affected_rows > 0) {
        echo json_encode(['status' => 'OK', 'msj' => 'Estatus de pago Actualizado']);
    } else {
        echo json_encode(['status' => 'error', 'msj' => 'NO se puede actualizar el estatus de pago']);
    }
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    error_log('[conf_pago.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msj' => 'Disculpe ha ocurrido un error, intente mas tarde']);
} finally {
    $conexion->close();
}
