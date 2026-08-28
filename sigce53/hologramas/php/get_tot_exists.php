<?php
/**
 * get_tot_exists.php — PHP 8.3
 * Genera el panel HTML con el desglose de existencias de hologramas
 * de un cliente, agrupado por marca y estado.
 * Devuelve JSON {status, msj (HTML)}.
 *
 * Cambios vs 5.6:
 *  - SQL concatenado ($client directo) → sentencia preparada
 *  - include → require_once con __DIR__
 *  - mb_convert_encoding(ISO-8859-1↔UTF-8) reemplazado por htmlspecialchars
 *    al insertar los valores en el HTML de salida
 *  - try/catch con error_log
 */
declare(strict_types=1);

require_once __DIR__ . '/../../common/conexion.php';


$client = (string)($_POST['cliente'] ?? '');
$client = substr($client, 0, 5);

try {
    $stmt = $conexion->prepare(
        "SELECT marcas.cve_marca, marcas.marca, h_existencias.serie,
                h_existencias.fol_ini, h_existencias.fol_fin,
                IF(h_existencias.existencias IS NULL, 0, h_existencias.existencias) AS existencias,
                h_existencias.edo, h_existencias.tipo,
                h_existencias.cliente_crm, h_existencias.marca_crm
         FROM marcas
         LEFT JOIN h_existencias ON h_existencias.no_cliente = marcas.no_cliente
                                 AND marcas.cve_marca = h_existencias.marca
         WHERE marcas.no_cliente = ? AND marcas.cve_marca != ''
         ORDER BY marcas.cve_marca, h_existencias.edo, h_existencias.fol_ini ASC"
    );
    $stmt->bind_param('s', $client);
    $stmt->execute();
    $result = $stmt->get_result();

    $tot = $result->num_rows;

    if ($tot > 0) {
        $respuesta = "<section id='plans'><div class='container'><div class='row'>";
        $m = null;
        $e = null;
        $aux = 1;
        $x = 0;
        $i = 0;

        while ($row = $result->fetch_row()) {
            $cve   = htmlspecialchars(trim((string)$row[0]), ENT_QUOTES, 'UTF-8');
            $marca = htmlspecialchars(trim((string)$row[1]), ENT_QUOTES, 'UTF-8');
            $serie = trim((string)($row[2] ?? ''));
            $f_ini = trim((string)($row[3] ?? ''));
            $f_fin = trim((string)($row[4] ?? ''));
            $existencia = trim((string)$row[5]);
            $edo   = htmlspecialchars(trim((string)($row[6] ?? '')), ENT_QUOTES, 'UTF-8');

            switch ($row[7]) {
                case 0:
                    $tipo_mez = '';
                    break;
                case 1:
                    $tipo_mez = 'MEZCAL';
                    break;
                case 2:
                    $tipo_mez = 'ARTESANAL';
                    break;
                case 3:
                    $tipo_mez = 'ANCESTRAL';
                    break;
                default:
                    $tipo_mez = '';
            }

            if ($existencia > 0) {
                $clientesel = $client;
                if (($row[8] ?? '') != '') { // cliente_crm
                    $clientesel = $row[8];
                }
                $clienteselEsc = htmlspecialchars((string)$clientesel, ENT_QUOTES, 'UTF-8');
                $folios = $clienteselEsc . $cve . "<font color='#000099'><b>" . str_pad($f_ini, 7, '0', STR_PAD_LEFT) . "</b></font>" . $serie
                        . " &nbsp;-&nbsp; " . $clienteselEsc . $cve . "<font color='#000099'><b>" . str_pad($f_fin, 7, '0', STR_PAD_LEFT) . "</b></font>" . $serie;
            } else {
                $folios = "<font color='#AF0707'><b>Sin existencias</b></font>";
            }

            if ($m !== $cve) {
                $color = $x === 0 ? 'success' : 'warning';
                $x = $x === 0 ? 1 : 0;

                if ($aux !== 1) {
                    $respuesta .= "</ul></div></div></div>";
                }

                $respuesta .= "<div class='col-md-3 text-center'></div>";
                $respuesta .= "<div class='col-md-7 text-center'><div class='panel panel-{$color} panel-pricing'>";
                $respuesta .= "  <div class='panel-heading'>
                            <a data-toggle='collapse' href='#collapseExample{$i}' aria-expanded='false' aria-controls='collapseExample'>
                            <h4><i class='fa fa-dot-circle-o'></i>&nbsp;&nbsp;{$cve} - {$marca}</h4>
                            </a>
                        </div>";
                $respuesta .= "<div class='collapse in' id='collapseExample{$i}'>";

                $i++;
                $m = $cve;
                $e = null; // fuerza a abrir el bloque de estado/<ul> aunque $edo venga vacío
            }

            if ($e !== $edo) {
                $respuesta .= "   <div class='panel-body text-center'>
                            <p><strong>{$edo}</strong></p>
                        </div>";
                $respuesta .= "<ul class='list-group text-center'>";
                $e = $edo;
            }

            $respuesta .= " <li class='list-group-item'><i class='fa fa-check'></i> {$folios} &nbsp;&nbsp;&nbsp;<b>[{$existencia}] &nbsp;&nbsp;&nbsp;[{$tipo_mez}]</b></li>";

            $aux++;
        }

        $respuesta .= "</ul></div></div></div></div></div></section>";
        $stmt->close();

        echo json_encode(['status' => 'OK', 'msj' => $respuesta]);
    } else {
        $stmt->close();
        echo json_encode(['status' => 'error', 'msj' => 'No se tienen registros de hologramas de esta MARCA']);
    }
} catch (mysqli_sql_exception $e) {
    error_log('[get_tot_exists.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msj' => 'Disculpe ha ocurrido un error, intente mas tarde']);
} finally {
    $conexion->close();
}
