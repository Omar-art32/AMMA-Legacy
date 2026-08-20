<?php
// Escudo para evitar que las advertencias rompan la respuesta JSON
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', '0');
// Encabezado JSON explícito
header('Content-Type: application/json; charset=utf-8');
try {
	$arr_tipos = array();
	include("../../../common/conexion.php");

	//ecepción segura con operador nulo para PHP 8
    $no_cliente = $_POST["cliente"] ?? '';
    $marca      = $_POST["marca"] ?? '';
    $edo        = $_POST["edo"] ?? '';

    // Sanitización básica de parámetros
    $no_cliente = $conexion->real_escape_string($no_cliente);
    $marca      = $conexion->real_escape_string($marca);
    $edo        = $conexion->real_escape_string($edo);
	$sql = "SELECT distinct(tipo) tipo FROM h_existencias WHERE no_cliente ='{$no_cliente}' and marca='{$marca}' and edo='{$edo}'";
	$result=$conexion->query($sql);
	$num_res=0;
	if($result) 
	{
		$num_res=$result->num_rows;
	}
	if($num_res>0)
	{
		while($row=$result->fetch_assoc())
		{
			$tipo_utf8 = mb_convert_encoding($row["tipo"] ?? '', 'UTF-8', 'ISO-8859-1');
            array_push($arr_tipos, array("tipo" => $tipo_utf8));
		}
	}
	$conexion->close();
	echo json_encode(array("status" => "OK", "msj" => 'Tipos encontrados', "tipos" => $arr_tipos,"num_res"=>$num_res));
}
catch (Exception $e) {
	echo json_encode(array("status" => "error", "msj" => "Error en la base de datos: " . $e->getMessage()));
	$conexion->close();
}	 
?>
 




