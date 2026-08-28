<?php
/**
 * get_exists.php — PHP 8.3 (hologramas/php/, usado por entradas.js)
 * Valida existencias disponibles para hologramas genéricos (tipo 'G')
 * o personalizados (tipo 'P', por cliente/marca/serie).
 * Devuelve JSON {status, msj, mto?, fini?, ffin?}.
 *
 * Cambios vs 5.6:
 *  - utf8_decode() eliminado (entrada ya viene en UTF-8)
 *  - SQL concatenado → sentencia preparada en ambas ramas
 *  - include → require_once con __DIR__
 *  - try/catch con error_log
 *  - Se quita el SQL crudo del mensaje de error (fuga de información)
 */
declare(strict_types=1);

require_once __DIR__ . '/../../common/conexion.php';


$tipo_h = (string)($_POST['tipo_h'] ?? '');
$client = (string)($_POST['cliente'] ?? '');
$marca  = (string)($_POST['marca'] ?? '');
$serie  = (string)($_POST['serie'] ?? '');

try {
    if ($tipo_h === 'G') {
        $stmt = $conexion->prepare(
            "SELECT existencias, fol_ini, fol_fin FROM h_existencias
             WHERE no_cliente = '--' AND marca = '--' AND serie = '-'"
        );
        $stmt->execute();
    } else {
        $stmt = $conexion->prepare(
            "SELECT existencias, fol_ini, fol_fin FROM h_existencias
             WHERE no_cliente = ? AND marca = ? AND serie = ?"
        );
        $stmt->bind_param('sss', $client, $marca, $serie);
        $stmt->execute();
    }
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_row();
        $mto_existe = trim((string)$row[0]);
        $f_ini = trim((string)$row[1]);
        $f_fin = trim((string)$row[2]);

        if ($mto_existe > 0) {
            echo json_encode(['status' => 'correcto', 'msj' => 'Existencias Viables', 'mto' => $mto_existe, 'fini' => $f_ini, 'ffin' => $f_fin]);
        } else {
            echo json_encode(['status' => 'error', 'msj' => 'Inventario vacio']);
        }
    } else {
        echo json_encode(['status' => 'error', 'msj' => 'No se tienen registros de hologramas de esta MARCA']);
    }
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    error_log('[get_exists.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msj' => 'Disculpe ha ocurrido un error, intente mas tarde']);
} finally {
    $conexion->close();
}
