<?php
/**
 * guardarvive.php — PHP 8.3
 * Registro completo de un vivero nuevo: datos, fotos, especies y constancia.
 * Todo dentro de una transacción.
 *
 * Cambios vs 5.6:
 *  - SQL concatenado (~4 INSERT) → sentencias preparadas
 *  - include → require_once con __DIR__
 *  - set_charset("utf8") eliminado
 *  - $e->getMessage() ya no se expone al cliente
 *  - Validación de entrada con ?? y cast
 */
declare(strict_types=1);

require_once __DIR__ . '/../common/conexion.php';

header('Content-Type: text/plain; charset=utf-8');
ini_set('display_errors', '0');
date_default_timezone_set('America/Mexico_City');

// ── Datos del formulario ──
$loc  = (int)($_POST['vlocal'] ?? 0);
$sta  = (string)($_POST['vstate'] ?? '');
$par  = (string)($_POST['vparaje'] ?? '');
$lati = (string)($_POST['vlat'] ?? '0');
$lon  = (string)($_POST['vlng'] ?? '0');
$refu = (string)($_POST['vreferenciau'] ?? '');
$ref  = (string)($_POST['vreferencia2'] ?? '');
$fec  = date('Y-m-d', strtotime((string)($_POST['vfecha'] ?? 'now')));
$cam  = (string)($_POST['vcampo'] ?? '');
$idus = (int)($_POST['idus'] ?? 0);
$status_predio = 1;
$now = date('Y-m-d');

// ── Archivos (fotos) ──
$nombre  = $_FILES['vfoto1']['name'] ?? '';
$ruta    = $_FILES['vfoto1']['tmp_name'] ?? '';
$nombre1 = $_FILES['vfoto2']['name'] ?? '';
$ruta1   = $_FILES['vfoto2']['tmp_name'] ?? '';
$nombre3 = $_FILES['vfoto3']['name'] ?? '';
$ruta3   = $_FILES['vfoto3']['tmp_name'] ?? '';
$nombre4 = $_FILES['vfoto4']['name'] ?? '';
$ruta4   = $_FILES['vfoto4']['tmp_name'] ?? '';

$destino = '';
$destino1 = '';

try {
    $conexion->autocommit(false);

    // ── Subir foto 1 ──
    if ($nombre !== '') {
        $ext = substr($nombre, strrpos($nombre, '.'));
        $nuevoNombre = 'vivero_' . rand(0, 9999999999) . $ext;
        $destino = 'fotosvive/' . $nuevoNombre;
        if (!move_uploaded_file($ruta, $destino)) {
            throw new RuntimeException('Error al subir la foto 1.');
        }
    }
    // ── Subir foto 2 ──
    if ($nombre1 !== '') {
        $ext = substr($nombre1, strrpos($nombre1, '.'));
        $nuevoNombre1 = 'vivero_' . rand(0, 9999999999) . $ext;
        $destino1 = 'fotosvive1/' . $nuevoNombre1;
        if (!move_uploaded_file($ruta1, $destino1)) {
            throw new RuntimeException('Error al subir la foto 2.');
        }
    }

    // ── Obtener siguiente ID de vivero ──
    $result = $conexion->query(
        "SELECT SUBSTR(id_paraje, 2, LENGTH(id_paraje)) AS id
         FROM paraje_vivero WHERE id = (SELECT MAX(id) FROM paraje_vivero)
         ORDER BY id DESC"
    );
    $id = 'V1';
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ((int)$row['id'] > 0) {
            $id = 'V' . ((int)$row['id'] + 1);
        }
    }

    // ── Subir fotos del mapa ──
    if ($nombre3 !== '') {
        $ext = substr($nombre3, strrpos($nombre3, '.'));
        $destino3 = 'constancia/imgconstancia/' . $id . '_01' . $ext;
        if (!move_uploaded_file($ruta3, $destino3)) {
            throw new RuntimeException('Error al subir la foto de ubicación 1.');
        }
    }
    if ($nombre4 !== '') {
        $ext = substr($nombre4, strrpos($nombre4, '.'));
        $destino4 = 'constancia/imgconstancia/' . $id . '_02' . $ext;
        if (!move_uploaded_file($ruta4, $destino4)) {
            throw new RuntimeException('Error al subir la foto de ubicación 2.');
        }
    }

    // ── INSERT vivero ──
    $stmt = $conexion->prepare(
        "INSERT INTO paraje_vivero (
            id_paraje, id_localidad, id_cliente, paraje, lat, lng,
            docpro, referencia, fecha, nombrep, fecha_paraje, rcampo,
            status, tipo, foto1, foto2, status_predio
        ) VALUES (?, ?, ?, ?, ?, ?, '', ?, ?, ?, ?, ?, '1', '2', ?, ?, ?)"
    );
    $stmt->bind_param(
        'sissss' . 'sssss' . 'ssi',
        $id, $loc, $sta, $par, $lati, $lon,
        $refu, $now, $ref, $fec, $cam,
        $destino, $destino1, $status_predio
    );
    $stmt->execute();
    $stmt->close();

    // ── INSERT especies ──
    $data = json_decode((string)($_POST['tMaguey'] ?? '[]'), true);
    if (is_array($data)) {
        $stmtP = $conexion->prepare(
            "INSERT INTO existenciaplanta_vivero (
                id_paraje, regmaguey, origen, id_comun, fecha_siembra,
                cantidadini, fecha_registro, status, existenciaplantas
            ) VALUES (?, ?, ?, ?, ?, ?, ?, '1', ?)"
        );
        foreach ($data as $value) {
            $reg     = (string)($value[0] ?? '');
            $origen  = (string)($value[1] ?? '');
            $comun   = (string)($value[2] ?? '');
            $fechaS  = date('Y-m-d', strtotime((string)($value[4] ?? 'now')));
            $cant    = (string)($value[3] ?? '0');
            $stmtP->bind_param('ssssssss', $id, $reg, $origen, $comun, $fechaS, $cant, $now, $cant);
            $stmtP->execute();
        }
        $stmtP->close();
    }

    // ── INSERT constancia ──
    $stmtC = $conexion->prepare(
        "INSERT INTO constancias_vivero (fecha, constancia, id_paraje, status)
         VALUES (?, ' ', ?, '1')"
    );
    $stmtC->bind_param('ss', $now, $id);
    $stmtC->execute();
    $stmtC->close();

    $conexion->commit();
    echo 'Registro realizado correctamente';
} catch (mysqli_sql_exception|RuntimeException $e) {
    $conexion->rollback();
    error_log('[guardarvive.php] ' . $e->getMessage());
    echo 'Error al realizar el registro';
} finally {
    $conexion->close();
}