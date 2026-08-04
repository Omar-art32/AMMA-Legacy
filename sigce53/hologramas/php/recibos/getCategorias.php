<?php
try {
	$arr_tipos = array();
	include("../../../common/conexion.php");
	$no_cliente=$_POST["cliente"];
	$marca=$_POST["marca"];
	$edo=$_POST["edo"];
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
		  array_push($arr_tipos, array("tipo" => $row["tipo"]));  
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
 




