<?php
/**
 * guardarm.php — PHP 8.3
 * Agrega especies de maguey a un predio existente y opcionalmente
 * actualiza superficie y polígono KML. Genera guías si corresponde.
 *
 * Cambios vs 5.6:
 *  - include('php/registro/conexion.php') → require_once centralizado
 *  - SQL concatenado (~5 consultas) → sentencias preparadas
 *  - set_charset("utf8") eliminado
 *  - $e->getMessage() ya no se expone al cliente
 *  - Validación de $num con regex
 */
declare(strict_types=1);

require_once __DIR__ . '/../common/conexion.php';

header('Content-Type: text/plain; charset=utf-8');
ini_set('display_errors', '0');
date_default_timezone_set('America/Mexico_City');

$num      = (string)($_POST['num'] ?? '');
$cboxGuiaM = (int)($_POST['cboxGuiaM'] ?? 0);
$supe     = (string)($_POST['superficieM'] ?? '');
$cboxMSP  = (int)($_POST['cboxMSP'] ?? 0);

if (!preg_match('/^[A-Za-z0-9]+$/', $num)) {
    echo 'Parámetros no válidos';
    exit;
}

// ── Polígono KML (si se envió archivo) ──
$pol = null;
$rut = $_FILES['poligonoM']['tmp_name'] ?? '';
if ($rut !== '' && file_exists($rut)) {
    $kml = file_get_contents($rut);
    $start = '<coordinates>';
    $end   = '</coordinates>';
    $pos = strpos($kml, $start);
    if ($pos !== false) {
        $a = substr($kml, $pos + strlen($start));
        $npos = strpos($a, $end);
        $poligono = ($npos !== false) ? trim(substr($a, 0, $npos)) : trim($a);

        $resultado = str_replace(',0 ', '*', $poligono);
        $resultado = str_replace(',', ' ', $resultado);
        $resultado = str_replace('*', ',', $resultado);
        if (str_ends_with($resultado, ',')) {
            $resultado = substr($resultado, 0, -1);
        } elseif (str_ends_with($resultado, '0')) {
            $resultado = substr($resultado, 0, -2);
        }
        $pol = $resultado;
    }
}

try {
    $conexion->autocommit(false);
    $data = json_decode((string)($_POST['tMagueys'] ?? '[]'), true);

    // ── Actualizar superficie ──
    if ($cboxMSP === 1) {
        $stmt = $conexion->prepare("UPDATE paraje SET superficie = ? WHERE id_paraje = ?");
        $stmt->bind_param('ss', $supe, $num);
        $stmt->execute();
        $stmt->close();

        // ── Actualizar polígono si se proporcionó ──
        if ($pol !== null) {
            // GEOMFROMTEXT requiere interpolación (no se puede bindear geometría como string)
            $sqlPol = "UPDATE paraje SET poligono = GEOMFROMTEXT('POLYGON((" . $conexion->real_escape_string($pol) . "))') WHERE id_paraje = ?";
            $stmt = $conexion->prepare($sqlPol);
            $stmt->bind_param('s', $num);
            $stmt->execute();
            $stmt->close();
        }
    }

    // ── INSERT especies ──
    if (is_array($data)) {
        $stmtP = $conexion->prepare(
            "INSERT INTO existenciaplanta (
                id_paraje, regmaguey, dis_surcometros, dis_planmetros,
                id_comun, cantidadini, edad, fecha_registro, status,
                existenciaplantas
            ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), '1', ?)"
        );
        foreach ($data as $value) {
            $reg   = (string)($value[0] ?? '');
            $surco = (string)($value[1] ?? '');
            $plan  = (string)($value[2] ?? '');
            $comun = (string)($value[3] ?? '');
            $cant  = (string)($value[4] ?? '0');
            $edad  = (string)($value[5] ?? '0');
            $stmtP->bind_param('ssssssss', $num, $reg, $surco, $plan, $comun, $cant, $edad, $cant);
            $stmtP->execute();
        }
        $stmtP->close();
    }

    // ── Generar guías si corresponde ──
    if ($cboxGuiaM === 1) {
        $stmtG = $conexion->prepare(
            "INSERT INTO cextracciones (id_paraje, status, fecha, constancia)
             VALUES (?, '1', NOW(), ' ')"
        );
        $stmtG->bind_param('s', $num);
        $stmtG->execute();
        $stmtG->close();
    } elseif (is_array($data)) {
        foreach ($data as $value) {
            if (((int)($value[5] ?? 0)) > 4) {
                $stmtG = $conexion->prepare(
                    "INSERT INTO cextracciones (id_paraje, status, fecha, constancia)
                     VALUES (?, '1', NOW(), ' ')"
                );
                for ($i = 0; $i < 5; $i++) {
                    $stmtG->bind_param('s', $num);
                    $stmtG->execute();
                }
                $stmtG->close();
            }
        }
    }

    $conexion->commit();
    echo 'Registro realizado correctamente';
} catch (mysqli_sql_exception|RuntimeException $e) {
    $conexion->rollback();
    error_log('[guardarm.php] ' . $e->getMessage());
    echo 'Error al realizar el registro';
} finally {
    $conexion->close();
}