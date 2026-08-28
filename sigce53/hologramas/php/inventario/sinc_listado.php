<?php
/**
 * sinc_listado.php — PHP 8.3
 * "Sincroniza" el estatus de h_pedidos marcando sinc_down = status para
 * los pedidos pendientes. Ya operaba solo en local en el original (la
 * lectura remota estaba comentada y sustituida por la misma consulta
 * contra $conexion); se conserva ese comportamiento.
 * Devuelve JSON {status, msj, tipo_r}.
 *
 * Cambios vs 5.6:
 *  - SQL concatenado → sentencias preparadas
 *  - include → require_once con __DIR__
 *  - Se quita una línea sin efecto ("$ids;")
 *  - try/catch/finally con error_log
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../common/conexion.php';


try {
    $cont_err = 0;
    $ids = '';
    $sep = '';

    $get_pendientes = $conexion->query("SELECT * FROM h_pedidos WHERE status > 1 AND status != sinc_down");

    if ($get_pendientes->num_rows > 0) {
        $stmtUp = $conexion->prepare(
            "UPDATE h_pedidos SET status = ? WHERE no_pedido = ? AND no_cliente = ? AND marca = ? AND edo = ? AND fi = ?"
        );

        while ($row = $get_pendientes->fetch_assoc()) {
            $id_row = $row['id_row'];
            $status = $row['status'];
            $id_pe  = $row['no_pedido'];
            $no_c   = $row['no_cliente'];
            $mca    = $row['marca'];
            $edo    = $row['edo'];
            $fi     = $row['fi'];

            $stmtUp->bind_param('iissss', $status, $id_pe, $no_c, $mca, $edo, $fi);
            $res_up = $stmtUp->execute();

            if (!$res_up) {
                $cont_err++;
            } else {
                $ids .= $sep . $id_row;
                $sep = ',';
            }
        }
        $stmtUp->close();

        if ($cont_err === 0 && $ids !== '') {
            $res_down = $conexion->query("UPDATE h_pedidos SET sinc_down = status WHERE id_row IN ($ids)");
            if ($res_down === true) {
                echo json_encode(['status' => 'OK', 'msj' => 'La sincronizacion se ha realizado correctamente', 'tipo_r' => '2']);
            } else {
                echo json_encode(['status' => 'error', 'msj' => 'No se pudo finalizar la sincronizacion', 'tipo_r' => '2']);
            }
        } else {
            echo json_encode(['status' => 'error', 'msj' => 'Ocurrieron errores durante la sincronizacion', 'tipo_r' => '2']);
        }
    } else {
        echo json_encode(['status' => 'OK', 'msj' => 'Sin registros para sincronizar', 'tipo_r' => '2']);
    }
} catch (mysqli_sql_exception $e) {
    error_log('[sinc_listado.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msj' => 'Disculpe ha ocurrido un error, intente mas tarde']);
} finally {
    $conexion->close();
}
