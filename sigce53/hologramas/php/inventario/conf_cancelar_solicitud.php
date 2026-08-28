<?php
/**
 * conf_cancelar_solicitud.php — PHP 8.3
 * Marca un detalle de solicitud (sh_detalle) como cancelado (status=7)
 * y genera la alerta correspondiente.
 * Devuelve JSON {status, msj}.
 *
 * Cambios vs 5.6:
 *  - SQL concatenado → sentencias preparadas
 *  - include → require_once con __DIR__
 *  - Sincronización remota (ya comentada en el original) se deja igual,
 *    documentada — dependía de common/conexion_remota.php, que no existe
 *    en el repo actual
 *  - try/catch/finally con error_log
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../common/conexion.php';


try {
    $conexion->autocommit(false);
    // NOTA: sincronización remota deshabilitada — dependía de $con_rem
    // (common/conexion_remota.php), que no existe en el repo actual.

    $folio = (int)($_POST['folio'] ?? 0);
    $id_s  = (int)($_POST['id_s'] ?? 0);
    $user  = (string)($_POST['user'] ?? '');

    $stmt = $conexion->prepare(
        "UPDATE sh_detalle SET status = 7, fecha_cancelar = NOW(), sinc_up = 7, usr_cancelar = ? WHERE id = ?"
    );
    $stmt->bind_param('si', $user, $folio);
    if (!$stmt->execute()) {
        throw new Exception('Error al actualizar el estatus de Sincronizacion en la tabla sh_pedidos');
    }
    if ($conexion->affected_rows < 1) {
        throw new Exception('No se actualizo ningun registro');
    }
    $stmt->close();

    /*
     * ------------ ACTUALIZAR EN LA BD REMOTA (DESHABILITADO) ------------
     * $sql_pag_rem = "update sh_detalle set status=2 where id=$folio";
     * $res_up_pago_rem = $con_rem->query($sql_pag_rem);
     * ----------------------------------------------------------------------
     */

    // CREAMOS LA NOTIFICACION
    $stmtAlert = $conexion->prepare(
        "INSERT INTO g_alertas (id_solicitud, id_referencia, id_msj, fecha) VALUES (?, ?, 7, NOW())"
    );
    $stmtAlert->bind_param('ii', $id_s, $folio);
    if (!$stmtAlert->execute()) {
        throw new Exception('NO se pudo guardar la alerta');
    }
    $stmtAlert->close();

    $conexion->commit();
    echo json_encode(['status' => 'OK', 'msj' => 'Se actualizó el status a Pendiente de pago']);
} catch (Exception $e) {
    $conexion->rollback();
    error_log('[conf_cancelar_solicitud.php] ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'msj' => 'Error en la base de datos: ' . $e->getMessage()]);
} finally {
    $conexion->close();
}
