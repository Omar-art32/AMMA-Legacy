<?php
include('../../../common/conexion.php');
if (isset($_GET['term'])){
	$return_arr = array();
    $busca=$_GET['term'];
	$result = $conexion->query("SELECT distinct(no_pedido) FROM h_pedidos where no_pedido LIKE '%{$busca}%' limit 10");
    // Se obtiene el resultado de la consulta
    while($row = $result->fetch_array()) {
	        $return_arr[] =  $row['no_pedido'];
	    }
    /* Toss back results as json encoded array. */
    echo json_encode($return_arr);
}
?>