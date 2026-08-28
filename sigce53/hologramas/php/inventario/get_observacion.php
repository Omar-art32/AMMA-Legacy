<?php
/**
 * get_observacion.php — PHP 8.3
 * Arma el historial (creación, pago, lista, generación, ingreso a
 * inventario) de un holograma dado su id_row.
 * Devuelve JSON {status, msj (HTML)}.
 *
 * Cambios vs 5.6:
 *  - Ya usaba sentencia preparada y try/catch — solo se añade
 *    declare(strict_types=1), require_once con __DIR__ y header JSON
 *    explícito para uniformar con el resto del módulo.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../common/conexion.php';


$msj = '';

try {
    $id = (string)($_POST['id'] ?? '');

    $sql = "SELECT hp.fecha, hp.usr, sh.fecha_pago, sh.usr_pago, sh.fecha_lista, sh.usr_lista,
            shp.tipo_pago, shp.comprobante, he.usr, he.fecha, s.fecha
            FROM h_pedidos hp
            INNER JOIN sh_detalle sh ON sh.id = hp.id_sh_d
            INNER JOIN sh_pedidos shp ON sh.id_solicitud = shp.id_solicitud
            INNER JOIN solicitudes s ON s.id = sh.id_solicitud
            LEFT JOIN h_entradas he ON (hp.no_cliente = he.no_cliente AND hp.edo = he.edo AND hp.marca = he.marca AND hp.fi = he.fol_ini AND hp.ff = he.fol_fin AND hp.cantidad = he.cantidad)
            WHERE hp.id_row = ?";
    $ps = $conexion->prepare($sql);
    $ps->bind_param('s', $id);
    if (!$ps->execute()) {
        throw new Exception('No se pudo encontrar las observaciones');
    }
    $ps->store_result();
    $ps->bind_result($hp_fecha, $hp_usr, $sh_fecha_pago, $sh_usr_pago, $sh_fecha_lista, $sh_usr_lista, $tipo_pago, $comprobante, $usr_entrada, $fecha_entrada, $s_fecha);

    while ($ps->fetch()) {
        $msj .= '<p><b> <span class="glyphicon glyphicon-asterisk"> </span> Creación de pedido por el Cliente </b><p>';
        $msj .= '<p>Fecha : <b>' . $s_fecha . '</b><p>';

        $msj .= '<p><b> <span class="glyphicon glyphicon-asterisk"> </span> Confirmó pago  </b><p>';
        $msj .= '<p>Usuario: <b>' . $sh_usr_pago . '</b><p>';
        $msj .= '<p>Fecha : <b>' . $sh_fecha_pago . '</b><p>';

        $msj .= '<p><b> <span class="glyphicon glyphicon-asterisk"> </span> Agregó a lista de pedido </b><p>';
        $msj .= '<p>Usuario: <b>' . $sh_usr_lista . '</b><p>';
        $msj .= '<p>Fecha : <b>' . $sh_fecha_lista . '</b><p>';

        $msj .= '<p><b> <span class="glyphicon glyphicon-asterisk"> </span> Generó pedido  </b><p>';
        $msj .= '<p>Usuario: <b>' . $hp_usr . '</b><p>';
        $msj .= '<p>Fecha : <b>' . $hp_fecha . '</b><p>';

        if ($usr_entrada != '') {
            $msj .= '<p><b> <span class="glyphicon glyphicon-asterisk"> </span> Ingresó hologramas a inventario  </b><p>';
            $msj .= '<p>Usuario: <b>' . $usr_entrada . '</b><p>';
            $msj .= '<p>Fecha : <b>' . $fecha_entrada . '</b><p>';
        }
    }
    $ps->close();

    echo json_encode(['status' => 'OK', 'msj' => $msj]);
} catch (Exception $e) {
    error_log('[get_observacion.php] ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'msj' => 'Error en la base de datos: ' . $e->getMessage()]);
} finally {
    $conexion->close();
}
