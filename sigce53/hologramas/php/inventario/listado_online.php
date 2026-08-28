<?php
/**
 * listado_online.php — PHP 8.3
 * Fuente de datos (jqGrid) para la pestaña "Pedidos Online".
 * Devuelve JSON con el formato esperado por jqGrid {page, total, records, rows}.
 *
 * Cambios vs 5.6:
 *  - SQL concatenado (filtros $clave/$clave1..4) → condiciones armadas
 *    con marcadores (?) y ejecutadas con sentencia preparada. 'campo'
 *    se valida contra una lista blanca (los únicos valores que envía
 *    pedidos_online.js: no_cliente, id_solicitud, marca, estatus, todos).
 *  - $respuesta->sql[0] = $consulta ELIMINADO: exponía el SQL crudo
 *    (incluyendo los filtros) en la respuesta JSON — fuga de información.
 *  - include → require_once con __DIR__
 *  - mysqli_set_charset("utf8") eliminado (conexion.php ya usa utf8mb4)
 *  - Las dos ramas (con/sin filtro) del original eran casi idénticas;
 *    se unifican en un solo flujo con una función compartida para
 *    construir cada renglón, evitando mantener la lógica duplicada
 *  - Salida a HTML (botones de acción, comprobante) escapada con
 *    htmlspecialchars donde el valor viene de datos guardados en BD
 *  - try/catch con error_log
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../common/conexion.php';
require_once __DIR__ . '/../../../common/cfg_server.php';


const CAMPOS_VALIDOS = ['no_cliente', 'id_solicitud', 'marca', 'estatus', 'todos'];

function statusOnlineLabel(mixed $status): string
{
    return match ((int)$status) {
        1 => 'REVISIÓN',
        2 => 'AUTORIZADO',
        3 => 'EN LISTA',
        4, 5, 6 => 'SOLICITADO A PROVEEDOR',
        7 => 'CANCELADO',
        default => '',
    };
}

function pagoOpcionLabel(mixed $opcion): string
{
    return match ((int)$opcion) {
        2 => 'PAQUETE EMPRENDEDOR',
        3 => 'CARGO A ESTADO DE CUENTA',
        4 => 'SEFADER',
        5 => 'AUTORIZADO POR UT',
        default => 'PAGO NORMAL',
    };
}

function tipoMezcalOnlineLabel(mixed $tipo): string
{
    return match ((int)$tipo) {
        1 => 'MEZCAL',
        2 => 'ARTESANAL',
        3 => 'ANCESTRAL',
        default => 'N/A',
    };
}

function tipoPagoInfo(string $tipoPago): array
{
    if (strstr($tipoPago, 'TRANSFERENCIA') != '') return ['TF', 2];
    if (strstr($tipoPago, 'CHEQUE') != '') return ['CH', 3];
    if (strstr($tipoPago, 'EFECTIVO') != '' || strstr($tipoPago, 'DEPOSIT (IN') != '') return ['EF', 1];
    return ['OT', 4];
}

$page  = (int)($_POST['page'] ?? 1);
$limit = max(1, (int)($_POST['rows'] ?? 10));
$depto = (string)($_POST['depto'] ?? '');
$cargo = (int)($_POST['cargo'] ?? 0);
$idus  = (string)($_POST['clvuser'] ?? '');
$nivel = (string)($_POST['nivel'] ?? '');

try {
    // ----- ARMAR CONDICIONES (con lista blanca de columnas) -----
    $condiciones = [];
    $params = [];
    $types = '';

    if (isset($_POST['campo']) && in_array($_POST['campo'], CAMPOS_VALIDOS, true)) {
        $campo = (string)$_POST['campo'];

        if ($campo === 'todos') {
            $clave1 = (string)($_POST['valor1'] ?? '');
            $clave2 = (string)($_POST['valor2'] ?? '');
            $clave3 = (string)($_POST['valor3'] ?? '');
            $clave4 = (string)($_POST['valor4'] ?? '');

            if ($clave1 !== '') {
                $condiciones[] = 'shp.id_solicitud = ?';
                $params[] = $clave1;
                $types .= 's';
            }
            if ($clave2 !== '') {
                $condiciones[] = 'marcas.marca = ?';
                $params[] = $clave2;
                $types .= 's';
            }
            if ($clave3 > 0) {
                if ((int)$clave3 === 4) {
                    $condiciones[] = 'sh_detalle.status IN (4,5,6)';
                } else {
                    $condiciones[] = 'sh_detalle.status = ?';
                    $params[] = $clave3;
                    $types .= 's';
                }
            }
            if ($clave4 !== '') {
                $condiciones[] = 'shp.no_cliente = ?';
                $params[] = $clave4;
                $types .= 's';
            }
        } else {
            $clave = (string)($_POST['valor'] ?? '');
            if ($campo === 'marca') {
                $condiciones[] = 'marcas.marca = ?';
                $params[] = $clave;
                $types .= 's';
            } elseif ($campo === 'estatus') {
                if ((int)$clave === 4) {
                    $condiciones[] = 'sh_detalle.status IN (4,5,6)';
                } else {
                    $condiciones[] = 'sh_detalle.status = ?';
                    $params[] = $clave;
                    $types .= 's';
                }
            } else {
                $condiciones[] = "shp.$campo = ?";
                $params[] = $clave;
                $types .= 's';
            }
        }
    }

    $whereSql = count($condiciones) > 0 ? ('WHERE ' . implode(' AND ', $condiciones)) : '';

    // ----- CONTEO -----
    $sqlCont = "SELECT COUNT(*) AS count
                FROM sh_pedidos shp
                INNER JOIN solicitudes ON solicitudes.id = shp.id_solicitud
                INNER JOIN sh_detalle ON sh_detalle.id_solicitud = shp.id_solicitud
                INNER JOIN marcas ON marcas.no_cliente = shp.no_cliente AND marcas.cve_marca = sh_detalle.marca
                $whereSql";
    $stmtCont = $conexion->prepare($sqlCont);
    if ($types !== '') {
        $stmtCont->bind_param($types, ...$params);
    }
    $stmtCont->execute();
    $count = (int)$stmtCont->get_result()->fetch_assoc()['count'];
    $stmtCont->close();

    $total_pages = $count > 0 ? (int)ceil($count / $limit) : 0;
    if ($page > $total_pages) {
        $page = $total_pages;
    }
    $start = ($total_pages === 0) ? 0 : max(0, $limit * $page - $limit);

    // ----- CONSULTA PAGINADA -----
    $sql = "SELECT solicitudes.id, solicitudes.folio, solicitudes.anio, sh_detalle.id id_det, shp.id_solicitud,
                   DATE(solicitudes.fecha) fecha, shp.no_cliente, sh_detalle.marca cve_marca, marcas.marca, sh_detalle.tipo,
                   sh_detalle.edo, sh_detalle.urgente, sh_detalle.cantidad, sh_detalle.importe, sh_detalle.status,
                   shp.comprobante, sh_detalle.observaciones, shp.tipo_pago, TIME(solicitudes.fecha) hora,
                   sh_detalle.pago_opcion pago_opcion, sh_detalle.pago_promo
            FROM sh_pedidos shp
            INNER JOIN solicitudes ON solicitudes.id = shp.id_solicitud
            INNER JOIN sh_detalle ON sh_detalle.id_solicitud = shp.id_solicitud
            INNER JOIN marcas ON marcas.no_cliente = shp.no_cliente AND marcas.cve_marca = sh_detalle.marca
            $whereSql
            ORDER BY shp.id_solicitud DESC
            LIMIT ?, ?";
    $stmt = $conexion->prepare($sql);
    $allParams = [...$params, $start, $limit];
    $allTypes = $types . 'ii';
    $stmt->bind_param($allTypes, ...$allParams);
    $stmt->execute();
    $result = $stmt->get_result();

    $respuesta = new stdClass();
    $respuesta->page[0] = $page;
    $respuesta->total[0] = $total_pages;
    $respuesta->records[0] = $count;

    $i = 0;
    while ($fila = $result->fetch_assoc()) {
        [$tp, $tpe] = tipoPagoInfo((string)$fila['tipo_pago']);

        if ((string)$fila['comprobante'] !== '') {
            $hrefComprobante = htmlspecialchars('../../clientes/hologramas/php/files/' . $fila['comprobante'] . '?' . uniqid(), ENT_QUOTES, 'UTF-8');
            $tituloComprobante = htmlspecialchars((string)$fila['tipo_pago'], ENT_QUOTES, 'UTF-8');
            $comp = '<p title="' . $tituloComprobante . '" ><a href="' . $hrefComprobante . '" target="_blank" > ' . $tp . ' </a><p>';
        } else {
            $comp = '<p title="' . htmlspecialchars((string)$fila['tipo_pago'], ENT_QUOTES, 'UTF-8') . '" >' . $tp . '<p>';
        }
        $fpago = ($cargo == 7 || $cargo == 12 || $cargo == 13 || $cargo == 14 || $cargo == 20 || $idus == 1) ? $comp : '';

        $marca = $fila['cve_marca'] . ' - ' . $fila['marca'];
        $pago_opcion = pagoOpcionLabel($fila['pago_opcion']);
        $status = statusOnlineLabel($fila['status']);
        if ((int)$fila['status'] !== 7 && (int)$fila['status'] !== 1) {
            $status .= "<br><span style='font-size: 8px;color:blue;'>{$pago_opcion}</span>";
        }
        $tipo_mez = tipoMezcalOnlineLabel($fila['tipo']);
        $prioridad = ((int)$fila['urgente'] === 1) ? 'URGENTE' : 'NORMAL';

        $idDet = (int)$fila['id_det'];
        $idSol = (int)$fila['id'];
        $link = '';

        if (($depto === 'DA' || $idus == '1' || $idus == '4') || $nivel == '1') {
            if ((int)$fila['status'] === 1) {
                $link = "<button type=\"button\"  name=\"btn_asignar\" id=\"btn_asignar\" class=\"btn btn-sm btn-success\" onClick=confirma_pago_online_otro($idDet,$idSol,$tpe,{$fila['urgente']})><i class=\"fa fa-lg fa-usd\"></i></button>";
                if ((string)$fila['comprobante'] !== '') {
                    $url = "'http://" . $svr_dir . '/clientes/hologramas/php/files/' . $fila['comprobante'] . '?' . uniqid() . "'";
                    $link .= '&nbsp;<button type="button"  name="btn_asignar" id="btn_asignar" class="btn btn-sm btn-danger" onClick="ver_comprobante(' . $url . ')" style="padding-left:7px !important; padding-right:7px !important;"><i class="fa fa-lg fa-file" style="color:#fff"></i></button>';
                }
                $link .= "&nbsp;&nbsp;<button type=\"button\" name=\"btn_cancelar\" id=\"btn_cancelar\" class=\"btn btn-sm btn-warning\" onclick=\"cancelar_solicitud($idDet,$idSol)\"><i class=\"fa fa-lg fa-close\"></i></button>";
            } else {
                if ((string)$fila['comprobante'] !== '') {
                    $url = "'http://" . $svr_dir . '/clientes/hologramas/php/files/' . $fila['comprobante'] . '?' . uniqid() . "'";
                    $link = '<button type="button"  name="btn_asignar" id="btn_asignar" class="btn btn-sm btn-danger" onClick="ver_comprobante(' . $url . ')" style="padding-left:7px !important; padding-right:7px !important;"><i class="fa fa-lg fa-file" style="color:#fff"></i></button>';
                }
                if ((string)$fila['pago_opcion'] === '5') {
                    $link .= "&nbsp;&nbsp;<button type=\"button\"  name=\"btn_asignar\" id=\"btn_asignar\" class=\"btn btn-sm btn-success\" onClick=\"modifica_pago_online_otro($idDet,$idSol,$tpe)\"><i class=\"fa fa-lg fa-usd\"></i></button>";
                }
            }
        } elseif ($depto === 'OC' || $idus == '1') {
            if ((int)$fila['status'] === 2) {
                $link = "<button type=\"button\"  name=\"btn_asignar\" id=\"btn_asignar\" class=\"btn btn-sm btn-success\" onClick=\"get_data_online_tmp($idDet,{$fila['cantidad']})\"><i class=\"fa fa-lg fa-cart-plus\"></i></button>&nbsp;<button type=\"button\"  name=\"btnEditPO\" id=\"btnEditPO\" class=\"btn btn-sm btn-primary\" onClick=\"getEditarPO($idDet)\"><i class=\"fa fa-lg fa-edit\"></i></button>&nbsp;&nbsp;<button type=\"button\" name=\"btn_cancelar\" id=\"btn_cancelar\" class=\"btn btn-sm btn-warning\" onclick=\"cancelar_solicitud($idDet,$idSol)\"><i class=\"fa fa-lg fa-close\"></i></button>";
            }
        }

        if ((string)$fila['observaciones'] !== '') {
            $link .= "&nbsp;<button type=\"button\" name=\"btn_info\" id=\"btn_info\" class=\"btn btn-sm btn-info\" onclick=\"get_observacion_pedido($idDet)\"><i class=\"fa fa-lg fa-info\"></i></button>";
        }

        $divide = (float)$fila['importe'] / max(1, (int)$fila['cantidad']);
        $txtdivide = ((string)$fila['pago_promo'] === '1') ? "<br><span style='font-size: 8px;color:red;'>BUEN FIN</span>" : '';
        $importe = '$ ' . number_format((float)$fila['importe'], 2, '.', ',') . $txtdivide;

        $folio = ($fila['id'] < 33159) ? $fila['folio'] . '/' . substr((string)$fila['anio'], -2, 2) : $fila['folio'];

        $respuesta->rows[$i]['id'] = $fila['id_det'];
        $respuesta->rows[$i]['cell'] = [
            $folio, $fila['fecha'] . '  ' . $fila['hora'], $fila['no_cliente'], $marca, $tipo_mez, $fila['edo'],
            number_format((float)$fila['cantidad'], 0), $importe, $prioridad, $status, $link, $fpago, $pago_opcion, $fila['status'],
        ];
        $respuesta->rows[$i]['opera'] = $divide . ':' . (float)$fila['importe'] . ':' . (int)$fila['cantidad'];
        $i++;
    }
    $stmt->close();

    echo json_encode($respuesta);
} catch (mysqli_sql_exception $e) {
    error_log('[listado_online.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['page' => [0], 'total' => [0], 'records' => [0], 'rows' => []]);
} finally {
    $conexion->close();
}
