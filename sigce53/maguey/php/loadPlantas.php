<?php
/**
 * php/loadPlantas.php — PHP 8.3
 * Cargador del grid de plantas (JSON {total, rows} con paginación).
 *
 * Cambios vs 5.6:
 *  - Inyección SQL cerrada en search, fechas, cliente, limit/offset
 *  - BUG DE ORIGEN CORREGIDO: cuando solo se filtraba por usuario (idus) sin
 *    búsqueda/fechas/cliente, el fragmento de conflicto de intereses se
 *    concatenaba como " AND (...)" SIN un WHERE previo → SQL inválido.
 *    Ahora todas las condiciones se acumulan en un arreglo y el WHERE se
 *    arma una sola vez.
 *  - POSIBLE BUG DE ORIGEN: el filtro de conflicto usaba la columna
 *    "no_cliente", que no existe en ninguna de las tablas del JOIN
 *    (ep, p, c, l, m, e). Se corrigió a p.id_cliente, que es la columna
 *    equivalente en esta consulta. REVISAR CON NEGOCIO.
 *  - Introspección con LIMIT 0 (el original leía existenciaplanta COMPLETA
 *    solo para obtener los nombres de columna)
 *  - Lista de conflicto validada con regex antes del IN()
 *  - $registro nuevo por fila; Content-Type; sin fuga de errores
 */
declare(strict_types=1);

require_once __DIR__ . '/../../common/conexion.php';

header('Content-Type: application/json; charset=utf-8');

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
$idus   = (int)($_GET['idus'] ?? 0);

$rows = [];

try {
    // Nombres de columna (LIMIT 0 = solo metadatos)
    $infoCampo = $conexion->query('SELECT * FROM existenciaplanta LIMIT 0')->fetch_fields();

    // -----------------------------------------------------------------
    // Condiciones dinámicas
    // -----------------------------------------------------------------
    $cond   = ["p.status = '1'"];
    $tipos  = '';
    $params = [];

    if ($search !== '') {
        $cond[] = "(p.id_paraje LIKE CONCAT('%', ?, '%')
                 OR p.paraje LIKE CONCAT('%', ?, '%')
                 OR p.lat LIKE CONCAT('%', ?, '%')
                 OR p.lng LIKE CONCAT('%', ?, '%')
                 OR p.id_cliente LIKE CONCAT('%', ?, '%')
                 OR p.nombrep LIKE CONCAT('%', ?, '%')
                 OR p.rcampo LIKE CONCAT('%', ?, '%')
                 OR c.nombre LIKE CONCAT('%', ?, '%')
                 OR ep.regmaguey LIKE CONCAT('%', ?, '%'))";
        $tipos .= str_repeat('s', 9);
        for ($i = 0; $i < 9; $i++) $params[] = $search;
    }
    if ($fechaini !== '' && $fechafin === '') {
        $cond[] = 'DATE(ep.fecharegistro) = ?';
        $tipos .= 's';
        $params[] = $fechaini;
    } elseif ($fechaini !== '' && $fechafin !== '') {
        $cond[] = 'DATE(ep.fecharegistro) BETWEEN ? AND ?';
        $tipos .= 'ss';
        array_push($params, $fechaini, $fechafin);
    }
    if ($clientesel !== '' && $clientesel !== '0') {
        $cond[] = 'p.id_cliente = ?';
        $tipos .= 's';
        $params[] = $clientesel;
    }

    // Conflicto de intereses (misma validación que el resto del sistema)
    if ($idus > 0) {
        $stmt = $conexion->prepare('SELECT getConflictoIntereses(?)');
        $stmt->bind_param('i', $idus);
        $stmt->execute();
        $stmt->bind_result($clientes_conflicto);
        $stmt->fetch();
        $stmt->close();

        if ($clientes_conflicto !== null && $clientes_conflicto !== ''
            && preg_match("/^'[A-Za-z0-9]+'(,'[A-Za-z0-9]+')*$/", $clientes_conflicto)) {
            $cond[] = "p.id_cliente NOT IN ({$clientes_conflicto})";
        }
    }

    $where = 'WHERE ' . implode(' AND ', $cond);

    $cadenaSql = "SELECT ep.*, DATE(ep.fecharegistro) fecharegistro, c.nombre comun,
        l.localidad local, m.nombre municipio, e.nombre mestado, p.paraje, p.id_cliente
        FROM existenciaplanta ep
        INNER JOIN paraje p ON ep.id_paraje = p.id_paraje
        INNER JOIN comun c ON ep.id_comun = c.id_comun
        INNER JOIN localidades l ON p.id_localidad = l.id
        INNER JOIN municipios m ON l.MunicipioID = m.id
        INNER JOIN estados e ON m.estado = e.clave
        {$where} ORDER BY ep.fecharegistro ASC";

    // Total
    $stmt = $conexion->prepare($cadenaSql);
    if ($tipos !== '') $stmt->bind_param($tipos, ...$params);
    $stmt->execute();
    $stmt->store_result();
    $totalRes = $stmt->num_rows;
    $stmt->close();

    // Página
    $stmt = $conexion->prepare($cadenaSql . " LIMIT {$limit} OFFSET {$offset}");
    if ($tipos !== '') $stmt->bind_param($tipos, ...$params);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    foreach ($result as $row) {
        $registro = [];
        foreach ($infoCampo as $valor) {
            if ($valor->name !== 'poligono') {
                $registro[$valor->name] = $row[$valor->name];
            }
        }
        $registro['comun'] = $row['comun'];
        $registro['paraje'] = $row['paraje'];
        $registro['id_cliente'] = $row['id_cliente'];
        $registro['fecharegistro'] = $row['fecharegistro'];
        $registro['local'] = $row['local'];
        $registro['municipio'] = $row['municipio'];
        $registro['mestado'] = $row['mestado'];
        $registro['limit'] = $limit;
        $registro['offset'] = $offset;
        $rows[] = $registro;
    }

    echo json_encode(['total' => $totalRes, 'rows' => $rows]);
} catch (mysqli_sql_exception $e) {
    error_log('[loadPlantas.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['total' => 0, 'rows' => [], 'error' => 'Error al cargar plantas']);
} finally {
    $conexion->close();
}