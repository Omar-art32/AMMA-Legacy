<?php
/**
 * get_data_detalle.php — PHP 8.3
 * Trae el detalle de un renglón de sh_detalle y el último folio conocido
 * (en cola, en proceso o en entradas) para esa marca/serie, junto con el
 * siguiente No. de Pedido.
 * Devuelve JSON {status, msj, folio_det, no_pedido, cliente, edo, marca,
 * nom_marca, serie, tipo, cantidad, urgente, mto, fini, ffin}.
 *
 * Cambios vs 5.6:
 *  - utf8_encode() eliminado (entrada ya viene en UTF-8)
 *  - SQL concatenado ($folio_sh_det, $cliente/$marca/$serie) → sentencias
 *    preparadas en todas las consultas
 *  - Se quita el SQL crudo del mensaje de error (fuga de información)
 *  - include → require_once con __DIR__
 *  - try/catch con error_log
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../common/conexion.php';


$folio_sh_det = (int)($_POST['folio'] ?? 0);
$no_pedido = 0;

try {
    // OBTENEMOS EL NUMERO DE PEDIDO ACTUAL
    $res_tmp = $conexion->query("SELECT IF(MAX(no_pedido) IS NULL, 0, MAX(no_pedido)) FROM h_tmp_pedido");
    if ($res_tmp && $res_tmp->num_rows === 1) {
        $row_tmp = $res_tmp->fetch_row();
        $no_pedido = $row_tmp[0];
        if ($no_pedido == 0) {
            $result = $conexion->query("SELECT IF(MAX(no_pedido) IS NULL, 0, MAX(no_pedido)) FROM h_pedidos");
            if ($result && $result->num_rows === 1) {
                $row = $result->fetch_row();
                $no_pedido = $row[0] + 1;
            }
        }
    }

    $cliente = '';
    $edo = '';
    $marca = '';
    $nom_marca = '';
    $serie = '';
    $tipo = 0;
    $cantidad = 0;
    $urgente = 0;
    $mto_existe = 0;
    $f_ini = 0;
    $f_fin = 0;
    $msj = '';

    $stmtDet = $conexion->prepare(
        "SELECT solicitudes.fecha, sh_pedidos.no_cliente, sh_detalle.id_solicitud, sh_detalle.marca cve_marca,
                marcas.marca, marcas.serie, sh_detalle.tipo, sh_detalle.edo, sh_detalle.cantidad, sh_detalle.urgente,
                sh_detalle.importe, sh_detalle.status
         FROM sh_pedidos
         INNER JOIN solicitudes ON solicitudes.id = sh_pedidos.id_solicitud AND solicitudes.tipo = 2
         INNER JOIN sh_detalle ON sh_detalle.id_solicitud = sh_pedidos.id_solicitud
         INNER JOIN marcas ON marcas.no_cliente = sh_pedidos.no_cliente AND marcas.cve_marca = sh_detalle.marca
         WHERE sh_detalle.id = ?"
    );
    $stmtDet->bind_param('i', $folio_sh_det);
    $stmtDet->execute();
    $res_detalle = $stmtDet->get_result();

    if ($res_detalle && $res_detalle->num_rows === 1) {
        $row = $res_detalle->fetch_assoc();
        $cliente   = $row['no_cliente'];
        $edo       = $row['edo'];
        $marca     = $row['cve_marca'];
        $nom_marca = $row['marca'];
        $serie     = $row['serie'];
        $tipo      = $row['tipo'];
        $cantidad  = $row['cantidad'];
        $urgente   = $row['urgente'];
        $stmtDet->close();

        // AHORA OBTENEMOS LOS ULTIMOS FOLIOS PEDIDOS O REGISTRADOS EN INVENTARIO
        $stmtTmp = $conexion->prepare(
            "SELECT fi, ff, cantidad FROM h_tmp_pedido WHERE no_cliente = ? AND marca = ? AND serie = ? ORDER BY ff DESC LIMIT 1"
        );
        $stmtTmp->bind_param('sss', $cliente, $marca, $serie);
        $stmtTmp->execute();
        $res_tmp_pedido = $stmtTmp->get_result();

        if ($res_tmp_pedido->num_rows > 0) {
            $row = $res_tmp_pedido->fetch_row();
            $f_ini = trim((string)$row[0]);
            $f_fin = trim((string)$row[1]);
            $mto_existe = trim((string)$row[2]);
            $msj = 'Pedido en cola';
        } else {
            $stmtPed = $conexion->prepare(
                "SELECT fi, ff, cantidad FROM h_pedidos WHERE no_cliente = ? AND marca = ? AND serie = ? ORDER BY ff DESC LIMIT 1"
            );
            $stmtPed->bind_param('sss', $cliente, $marca, $serie);
            $stmtPed->execute();
            $res_pedidos = $stmtPed->get_result();

            if ($res_pedidos->num_rows > 0) {
                $row = $res_pedidos->fetch_row();
                $f_ini = trim((string)$row[0]);
                $f_fin = trim((string)$row[1]);
                $mto_existe = trim((string)$row[2]);
                $msj = 'Ultimo pedido';
            } else {
                $stmtEnt = $conexion->prepare(
                    "SELECT fol_ini, fol_fin, cantidad FROM h_entradas WHERE no_cliente = ? AND marca = ? AND serie = ? ORDER BY fol_fin DESC LIMIT 1"
                );
                $stmtEnt->bind_param('sss', $cliente, $marca, $serie);
                $stmtEnt->execute();
                $res_entradas = $stmtEnt->get_result();

                if ($res_entradas->num_rows > 0) {
                    $row = $res_entradas->fetch_row();
                    $f_ini = trim((string)$row[0]);
                    $f_fin = trim((string)$row[1]);
                    $mto_existe = trim((string)$row[2]);
                    $msj = 'Ultima Entrada';
                } else {
                    $msj = 'Sin registros de entradas o pedidos';
                }
                $stmtEnt->close();
            }
            $stmtPed->close();
        }
        $stmtTmp->close();

        if ($no_pedido > 0) {
            $f_ini = $f_fin + 1;
            $f_fin = $f_fin + $cantidad;
            echo json_encode([
                'status' => 'OK', 'msj' => $msj, 'folio_det' => $folio_sh_det, 'no_pedido' => $no_pedido,
                'cliente' => $cliente, 'edo' => $edo, 'marca' => $marca, 'nom_marca' => $nom_marca,
                'serie' => $serie, 'tipo' => $tipo, 'cantidad' => $cantidad, 'urgente' => $urgente,
                'mto' => $mto_existe, 'fini' => $f_ini, 'ffin' => $f_fin,
            ]);
        } else {
            echo json_encode(['status' => 'error', 'msj' => 'No se pudo obtener el numero de pedido']);
        }
    } else {
        $stmtDet->close();
        echo json_encode(['status' => 'error', 'msj' => 'No se encontró el detalle del pedido']);
    }
} catch (mysqli_sql_exception $e) {
    error_log('[get_data_detalle.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msj' => 'Disculpe ha ocurrido un error, intente mas tarde']);
} finally {
    $conexion->close();
}
