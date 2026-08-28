<?php
/**
 * php/loadGuias.php — PHP 8.3
 * Cargador del grid de guías (JSON {total, rows} con paginación limit/offset).
 *
 * Cambios vs 5.6:
 *  - Inyección SQL cerrada: search, fechas, cliente, limit y offset ahora son
 *    parámetros vinculados / enteros validados (antes: interpolación directa)
 *  - Introspección barata: los nombres de columna se obtienen con LIMIT 0
 *    (el original hacía SELECT * de las tablas COMPLETAS solo para leer campos)
 *  - Statement del fallback de ensambles preparado UNA vez fuera del bucle
 *    (antes: prepare() por fila con el valor concatenado)
 *  - $registro se reinicia por fila (antes arrastraba claves entre filas)
 *  - Sin espacios antes de <?php (ensuciaban la respuesta JSON)
 *  - Content-Type y manejo de excepciones sin exponer $conexion->error
 *  - Fechas validadas con formato YYYY-MM-DD
 */
declare(strict_types=1);

require_once __DIR__ . '/../../common/conexion.php';

header('Content-Type: application/json; charset=utf-8');

/** Valida fecha YYYY-MM-DD; regresa '' si no cumple. */
function fechaValida(string $f): string
{
    return preg_match('/^\d{4}-\d{2}-\d{2}$/', $f) ? $f : '';
}

$limit  = (int)($_GET['limit'] ?? 25);
$offset = (int)($_GET['offset'] ?? 0);
$search = (string)($_GET['search'] ?? '');
$clientesel = (string)($_GET['clientesel'] ?? '');
$fechaini = fechaValida((string)($_GET['fechaini'] ?? ''));
$fechafin = fechaValida((string)($_GET['fechafin'] ?? ''));

$rows = [];

