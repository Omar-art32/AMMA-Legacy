<?php
/**
 * actualizar_predio.php — PHP 8.3
 * Endpoint AJAX del radio Mostrar/Ocultar de la pantalla Consulta de Predio.
 * Actualiza status_predio en paraje o paraje_vivero. Responde JSON.
 *
 * Cambios vs versión original:
 *  - BUG CORREGIDO: bind_param("ii", ...) trataba id_paraje como entero;
 *    como los IDs son alfanuméricos ('P1'), se convertían a 0 y el UPDATE
 *    nunca afectaba filas. Ahora se bindea como string ("is").
 *  - Verificación de sesión: el original no validaba login — cualquier
 *    request directo podía cambiar el estatus de un predio. Ahora se exige
 *    al menos una sesión activa con seccion_4_4 = logged (mismo criterio
 *    que buscar.php; el JS no envía d_s, por eso se busca en $_SESSION).
 *  - Conexión unificada a common/conexion.php (antes: php/registro/conexion.php)
 *  - Whitelist del nombre de tabla a partir de tipoP validado
 *  - No se expone $e->getMessage() al cliente (iba directo en el JSON);
 *    el detalle queda en error_log.
 */
declare(strict_types=1);

session_start();

header('Content-Type: application/json; charset=utf-8');

function is_ajax(): bool
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * El frontend no envía el token d_s, así que se verifica que exista
 * al menos una sesión del módulo con estatus logged.
 */
function sesion_valida(): bool
{
    foreach ($_SESSION as $dato) {
        if (is_array($dato)
            && ($dato['seccion_4_4'] ?? '') === 'logged') {
            return true;
        }
    }
    return false;
}

if (!is_ajax() || ($_POST['action'] ?? '') !== 'actualizarPredio') {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msj' => 'Petición no válida.']);
    exit;
}

if (!sesion_valida()) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'msj' => 'Sesión no válida.']);
    exit;
}

// ---------------------------------------------------------------------
// Validación de entrada
// ---------------------------------------------------------------------
$valor     = (string)($_POST['valor'] ?? '');
$id_paraje = (string)($_POST['id_paraje'] ?? '');
$tipoP     = (string)($_POST['tipoP'] ?? '');

if (!in_array($valor, ['0', '1'], true)
    || !preg_match('/^[A-Za-z0-9]+$/', $id_paraje)
    || !in_array($tipoP, ['1', '2'], true)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msj' => 'Parámetros no válidos.']);
    exit;
}

$valor = (int)$valor;
$tabla = ($tipoP === '1') ? 'paraje' : 'paraje_vivero';   // whitelist

// ---------------------------------------------------------------------
// Actualización
// ---------------------------------------------------------------------
require_once __DIR__ . '/../common/conexion.php';

try {
    $ps = $conexion->prepare("UPDATE {$tabla} SET status_predio = ? WHERE id_paraje = ?");
    $ps->bind_param('is', $valor, $id_paraje);
    $ps->execute();
    $afectadas = $ps->affected_rows;
    $ps->close();

    // affected_rows = 0 también ocurre si el valor ya era el mismo;
    // se reporta éxito igual que el original, pero queda en el log.
    if ($afectadas === 0) {
        error_log("[actualizar_predio.php] UPDATE sin filas afectadas ({$tabla}, id_paraje={$id_paraje})");
    }

    echo json_encode(['status' => 'correcto', 'msj' => 'Predio Actualizado.']);
} catch (mysqli_sql_exception $e) {
    error_log('[actualizar_predio.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msj' => 'Error en la base de datos.']);
} finally {
    $conexion->close();
}