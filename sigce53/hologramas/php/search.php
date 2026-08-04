<?php
include('../../common/conexion.php');
if (isset($_GET['term'])){
	$return_arr = array();
    $busca=$_GET['term'];
	$result = $conexion->query("SELECT no_cliente,nombre FROM clientes WHERE no_cliente LIKE '%{$busca}%' limit 10");
    // Se obtiene el resultado de la consulta   
		while($row = $result->fetch_array()) {
		$row_array['value'] = $row['no_cliente'];

		 array_push($return_arr,$row_array);
	    }
    /* Toss back results as json encoded array. */
    echo json_encode($return_arr);
}
?>