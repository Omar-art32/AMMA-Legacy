<?php
include('../../common/conexion.php');

	$coincidencias = array();
    $busca=$_POST["strBusqueda"];
	$result = $conexion->query("SELECT no_cliente,nombre FROM clientes WHERE no_cliente LIKE '%{$busca}%' limit 10");
    // Se obtiene el resultado de la consulta   
		while($row = $result->fetch_array()) {

		 array_push($coincidencias,$row['no_cliente']);
	    }
    /* Toss back results as json encoded array. */
    //echo json_encode($return_arr);
	echo json_encode(array("status" => "OK", "msj" => "Operacion exitosa.", "suggest" => $coincidencias));


/*function autocompAsen() {
	
	$busqueda = $_POST["strBusqueda"];
	try {
		$coincidencias = array();
		$sql = "SELECT nombre FROM asentamientos WHERE cp = ? AND nombre LIKE ?";

		include("../../common/conexion.php");

		if ($ps = $conexion->prepare($sql)) {
			$ps->bind_param("ss", $cp, $busqueda);
			$ps->execute();

			$result = $ps->get_result();

			if ($result->num_rows > 0) {
				while($row=$result->fetch_assoc()) {
					array_push($coincidencias, utf8_encode($row["nombre"]));
				}
			}

			$ps->close();

			$conexion->close();

			echo json_encode(array("status" => "correcto", "msj" => "Operacion exitosa.", "coincidencias" => $coincidencias));
		}
		else {
			echo json_encode(array("status" => "error", "msj" => "Error al preparar la setencia SQL.")); 
		}
	}
	catch (mysqli_sql_exception $e) {
		echo json_encode(array("status" => "error", "msj" => "Error en la base de datos: $e->getMessage()"));
		$conexion->close();
	}	

}*/
?>