<?php
/**
 * get_last_req.php — PHP 8.3
 * Devuelve el último folio conocido para una marca/serie, revisando en
 * orden: pedidos en cola (h_tmp_pedido), pedidos en proceso (h_pedidos)
 * y finalmente la última entrada real (h_entradas).
 * Devuelve JSON {status, msj, mto, fini, ffin}.
 *
 * Cambios vs 5.6:
 *  - utf8_decode() eliminado (entrada ya viene en UTF-8)
 *  - SQL concatenado ($client sin comillas ni tipo forzado, $marca/$serie
 *    directos) → sentencias preparadas en las 3 consultas
 *  - include → require_once con __DIR__
 *  - try/catch con error_log
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../common/conexion.php';


$client = substr((string)($_POST['cliente'] ?? ''), 0, 4);
$marca  = (string)($_POST['marca'] ?? '');
$serie  = (string)($_POST['serie'] ?? '');

try {
    $stmt = $conexion->prepare(
        "SELECT fi, ff, cantidad FROM h_tmp_pedido
         WHERE no_cliente = ? AND marca = ? AND serie = ? ORDER BY ff DESC LIMIT 1"
    );
    $stmt->bind_param('sss', $client, $marca, $serie);
    $stmt->execute();
    $res_tmp_pedido = $stmt->get_result();

    if ($res_tmp_pedido->num_rows > 0) {
        $row = $res_tmp_pedido->fetch_row();
        echo json_encode(['status' => 'correcto', 'msj' => 'Pedido en cola', 'mto' => trim((string)$row[2]), 'fini' => trim((string)$row[0]), 'ffin' => trim((string)$row[1])]);
        $stmt->close();
    } else {
        $stmt->close();
        $stmt = $conexion->prepare(
            "SELECT fi, ff, cantidad FROM h_pedidos
             WHERE no_cliente = ? AND marca = ? AND serie = ? ORDER BY ff DESC LIMIT 1"
        );
        $stmt->bind_param('sss', $client, $marca, $serie);
        $stmt->execute();
        $res_pedidos = $stmt->get_result();

        if ($res_pedidos->num_rows > 0) {
            $row = $res_pedidos->fetch_row();
            echo json_encode(['status' => 'correcto', 'msj' => 'Pedido en proceso', 'mto' => trim((string)$row[2]), 'fini' => trim((string)$row[0]), 'ffin' => trim((string)$row[1])]);
            $stmt->close();
        } else {
            $stmt->close();
            $stmt = $conexion->prepare(
                "SELECT fol_ini, fol_fin, cantidad FROM h_entradas
                 WHERE no_cliente = ? AND marca = ? AND serie = ? ORDER BY fol_fin DESC LIMIT 1"
            );
            $stmt->bind_param('sss', $client, $marca, $serie);
            $stmt->execute();
            $res_entradas = $stmt->get_result();

            if ($res_entradas->num_rows > 0) {
                $row = $res_entradas->fetch_row();
                echo json_encode(['status' => 'correcto', 'msj' => 'Ultima Entrada', 'mto' => trim((string)$row[2]), 'fini' => trim((string)$row[0]), 'ffin' => trim((string)$row[1])]);
            } else {
                echo json_encode(['status' => 'error', 'msj' => 'No haya registros de entradas ni pedidos', 'mto' => '0', 'fini' => '0', 'ffin' => '0', 'ne' => '0']);
            }
            $stmt->close();
        }
    }
} catch (mysqli_sql_exception $e) {
    error_log('[get_last_req.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msj' => 'Disculpe ha ocurrido un error, intente mas tarde']);
} finally {
    $conexion->close();
}
