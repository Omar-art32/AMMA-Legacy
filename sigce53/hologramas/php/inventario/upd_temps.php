<?php
/**
 * upd_temps.php — PHP 8.3
 * Actualiza el número de holograma de un registro en h_tmp_pedido.
 * Devuelve JSON {status, msj}.
 *
 * Cambios vs 5.6:
 *  - SQL concatenado ($valor, $idrow directos) → sentencia preparada
 *  - include → require_once con __DIR__
 *  - try/catch con error_log
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../common/conexion.php';


$valor = (string)($_POST['valor'] ?? '');
$idrow = (isset($_POST['idrow']) && (int)$_POST['idrow'] > 0) ? (int)$_POST['idrow'] : 0;

if ($idrow === 0) {
    echo json_encode(['status' => 'Error', 'msj' => 'No se puede actualizar el registro, actualice la página e intente de nuevo']);
    exit;
}

try {
    $stmt = $conexion->prepare("UPDATE h_tmp_pedido SET holograma = ? WHERE id_row = ?");
    $stmt->bind_param('si', $valor, $idrow);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'OK', 'msj' => 'Bien']);
    } else {
        echo json_encode(['status' => 'Error', 'msj' => 'Error al realizar el registro']);
    }
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    error_log('[upd_temps.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'Error', 'msj' => 'Disculpe ha ocurrido un error, intente mas tarde']);
} finally {
    $conexion->close();
}
