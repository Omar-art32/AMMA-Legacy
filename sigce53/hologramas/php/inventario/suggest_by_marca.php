<?php
include('../../../common/conexion.php');
mysqli_set_charset($conexion,"utf8");
if (isset($_GET['term'])){
	$return_arr = array();
    $busca=$_GET['term'];
	$result = $conexion->query("select distinct(marcas.marca) marca from h_pedidos inner join marcas on marcas.no_cliente=h_pedidos.no_cliente and marcas.cve_marca=h_pedidos.marca
	where marcas.marca LIKE '%{$busca}%' group by h_pedidos.no_cliente,h_pedidos.marca limit 10");
    // Se obtiene el resultado de la consulta
    while($row = $result->fetch_array()) {
	        $return_arr[] =  $row['marca'];
	    }
    /* Toss back results as json encoded array. */
    echo json_encode($return_arr);
}
?>
