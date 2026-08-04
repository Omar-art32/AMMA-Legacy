<?php
include('../../../common/conexion.php');
if (isset($_GET['term'])){
	$return_arr = array();
    $busca=$_GET['term'];
	$result = $conexion->query("SELECT distinct(id_solicitud) FROM sh_pedidos where id_solicitud LIKE '%{$busca}%' limit 10");
    // Se obtiene el resultado de la consulta
    while($row = $result->fetch_array()) {
	        $return_arr[] =  $row['id_solicitud'];
	    }
    /* Toss back results as json encoded array. */
    echo json_encode($return_arr);
}
?>