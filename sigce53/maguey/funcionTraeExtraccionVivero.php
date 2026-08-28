<?php
/**
 * funcionTraeExtraccionVivero.php — PHP 8.3
 * Lista de viveros con guías de extracción. JSON {data: [...]}.
 * Mismo patrón que funcionTraeExtraccion.php pero con paraje_vivero.
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

    $consulta = "SELECT pv.id_cliente, pv.id_paraje, pv.constancia_extracciones,
                        c.no_cliente, c.nombre AS nombreCliente
                 FROM paraje_vivero pv
                 INNER JOIN cextracciones ce ON ce.id_paraje = pv.id_paraje COLLATE utf8_general_ci
                 LEFT JOIN clientes c ON c.no_cliente = pv.id_cliente
                 WHERE 1=1 {$sql_conflicto}
                 GROUP BY pv.id_paraje
                 ORDER BY pv.id_paraje ASC";
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
        $constancias .= '<div id="items_en_uso_extraccionesv" class="col-md-4"><a href="#" id="extracciones_' . $idp . '"><img width="35px" src="images/exchange.svg"></a></div>';

        $agregar = '<a href="" title="Constancias" class="btn btn-primary" onclick="constancias(\'' . $idp . '\',\'' . $noc . '\',\'' . $nom . '\')" data-toggle="modal" data-target="#exampleModalCenter"><span class="glyphicon glyphicon-plus"></span></a>';

        $data[] = [
            'vparaje'      => $row['id_paraje'],
            'vcliente'     => $row['no_cliente'] ?? '',
            'vnombre'      => $row['nombreCliente'] ?? '',
            'vconstancias' => $constancias,
            'vopciones'    => $agregar,
        ];
    }

    echo json_encode(['data' => $data]);
} catch (mysqli_sql_exception $e) {
    error_log('[funcionTraeExtraccionVivero.php] ' . $e->getMessage());
    echo json_encode(['data' => []]);
} finally {
    $conexion->close();
}