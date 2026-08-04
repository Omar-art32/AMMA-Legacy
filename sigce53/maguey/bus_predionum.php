<?php
	include("../common/conexion.php");
	$conexion->set_charset("utf8");
	if (isset($_GET['term'])){
		$return_arr = array();
	    $busca=$_GET['term'];
		$result = $conexion->query("SELECT id_paraje,paraje,id_cliente,superficie,maguey_con_registro,IF(poligono IS NULL, 0, 1) AS poligono FROM paraje where id_paraje like '%".$_GET['term']."%' and paraje !=' 'and tipo='1'");
	    // Se obtiene el resultado de la consulta
	    while($row = $result->fetch_array()) {
		    //$row_array['id'] = $row['id'];
			$row_array['value'] = $row['id_paraje'];
			$row_array['nombrepre'] = $row['paraje'];
			$row_array['clientep'] = $row['id_cliente'];
			$row_array['superficie'] = $row['superficie'];
			$row_array['maguey_con_registro'] = $row['maguey_con_registro'];
			$row_array['poligono'] = $row['poligono'];
			//$row_array['abbre'] = $row['tipo_persona'];
			array_push($return_arr,$row_array);
		}
	    /* Toss back results as json encoded array. */
	    echo json_encode($return_arr);
	}

	$conexion->close(); 
?>

