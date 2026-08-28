<?php
/**
 * get_temps.php — PHP 8.3
 * Lista el carrito temporal de requisición (h_tmp_pedido) para un
 * No. de Pedido.
 * Devuelve JSON {status, lista|msj}.
 *
 * Cambios vs 5.6:
 *  - SQL concatenado ($no_pedido directo) → sentencia preparada
 *  - utf8_encode() eliminado de utf8_converter(); esa función actuaba
 *    como red de seguridad para filas viejas guardadas en Latin1, así
 *    que se sustituye por el equivalente real
 *    (mb_convert_encoding(..., 'UTF-8', 'ISO-8859-1')) en vez de dejarla
 *    como no-op
 *  - Se quita el SQL crudo de los mensajes de error (fuga de información)
 *  - include → require_once con __DIR__
 *  - try/catch con error_log
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../common/conexion.php';


/**
 * Red de seguridad: convierte a UTF-8 los valores que no lo sean ya
 * (filas antiguas guardadas en Latin1).
 */
function utf8_converter(array $array): array
{
    array_walk_recursive($array, function (&$item) {
        if (is_string($item) && !mb_detect_encoding($item, 'utf-8', true)) {
            $item = mb_convert_encoding($item, 'UTF-8', 'ISO-8859-1');
        }
    });
    return $array;
}

$no_pedido = (string)($_POST['no_pedido'] ?? '');

try {
    $stmt = $conexion->prepare(
        "SELECT h_tmp_pedido.no_pedido, h_tmp_pedido.no_cliente, h_tmp_pedido.marca cve, marcas.marca,
                h_tmp_pedido.edo, h_tmp_pedido.serie, h_tmp_pedido.tipo, h_tmp_pedido.fi, h_tmp_pedido.ff,
                h_tmp_pedido.cantidad, h_tmp_pedido.pagado, h_tmp_pedido.urgente, h_tmp_pedido.id_row, h_tmp_pedido.holograma
         FROM h_tmp_pedido
         LEFT JOIN marcas ON marcas.no_cliente = h_tmp_pedido.no_cliente AND marcas.cve_marca = h_tmp_pedido.marca
         WHERE no_pedido = ?
         ORDER BY fecha ASC"
    );
    $stmt->bind_param('s', $no_pedido);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $lista_req = [];
        while ($r = $result->fetch_assoc()) {
            $lista_req[] = $r;
        }
        $lista_req = utf8_converter($lista_req);
        echo json_encode(['status' => 'OK', 'lista' => $lista_req]);
    } else {
        echo json_encode(['status' => 'Error', 'msj' => 'No se encontraron registros']);
    }
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    error_log('[get_temps.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'Error', 'msj' => 'Error al realizar el registro']);
} finally {
    $conexion->close();
}
