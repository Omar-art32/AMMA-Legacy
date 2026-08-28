<?php
/**
 * get_last.php — PHP 8.3 (hologramas/php/inventario/, usado por entradas.js)
 * Devuelve la última entrada de folios registrada para hologramas
 * genéricos (tipo 'G') o personalizados (tipo 'P').
 * Devuelve JSON {status, msj, mto?, fini?, ffin?}.
 *
 * Cambios vs 5.6:
 *  - utf8_decode() eliminado (entrada ya viene en UTF-8)
 *  - SQL concatenado → sentencia preparada en la rama 'P'
 *  - include → require_once con __DIR__
 *  - try/catch con error_log
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../common/conexion.php';


$tipo_h = (string)($_POST['tipo_h'] ?? '');
$client = substr((string)($_POST['cliente'] ?? ''), 0, 4);
$marca  = (string)($_POST['marca'] ?? '');
$serie  = (string)($_POST['serie'] ?? '');

try {
    if ($tipo_h === 'G') {
        $stmt = $conexion->prepare(
            "SELECT fol_ini, fol_fin, cantidad FROM h_entradas
             WHERE no_cliente = '--' AND marca = '--' AND serie = '-'
             ORDER BY fol_fin DESC LIMIT 1"
        );
        $stmt->execute();
    } else {
        $stmt = $conexion->prepare(
            "SELECT fol_ini, fol_fin, cantidad FROM h_entradas
             WHERE no_cliente = ? AND marca = ? AND serie = ?
             ORDER BY fol_fin DESC LIMIT 1"
        );
        $stmt->bind_param('sss', $client, $marca, $serie);
        $stmt->execute();
    }
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_row();
        $f_ini = trim((string)$row[0]);
        $f_fin = trim((string)$row[1]);
        $mto_existe = trim((string)$row[2]);
        echo json_encode(['status' => 'correcto', 'msj' => 'Existencia Actual', 'mto' => $mto_existe, 'fini' => $f_ini, 'ffin' => $f_fin]);
    } else {
        echo json_encode(['status' => 'error', 'msj' => 'No se tienen entradas de hologramas de esta MARCA', 'ne' => '0']);
    }
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    error_log('[get_last.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msj' => 'No se pudo ejecutar la consulta', 'ne' => '1']);
} finally {
    $conexion->close();
}
