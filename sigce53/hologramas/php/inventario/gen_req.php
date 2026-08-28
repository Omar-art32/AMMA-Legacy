<?php
/**
 * gen_req.php — PHP 8.3
 * Mueve los pedidos pagados de h_tmp_pedido a h_pedidos (requisición
 * final), genera alertas y limpia/incrementa el temporal.
 * Devuelve JSON {status, msj}.
 *
 * Cambios vs 5.6:
 *  - utf8_decode() eliminado (entrada ya viene en UTF-8)
 *  - SQL concatenado → sentencias preparadas
 *  - include → require_once con __DIR__
 *  - BUG CORREGIDO: $ids_alert (usado en "GENERAR LAS ALERTAS") solo se
 *    llenaba dentro del bloque de sincronización remota, que estaba
 *    comentado por completo → $ids_alert nunca se definía y las alertas
 *    de este flujo estaban silenciosamente desactivadas. La consulta que
 *    arma $ids_alert corría contra $conexion (LOCAL), no contra la BD
 *    remota, así que se recupera aquí sin depender de $con_rem.
 *  - Sincronización remota (INSERT en h_pedidos remoto) se deja
 *    comentada/documentada — dependía de common/conexion_remota.php,
 *    que no existe en el repo actual
 *  - error_reporting(0) eliminado (ocultaba errores reales)
 *  - try/catch/finally con error_log
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../common/conexion.php';


try {
    $conexion->autocommit(false);
    // NOTA: sincronización remota deshabilitada — dependía de $con_rem
    // (common/conexion_remota.php), que no existe en el repo actual.

    $no_pedido = (string)($_POST['no_pedido'] ?? '');
    $user      = (string)($_POST['user'] ?? '');
    $fecha     = date('Y-m-d H:i:s');

    $stmt = $conexion->prepare(
        "INSERT INTO h_pedidos (id_sh_d, no_pedido, fecha, no_cliente, edo, marca, serie, tipo, holograma, fi, ff, cantidad, pagado, urgente, status, usr)
         (SELECT id_sh_d, no_pedido, ?, no_cliente, edo, marca, serie, tipo, holograma, fi, ff, cantidad, pagado, urgente, 0, ?
          FROM h_tmp_pedido WHERE no_pedido = ? AND pagado = 1)"
    );
    $stmt->bind_param('ssi', $fecha, $user, $no_pedido);
    if (!$stmt->execute()) {
        throw new Exception('Error al agregar los datos en la tabla pedidos');
    }
    $stmt->close();

    // ARMAMOS LA LISTA DE id_sh_d PARA LAS ALERTAS (consulta LOCAL,
    // independiente del bloque remoto que estaba deshabilitado)
    $ids_alert = '';
    $sep = '';
    $stmtSel = $conexion->prepare(
        "SELECT id_sh_d FROM h_pedidos WHERE no_pedido = ? AND id_sh_d != 0"
    );
    $stmtSel->bind_param('i', $no_pedido);
    $stmtSel->execute();
    $resSel = $stmtSel->get_result();
    while ($row = $resSel->fetch_assoc()) {
        $ids_alert .= $sep . (int)$row['id_sh_d'];
        $sep = ',';
    }
    $stmtSel->close();

    /*
     * ------------ INSERTAR EN LA BD REMOTA (DESHABILITADO) ------------
     * $sql_remota = "INSERT INTO h_pedidos (...) VALUES ...";
     * $in_rem = $con_rem->query($sql_remota);
     * if ($in_rem != true) throw new Exception("Error al agregar los datos en la tabla pedidos REMOTA");
     * --------------------------------------------------------------------
     */

    // ACTUALIZAR EL ESTATUS A ENVIADO
    $stmtUp = $conexion->prepare(
        "UPDATE h_pedidos SET status = 1, sinc_up = 1 WHERE no_pedido = ?"
    );
    $stmtUp->bind_param('i', $no_pedido);
    if (!$stmtUp->execute()) {
        throw new Exception('Error al actualizar el estatus en la tabla pedidos local 1');
    }
    $stmtUp->close();

    // GENERAR LAS ALERTAS
    if ($ids_alert !== '') {
        $sql_alertas = "INSERT INTO g_alertas (id_solicitud, id_referencia, id_msj, fecha)
                         (SELECT id_solicitud, id_referencia, 2, NOW() FROM g_alertas WHERE id_referencia IN ($ids_alert))";
        $res_alertas = $conexion->query($sql_alertas);
        if ($res_alertas != true) {
            throw new Exception('Error al actualizar el estatus en la tabla pedidos local 2');
        }
    }

    // ELIMINAR EL PEDIDO TEMPORAL
    $stmtDel = $conexion->prepare("DELETE FROM h_tmp_pedido WHERE no_pedido = ? AND pagado = 1");
    $stmtDel->bind_param('i', $no_pedido);
    if (!$stmtDel->execute()) {
        throw new Exception('Se envío la requisición, pero no se han eliminado los archivos temporales, por favor informe a sistemas');
    }
    $stmtDel->close();

    $stmtUpNum = $conexion->prepare("UPDATE h_tmp_pedido SET no_pedido = no_pedido + 1 WHERE no_pedido = ?");
    $stmtUpNum->bind_param('i', $no_pedido);
    if (!$stmtUpNum->execute()) {
        throw new Exception('Error actualizar el No de Pedido temporal LOCAL');
    }
    $stmtUpNum->close();

    $conexion->commit();
    echo json_encode(['status' => 'OK', 'msj' => 'El pedido se envio correctamente']);
} catch (Exception $e) {
    $conexion->rollback();
    error_log('[gen_req.php] ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'msj' => 'Error en la base de datos: ' . $e->getMessage()]);
} finally {
    $conexion->close();
}
