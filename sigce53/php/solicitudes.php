<?php
session_start();
$notificaciones = array();
$notificaciones2 = array();
$historial = array();
$noti = 0;
include("../common/conexion.php");
mysqli_query($conexion, "SET NAMES 'utf8'");
$resultado = 0;

$caso = $_POST['caso'];

switch ($caso) {
    case 'atendida':
        $id = $_POST["id"];
        $visto = $_POST["visto"];
        $sql = "UPDATE notificaciones_instalaciones set visto='$visto', fecha_revision=now() where id='$id'";
        $resultado = mysqli_query($conexion, $sql);
        break;
    case 'notificacion':
        $id_s = $_POST["id"];
        $usuario = $_SESSION[$id_s]['id_us'];

        if ($usuario == 1) {
            $sql = "SELECT ni.id, i.id AS id_instalacion, i.tipo,ni.fecha, no_cliente, c.nombre AS nombreC, au.nombre AS agrego, ni.agrego_usuario,
            (SELECT ti.descripcion FROM tipos_instalaciones ti WHERE ti.id=i.tipo) As tipo_instalacion
            FROM a_usuarios au
            INNER JOIN notificaciones_instalaciones ni ON ni.agrego = au.id_us
            INNER JOIN instalaciones i ON i.id = ni.id_instalacion
            INNER JOIN clientes_instalaciones ci ON ci.instalacion = i.id
            INNER JOIN clientes c ON c.no_cliente = ci.cliente
            WHERE ni.visto = '0' AND ni.agrego_usuario<>2;";

            $query = $conexion->query($sql);

            while ($r = $query->fetch_assoc()) {
                array_push($notificaciones, array("id" => $r['id'], "id_instalacion" => $r['id_instalacion'], "fecha" => $r['fecha'], "no_cliente" => $r['no_cliente'], "agrego" => $r['agrego'], "nombre" => $r['nombreC'], "tipo" => $r['tipo_instalacion']));
            }

            $sql = "SELECT ni.id, ni.id_instalacion, ni.tipo_instalacion AS tipo,ni.fecha,
            IF (ni.tipo_instalacion=1,(SELECT no_cliente FROM rv_maestro_fabrica WHERE id=id_instalacion),(SELECT no_cliente FROM rv_envasadora_encargado WHERE id=id_instalacion)) AS no_cliente,
            IF (ni.tipo_instalacion=1,(SELECT c.nombre FROM rv_maestro_fabrica INNER JOIN clientes c ON c.no_cliente= rv_maestro_fabrica.no_cliente WHERE id=id_instalacion),(SELECT c.nombre FROM rv_envasadora_encargado INNER JOIN clientes c ON c.no_cliente= rv_envasadora_encargado.no_cliente WHERE id=id_instalacion)) AS nombreC,
            v.nombre AS agrego, ni.agrego_usuario, IF (ni.tipo_instalacion=1, 'FABRICA','ENVASADORA') AS tipo_instalacion
            FROM verificadores v
            INNER JOIN notificaciones_instalaciones ni ON ni.agrego = v.id_us
            WHERE ni.agrego_usuario=2 AND ni.visto='0';";

            $query = $conexion->query($sql);

            while ($r = $query->fetch_assoc()) {
                array_push($notificaciones2, array("id" => $r['id'], "id_instalacion" => $r['id_instalacion'], "fecha" => $r['fecha'], "no_cliente" => $r['no_cliente'], "agrego" => $r['agrego'], "nombre" => $r['nombreC'], "tipo" => $r['tipo_instalacion']));
            }

            $sql = "SELECT ni.id, ni.id_instalacion, i.tipo,ni.fecha, ni.visto,no_cliente, c.nombre AS nombreC, au.nombre AS agrego, ni.agrego_usuario,
            (SELECT ti.descripcion FROM tipos_instalaciones ti WHERE ti.id=i.tipo) As tipo_instalacion
            FROM a_usuarios au
            LEFT JOIN notificaciones_instalaciones ni ON ni.agrego = au.id_us
            INNER JOIN instalaciones i ON i.id = ni.id_instalacion
            INNER JOIN clientes_instalaciones ci ON ci.instalacion = i.id
            INNER JOIN clientes c ON c.no_cliente = ci.cliente
            WHERE ni.agrego_usuario<>2
            UNION
            SELECT ni.id, ni.id_instalacion, ni.tipo_instalacion AS tipo,ni.fecha,ni.visto,
            IF (ni.tipo_instalacion=1,(SELECT no_cliente FROM rv_maestro_fabrica WHERE id=id_instalacion),(SELECT no_cliente FROM rv_envasadora_encargado WHERE id=id_instalacion)) AS no_cliente,
            IF (ni.tipo_instalacion=1,(SELECT c.nombre FROM rv_maestro_fabrica INNER JOIN clientes c ON c.no_cliente= rv_maestro_fabrica.no_cliente WHERE id=id_instalacion),(SELECT c.nombre FROM rv_envasadora_encargado INNER JOIN clientes c ON c.no_cliente= rv_envasadora_encargado.no_cliente WHERE id=id_instalacion)) AS nombreC,
            v.nombre AS agrego, ni.agrego_usuario, IF (ni.tipo_instalacion=1, 'FABRICA','ENVASADORA') AS tipo_instalacion
            FROM verificadores v
            INNER JOIN notificaciones_instalaciones ni ON ni.agrego = v.id_us
            WHERE ni.agrego_usuario=2;";

            $query = $conexion->query($sql);

            while ($r = $query->fetch_assoc()) {
                $visto = ($r['visto']) ? "SI" : "NO";
                array_push($historial, array("id" => $r['id'], "id_instalacion" => $r['id_instalacion'], "fecha" => $r['fecha'], "no_cliente" => $r['no_cliente'], "agrego" => $r['agrego'], "nombre" => $r['nombreC'], "tipo" => $r['tipo_instalacion'], "visto" => $visto));
            }
        }

        break;
    default:
        $resultado = 0;
        break;
}

echo json_encode(array("status" => $resultado, "notificaciones" => $notificaciones,"notificaciones2" => $notificaciones2, "historial" => $historial, "noti" => $noti));

mysqli_close($conexion);
