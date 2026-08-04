<?php
include('conexion.php');
if (isset($_GET['term'])){
	$return_arr = array();
    $busca=$_GET['term'];
	$result = $conexion->query("SELECT * FROM clientes WHERE nombre!='--' and  no_cliente LIKE '%{$busca}%' limit 10");
    // Se obtiene el resultado de la consulta
    while($row = $result->fetch_array()) {
	        $return_arr[] =  $row['no_cliente'];
	    }
    /* Toss back results as json encoded array. */
    echo json_encode($return_arr);
}
$conexion->close(); 


?>