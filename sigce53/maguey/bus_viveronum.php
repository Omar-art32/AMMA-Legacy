<?php
	include('php/registro/conexion.php');
	$conexion->set_charset("utf8");
	if (isset($_GET['term'])){
		$return_arr = array();
	    $busca=$_GET['term'];
		$result = $conexion->query("SELECT * FROM paraje_vivero WHERE id_paraje LIKE '%".$_GET['term']."%' and paraje !=' 'and tipo='2'");
	    // Se obtiene el resultado de la consulta
	    while($row = $result->fetch_array()) {
		    //$row_array['id'] = $row['id'];
			$row_array['value'] = $row['id_paraje'];
			$row_array['nombrepre'] = $row['paraje'];
			$row_array['clientep'] = $row['id_cliente'];
			//$row_array['abbre'] = $row['tipo_persona'];
			 array_push($return_arr,$row_array);
		    }
	    echo json_encode($return_arr);
	}

	$conexion->close(); 
?>

