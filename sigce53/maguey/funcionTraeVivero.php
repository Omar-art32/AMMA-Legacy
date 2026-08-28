<?php
/**
 * funcionTraeVivero.php — PHP 8.3
 * Lista de viveros con constancias. JSON {data: [...]}.
 *
 * Cambios vs 5.6: N+1 eliminada, JSON con json_encode, conflicto validado.
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
            $sql_conflicto = " AND pv.id_cliente NOT IN ({$clientes_conflicto}) ";
        }
    }

    $consulta = "SELECT pv.id_paraje, pv.id_cliente, pv.constancia_vivero,
                        c.no_cliente, c.nombre AS nombreCliente
                 FROM paraje_vivero pv
                 INNER JOIN constancias_vivero cv ON pv.id_paraje = cv.id_paraje
                 LEFT JOIN clientes c ON c.no_cliente = pv.id_cliente
                 WHERE pv.tipo = 2 {$sql_conflicto}
                 GROUP BY pv.id_paraje";
    $result = $conexion->query($consulta);

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $idp = htmlspecialchars($row['id_paraje'] ?? '', ENT_QUOTES);

        $constancias = '';
        if (($row['constancia_vivero'] ?? '') !== '') {
            $archivo = htmlspecialchars($row['constancia_vivero'], ENT_QUOTES);
            $constancias = '<div class="col-md-4"><a href="constancia/pdfConstanciaVivero/' . $archivo . '" target="_blank"><img width="35px" src="images/pdf.svg"></a></div>';
        }
        $constancias .= '<div id="items_en_uso_vivero" class="col-md-4"><a href="#" id="vivero_' . $idp . '"><img width="35px" src="images/exchange.svg"></a></div>';

        $data[] = [
            'paraje'      => $row['id_paraje'],
            'cliente'     => $row['no_cliente'] ?? '',
            'nombre'      => $row['nombreCliente'] ?? '',
            'constancias' => $constancias,
        ];
    }

    echo json_encode(['data' => $data]);
} catch (mysqli_sql_exception $e) {
    error_log('[funcionTraeVivero.php] ' . $e->getMessage());
    echo json_encode(['data' => []]);
} finally {
    $conexion->close();
}