<?php
/**
 * guardaEditarPO.php — PHP 8.3
 * Edita tipo/estado de un detalle de sh_detalle y registra el cambio en
 * la bitácora sh_up_detalle.
 * Devuelve JSON {status, msj}.
 *
 * Cambios vs 5.6:
 *  - SQL concatenado (SELECT, UPDATE dinámico, INSERT bitácora) →
 *    sentencias preparadas
 *  - BUG CORREGIDO: si no cambiaba ni tipo ni estado, el UPDATE
 *    quedaba como "UPDATE sh_detalle set  WHERE id=..." (SET vacío,
 *    error de sintaxis SQL) y tronaba con una excepción genérica.
 *    Ahora, si no hay cambios, se responde directo sin tocar la BD.
 *  - include → require_once con __DIR__
 *  - try/catch/finally con error_log
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../common/conexion.php';


try {
    session_start();
    $conexion->autocommit(false);

    $usr        = (string)($_POST['usr_up'] ?? '');
    $id_detalle = (int)($_POST['txtIdEditarPO'] ?? 0);
    $tipo       = (string)($_POST['cbo_tipoEPO'] ?? '');
    $edo        = (string)($_POST['cbo_edosEPO'] ?? '');
    $obs        = (string)($_POST['txtObsPO'] ?? '');

    $stmtSel = $conexion->prepare("SELECT tipo, edo FROM sh_detalle WHERE id = ? LIMIT 1");
    $stmtSel->bind_param('i', $id_detalle);
    if (!$stmtSel->execute()) {
        throw new Exception('No se encontraron datos.');
    }
    $row_det = $stmtSel->get_result()->fetch_assoc();
    $stmtSel->close();

    if ($row_det === null) {
        throw new Exception('No se encontraron datos.');
    }

    $campos = [];
    $setClauses = [];
    $setParams = [];
    $setTypes = '';
    $old_tipo = '';
    $old_edo = '';

    if ($row_det['tipo'] != $tipo) {
        $campos[] = 'tipo';
        $setClauses[] = 'tipo = ?';
        $setTypes .= 's';
        $setParams[] = $tipo;
        $old_tipo = $row_det['tipo'];
    }
    if ($row_det['edo'] != $edo) {
        $campos[] = 'estado';
        $setClauses[] = 'edo = ?';
        $setTypes .= 's';
        $setParams[] = $edo;
        $old_edo = $row_det['edo'];
    }

    $cont_cambios = count($campos);
    if ($cont_cambios === 1) {
        $cambios = 'El campo ' . $campos[0] . ' fue modificado';
    } elseif ($cont_cambios === 2) {
        $cambios = 'Los campos ' . implode(', ', $campos) . ' fueron modificados';
    } else {
        $cambios = '';
    }

    if ($cont_cambios > 0) {
        $sql_up_detalle = 'UPDATE sh_detalle SET ' . implode(', ', $setClauses) . ' WHERE id = ?';
        $stmtUp = $conexion->prepare($sql_up_detalle);
        $setTypes .= 'i';
        $setParams[] = $id_detalle;
        $stmtUp->bind_param($setTypes, ...$setParams);
        if (!$stmtUp->execute()) {
            throw new Exception('Error al actualizar el detalle del pedido.');
        }
        $stmtUp->close();

        $stmtBit = $conexion->prepare(
            "INSERT INTO sh_up_detalle (id_detalle, old_tipo, old_edo, cambios, observaciones, usr_up, fecha_up)
             VALUES (?, ?, ?, ?, ?, ?, NOW())"
        );
        $stmtBit->bind_param('isssss', $id_detalle, $old_tipo, $old_edo, $cambios, $obs, $usr);
        if (!$stmtBit->execute()) {
            throw new Exception('Error al insertar detalles en la bitacora.');
        }
        $stmtBit->close();
    }

    $conexion->commit();
    echo json_encode(['status' => 'OK', 'msj' => 'Detalle actualizado correctamente']);
} catch (Exception $e) {
    $conexion->rollback();
    error_log('[guardaEditarPO.php] ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'msj' => $e->getMessage()]);
} finally {
    $conexion->close();
}
