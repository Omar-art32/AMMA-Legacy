<?php
try {
	$estados = array();
	$edo="";
	include("../../common/conexion.php");
	$no_cliente=$_POST["cliente"];
	$marca=$_POST["marca"];
	$sql = "SELECT distinct(edo) FROM h_existencias WHERE no_cliente ='{$no_cliente}' and marca='{$marca}';";	
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
		  if($row["edo"]=="")
		  {
			  $edo="NA";
		  }
		  else
		  {
			  $edo=$row["edo"];
		  }
		  array_push($estados, array("nombre" => utf8_encode($edo)));  
		}
	}
	if($num_res==1&&$edo=="NA")
	{
		$num_res=0;
	}
	$conexion->close();

	echo json_encode(array("status" => "correcto", "msj" => $sql, "estados" => $estados,"num_res"=>$num_res));
}
catch (Exception $e) {
	echo json_encode(array("status" => "error", "msj" => "Error en la base de datos: " . $e->getMessage()));
	$conexion->close();
}	 
?>
 




