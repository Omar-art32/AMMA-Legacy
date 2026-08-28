<?php
/**
 * operar_historial.php — PHP 8.3
 * Registra en bitácora (historial_cextracciones) y genera guías nuevas
 * para un predio. Recibe POST con datos[id_paraje], datos[no_cliente],
 * datos[nombre], datos[cantidad].
 *
 * Cambios vs 5.6:
 *  - SQL concatenado (~3 INSERT) → sentencias preparadas
 *  - include("common/conexion.php") → require_once con __DIR__
 *    (la ruta original sin ../ sugiere que se llama desde la raíz de maguey)
 *  - Transacción agregada (antes: si fallaba a media inserción, quedaban datos parciales)
 *  - Validación de entrada con ?? y cast
 *  - print_r → echo
 */
declare(strict_types=1);

require_once __DIR__ . '/../common/conexion.php';

header('Content-Type: text/plain; charset=utf-8');
ini_set('display_errors', '0');

$id_paraje = (string)($_POST['datos']['id_paraje'] ?? '');
$no_cliente = (string)($_POST['datos']['no_cliente'] ?? '');
$nombre    = (string)($_POST['datos']['nombre'] ?? '');
$cantidad  = (int)($_POST['datos']['cantidad'] ?? 0);

if ($id_paraje === '' || $cantidad < 1) {
    echo 'Parámetros no válidos';
    exit;
}

try {
    $conexion->autocommit(false);

    // Obtener constancia del predio
    $stmt = $conexion->prepare("SELECT constancia FROM cextracciones WHERE id_paraje = ? LIMIT 1");
    $stmt->bind_param('s', $id_paraje);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $constancia = $row['constancia'] ?? '';
    $stmt->close();

    // Registrar en bitácora
    $stmtH = $conexion->prepare(
        "INSERT INTO historial_cextracciones (id_paraje, no_cliente, nombre, cantidad, fecha)
         VALUES (?, ?, ?, ?, ?)"
    );
    $fecha = date('Y-m-d');
    $stmtH->bind_param('sssis', $id_paraje, $no_cliente, $nombre, $cantidad, $fecha);
    $stmtH->execute();
    $stmtH->close();

    // Generar guías
    $stmtG = $conexion->prepare(
        "INSERT INTO cextracciones (id_paraje, status, fecha, constancia)
         VALUES (?, '1', ?, ?)"
    );
    for ($i = 0; $i < $cantidad; $i++) {
        $stmtG->bind_param('sss', $id_paraje, $fecha, $constancia);
        $stmtG->execute();
    }
    $stmtG->close();

    $conexion->commit();
    echo 'Proceso realizado exitosamente';
} catch (mysqli_sql_exception|RuntimeException $e) {
    $conexion->rollback();
    error_log('[operar_historial.php] ' . $e->getMessage());
    echo 'Error al realizar la operación';
} finally {
    $conexion->close();
}