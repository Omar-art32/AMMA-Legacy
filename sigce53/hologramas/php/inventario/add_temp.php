<?php
/**
 * add_temp.php — PHP 8.3
 * Agrega un renglón a h_tmp_pedido (carrito de requisición) y, si viene
 * de una solicitud online (folio_det), marca ese detalle como "en lista".
 * Devuelve JSON {status, msj, id_row}.
 *
 * Cambios vs 5.6:
 *  - utf8_decode() eliminado (entrada ya viene en UTF-8)
 *  - SQL concatenado (INSERT/UPDATE) → sentencias preparadas
 *  - include → require_once con __DIR__
 *  - Sincronización remota (comentada en el original) se deja igual,
 *    documentada — depende de common/conexion_remota.php, que no existe
 *    en el repo actual; no se activa
 *  - try/catch/finally con error_log
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../common/conexion.php';


try {
    $conexion->autocommit(false);
    // NOTA: sincronización remota deshabilitada — dependía de $con_rem
    // (common/conexion_remota.php), que no existe en el repo actual.

    $user      = (string)($_POST['user'] ?? '');
    $no_pedido = (string)($_POST['no_pedido'] ?? '');

    $datos    = json_decode((string)($_POST['datos'] ?? '{}'), true);
    $cliente  = (string)($datos['cte'] ?? '');
    $marca    = (string)($datos['marca'] ?? '');
    $edo      = (string)($datos['edo'] ?? '');
    $serie    = (string)($datos['serie'] ?? '');
    $tipo     = (string)($datos['tipo'] ?? '');
    $cantidad = (string)($datos['cantidad'] ?? '');
    $pagado   = (string)($datos['pagado'] ?? '');
    $urgente  = (string)($datos['urgente'] ?? '');
    $fini     = (string)($datos['fini'] ?? '');
    $ffin     = (string)($datos['ffin'] ?? '');
    $fecha    = date('Y-m-d H:i:s');
    $folio_det = 0;

    if (isset($datos['folio_det'])) {
        $folio_det = (int)$datos['folio_det'];
        $stmt = $conexion->prepare(
            "INSERT INTO h_tmp_pedido (id_sh_d, no_pedido, fecha, no_cliente, edo, marca, serie, tipo, fi, ff, cantidad, pagado, urgente, status, usr)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '0', ?)"
        );
        $stmt->bind_param(
            'isssssssssssss',
            $folio_det, $no_pedido, $fecha, $cliente, $edo, $marca, $serie, $tipo, $fini, $ffin, $cantidad, $pagado, $urgente, $user
        );
    } else {
        $stmt = $conexion->prepare(
            "INSERT INTO h_tmp_pedido (no_pedido, fecha, no_cliente, edo, marca, serie, tipo, fi, ff, cantidad, pagado, urgente, status, usr)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '0', ?)"
        );
        $stmt->bind_param(
            'ssssssssssss',
            $no_pedido, $fecha, $cliente, $edo, $marca, $serie, $tipo, $fini, $ffin, $cantidad, $pagado, $urgente, $user
        );
    }

    if (!$stmt->execute()) {
        throw new Exception('Error al agregar el pedido a la lista temporal');
    }
    $stmt->close();

    if ($folio_det !== 0) {
        $stmtUp = $conexion->prepare(
            "UPDATE sh_detalle SET status = 3, fecha_lista = NOW(), usr_lista = ? WHERE id = ?"
        );
        $stmtUp->bind_param('si', $user, $folio_det);
        if (!$stmtUp->execute()) {
            throw new Exception('No se pudo actualizar el estatus del pedido en la base local');
        }
        $stmtUp->close();
        // $res_up_rem = $con_rem->query(...) — deshabilitado (ver nota arriba)
    }

    $conexion->commit();

    // OBTENEMOS EL NUMERO DE PEDIDO ACTUAL
    $id_row = null;
    $res_tmp = $conexion->query("SELECT MAX(id_row) maxid FROM h_tmp_pedido");
    if ($res_tmp && $res_tmp->num_rows === 1) {
        $row_tmp = $res_tmp->fetch_row();
        $id_row = $row_tmp[0];
    }

    echo json_encode(['status' => 'OK', 'msj' => 'Se agrego correctamente al temporal', 'id_row' => $id_row]);
} catch (Exception $e) {
    $conexion->rollback();
    error_log('[add_temp.php] ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'msj' => 'Error en la base de datos: ' . $e->getMessage()]);
} finally {
    $conexion->close();
}
