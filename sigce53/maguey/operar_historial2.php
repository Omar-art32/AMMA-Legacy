<?php
/**
 * operar_historial2.php — PHP 8.3
 * Igual que operar_historial.php pero obtiene nombre del cliente desde
 * una conexión remota (conexion_remota.php).
 *
 * Cambios vs 5.6:
 *  - SQL concatenado → sentencias preparadas
 *  - include → require_once con __DIR__
 *  - Transacción agregada
 *  - Validación con ?? y cast
 *  - NOTA: conexion_remota.php probablemente NO existe en el ambiente
 *    de pruebas. Si falla, se cae al catch con mensaje genérico.
 *    En una fase posterior, evaluar si esta conexión remota sigue
 *    siendo necesaria o si los datos de clientes ya están en la BD local.
 */
declare(strict_types=1);

require_once __DIR__ . '/../common/conexion.php';

// Conexión remota: se conserva el include original con manejo de error
$conexion_remota = null;
try {
    if (file_exists(__DIR__ . '/php/registro/conexion_remota.php')) {
        include __DIR__ . '/php/registro/conexion_remota.php';
    }
} catch (\Throwable $e) {
    error_log('[operar_historial2.php] conexion_remota no disponible: ' . $e->getMessage());
}

header('Content-Type: text/plain; charset=utf-8');
ini_set('display_errors', '0');

$id_paraje = (string)($_POST['datos']['id_paraje'] ?? '');
$cantidad  = (int)($_POST['datos']['cantidad'] ?? 0);

if ($id_paraje === '' || $cantidad < 1) {
    echo 'Parámetros no válidos';
    exit;
}

try {
    $conexion->autocommit(false);

    // Obtener cliente del predio
    $stmt = $conexion->prepare("SELECT id_cliente FROM paraje WHERE id_paraje = ?");
    $stmt->bind_param('s', $id_paraje);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $cliente = $row['id_cliente'] ?? '';
    $stmt->close();

    // Obtener nombre del cliente (de la BD remota si existe, si no de la local)
    $no_cliente = '';
    $nombreCliente = '';
    if ($cliente !== '') {
        $conn = ($conexion_remota instanceof mysqli) ? $conexion_remota : $conexion;
        $stmtC = $conn->prepare("SELECT no_cliente, nombre FROM clientes WHERE no_cliente = ?");
        $stmtC->bind_param('s', $cliente);
        $stmtC->execute();
        $rowC = $stmtC->get_result()->fetch_assoc();
        $no_cliente = $rowC['no_cliente'] ?? '';
        $nombreCliente = $rowC['nombre'] ?? '';
        $stmtC->close();
    }

    // Registrar en bitácora
    $fecha = date('Y-m-d');
    $stmtH = $conexion->prepare(
        "INSERT INTO historial_cextracciones (id_paraje, no_cliente, nombre, cantidad, fecha)
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmtH->bind_param('sssis', $id_paraje, $no_cliente, $nombreCliente, $cantidad, $fecha);
    $stmtH->execute();
    $stmtH->close();

    // Generar guías
    $stmtG = $conexion->prepare(
        "INSERT INTO cextracciones (id_paraje, status, fecha, constancia)
         VALUES (?, '1', ?, '')"
    );
    for ($i = 0; $i < $cantidad; $i++) {
        $stmtG->bind_param('ss', $id_paraje, $fecha);
        $stmtG->execute();
    }
    $stmtG->close();

    $conexion->commit();
    echo 'Proceso realizado exitosamente';
} catch (mysqli_sql_exception|RuntimeException $e) {
    $conexion->rollback();
    error_log('[operar_historial2.php] ' . $e->getMessage());
    echo 'Error al realizar la operación';
} finally {
    $conexion->close();
    if ($conexion_remota instanceof mysqli) $conexion_remota->close();
}