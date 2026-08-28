<?php
/**
 * add_marca.php — PHP 8.3
 * Da de alta una nueva marca (cve_marca + nombre) para un cliente.
 * Devuelve JSON {status, msj, remoto, msj_remoto, upmarc, msj_upmarc}.
 *
 * Cambios vs 5.6:
 *  - BUG CORREGIDO: leía $_POST['marca_new '] (con un espacio de más al
 *    final de la clave) — nunca podía coincidir con el campo real del
 *    formulario ('marca_new'), así que $marca_raw siempre llegaba vacío
 *    y la marca se guardaba en blanco.
 *  - include('../../common/conexion_remota.php') ELIMINADO: ese archivo
 *    no existe en el repo → esto causaba un Fatal Error en cada
 *    llamada (Call to a member function query() on null). Todo el
 *    bloque de sincronización remota (BD en 50.63.227.48 / crmreg) se
 *    deja COMENTADO, no borrado — no está confirmado si ese servidor
 *    remoto sigue vigente. Si se retoma, hay que restaurar una conexión
 *    real a $con_rem antes de descomentar.
 *  - SQL concatenado → sentencias preparadas
 *  - include → require_once con __DIR__
 *  - error_reporting()/ini_set('display_errors') como parche → eliminado
 *  - try/catch con error_log
 */
declare(strict_types=1);

require_once __DIR__ . '/../../common/conexion.php';


$client = substr((string)($_POST['nc_marca'] ?? ''), 0, 4);
$letra  = (string)($_POST['letra'] ?? '');
$marca  = strtoupper((string)($_POST['marca_new'] ?? ''));

$respuesta = ['status' => '', 'msj' => '', 'remoto' => '', 'msj_remoto' => '', 'upmarc' => '', 'msj_upmarc' => ''];

try {
    $stmt = $conexion->prepare("SELECT * FROM marcas WHERE no_cliente = ? AND cve_marca = ?");
    $stmt->bind_param('ss', $client, $letra);
    $stmt->execute();
    $existe_m = $stmt->get_result()->num_rows;
    $stmt->close();

    if ($existe_m === 0) {
        $stmtIns = $conexion->prepare(
            "INSERT INTO marcas (no_cliente, cve_marca, marca, serie, sinc) VALUES (?, ?, ?, 'A', 0)"
        );
        $stmtIns->bind_param('sss', $client, $letra, $marca);

        if ($stmtIns->execute()) {
            $respuesta['status'] = 'OK';
            $respuesta['msj'] = 'Marca Agregada -LOCAL-';
        } else {
            $respuesta['status'] = 'Error';
            $respuesta['msj'] = 'Error agregar marca  -LOCAL-';
        }
        $stmtIns->close();

        /*
         * ------------ GUARDAR EN LA BD REMOTA (DESHABILITADO) ------------
         * Dependía de $con_rem, definido en common/conexion_remota.php.
         * Ese archivo no existe en el repo actual — se desconoce si el
         * servidor remoto (antes 50.63.227.48 / BD crmreg) sigue en uso.
         * Se deja el flujo documentado por si hay que reactivarlo:
         *
         * $sep = '';
         * $sql_no_sinc = "SELECT no_cliente,cve_marca,marca,serie,sinc FROM marcas WHERE sinc=0";
         * $get_pendientes = $conexion->query($sql_no_sinc);
         * $sql_ins_pendientes = "insert into marcas_tmp(no_cliente,cve_marca,marca,serie,sinc) values";
         * while ($row = $get_pendientes->fetch_assoc()) {
         *     $sql_ins_pendientes .= $sep."('{$row['no_cliente']}', '{$row['cve_marca']}', '{$row['marca']}', '{$row['serie']}', '0')";
         *     $sep = ',';
         * }
         * $sql_ins_pendientes .= ';';
         * $res_temps = $con_rem->query("delete from marcas_tmp");
         * $remoto_ins = $con_rem->query($sql_ins_pendientes);
         * if ($remoto_ins == false) {
         *     $respuesta['remoto'] = 'Error';
         *     $respuesta['msj_remoto'] = 'Error agregar marca  -REMOTO-';
         * } else {
         *     $sql_ins_final = "insert into marcas(no_cliente,cve_marca,marca,serie,sinc)
         *                       (select no_cliente,cve_marca,marca,serie,sinc from marcas_tmp
         *                        where concat(no_cliente,cve_marca) not in
         *                        (select concat(no_cliente,cve_marca) from marcas))";
         *     $con_rem->query($sql_ins_final);
         *     $res_up = $conexion->query("update marcas set sinc=1 where sinc=0");
         *     $respuesta['upmarc'] = $res_up ? 'OK' : 'Error';
         *     $respuesta['msj_upmarc'] = $res_up ? 'Marca en Linea -LOCAL-' : 'Error actualizar marca -LOCAL-';
         *     $respuesta['remoto'] = 'OK';
         *     $respuesta['msj_remoto'] = 'Marca Agregada -REMOTO-';
         * }
         * ------------------------------------------------------------------
         */
    } else {
        $respuesta['status'] = 'Error';
        $respuesta['msj'] = 'Esta marca ya existe';
    }

    echo json_encode($respuesta);
} catch (mysqli_sql_exception $e) {
    error_log('[add_marca.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'Error', 'msj' => 'Disculpe ha ocurrido un error, intente mas tarde']);
} finally {
    $conexion->close();
}
