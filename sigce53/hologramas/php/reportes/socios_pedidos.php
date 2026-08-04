<?php
include('../../../common/conexion.php');
if (isset($_GET['term'])){
	$return_arr = array();
    $busca=$_GET['term'];
	$result = $conexion->query("SELECT distinct(no_cliente) from h_pedidos where no_cliente like '%$busca%' limit 10");
    // Se obtiene el resultado de la consulta
    while($row = $result->fetch_array()) {
	        $return_arr[] =  $row['no_cliente'];
	    }
    /* Toss back results as json encoded array. */
    echo json_encode($return_arr);
}


?>