<?php
/**
 * listado.php — PHP 8.3
 * Fuente de datos (jqGrid) para la pestaña "Solicitados al Proveedor".
 * Devuelve JSON con el formato esperado por jqGrid {page, total, records, rows}.
 *
 * Cambios vs 5.6:
 *  - SQL concatenado ($clave, $campo, $sidx, $sord, $limit, $start) →
 *    sentencias preparadas para los valores, y listas blancas para los
 *    identificadores de columna/orden que no se pueden parametrizar
 *    directamente (nombre de columna a filtrar/ordenar y ASC/DESC).
 *    listado.js solo envía 'campo' como uno de: status, no_pedido,
 *    marca, no_cliente — se valida contra esa lista.
 *  - include → require_once con __DIR__
 *  - mysqli_set_charset("utf8") eliminado (conexion.php ya usa utf8mb4)
 *  - try/catch con error_log
 *  - Salida a HTML (botones de acción, comprobante) escapada con
 *    htmlspecialchars donde el valor viene de datos guardados en BD
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../common/conexion.php';


// Columnas válidas para filtrar (whitelist — evita inyección vía nombre de columna)
const CAMPOS_VALIDOS = ['status', 'no_pedido', 'marca', 'no_cliente'];
// Columnas válidas para ordenar (whitelist)
const SIDX_VALIDOS = [
    'no_pedido' => 'h_pedidos.no_pedido',
    'fecha'     => 'h_pedidos.fecha',
    'no_cliente'=> 'h_pedidos.no_cliente',
    'marca'     => 'marcas.marca',
    'fi'        => 'h_pedidos.fi',
    'ff'        => 'h_pedidos.ff',
    'cantidad'  => 'h_pedidos.cantidad',
    'status'    => 'h_pedidos.status',
    'holograma' => 'h_pedidos.holograma',
    'folio'     => 's.folio',
];

function tipoMezcalLabel(mixed $tipo, string $sinRegistroLabel = 'N/A'): string
{
    return match ((int)$tipo) {
        1 => 'MEZCAL',
        2 => 'ARTESANAL',
        3 => 'ANCESTRAL',
        default => $sinRegistroLabel,
    };
}

function statusLabel(mixed $status): string
{
    return match ((int)$status) {
        0 => 'SIN SOLICITAR',
        1 => 'SOLICITADO',
        2 => 'RECIBIDO',
        3 => 'PROCESANDO',
        4 => 'IMPRESO',
        5 => 'ENTREGADO',
        6 => 'EN INVENTARIO',
        default => '',
    };
}

function tipoPagoCorto(string $tipoPago): string
{
    if (strstr($tipoPago, 'TRANSFERENCIA') != '') return 'TF';
    if (strstr($tipoPago, 'CHEQUE') != '') return 'CH';
    if (strstr($tipoPago, 'EFECTIVO') != '' || strstr($tipoPago, 'DEPOSIT (IN') != '') return 'EF';
    return 'OT';
}

$page  = (int)($_POST['page'] ?? 0);
$limit = max(1, (int)($_POST['rows'] ?? 30));
$sidxRaw = (string)($_POST['sidx'] ?? '');
$sordRaw = strtolower((string)($_POST['sord'] ?? ''));
$depto = (string)($_POST['depto'] ?? '');
$cargo = (int)($_POST['cargo'] ?? 0);
$idus  = (int)($_POST['clvuser'] ?? 0);

$sidx = SIDX_VALIDOS[$sidxRaw] ?? SIDX_VALIDOS['no_pedido'];
$sord = $sordRaw === 'asc' ? 'ASC' : 'DESC';

try {
    $campo = null;
    $clave = null;
    if (isset($_POST['campo']) && in_array($_POST['campo'], CAMPOS_VALIDOS, true)) {
        $campo = (string)$_POST['campo'];
        $clave = (string)($_POST['clave'] ?? '');
    }

    if ($campo !== null) {
        // ----- CON FILTRO -----
        if ($campo === 'marca') {
            $stmtCount = $conexion->prepare(
                "SELECT COUNT(*) AS count FROM h_pedidos
                 INNER JOIN marcas ON marcas.no_cliente = h_pedidos.no_cliente AND marcas.cve_marca = h_pedidos.marca
                 WHERE marcas.marca = ?"
            );
        } else {
            $stmtCount = $conexion->prepare("SELECT COUNT(*) AS count FROM h_pedidos WHERE $campo = ?");
        }
        $stmtCount->bind_param('s', $clave);
        $stmtCount->execute();
        $count = (int)$stmtCount->get_result()->fetch_assoc()['count'];
        $stmtCount->close();

        $total_pages = $count > 0 ? (int)ceil($count / $limit) : 0;
        if ($page > $total_pages) {
            $page = $total_pages;
        }
        $start = max(0, $limit * $page - $limit);

        $whereClause = $campo === 'marca' ? 'marcas.marca = ?' : "h_pedidos.$campo = ?";
        $sql = "SELECT h_pedidos.id_row, h_pedidos.no_pedido, DATE(h_pedidos.fecha) fecha, h_pedidos.no_cliente, h_pedidos.marca cve,
                       h_pedidos.serie, marcas.marca, h_pedidos.edo, h_pedidos.tipo, h_pedidos.fi, h_pedidos.ff, h_pedidos.cantidad,
                       h_pedidos.status, h_pedidos.urgente, shp.tipo_pago, shp.comprobante, s.folio, h_pedidos.holograma
                FROM h_pedidos
                INNER JOIN marcas ON marcas.no_cliente = h_pedidos.no_cliente AND marcas.cve_marca = h_pedidos.marca
                INNER JOIN sh_detalle sh ON sh.id = h_pedidos.id_sh_d
                INNER JOIN sh_pedidos shp ON sh.id_solicitud = shp.id_solicitud
                INNER JOIN solicitudes s ON s.id = shp.id_solicitud
                WHERE $whereClause
                ORDER BY $sidx $sord
                LIMIT ?, ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('sii', $clave, $start, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        // ----- SIN FILTRO -----
        $result = $conexion->query("SELECT COUNT(*) AS count FROM h_pedidos");
        $count = (int)$result->fetch_assoc()['count'];

        $total_pages = $count > 0 ? (int)ceil($count / $limit) : 0;
        if ($page > $total_pages) {
            $page = $total_pages;
        }
        $start = max(0, $limit * $page - $limit);

        $sql = "SELECT h_pedidos.id_row, h_pedidos.no_pedido, DATE(h_pedidos.fecha) fecha, h_pedidos.no_cliente, h_pedidos.marca cve,
                       h_pedidos.serie, marcas.marca, h_pedidos.edo, h_pedidos.tipo, h_pedidos.fi, h_pedidos.ff, h_pedidos.cantidad,
                       h_pedidos.status, h_pedidos.urgente, shp.tipo_pago, shp.comprobante, s.folio, h_pedidos.holograma
                FROM h_pedidos
                INNER JOIN marcas ON marcas.no_cliente = h_pedidos.no_cliente AND marcas.cve_marca = h_pedidos.marca
                INNER JOIN sh_detalle sh ON sh.id = h_pedidos.id_sh_d
                INNER JOIN sh_pedidos shp ON sh.id_solicitud = shp.id_solicitud
                INNER JOIN solicitudes s ON s.id = shp.id_solicitud
                ORDER BY $sidx $sord
                LIMIT ?, ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('ii', $start, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
    }

    $respuesta = new stdClass();
    $respuesta->page[0] = $page;
    $respuesta->total[0] = $total_pages;
    $respuesta->records[0] = $count;

    $i = 0;
    $old_value = '';

    while ($fila = $result->fetch_assoc()) {
        $tp = tipoPagoCorto((string)$fila['tipo_pago']);
        if ((string)$fila['comprobante'] !== '') {
            $hrefComprobante = htmlspecialchars('../../clientes/hologramas/php/files/' . $fila['comprobante'] . '?' . uniqid(), ENT_QUOTES, 'UTF-8');
            $tituloComprobante = htmlspecialchars((string)$fila['tipo_pago'], ENT_QUOTES, 'UTF-8');
            $comp = '<p title="' . $tituloComprobante . '" ><a href="' . $hrefComprobante . '" target="_blank" > ' . $tp . ' </a><p>';
        } else {
            $comp = '<p title="' . htmlspecialchars((string)$fila['tipo_pago'], ENT_QUOTES, 'UTF-8') . '" >' . $tp . '<p>';
        }
        $fpago = ($cargo == 7 || $cargo == 12 || $cargo == 13 || $cargo == 20 || $idus == 1) ? $comp : '';

        $new_value = $fila['no_pedido'];
        $acciones = '';
        if ($new_value != $old_value) {
            if ((int)$fila['status'] === 0 && $cargo === 14) {
                $npEsc = (int)$new_value;
                $acciones = "<button type='button' name='btnReenviar' id='btnReenviar' class='btn btn-success btn-md' style='margin-top:0;' onClick='re_enviar($npEsc);'>"
                          . "<span class='glyphicon glyphicon-upload'></span></button>";
            }
        }

        $tipo_mez = tipoMezcalLabel($fila['tipo']);
        $status = statusLabel($fila['status']);

        if ((int)$fila['status'] === 5) {
            $id_fila = (int)$fila['id_row'];
            if (in_array($cargo, [12, 13, 14], true) || $idus === 1) {
                $acciones = "<button type='button' name='btnIngresar' id='btnIngresar' class='btn btn-primary btn-md class-botones-ingresar' style='margin-top:0;' onClick='ingresarPedido($id_fila);'><i class='fa fa-lg fa-sign-in' aria-hidden='true'></i></button>";
            }
        }

        $prioridad = ((int)$fila['urgente'] === 1) ? 'URGENTE' : 'NORMAL';
        $old_value = $new_value;

        $f_ini = $fila['no_cliente'] . $fila['cve'] . str_pad((string)$fila['fi'], 7, '0', STR_PAD_LEFT) . $fila['serie'];
        $f_fin = $fila['no_cliente'] . $fila['cve'] . str_pad((string)$fila['ff'], 7, '0', STR_PAD_LEFT) . $fila['serie'];
        $holograma = ($fila['holograma'] == '1') ? 'NUEVO V1' : (($fila['holograma'] == '2') ? 'NUEVO V2' : 'GENÉRICO');

        $respuesta->rows[$i]['id'] = $fila['id_row'];
        $respuesta->rows[$i]['cell'] = [
            $fila['no_pedido'], $fila['folio'], $fila['fecha'], $fila['no_cliente'], $fila['marca'], $fila['edo'],
            $tipo_mez, $f_ini, $f_fin, $fila['cantidad'], $holograma, $prioridad, $status, $acciones, $fpago,
        ];
        $i++;
    }
    $stmt->close();

    echo json_encode($respuesta);
} catch (mysqli_sql_exception $e) {
    error_log('[listado.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['page' => [0], 'total' => [0], 'records' => [0], 'rows' => []]);
} finally {
    $conexion->close();
}
