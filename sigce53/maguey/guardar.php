<?php
/**
 * guardar.php — PHP 8.3
 * Registro completo de un predio nuevo: datos del paraje, documento de
 * propiedad (foto), imágenes de constancia, especies de maguey y guías.
 * Todo dentro de una transacción.
 *
 * Cambios vs 5.6:
 *  - TODAS las consultas con sentencias preparadas (~6 INSERT/SELECT
 *    que antes concatenaban $_POST directo en el SQL)
 *  - set_charset("utf8") eliminado (conexion.php ya usa utf8mb4)
 *  - include → require_once con __DIR__
 *  - echo $sqlparaje (debug de SQL en producción) eliminado
 *  - $e->getMessage() ya no se expone al cliente (iba directo al echo)
 *  - Validación de entrada con ?? y cast de tipos
 *  - Polígono KML: bloque comentado en el original, se preserva comentado
 *  - Nota: el MAX(id) para consecutivo de predio y guía sigue expuesto
 *    a condición de carrera (documentado como mejora futura)
 */
declare(strict_types=1);

require_once __DIR__ . '/../common/conexion.php';

header('Content-Type: text/plain; charset=utf-8');
ini_set('display_errors', '0');
date_default_timezone_set('America/Mexico_City');

// ── Datos del formulario ──
$loc   = (int)($_POST['local'] ?? 0);
$sta   = (string)($_POST['state'] ?? '');
$par   = (string)($_POST['paraje'] ?? '');
$lati  = (string)($_POST['lat'] ?? '0');
$lon   = (string)($_POST['lng'] ?? '0');
$ten   = (string)($_POST['tenencia'] ?? '');
$supe  = (string)($_POST['superficie'] ?? '0');
$refu  = '';
$usu   = (string)($_POST['usufruto'] ?? '');
$ref   = (string)($_POST['referencia2'] ?? '');
$nombre_asociado = (string)($_POST['abbrev'] ?? '');
$fec   = date('Y-m-d', strtotime((string)($_POST['fecha'] ?? 'now')));
$cam   = (string)($_POST['campo'] ?? '');
$status_predio = '1';
$cboxGuia    = (int)($_POST['cboxGuia'] ?? 0);
$cboxMCR     = (int)($_POST['cboxMCR'] ?? 0);
$SelServicio = (string)($_POST['SelServicio'] ?? '');
$idus        = (int)($_POST['idus'] ?? 0);

// ── Archivos ──
$nombre  = $_FILES['archivo']['name'] ?? '';
$ruta    = $_FILES['archivo']['tmp_name'] ?? '';
$nombrei1 = $_FILES['imagen1']['name'] ?? '';
$rutai1   = $_FILES['imagen1']['tmp_name'] ?? '';
$nombrei2 = $_FILES['imagen2']['name'] ?? '';
$rutai2   = $_FILES['imagen2']['tmp_name'] ?? '';

$destino = '';

