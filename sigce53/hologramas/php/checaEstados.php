<?php
// Silenciar avisos/deprecated para asegurar salida JSON pura
	error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
	ini_set('display_errors', '0');
	header('Content-Type: application/json; charset=utf-8');
try {
	
	$estados = [];
	$edo="";
	include(__DIR__ . "/../../common/conexion.php");

	$no_cliente = $_POST["cliente"] ?? '';
    $marca      = $_POST["marca"] ?? '';
    
    // Limpieza básica de parámetros
    $no_cliente = $conexion->real_escape_string($no_cliente);
    $marca      = $conexion->real_escape_string($marca);
	
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
		  // Sustitución de utf8_encode por mb_convert_encoding
          $edo_utf8 = mb_convert_encoding($edo, 'UTF-8', 'ISO-8859-1');
		  $estados[] = ["nombre" => $edo_utf8];
		}
	}
	if($num_res==1&&$edo=="NA")
	{
		$num_res=0;
	}
	$conexion->close();

	echo json_encode(["status" => "correcto", "msj" => $sql, "estados" => $estados,"num_res"=>$num_res]);
}
catch (Exception $e) {
	echo json_encode(["status" => "error", "msj" => "Error en la base de datos: " . $e->getMessage()]);
	$conexion->close();
}	 
?>



