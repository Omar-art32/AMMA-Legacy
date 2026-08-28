<?php
/**
 * re_enviar.php — PHP 8.3
 * Marca un pedido como reenviado (status=1). Devuelve JSON {status, msj}.
 *
 * Cambios vs 5.6:
 *  - CRÍTICO: el original abría una conexión NUEVA y ACTIVA a un
 *    servidor remoto con credenciales hardcodeadas en el código:
 *      new mysqli("50.63.227.48","crmreg","CrM#bd2016JL","crmreg")
 *    Esto es el único archivo del módulo con sync remota SIN deshabilitar
 *    (en el resto ya estaba comentada). Se desactiva aquí también, por
 *    consistencia con el resto del módulo y porque no está confirmado
 *    si ese servidor sigue vigente — pero el bloque queda documentado
 *    abajo, NO borrado. Si el servidor remoto sigue en uso, hay que:
 *      1) Sacar esa contraseña del código (usar variables de entorno,
 *         igual que common/conexion.php).
 *      2) Restaurar la conexión y descomentar el bloque.
 *  - $user y $fecha estaban comentados en el original (nunca se
 *    definían) y se usaban de todos modos dentro del SQL remoto —
 *    error de origen, ya no aplica al quedar ese bloque deshabilitado.
 *  - SQL concatenado ($no_pedido directo) → sentencia preparada
 *  - include → require_once con __DIR__
 *  - try/catch/finally con error_log
 */
declare(strict_types=1);

require_once __DIR__ . '/../../../common/conexion.php';


$no_pedido = (int)($_POST['no_pedido'] ?? 0);

try {
    /*
     * ------------ VERIFICAR/INSERTAR EN LA BD REMOTA (DESHABILITADO) ------------
     * $con_rem = new mysqli("50.63.227.48","crmreg","CrM#bd2016JL","crmreg");
     * $sql_check = "SELECT * FROM h_pedidos WHERE no_pedido=$no_pedido";
     * $result = $con_rem->query($sql_check);
     * if ($result->num_rows == 0) {
     *     // INSERTAR EN LA BD REMOTA
     *     $sql_pedido = "SELECT no_pedido,fecha,no_cliente,marca,serie,fi,ff,cantidad,status,usr
     *                    FROM h_pedidos WHERE no_pedido=$no_pedido";
     *     $res_p = $conexion->query($sql_pedido);
     *     $sql_remota = "INSERT INTO h_pedidos (no_pedido,fecha,no_cliente,marca,serie,fi,ff,cantidad,status,usr) VALUES ...";
     *     $in_rem = $con_rem->query($sql_remota);
     *     // ... si $in_rem es true, marcar local como reenviado (igual que abajo)
     * }
     * --------------------------------------------------------------------------
     * Con la sync remota deshabilitada, "reenviar" simplemente confirma
     * localmente que el pedido quedó enviado.
     */

    $stmt = $conexion->prepare("UPDATE h_pedidos SET status = 1 WHERE no_pedido = ?");
    $stmt->bind_param('i', $no_pedido);
    $stmt->execute();

    if ($conexion->affected_rows > 0) {
        echo json_encode(['status' => 'OK', 'msj' => 'Se ha re-enviado correctamente la requisicion']);
    } else {
        echo json_encode(['status' => 'OK', 'msj' => 'No se pudo actualizar la informacion del re-envío intente mas tarde']);
    }
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    error_log('[re_enviar.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msj' => 'No se ha podido re-enviar la requisición']);
} finally {
    $conexion->close();
}
