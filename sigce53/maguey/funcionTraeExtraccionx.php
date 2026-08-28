<?php
/**
 * funcionTraeExtraccionx.php — PHP 8.3
 * Variante de funcionTraeExtraccion.php que incluye botón de agregar.
 * Devuelve JSON {data: [...]}.
 *
 * Cambios vs 5.6:
 *  - conexion_remota eliminada (clientes por JOIN local)
 *  - N+1 eliminada, JSON con json_encode, HTML escapado
 *  - NOTA: este archivo es casi idéntico a funcionTraeExtraccion.php
 *    pero incluye la columna 'opciones' con botón. Candidato a unificarse.
 */
declare(strict_types=1);

require_once __DIR__ . '/../common/conexion.php';

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', '0');

try {
    $consulta = "SELECT p.id_cliente, p.id_paraje, p.constancia_extracciones,
                        ce.id_extraccion, c.no_cliente, c.nombre AS nombreCliente
                 FROM paraje p
                 INNER JOIN cextracciones ce ON ce.id_paraje = p.id_paraje
                 LEFT JOIN clientes c ON c.no_cliente = p.id_cliente
                 GROUP BY p.id_paraje";
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

        $agregar = '<a href="" title="Constancias" class="btn btn-primary" onclick="constancias(\'' . $idp . '\',\'' . $noc . '\',\'' . $nom . '\')" data-toggle="modal" data-target="#exampleModalCenter"><span class="glyphicon glyphicon-plus"></span></a>';

        $data[] = [
            'paraje'      => $row['id_paraje'],
            'noguia'      => $row['id_extraccion'],
            'cliente'     => $row['no_cliente'] ?? '',
            'nombre'      => $row['nombreCliente'] ?? '',
            'constancias' => $constancias,
            'opciones'    => $agregar,
        ];
    }

    echo json_encode(['data' => $data]);
} catch (mysqli_sql_exception $e) {
    error_log('[funcionTraeExtraccionx.php] ' . $e->getMessage());
    echo json_encode(['data' => []]);
} finally {
    $conexion->close();
}