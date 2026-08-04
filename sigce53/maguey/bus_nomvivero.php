<?php
include('php/registro/conexion.php');
if (isset($_GET['pred'])){
	$return_arr=array();
    $busca=$_GET['pred'];
	$result = $conexion->query("SELECT * FROM paraje where id_paraje like '%".$_GET['pred']."%'");
    // Se obtiene el resultado de la consulta
    while($row = $result->fetch_array()) {
	    $row_array['label'] = $row['id_paraje'];
		$row_array['value'] = $row['paraje'];
		array_push($return_arr,$row_array);
	}
    /*Toss back results as json encoded array. */
    echo json_encode($return_arr);
}
$conexion->close(); 
?>