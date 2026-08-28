<?php
/**
 * get_marca.php — PHP 8.3
 * Lista las marcas ya registradas de un cliente y sugiere la siguiente
 * letra disponible (A, B, C...) para dar de alta una nueva marca.
 * Devuelve JSON {status, lista, next}.
 *
 * Cambios vs 5.6:
 *  - error_reporting()/ini_set('display_errors') como parche → eliminado
 *  - SQL concatenado ($client directo) → sentencia preparada
 *  - include → require_once con __DIR__
 *  - mb_convert_encoding reemplazado por htmlspecialchars al insertar en HTML
 *  - try/catch con error_log
 */
declare(strict_types=1);

require_once __DIR__ . '/../../common/conexion.php';


$arr_let = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];

$client = (string)($_POST['cliente'] ?? '');
$client = substr($client, 0, 4);

try {
    $stmt = $conexion->prepare(
        "SELECT cve_marca, marca FROM marcas WHERE SUBSTR(no_cliente,1,4) = ? GROUP BY cve_marca ORDER BY cve_marca ASC"
    );
    $stmt->bind_param('s', $client);
    $stmt->execute();
    $result = $stmt->get_result();

    $tot = $result->num_rows;
    $list_marcas = '';
    $c_mar = 0;

    if ($tot > 0) {
        $list_marcas = '<p style="line-height:25px;">';
        while ($row = $result->fetch_assoc()) {
            $cve   = htmlspecialchars((string)$row['cve_marca'], ENT_QUOTES, 'UTF-8');
            $marca = htmlspecialchars((string)$row['marca'], ENT_QUOTES, 'UTF-8');
            $list_marcas .= "{$cve} - {$marca} <br>";
            $c_mar++;
        }
        $list_marcas .= '</p>';
    } else {
        $list_marcas = 'Sin marcas previas';
    }
    $stmt->close();

    $next = $arr_let[$c_mar] ?? '';
    echo json_encode(['status' => 'correcto', 'lista' => $list_marcas, 'next' => $next]);
} catch (mysqli_sql_exception $e) {
    error_log('[get_marca.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msj' => 'Ha ocurrido un error, intente más tarde']);
} finally {
    $conexion->close();
}
