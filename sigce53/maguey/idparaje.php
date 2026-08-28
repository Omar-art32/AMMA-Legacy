<?php
/**
 * idparaje.php — PHP 8.3
 * Genera el siguiente ID consecutivo de predio (P) o vivero (V).
 * Devuelve JSON {exito, id}.
 *
 * Cambios vs 5.6:
 *  - $_POST['tipo'] con ?? y validación (solo P o V)
 *  - include → require_once con __DIR__
 *  - Whitelist de tabla según tipo (nunca interpolación directa)
 *  - Header JSON explícito, try/catch
 *  - Nota: el MAX(id) sigue expuesto a condición de carrera
 *    (documentado, igual que en loadPredios/agregaG)
 */
declare(strict_types=1);

require_once __DIR__ . '/../common/conexion.php';

header('Content-Type: application/json; charset=utf-8');

$tipo = (string)($_POST['tipo'] ?? '');

if (!in_array($tipo, ['P', 'V'], true)) {
    echo json_encode(['exito' => '0', 'id' => '']);
    exit;
}

try {
    $tabla = ($tipo === 'P') ? 'paraje' : 'paraje_vivero';
    $prefijo = $tipo;

    $consulta = "SELECT SUBSTR(id_paraje, 2, LENGTH(id_paraje)) AS id
                 FROM {$tabla}
                 WHERE id = (SELECT MAX(id) FROM {$tabla})
                 ORDER BY id DESC";
    $result = $conexion->query($consulta);

    $id = $prefijo . '1';
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ((int)$row['id'] > 0) {
            $id = $prefijo . ((int)$row['id'] + 1);
        }
    }

    echo json_encode(['exito' => '1', 'id' => $id]);
} catch (mysqli_sql_exception $e) {
    error_log('[idparaje.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['exito' => '0', 'id' => '']);
} finally {
    $conexion->close();
}