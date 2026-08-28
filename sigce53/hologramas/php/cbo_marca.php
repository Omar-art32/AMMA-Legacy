<?php
/**
 * cbo_marca.php — PHP 8.3
 * Genera el <select> de marcas registradas para un cliente (No. de Control).
 * Devuelve JSON {status, cbo|msj}.
 *
 * Cambios vs 5.6:
 *  - error_reporting()/ini_set('display_errors') como parche → eliminado;
 *    ya no hace falta porque no quedan llamadas deprecated
 *  - SQL concatenado ($client directo) → sentencia preparada
 *  - include → require_once con __DIR__
 *  - mb_convert_encoding(ISO-8859-1 → UTF-8) reemplazado por htmlspecialchars
 *    donde el valor va directo a HTML (id, marca) — evita inyección de HTML
 *    además de mojibake si el dato ya viene en UTF-8
 *  - try/catch con error_log
 */
declare(strict_types=1);

require_once __DIR__ . '/../../common/conexion.php';


$client   = (string)($_POST['cliente'] ?? '');
$funcion  = (string)($_POST['funcion'] ?? '');
$id       = (string)($_POST['id'] ?? '');

$idEsc = htmlspecialchars($id, ENT_QUOTES, 'UTF-8');
$funcionEsc = htmlspecialchars($funcion, ENT_QUOTES, 'UTF-8');

try {
    $stmt = $conexion->prepare(
        "SELECT cve_marca, marca FROM marcas WHERE no_cliente = ? GROUP BY cve_marca"
    );
    $stmt->bind_param('s', $client);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $cbo = '<select name="' . $idEsc . '" id="' . $idEsc . '" class="cbo-medium form-control" '
             . 'style="float:left;" onChange="' . $funcionEsc . '();"><option value="0">Seleccionar</option>';

        while ($row = $result->fetch_assoc()) {
            $cve   = htmlspecialchars((string)$row['cve_marca'], ENT_QUOTES, 'UTF-8');
            $marca = htmlspecialchars((string)$row['marca'], ENT_QUOTES, 'UTF-8');
            $cbo  .= "<option value=\"{$cve}\">{$cve} - {$marca}</option>";
        }

        $cbo .= "</select>&nbsp;<button type='button' name='btnAddMarca' id='btnAddMarca' "
              . "class='btn btn-success btn-xs' style='vertical-align:top;' onClick='addMarca()'>"
              . "<span class='glyphicon glyphicon-plus'></span></button>";

        echo json_encode(['status' => 'correcto', 'cbo' => $cbo]);
    } else {
        $msj = "<font color='#990000'>Sin Marcas&nbsp;&nbsp;</font>"
             . "<button type='button' name='btnAddMarca' id='btnAddMarca' class='btn btn-success btn-xs' "
             . "onClick='addMarca()'><span class='glyphicon glyphicon-plus'></span></button>";
        echo json_encode(['status' => 'error', 'msj' => $msj]);
    }
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    error_log('[cbo_marca.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msj' => 'Disculpe, ha ocurrido un error, intente más tarde']);
} finally {
    $conexion->close();
}