try {
    $conexion->autocommit(false);

    // ── Subir documento de propiedad ──
    if ($nombre !== '') {
        $ext = substr($nombre, strrpos($nombre, '.'));
        $nuevoNombre = 'docpro_' . rand(0, 9999999999) . $ext;
        $destino = 'docpro/' . $nuevoNombre;
        if (!move_uploaded_file($ruta, $destino)) {
            throw new RuntimeException('Error al subir el documento de propiedad.');
        }
    }

    // ── Obtener siguiente ID de predio ──
    $result = $conexion->query(
        "SELECT SUBSTR(id_paraje, 2, LENGTH(id_paraje)) AS id
         FROM paraje WHERE id = (SELECT MAX(id) FROM paraje)
         ORDER BY id DESC"
    );
    $id = 'P1';
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ((int)$row['id'] > 0) {
            $id = 'P' . ((int)$row['id'] + 1);
        }
    }

    // ── Subir imágenes de constancia ──
    if ($nombrei1 !== '') {
        $ext = substr($nombrei1, strrpos($nombrei1, '.'));
        $destino1 = 'constancia/imgconstancia/' . $id . '_01' . $ext;
        if (!move_uploaded_file($rutai1, $destino1)) {
            throw new RuntimeException('Error al subir la foto de ubicación 1.');
        }
    }
    if ($nombrei2 !== '') {
        $ext = substr($nombrei2, strrpos($nombrei2, '.'));
        $destino2 = 'constancia/imgconstancia/' . $id . '_02' . $ext;
        if (!move_uploaded_file($rutai2, $destino2)) {
            throw new RuntimeException('Error al subir la foto de ubicación 2.');
        }
    }

    // ── Nombre del productor (referencia o abbrev) ──
    $nombrep = ($ref !== '') ? $ref : $nombre_asociado;

    // ── INSERT paraje ──
    $stmt = $conexion->prepare(
        "INSERT INTO paraje (
            id_paraje, id_localidad, id_cliente, paraje, lat, lng,
            poligono, tenencia, superficie, docpro, referencia, usufruto,
            fecha, nombrep, fecha_paraje, rcampo, status, tipo,
            maguey_con_registro, status_predio, id_us, servicio
        ) VALUES (
            ?, ?, ?, ?, ?, ?,
            NULL, ?, ?, ?, '', ?,
            NOW(), ?, ?, ?, '1', '1',
            ?, ?, ?, ?
        )"
    );
    $stmt->bind_param(
        'sissss' . 'sss' . 's' . 'sss' . 'isis',
        $id, $loc, $sta, $par, $lati, $lon,
        $ten, $supe, $destino,
        $usu,
        $nombrep, $fec, $cam,
        $cboxMCR, $status_predio, $idus, $SelServicio
    );
    $stmt->execute();
    $stmt->close();

    // ── INSERT especies de maguey ──
    $data = json_decode((string)($_POST['tMaguey'] ?? '[]'), true);
    if (is_array($data)) {
        $stmtP = $conexion->prepare(
            "INSERT INTO existenciaplanta (
                regmaguey, dis_surcometros, dis_planmetros, id_comun,
                cantidadini, edad, fecha_registro, status,
                existenciaplantas, id_paraje, id_us
            ) VALUES (?, ?, ?, ?, ?, ?, NOW(), '1', ?, ?, ?)"
        );
        foreach ($data as $value) {
            $regmaguey = (string)($value[0] ?? '');
            $surco     = (string)($value[1] ?? '');
            $plan      = (string)($value[2] ?? '');
            $idComun   = (string)($value[6] ?? $value[3] ?? '');
            $cantini   = (string)($value[4] ?? '0');
            $edad      = (string)($value[5] ?? '0');
            $stmtP->bind_param(
                'ssssssssi',
                $regmaguey, $surco, $plan, $idComun,
                $cantini, $edad, $cantini, $id, $idus
            );
            $stmtP->execute();
        }
        $stmtP->close();
    }

    // ── INSERT constancia ──
    $stmtC = $conexion->prepare(
        "INSERT INTO constancias (fecha, constancia, id_paraje, status, id_us)
         VALUES (NOW(), ' ', ?, '1', ?)"
    );
    $stmtC->bind_param('si', $id, $idus);
    $stmtC->execute();
    $stmtC->close();

    // ── Generar guías ──
    if ($cboxGuia > 0) {
        $resG = $conexion->query(
            "SELECT SUBSTR(id_extraccion, 2, LENGTH(id_extraccion)) AS id
             FROM cextracciones
             WHERE id = (SELECT MAX(id) FROM cextracciones
                         WHERE SUBSTRING(id_extraccion, 1, 2) != 'GP')
             ORDER BY id DESC"
        );
        $idS = 1;
        if ($resG->num_rows > 0) {
            $rowG = $resG->fetch_assoc();
            if ((int)$rowG['id'] > 0) {
                $idS = (int)$rowG['id'] + 1;
            }
        }

        $stmtG = $conexion->prepare(
            "INSERT INTO cextracciones (id_extraccion, id_paraje, status, fecha, constancia, id_us)
             VALUES (?, ?, '1', NOW(), ' ', ?)"
        );
        for ($i = 0; $i < $cboxGuia; $i++) {
            $idG = 'G' . ($idS + $i);
            $stmtG->bind_param('ssi', $idG, $id, $idus);
            $stmtG->execute();
        }
        $stmtG->close();
    }

    $conexion->commit();
    echo 'Registro realizado correctamente';
} catch (mysqli_sql_exception|RuntimeException $e) {
    $conexion->rollback();
    error_log('[guardar.php] ' . $e->getMessage());
    echo 'Error al realizar el registro';
} finally {
    $conexion->close();
}