<?php
/**
 * guardarvive2.php — PHP 8.3
 * Agrega especies de maguey a un vivero existente.
 *
 * Cambios vs 5.6:
 *  - include('php/registro/conexion.php') → require_once centralizado
 *  - SQL concatenado → sentencia preparada
 *  - $e->getMessage() ya no se expone al cliente
 *  - Validación de $num con regex
 *  - ini_set timezone movido fuera del loop (se ejecutaba en cada iteración)
 */
declare(strict_types=1);

require_once __DIR__ . '/../common/conexion.php';

header('Content-Type: text/plain; charset=utf-8');
ini_set('display_errors', '0');
date_default_timezone_set('America/Mexico_City');

$num = (string)($_POST['num'] ?? '');
$now = date('Y-m-d');

if (!preg_match('/^[A-Za-z0-9]+$/', $num)) {
    echo 'Parámetros no válidos';
    exit;
}

try {
    $conexion->autocommit(false);
    $data = json_decode((string)($_POST['tMagueys'] ?? '[]'), true);

    if (is_array($data)) {
        $stmt = $conexion->prepare(
            "INSERT INTO existenciaplanta_vivero (
                id_paraje, regmaguey, origen, id_comun, fecha_siembra,
                cantidadini, fecha_registro, status, existenciaplantas
            ) VALUES (?, ?, ?, ?, ?, ?, ?, '1', ?)"
        );
        foreach ($data as $value) {
            $reg    = (string)($value[0] ?? '');
            $origen = (string)($value[1] ?? '');
            $comun  = (string)($value[2] ?? '');
            $cant   = (string)($value[3] ?? '0');
            $fechaS = date('Y-m-d', strtotime((string)($value[4] ?? 'now')));
            $stmt->bind_param('ssssssss', $num, $reg, $origen, $comun, $fechaS, $cant, $now, $cant);
            $stmt->execute();
        }
        $stmt->close();
    }

    $conexion->commit();
    echo 'Registro realizado correctamente';
} catch (mysqli_sql_exception $e) {
    $conexion->rollback();
    error_log('[guardarvive2.php] ' . $e->getMessage());
    echo 'Error al realizar el registro';
} finally {
    $conexion->close();
}