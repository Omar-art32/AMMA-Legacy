<?php
/**
 * ingresarPedido.php — PHP 8.3
 * Ingresa a inventario (h_entradas / h_existencias) un pedido ya
 * entregado, marca el pedido como "En Inventario" y dispara la
 * notificación (SMS vía tabla notificaciones + push FCM) al cliente.
 * Devuelve JSON {status, msj}.
 *
 * Cambios vs 5.6:
 *  - CRÍTICO: cada función auxiliar (insertarNotificacion,
 *    recuperarFolioNot, guardarError, send_push) abría su PROPIA
 *    conexión nueva a la BD (include('conexion.php') otra vez dentro de
 *    cada función) y hacía su propio commit/rollback/close. Esto
 *    significa que el registro de notificación NO era atómico junto
 *    con el INSERT de la entrada/existencias: si la notificación
 *    fallaba, la función devolvía "ERROR" y el script principal SÍ
 *    hacía rollback de la entrada — pero si la notificación tenía
 *    éxito y luego algo más fallaba, no había forma de revertirla
 *    porque ya estaba en su propia transacción, ya cerrada. Además se
 *    abrían hasta 4 conexiones por request. Se consolida todo en la
 *    ÚNICA conexión/transacción del script principal, pasada por
 *    parámetro a cada función.
 *  - include → require_once con __DIR__
 *  - Se documenta (no se corrige, requiere credenciales nuevas) que
 *    send_push() usa la API legacy de FCM (https://fcm.googleapis.com/fcm/send),
 *    que Google dio de baja en junio 2024 — esta llamada actualmente
 *    falla siempre en el servidor de Google, aunque el código "corra"
 *    sin error PHP. Para recuperar el push hay que migrar a la API
 *    HTTP v1 de FCM con credenciales de cuenta de servicio.
 *  - try/catch/finally con error_log en cada función
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../common/conexion.php';


/**
 * Recupera el siguiente consecutivo de notificación para el año dado.
 * Usa la MISMA conexión/transacción del llamador (no abre una nueva).
 */
function recuperarFolioNot(mysqli $conexion, int $anioActual): int|string
{
    try {
        $numNotificacion = 1;
        $sql = "SELECT MAX(numero) AS max_notificacion FROM notificaciones.notificaciones WHERE anio = ?";
        $ps = $conexion->prepare($sql);
        $ps->bind_param('i', $anioActual);
        if (!$ps->execute()) {
            throw new Exception('Error al recuperar numero de Notificacion');
        }
        $ps->bind_result($max_notificacion);
        $ps->fetch();
        $ps->close();

        return ($max_notificacion !== null) ? $max_notificacion + 1 : $numNotificacion;
    } catch (Exception $e) {
        error_log('[ingresarPedido.php:recuperarFolioNot] ' . $e->getMessage());
        return 'ERROR';
    }
}

/**
 * Registra un error de notificación pendiente (p.ej. cliente sin teléfono
 * válido). Usa la MISMA conexión/transacción del llamador.
 */
function guardarError(mysqli $conexion, int $id_solicitud, string $no_cliente, int $mensaje_error, int $tipo_notificacion): string
{
    try {
        $sql = "INSERT INTO notificaciones.notificaciones_pendientes (id_solicitud, no_cliente, mensaje_error, tipo, fecha) VALUES (?, ?, ?, ?, NOW())";
        $ps = $conexion->prepare($sql);
        $ps->bind_param('isii', $id_solicitud, $no_cliente, $mensaje_error, $tipo_notificacion);
        if (!$ps->execute()) {
            throw new Exception('Error al registrar error de notificacion');
        }
        $ps->close();
        return 'OK';
    } catch (Exception $e) {
        error_log('[ingresarPedido.php:guardarError] ' . $e->getMessage());
        return 'ERROR';
    }
}

/**
 * Envía la notificación push vía FCM.
 * NOTA: usa la API legacy de FCM, dada de baja por Google en junio 2024.
 * Esta llamada actualmente no entrega notificaciones reales; se deja
 * documentada tal cual hasta que se migre a la API HTTP v1 de FCM.
 */
