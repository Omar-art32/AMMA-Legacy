<?php
/**
 * cbo_marca_req.php — PHP 8.3
 * Genera el <select> de marcas registradas para el formulario de
 * Requisición. Devuelve JSON {status, cbo|msj}.
 *
 * Cambios vs 5.6:
 *  - utf8_decode()/utf8_encode() eliminados (entrada ya viene en UTF-8)
 *  - SQL concatenado ($client directo) → sentencia preparada
 *  - include → require_once con __DIR__
 *  - htmlspecialchars al insertar valores de BD en HTML
 *  - try/catch con error_log
 */
declare(strict_types=1);

require_once __DIR__ . '/../../common/conexion.php';


$client = (string)($_POST['cliente'] ?? '');
$client = substr($client, 0, 4);

try {
    $stmt = $conexion->prepare(
        "SELECT cve_marca, marca FROM marcas WHERE SUBSTR(no_cliente,1,4) = ? GROUP BY cve_marca"
    );
    $stmt->bind_param('s', $client);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $cbo = '<select name="cbo_marcas_req" id="cbo_marcas_req" class="cbo-medium form-control" '
             . 'style="float:left;" onChange="getSerie_req();"><option value="0">Seleccionar</option>';

        while ($row = $result->fetch_assoc()) {
            $cve   = htmlspecialchars((string)$row['cve_marca'], ENT_QUOTES, 'UTF-8');
            $marca = htmlspecialchars((string)$row['marca'], ENT_QUOTES, 'UTF-8');
            $cbo  .= "<option value='{$cve}'>{$cve} - {$marca}</option>";
        }
        $cbo .= '</select>';

        echo json_encode(['status' => 'correcto', 'cbo' => $cbo]);
    } else {
        $msj = "<font color='#990000'>Sin Marcas&nbsp;&nbsp;</font>";
        echo json_encode(['status' => 'error', 'msj' => $msj]);
    }
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    error_log('[cbo_marca_req.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msj' => 'Disculpe ha ocurrido un error, intente mas tarde']);
} finally {
    $conexion->close();
}
