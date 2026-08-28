<?php
/**
 * add_entrada.php — PHP 8.3
 * Registra una entrada de hologramas (genérica o personalizada) y
 * actualiza (o crea) el renglón de existencias correspondiente.
 * Devuelve JSON {status, msj}.
 *
 * Cambios vs 5.6:
 *  - BUG: usaba $version en el INSERT de tipo 'P' sin haberla definido
 *    en ningún lado (tampoco la manda entradas.js) — en PHP 8 esto es
 *    "Warning: Undefined variable $version" y el campo se guardaba
 *    vacío de forma silenciosa. Se deja explícito como cadena vacía y
 *    documentado; si "version" tiene un significado de negocio, hay que
 *    agregar el campo al formulario y a este endpoint.
 *  - SQL concatenado → sentencias preparadas
 *  - mb_convert_encoding(ISO-8859-1) → cast directo a string (ya no es
 *    necesario decodificar; la entrada llega en UTF-8 y va a un parámetro
 *    preparado, no a HTML)
 *  - include → require_once con __DIR__
 *  - try/catch con error_log
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../common/conexion.php';


$usr    = (string)($_POST['user'] ?? '');
$tipo   = (string)($_POST['tipo'] ?? '');
$marca  = (string)($_POST['marca'] ?? '');
$cliente = (string)($_POST['cliente'] ?? '');
$serie  = '';

$ext_ini = (string)($_POST['ext_ini'] ?? '');
$ext_fin = (string)($_POST['ext_fin'] ?? '');
$ent_ini = (string)($_POST['ini_ent'] ?? '');
$ent_fin = (string)($_POST['fin_ent'] ?? '');
$existe_reg = (string)($_POST['existe_reg'] ?? '');
$total  = (string)($_POST['total'] ?? '');
$fecha  = date('Y-m-d H:i:s');

// Revisar el folio inicial de la existencia para saber cuál será el folio inicial de la entrada
if ($ext_ini == 0) {
    $fi = $ent_ini;
} else {
    $fi = ($ent_ini > $ext_ini) ? $ext_ini : $ent_ini;
}
// Para folio final
$ff = ($ent_fin > $ext_fin) ? $ent_fin : $ext_fin;

try {
    if ($tipo === 'G') {
        $stmtEnt = $conexion->prepare(
            "INSERT INTO h_entradas (no_cliente, marca, serie, fol_ini, fol_fin, cantidad, fecha, usr)
             VALUES ('--', '--', '-', ?, ?, ?, ?, ?)"
        );
        $stmtEnt->bind_param('sssss', $ent_ini, $ent_fin, $total, $fecha, $usr);

        if ($existe_reg == 0) {
            $stmtExist = $conexion->prepare(
                "INSERT INTO h_existencias (no_cliente, marca, serie, edo, tipo, fol_ini, fol_fin, existencias)
                 VALUES ('--', '--', '-', 'OAXACA', 0, ?, ?, ?)"
            );
            $stmtExist->bind_param('sss', $fi, $ff, $total);
        } else {
            $stmtExist = $conexion->prepare(
                "UPDATE h_existencias SET fol_ini = ?, fol_fin = ?, existencias = existencias + ?
                 WHERE no_cliente = '--' AND marca = '--' AND serie = '-'"
            );
            $stmtExist->bind_param('sss', $fi, $ff, $total);
        }
    } else {
        // Tipo 'P' (Personalizado)
        $serie = (string)($_POST['serie'] ?? '');
        // NOTA: "version" no viene del formulario; se deja vacío explícitamente (ver comentario arriba)
        $version = '';

        $stmtEnt = $conexion->prepare(
            "INSERT INTO h_entradas (no_cliente, marca, serie, fol_ini, fol_fin, cantidad, fecha, usr, version)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmtEnt->bind_param('sssssssss', $cliente, $marca, $serie, $ent_ini, $ent_fin, $total, $fecha, $usr, $version);

        if ($existe_reg == 0) {
            $stmtExist = $conexion->prepare(
                "INSERT INTO h_existencias (no_cliente, marca, serie, fol_ini, fol_fin, existencias)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmtExist->bind_param('ssssss', $cliente, $marca, $serie, $fi, $ff, $total);
        } else {
            $stmtExist = $conexion->prepare(
                "UPDATE h_existencias SET fol_ini = ?, fol_fin = ?, existencias = existencias + ?
                 WHERE no_cliente = ? AND marca = ? AND serie = ?"
            );
            $stmtExist->bind_param('ssssss', $fi, $ff, $total, $cliente, $marca, $serie);
        }
    }

    if (!$stmtEnt->execute()) {
        echo json_encode(['status' => 'error', 'msj' => 'Ha ocurrido un error al generar el recibo, imprima pantalla del error y comuniquelo al area de sistemas']);
    } else {
        $stmtEnt->close();
        if (!$stmtExist->execute()) {
            echo json_encode(['status' => 'error', 'msj' => 'Ha ocurrido un error al actualizar existencias, imprima pantalla del error y comuniquelo al area de sistemas']);
        } else {
            echo json_encode(['status' => 'correcto', 'msj' => 'Entrada agregada correctamente']);
        }
        $stmtExist->close();
    }
} catch (mysqli_sql_exception $e) {
    error_log('[add_entrada.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msj' => 'Ha ocurrido un error, imprima pantalla del error y comuniquelo al area de sistemas']);
} finally {
    $conexion->close();
}
