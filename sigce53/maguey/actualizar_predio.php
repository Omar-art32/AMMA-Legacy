<?php
session_start();

require_once "funciones_comunes.php";



if (is_ajax())
{
	if (isset($_POST["action"]) && !empty($_POST["action"])) { 
		$action = $_POST["action"];
		switch($action) {
			case "actualizarPredio": actualizarPredio(); break; 
		}
	}
}

function is_ajax() {
	return isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest";
}




function actualizarPredio(){
	$valor = $_POST["valor"];
	$id_paraje = $_POST["id_paraje"];
	$tipoP = $_POST["tipoP"];

            
	try
	{

	include ("php/registro/conexion.php");
	$conexion->autocommit(FALSE);

	$tipo=($tipoP==1)?'paraje':'paraje_vivero';

		$sql = "UPDATE $tipo SET status_predio = ? WHERE id_paraje= ?";
		$ps = $conexion->prepare($sql);
		$ps->bind_param("ii", $valor, $id_paraje);
		if (!$ps->execute()) throw new Exception("Error al actualizar Predio.");
		$ps->close();

	$conexion->commit();
	echo json_encode(array("status" => "correcto", "msj" => "Predio Actualizado."));
	$conexion->close();

	}
	catch (mysqli_sql_exception $e) {
		echo json_encode(array("status" => "error", "msj" => "Error en la base de datos: " . $e->getMessage()));
		$conexion->close();
	}


}

?>
