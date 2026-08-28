<?php
/**
 * funcionTraeExtraccion.php — PHP 8.3
 * Lista de predios con guías de extracción (para la tabla de constancias).
 * Devuelve JSON {data: [...]}.
 *
 * Cambios vs 5.6:
 *  - Consulta N+1 eliminada: nombre del cliente por JOIN (antes: 1 query por fila)
 *  - JSON manual con concatenación de strings → json_encode (seguro contra XSS)
 *  - Conflicto de intereses: lista validada con regex antes del IN()
 *  - include → require_once con __DIR__
 *  - set_charset("utf8") eliminado
 *  - Salida HTML de constancias escapada con htmlspecialchars
 *  - conexion_remota eliminada (los clientes están en la BD local)
 */
declare(strict_types=1);

require_once __DIR__ . '/../common/conexion.php';

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', '0');

$idus = (int)($_POST['idus'] ?? 0);

try {
    // Conflicto de intereses
    $sql_conflicto = '';
    if ($idus > 0) {
        $stmt = $conexion->prepare('SELECT getConflictoIntereses(?)');
        $stmt->bind_param('i', $idus);
        $stmt->execute();
        $stmt->bind_result($clientes_conflicto);
        $stmt->fetch();
        $stmt->close();
        if ($clientes_conflicto !== null && $clientes_conflicto !== ''
            && preg_match("/^'[A-Za-z0-9]+'(,'[A-Za-z0-9]+')*$/", $clientes_conflicto)) {
            $sql_conflicto = " AND p.id_cliente NOT IN ({$clientes_conflicto}) ";
        }
    }

    // Consulta con JOIN a clientes (elimina N+1)
    $consulta = "SELECT p.id_cliente, p.id_paraje, p.constancia_extracciones,
                        c.no_cliente, c.nombre AS nombreCliente
                 FROM paraje p
                 INNER JOIN cextracciones ce ON ce.id_paraje = p.id_paraje
                 LEFT JOIN clientes c ON c.no_cliente = p.id_cliente
                 WHERE 1=1 {$sql_conflicto}
                 GROUP BY p.id_paraje
                 ORDER BY p.id DESC";
    $result = $conexion->query($consulta);

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $idp = htmlspecialchars($row['id_paraje'] ?? '', ENT_QUOTES);
        $noc = htmlspecialchars($row['no_cliente'] ?? '', ENT_QUOTES);
        $nom = htmlspecialchars($row['nombreCliente'] ?? '', ENT_QUOTES);

        $constancias = '';
        if (($row['constancia_extracciones'] ?? '') !== '') {
            $archivo = htmlspecialchars($row['constancia_extracciones'], ENT_QUOTES);
            $constancias = '<div class="col-md-4"><a href="constancia/pdfConstanciaExtraccion/' . $archivo . '" target="_blank"><img width="35px" src="images/pdf.svg"></a></div>';
        }
        $constancias .= '<div id="items_en_uso_extracciones" class="col-md-4"><a href="#" id="extracciones_' . $idp . '"><img width="35px" src="images/exchange.svg"></a></div>';

        $data[] = [
            'paraje'      => $row['id_paraje'],
            'cliente'     => $row['no_cliente'] ?? '',
            'nombre'      => $row['nombreCliente'] ?? '',
            'constancias' => $constancias,
            'opciones'    => '',
        ];
    }

    echo json_encode(['data' => $data]);
} catch (mysqli_sql_exception $e) {
    error_log('[funcionTraeExtraccion.php] ' . $e->getMessage());
    echo json_encode(['data' => []]);
} finally {
    $conexion->close();
}