try {
    // -----------------------------------------------------------------
    // Nombres de columna de las dos tablas (para armar el registro con
    // las mismas claves que el original). LIMIT 0: solo metadatos.
    // -----------------------------------------------------------------
    $infoCampo  = $conexion->query('SELECT * FROM cextracciones LIMIT 0')->fetch_fields();
    $infoCampo2 = $conexion->query('SELECT * FROM historial_extraccion_verificadores LIMIT 0')->fetch_fields();

    // -----------------------------------------------------------------
    // Condiciones dinámicas parametrizadas
    // -----------------------------------------------------------------
    $cond   = ["p.status_predio = '1'", "p.status = '1'"];
    $tipos  = '';
    $params = [];

    if ($search !== '') {
        $cond[] = "(p.id_paraje LIKE CONCAT('%', ?, '%')
                 OR p.paraje LIKE CONCAT('%', ?, '%')
                 OR p.id_cliente LIKE CONCAT('%', ?, '%'))";
        $tipos .= 'sss';
        array_push($params, $search, $search, $search);
    }
    if ($fechaini !== '' && $fechafin === '') {
        $cond[] = 'DATE(c.fecharegistro) = ?';
        $tipos .= 's';
        $params[] = $fechaini;
    } elseif ($fechaini !== '' && $fechafin !== '') {
        $cond[] = 'DATE(c.fecharegistro) BETWEEN ? AND ?';
        $tipos .= 'ss';
        array_push($params, $fechaini, $fechafin);
    }
    if ($clientesel !== '' && $clientesel !== '0') {
        $cond[] = 'p.id_cliente = ?';   // el IN('valor') original era una igualdad
        $tipos .= 's';
        $params[] = $clientesel;
    }

    $where = 'WHERE ' . implode(' AND ', $cond);

    $cadenaSql = "SELECT c.*, hev.*, c.id_extraccion cid_extraccion, p.id_cliente,
        p.paraje, pe.tapada, pe.lts_producidos, p.maguey_con_registro, p.servicio,
        IF(pe.fecha_rendimiento = '0000-00-00', DATE(pe.periodo_destilacion_fin), DATE(pe.fecha_rendimiento)) pe_fecha
        FROM paraje p
        INNER JOIN cextracciones c ON p.id_paraje = c.id_paraje
        LEFT JOIN historial_extraccion_verificadores hev ON c.id_extraccion = hev.no_guia
        LEFT JOIN rv_produccion_entrada pe ON c.id_extraccion = pe.no_guia
        {$where} ORDER BY c.id ASC";

    // Total sin paginación
    $stmt = $conexion->prepare($cadenaSql);
    if ($tipos !== '') $stmt->bind_param($tipos, ...$params);
    $stmt->execute();
    $stmt->store_result();
    $totalRes = $stmt->num_rows;
    $stmt->close();

    // Página solicitada (limit/offset como enteros ya casteados)
    $stmt = $conexion->prepare($cadenaSql . " LIMIT {$limit} OFFSET {$offset}");
    if ($tipos !== '') $stmt->bind_param($tipos, ...$params);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Fallback de tapadas para guías ensambladas: preparado UNA vez
    $stmtEns = $conexion->prepare(
        "SELECT pe.tapada, pe.lts_producidos,
                IF(pe.fecha_rendimiento = '0000-00-00', pe.periodo_destilacion_fin, pe.fecha_rendimiento) pe_fecha
         FROM rv_produccion_entrada pe
         INNER JOIN rv_produccion_ensamble pen ON pe.id_produccion_entrada = pen.id_produccion_entrada
         WHERE pen.no_guia = ? LIMIT 1"
    );

    foreach ($result as $row) {
        $registro = [];   // nuevo por fila (el original lo arrastraba)

        foreach ($infoCampo as $valor) {
            if ($valor->name !== 'poligono') {
                $registro[$valor->name] = $row[$valor->name];
            }
        }
        foreach ($infoCampo2 as $valor) {
            if ($valor->name !== 'poligono') {
                $registro[$valor->name] = $row[$valor->name];
            }
        }

        $registro['tguia'] = match (true) {
            (int)$row['maguey_con_registro'] === 2 && $row['servicio'] === 'EXCLUSIVO' => 'DOCUMENTAL EXCLUSIVA',
            (int)$row['maguey_con_registro'] === 2 && $row['servicio'] === 'NORMAL'    => 'DOCUMENTAL NORMAL',
            default => 'EN SITIO',
        };
        $registro['estado'] = (($row['no_cliente_envia'] ?? '') != '') ? 'USADA' : 'DISPONIBLE';
        $registro['paraje'] = $row['paraje'];
        $registro['id_extraccion'] = $row['cid_extraccion'];
        $registro['tapada'] = '';
        $registro['lts_producidos'] = '';
        $registro['pe_fecha'] = '';
        $registro['id_cliente'] = $row['id_cliente'];

        // Dónde fue usada la guía
        if (($row['tapada'] ?? '') != '') {
            $registro['tapada'] = $row['tapada'];
            $registro['lts_producidos'] = $row['lts_producidos'];
            $registro['pe_fecha'] = $row['pe_fecha'];
        } elseif (($row['no_guia'] ?? '') != '') {
            $noGuia = (string)$row['no_guia'];
            $stmtEns->bind_param('s', $noGuia);
            $stmtEns->execute();
            foreach ($stmtEns->get_result()->fetch_all(MYSQLI_ASSOC) as $rowt) {
                $registro['tapada'] = $rowt['tapada'];
                $registro['lts_producidos'] = $rowt['lts_producidos'];
                $registro['pe_fecha'] = $rowt['pe_fecha'];
            }
        }

        $registro['limit'] = $limit;
        $registro['offset'] = $offset;
        $rows[] = $registro;
    }
    $stmtEns->close();

    echo json_encode(['total' => $totalRes, 'rows' => $rows]);
} catch (mysqli_sql_exception $e) {
    error_log('[loadGuias.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['total' => 0, 'rows' => [], 'error' => 'Error al cargar guías']);
} finally {
    $conexion->close();
}