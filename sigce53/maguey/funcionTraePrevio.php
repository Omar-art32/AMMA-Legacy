<?php
/**
 * funcionTraePrevio.php — PHP 8.3
 * Lista de predios con constancias de predio. JSON {data: [...]}.
 *
 * Cambios vs 5.6:
 *  - N+1 eliminada (nombre del cliente por JOIN)
 *  - JSON manual → json_encode
 *  - Conflicto validado con regex, WHERE corregido
 *  - conexion_remota eliminada (comentada en el original)
 */
declare(strict_types=1);

require_once __DIR__ . '/../common/conexion.php';

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', '0');

$idus = (int)($_POST['idus'] ?? 0);

try {
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

    $consulta = "SELECT p.id, p.id_cliente, p.id_paraje, p.constancia_predio,
                        c.no_cliente, c.nombre AS nombreCliente
                 FROM paraje p
                 INNER JOIN constancias co ON p.id_paraje = co.id_paraje COLLATE utf8_general_ci
                 LEFT JOIN clientes c ON c.no_cliente = p.id_cliente
                 WHERE 1=1 {$sql_conflicto}
                 GROUP BY p.id_paraje
                 ORDER BY p.id ASC";
    $result = $conexion->query($consulta);

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $idp = htmlspecialchars($row['id_paraje'] ?? '', ENT_QUOTES);

        $constancias = '';
        if (($row['constancia_predio'] ?? '') !== '') {
            $archivo = htmlspecialchars($row['constancia_predio'], ENT_QUOTES);
            $constancias = '<div class="col-md-4"><a href="constancia/pdfConstanciaPredio/' . $archivo . '" target="_blank"><img width="35px" src="images/pdf.svg"></a></div>';
        }
        $constancias .= '<div id="items_en_uso_predio" class="col-md-4"><a href="#" id="paraje_' . $idp . '"><img width="35px" src="images/exchange.svg"></a></div>';

        $data[] = [
            'id'          => $row['id'],
            'paraje'      => $row['id_paraje'],
            'cliente'     => $row['no_cliente'] ?? '',
            'nombre'      => $row['nombreCliente'] ?? '',
            'constancias' => $constancias,
        ];
    }

    echo json_encode(['data' => $data]);
} catch (mysqli_sql_exception $e) {
    error_log('[funcionTraePrevio.php] ' . $e->getMessage());
    echo json_encode(['data' => []]);
} finally {
    $conexion->close();
}