function send_push(mysqli $conexion): string
{
    try {
        $id_token = 1;
        $tokens = $conexion->prepare("SELECT token FROM notificaciones.tokens t WHERE t.id = ?");
        if (!$tokens) {
            throw new Exception('Ocurrio un error al consultar el token, REF:NOTIFICACIONES1');
        }
        $tokens->bind_param('i', $id_token);
        if (!$tokens->execute()) {
            throw new Exception('Ocurrio un error al consultar el token, REF:NOTIFICACIONES2');
        }
        $tokens->store_result();
        $row_cnt = $tokens->num_rows;
        $tokens->bind_result($token);
        $tokens->fetch();
        $tokens->close();

        if ($row_cnt === 1) {
            // API legacy de FCM — dada de baja por Google (ver nota arriba).
            $url = 'https://fcm.googleapis.com/fcm/send';
            $serverKey = 'AAAAiw1XPSg:APA91bE6kKiniQylZO1LI5Z1iHxmrQYEupI8POrC0dLtYdrnxjvhp3oaiahyPLq5POfBxV-eYyFmEnvDRtO1Hmc7f5v0gyIcii-yYGubOg87UJk9DjThQSSmPaTVphPridEvsQ4658rY';
            $notification = ['title' => 'Notificación CRM', 'text' => 'Enviar Notificación', 'sound' => 'default', 'badge' => '1'];

            $fields = ['registration_ids' => [$token], 'data' => $notification];
            $headers = ['Authorization:key=' . $serverKey, 'Content-Type: application/json'];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
        }

        return 'OK';
    } catch (Exception $e) {
        error_log('[ingresarPedido.php:send_push] ' . $e->getMessage());
        return 'ERROR';
    }
}

/**
 * Arma y envía la notificación SMS (tabla notificaciones) + push del
 * ingreso a inventario. Usa la MISMA conexión/transacción del llamador.
 */
function insertarNotificacion(mysqli $conexion, int $idFilaPedido, int $tipo_notificacion): string
{
    try {
        $anioActual = (int)date('Y');
        $anioAct = date('y');

        // DATOS DE LA SOLICITUD
        $sql = "SELECT s.id, s.folio, hp.no_cliente FROM h_pedidos hp
                INNER JOIN sh_detalle sh ON hp.id_sh_d = sh.id
                INNER JOIN solicitudes s ON sh.id_solicitud = s.id
                WHERE hp.id_row = ?";
        $solicitud = $conexion->prepare($sql);
        $solicitud->bind_param('i', $idFilaPedido);
        if (!$solicitud->execute()) {
            throw new Exception('Error al recuperar numero de folio Solicitud');
        }
        $solicitud->bind_result($id_solicitud, $folioSolicitud, $no_cliente);
        $solicitud->fetch();
        $solicitud->close();

        // TELEFONOS DEL CLIENTE
        $telefonos = $conexion->prepare(
            "SELECT t.numero
             FROM siig.clientes c
             INNER JOIN siig.clientes_telefonos ct ON ct.cliente = c.no_cliente
             INNER JOIN siig.telefonos t ON ct.telefono = t.id AND t.tipo = 0 AND t.status = 2 AND t.sms = 1
             WHERE c.no_cliente = ?"
        );
        if (!$telefonos) {
            throw new Exception('Ocurrio un error al consultar telefonos, REF:NOTIFICACIONES1');
        }
        $telefonos->bind_param('s', $no_cliente);
        if (!$telefonos->execute()) {
            throw new Exception('Ocurrio un error al consultar telefonos, REF:NOTIFICACIONES2');
        }
        $telefonos->store_result();
        $row_cnt = $telefonos->num_rows;
        $telefonos->bind_result($telefono);

        if ($row_cnt > 0) {
            $numConsecutivoNot = recuperarFolioNot($conexion, $anioActual);
            if ($numConsecutivoNot === 'ERROR') {
                throw new Exception('Error al recuperar numero de notificacion');
            }
            $claveNot = str_pad((string)$numConsecutivoNot, 5, '0', STR_PAD_LEFT);
            $folioNot = 'NOT-' . $claveNot . '/' . $anioAct;
            $mensaje = 'AMMA ' . $folioNot . ': ' . $folioSolicitud . ' hologramas impresos. NOTA: Este numero no admite mensajes de respuesta. http://www.amma.org.mx/m.php';

            while ($telefonos->fetch()) {
                $notificaciones = $conexion->prepare(
                    "INSERT INTO notificaciones.notificaciones
                        (numero, folio, anio, fecha, no_cliente, mensaje, tipo, telefono, id_solicitud, estatus)
                     VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, ?, 1)"
                );
                if (!$notificaciones) {
                    throw new Exception('Error al registrar la notificacion REF-1');
                }
                $notificaciones->bind_param('isissisi', $numConsecutivoNot, $folioNot, $anioActual, $no_cliente, $mensaje, $tipo_notificacion, $telefono, $id_solicitud);
                if (!$notificaciones->execute()) {
                    throw new Exception('Error al registrar la notificacion REF-2');
                }
                $notificaciones->close();
            }
        } else {
            $error = guardarError($conexion, $id_solicitud, $no_cliente, 1, $tipo_notificacion);
            if ($error === 'ERROR') {
                throw new Exception('Error al registrar error de notificacion');
            }
        }
        $telefonos->close();

        $error = send_push($conexion);
        if ($error === 'ERROR') {
            throw new Exception('Error al enviar push Notificacion');
        }

        return 'OK';
    } catch (Exception $e) {
        error_log('[ingresarPedido.php:insertarNotificacion] ' . $e->getMessage());
        return 'ERROR';
    }
}

