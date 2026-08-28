<?php
/**
 * get_observacion_pedido.php — PHP 8.3
 * Arma el historial de un detalle de pedido online (observaciones,
 * creación, pago, lista, generación).
 * Devuelve JSON {status, msj (HTML)}.
 *
 * Cambios vs 5.6:
 *  - utf8_encode() eliminado (entrada ya viene en UTF-8)
 *  - Ya usaba sentencia preparada y try/catch — se añade
 *    declare(strict_types=1), require_once con __DIR__ y header JSON
 *  - error_log en el catch
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../common/conexion.php';


$msj = '';

try {
    $id = (string)($_POST['id'] ?? '');

    $sql = "SELECT sh.observaciones, shp.tipo_pago, shp.comprobante, hp.fecha, hp.usr,
                   sh.fecha_pago, sh.usr_pago, sh.fecha_lista, sh.usr_lista, s.fecha
            FROM sh_detalle sh
            INNER JOIN sh_pedidos shp ON sh.id_solicitud = shp.id_solicitud
            LEFT JOIN h_pedidos hp ON sh.id = hp.id_sh_d
            INNER JOIN solicitudes s ON s.id = sh.id_solicitud
            WHERE sh.id = ?";
    $ps = $conexion->prepare($sql);
    $ps->bind_param('s', $id);
    if (!$ps->execute()) {
        throw new Exception('No se pudo encontrar las observaciones');
    }

    $ps->store_result();
    $ps->bind_result($observaciones, $tipo_pago, $comprobante, $hp_fecha, $hp_usr, $sh_fecha_pago, $sh_usr_pago, $sh_fecha_lista, $sh_usr_lista, $s_fecha);
    while ($ps->fetch()) {
        $msj .= '<p><b> ' . $observaciones . '</b><p>';

        if ($s_fecha != '') {
            $msj .= '<p><b> <span class="glyphicon glyphicon-asterisk"> </span> Creación de pedido por el Cliente </b><p>';
            $msj .= '<p>Fecha : <b>' . $s_fecha . '</b><p>';
        }
        if ($sh_usr_pago != '') {
            $msj .= '<p><b> <span class="glyphicon glyphicon-asterisk"> </span> Confirmó pago  </b><p>';
            $msj .= '<p>Usuario: <b>' . $sh_usr_pago . '</b><p>';
            $msj .= '<p>Fecha : <b>' . $sh_fecha_pago . '</b><p>';
        }
        if ($sh_usr_lista != '') {
            $msj .= '<p><b> <span class="glyphicon glyphicon-asterisk"> </span> Agregó a lista de pedido </b><p>';
            $msj .= '<p>Usuario: <b>' . $sh_usr_lista . '</b><p>';
            $msj .= '<p>Fecha : <b>' . $sh_fecha_lista . '</b><p>';
        }
        if ($hp_usr != '') {
            $msj .= '<p><b> <span class="glyphicon glyphicon-asterisk"> </span> Generó pedido  </b><p>';
            $msj .= '<p>Usuario: <b>' . $hp_usr . '</b><p>';
            $msj .= '<p>Fecha : <b>' . $hp_fecha . '</b><p>';
        }
    }
    $ps->close();

    echo json_encode(['status' => 'OK', 'msj' => $msj]);
} catch (Exception $e) {
    error_log('[get_observacion_pedido.php] ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'msj' => 'Error en la base de datos: ' . $e->getMessage()]);
} finally {
    $conexion->close();
}
