<?php
include('../../../common/conexion.php');
mysqli_set_charset($conexion,"utf8");
if (isset($_GET['term'])){
	$return_arr = array();
    $busca=$_GET['term'];
	$result = $conexion->query("select distinct(m.marca) marca from sh_detalle sh_d INNER JOIN sh_pedidos sh_p ON sh_p.id_solicitud=sh_d.id_solicitud INNER JOIN marcas m on m.no_cliente=sh_p.no_cliente and m.cve_marca=sh_d.marca where m.marca LIKE '%$busca%' group by sh_p.no_cliente,sh_d.marca limit 10");
    // Se obtiene el resultado de la consulta
    while($row = $result->fetch_array()) {
	        $return_arr[] =  $row['marca'];
	    }
    /* Toss back results as json encoded array. */
    echo json_encode($return_arr);
}
?>