try {
    $conexion->autocommit(false);

    $idFilaPedido = (int)($_POST['fila_pedido'] ?? 0);
    $fi_ing = (string)($_POST['fi_ing'] ?? '');
    $ff_ing = (string)($_POST['ff_ing'] ?? '');
    $usr = (string)($_POST['usr'] ?? '');

    $sql_edo = "SELECT MAX(fol_fin) f_fin FROM h_existencias
                INNER JOIN h_pedidos ON h_existencias.no_cliente = h_pedidos.no_cliente
                                     AND h_existencias.marca = h_pedidos.marca
                                     AND h_existencias.serie = h_pedidos.serie
                WHERE h_pedidos.id_row = ? AND h_existencias.edo = h_pedidos.edo AND h_existencias.tipo = h_pedidos.tipo
                LIMIT 1";
    $ps = $conexion->prepare($sql_edo);
    $ps->bind_param('i', $idFilaPedido);
    if (!$ps->execute()) {
        throw new Exception('Error en la base de datos');
    }
    $ps->bind_result($ff_entrada);
    $ps->store_result();
    $ps->fetch();
    $ps->close();

    // VALIDAR SI LOS FOLIOS SON DE HOLOGRAMAS NUEVOS :: 310322 :: 070725
    $sql_pedidos = $conexion->prepare("SELECT holograma FROM h_pedidos WHERE id_row = ?");
    if (!$sql_pedidos) {
        throw new Exception('Ocurrio un error al obtener la información (ERROR:01)');
    }
    $sql_pedidos->bind_param('i', $idFilaPedido);
    if (!$sql_pedidos->execute()) {
        throw new Exception('Ocurrio un error al obtener la información (ERROR:02)');
    }
    $sql_pedidos->store_result();
    $sql_pedidos->bind_result($holograma);
    $sql_pedidos->fetch();
    $sql_pedidos->close();
    $holograma++;
    $version = $holograma;

    if ($ff_entrada === null) {
        // SIN EXISTENCIAS PREVIAS
        $sql_ins = "INSERT INTO h_entradas (no_cliente, marca, edo, tipo, serie, fol_ini, fol_fin, cantidad, version, fecha, usr)
                    (SELECT no_cliente, marca, edo, tipo, serie, fi, ff, cantidad, ?, NOW(), ? FROM h_pedidos WHERE h_pedidos.id_row = ?)";
        $ps_e = $conexion->prepare($sql_ins);
        $ps_e->bind_param('isi', $version, $usr, $idFilaPedido);
        if (!$ps_e->execute()) {
            throw new Exception('Error al insertar la entrada');
        }
        $ps_e->close();

        $sql_exs = "INSERT INTO h_existencias (no_cliente, marca, edo, tipo, serie, fol_ini, fol_fin, existencias)
                    (SELECT no_cliente, marca, edo, tipo, serie, fi, ff, cantidad FROM h_pedidos WHERE h_pedidos.id_row = ?)";
        $ps_e = $conexion->prepare($sql_exs);
        $ps_e->bind_param('i', $idFilaPedido);
        if (!$ps_e->execute()) {
            throw new Exception('Error al insertar la existencia [FREE]');
        }
        $ps_e->close();

        $sql_ped = "UPDATE h_pedidos SET status = 6 WHERE id_row = ?";
        $ps_e = $conexion->prepare($sql_ped);
        $ps_e->bind_param('i', $idFilaPedido);
        if (!$ps_e->execute()) {
            throw new Exception('Error al actualizar el estatus del pedido [FREE]');
        }
        $ps_e->close();

        $mensaje_p = 'Los folios se han agregado correctamente a las existencias';
    } else {
        $sql_ins = "INSERT INTO h_entradas (no_cliente, marca, edo, tipo, serie, fol_ini, fol_fin, cantidad, version, fecha, usr)
                    (SELECT no_cliente, marca, edo, tipo, serie, fi, ff, cantidad, ?, NOW(), ? FROM h_pedidos WHERE h_pedidos.id_row = ?)";
        $ps_e = $conexion->prepare($sql_ins);
        $ps_e->bind_param('isi', $version, $usr, $idFilaPedido);
        if (!$ps_e->execute()) {
            throw new Exception('Error al insertar la entrada');
        }
        $ps_e->close();

        if ($fi_ing == ($ff_entrada + 1)) {
            // CONTINUACIÓN DE FOLIOS EXISTENTES
            $sql_exs = "UPDATE h_existencias
                        INNER JOIN h_pedidos ON h_existencias.no_cliente = h_pedidos.no_cliente
                                             AND h_existencias.marca = h_pedidos.marca
                                             AND h_existencias.edo = h_pedidos.edo
                                             AND h_existencias.serie = h_pedidos.serie
                                             AND h_existencias.tipo = h_pedidos.tipo
                        SET h_existencias.fol_ini = IF(h_existencias.existencias > 0, h_existencias.fol_ini, h_pedidos.fi),
                            h_existencias.fol_fin = h_pedidos.ff,
                            h_existencias.existencias = h_existencias.existencias + h_pedidos.cantidad
                        WHERE h_pedidos.id_row = ?
                          AND h_existencias.id_existencias = (
                              SELECT id_existencias FROM (SELECT * FROM h_existencias) AS h_existencias_sub
                              WHERE no_cliente = h_pedidos.no_cliente AND marca = h_pedidos.marca AND edo = h_pedidos.edo
                                AND serie = h_pedidos.serie AND tipo = h_pedidos.tipo
                              ORDER BY fol_fin DESC LIMIT 1
                          )";
            $ps_e = $conexion->prepare($sql_exs);
            $ps_e->bind_param('i', $idFilaPedido);
            if (!$ps_e->execute()) {
                throw new Exception('Error al actualizar la existencia [ESTADOS]');
            }
            $ps_e->close();

            $mensaje_p = 'Los folios se han agregado correctamente a las existencias';
        } else {
            // NUEVO RENGLÓN DE EXISTENCIAS (folios no contiguos)
            $sql_exs = "INSERT INTO h_existencias (no_cliente, marca, edo, tipo, serie, fol_ini, fol_fin, existencias)
                        (SELECT no_cliente, marca, edo, tipo, serie, fi, ff, cantidad FROM h_pedidos WHERE h_pedidos.id_row = ?)";
            $ps_e = $conexion->prepare($sql_exs);
            $ps_e->bind_param('i', $idFilaPedido);
            if (!$ps_e->execute()) {
                throw new Exception('Error al insertar la existencia [ESTADOS]');
            }
            $ps_e->close();

            $mensaje_p = 'Los folios se han agregado como un nuevo registro de existencias';
        }

        $sql_ped = "UPDATE h_pedidos SET status = 6 WHERE id_row = ?";
        $ps_e = $conexion->prepare($sql_ped);
        $ps_e->bind_param('i', $idFilaPedido);
        if (!$ps_e->execute()) {
            throw new Exception('Error al actualizar el estatus del pedido [ESTADOS]');
        }
        $ps_e->close();
    }

    $error = insertarNotificacion($conexion, $idFilaPedido, 1);
    if ($error === 'ERROR') {
        throw new Exception('Error al registrar Notificacion');
    }

    $conexion->commit();
    echo json_encode(['status' => 'OK', 'msj' => $mensaje_p]);
} catch (Exception $e) {
    $conexion->rollback();
    error_log('[ingresarPedido.php] ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'msj' => 'Error en la base de datos: ' . $e->getMessage()]);
} finally {
    $conexion->close();
}
