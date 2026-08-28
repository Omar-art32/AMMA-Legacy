<?php
/**
 * conf_pago_online.php — PHP 8.3
 * Confirma el pago de un pedido online: sube el comprobante (si viene
 * un archivo), actualiza sh_pedidos/sh_detalle y genera la alerta.
 * Devuelve JSON {status, msj}.
 *
 * Cambios vs 5.6:
 *  - SQL concatenado restante ($sql_pag_local, $sql_alert) → sentencias
 *    preparadas (el resto del archivo ya usaba prepared statements)
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

    $success = null;
    $nombreArchivo = null;

    // CARGA DE ARCHIVO
    if (!empty($_FILES['file'])) {
        $images = $_FILES['file'];
        $filenames = $images['name'];
        $idSolicitud = (int)($_POST['id_s'] ?? 0);

        $rtCliente = 'pdf_acuses';
        $arrext = explode('.', $filenames);
        $ext = array_pop($arrext);

        $stmt = $conexion->prepare(
            "SELECT no_cliente, id_solicitud, total, tipo_pago FROM sh_pedidos WHERE id_solicitud = ?"
        );
        $stmt->bind_param('i', $idSolicitud);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $nombreArchivo = 'FOL_' . $idSolicitud . '.' . $ext;
            $target = '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                    . 'clientes/hologramas/php/files/' . $nombreArchivo;

            if (move_uploaded_file($images['tmp_name'], $target)) {
                $success = true;
            } else {
                $success = false;
            }
        }
        $stmt->close();

        if ($success) {
            $stmtUpArchivo = $conexion->prepare(
                "UPDATE sh_pedidos SET comprobante = ? WHERE id_solicitud = ?"
            );
            $stmtUpArchivo->bind_param('si', $nombreArchivo, $idSolicitud);
            $success = $stmtUpArchivo->execute();
            $stmtUpArchivo->close();
        }
    } else {
        $success = true;
    }

    // PROCESAR SEGUN EL RESULTADO DE LA CARGA
    if ($success === true) {
        $idSolicitud = (int)($_POST['id_s'] ?? 0);

        $stmt = $conexion->prepare(
            "SELECT no_cliente, id_solicitud, total, tipo_pago FROM sh_pedidos WHERE id_solicitud = ?"
        );
        $stmt->bind_param('i', $idSolicitud);
        if (!$stmt->execute()) {
            throw new Exception('Error al consultar hologramas (ERR:002)');
        }
        $stmt->store_result();
        $stmt->bind_result($no_cliente, $id_solicitud, $total, $tipo_pago);
        $stmt->fetch();
        $stmt->close();

        $stmt = $conexion->prepare("SELECT status FROM sh_detalle WHERE id_solicitud = ?");
        $stmt->bind_param('i', $idSolicitud);
        if (!$stmt->execute()) {
            throw new Exception('Error al consultar hologramas (ERR:002)');
        }
        $stmt->store_result();
        $stmt->bind_result($status);
        $stmt->fetch();
        $stmt->close();

        $folio = (int)($_POST['folio'] ?? 0);
        $id_s  = $idSolicitud;
        $user  = (string)($_POST['user'] ?? '');
        $pago_opcion = (string)($_POST['pago_opcion'] ?? '');

        if (isset($_POST['forma_pago'])) {
            $forma_pago = ($_POST['forma_pago'] === '1') ? 'EFECTIVO (EN OFICINA AMMA)' : 'TRANSFERENCIA BANCARIA';
        } else {
            $forma_pago = '';
        }

        // ACTUALIZAMOS EN LA BD LOCAL
        if (isset($_POST['modificar']) && $_POST['modificar'] === '1') {
            $stmtPago = $conexion->prepare(
                "UPDATE sh_detalle SET pago_opcion = ?, usr_pago = ? WHERE id = ?"
            );
            $stmtPago->bind_param('ssi', $pago_opcion, $user, $folio);
        } else {
            if ($tipo_pago !== $forma_pago && $forma_pago !== '') {
                $stmtTipoPago = $conexion->prepare(
                    "UPDATE sh_pedidos SET tipo_pago = ? WHERE id_solicitud = ?"
                );
                $stmtTipoPago->bind_param('si', $forma_pago, $id_s);
                if (!$stmtTipoPago->execute()) {
                    throw new Exception(json_encode(['codigo' => '0010', 'error' => $conexion->error]));
                }
                $stmtTipoPago->close();
            }

            if ($status == '3' || $status == '4' || $status == '5') {
                $stmtPago = $conexion->prepare(
                    "UPDATE sh_detalle SET pago_opcion = ?, usr_pago = ? WHERE id = ?"
                );
                $stmtPago->bind_param('ssi', $pago_opcion, $user, $folio);
            } else {
                $stmtPago = $conexion->prepare(
                    "UPDATE sh_detalle SET status = 2, fecha_pago = NOW(), sinc_up = 2, pago_opcion = ?, usr_pago = ? WHERE id = ?"
                );
                $stmtPago->bind_param('ssi', $pago_opcion, $user, $folio);
            }
        }

        if (!$stmtPago->execute()) {
            throw new Exception('Error al actualizar el estatus de Sincronizacion en la tabla sh_pedidos');
        }
        if ($conexion->affected_rows < 1) {
            throw new Exception('No se actualizo ningun registro');
        }
        $stmtPago->close();

        /*
         * ------------ ACTUALIZAR EN LA BD REMOTA (DESHABILITADO) ------------
         * $sql_pag_rem = "update sh_detalle set status=2 where id=$folio";
         * $res_up_pago_rem = $con_rem->query($sql_pag_rem);
         * ----------------------------------------------------------------------
         */

        // CREAMOS LA NOTIFICACION
        $stmtAlert = $conexion->prepare(
            "INSERT INTO g_alertas (id_solicitud, id_referencia, id_msj, fecha) VALUES (?, ?, 1, NOW())"
        );
        $stmtAlert->bind_param('ii', $id_s, $folio);
        if (!$stmtAlert->execute()) {
            throw new Exception('NO se pudo guardar la alerta');
        }
        $stmtAlert->close();

        $conexion->commit();
        echo json_encode(['status' => 'OK', 'msj' => 'Se actualizó el status a Pagado' . $status]);
    } elseif ($success === false) {
        echo json_encode(['status' => 'error', 'msj' => 'Error al Subir el Archivo, contacte con su Administrador de Sistemas.']);
    } else {
        echo json_encode(['status' => 'error', 'msj' => 'No hay archivos para Importar.']);
    }
} catch (Exception $e) {
    $conexion->rollback();
    error_log('[conf_pago_online.php] ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'msj' => 'Error en la base de datos: ' . $e->getMessage()]);
} finally {
    $conexion->close();
}
