<?php
//include('php/registro/conexion.php');
include("../common/conexion.php");
if (isset($_GET['term'])){
	$return_arr = array();
    $busca=$_GET['term'];
	$result = $conexion->query("SELECT * FROM clientes where no_cliente like '%".$_GET['term']."%'");
    // Se obtiene el resultado de la consulta
    while($row = $result->fetch_array()) {
	    $row_array['label'] = $row['no_cliente'];
		$row_array['value'] = $row['nombre'];
		array_push($return_arr,$row_array);
	}
    /* Toss back results as json encoded array. */
    echo json_encode($return_arr);
}
$conexion->close(); 
?>