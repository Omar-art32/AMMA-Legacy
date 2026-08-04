<?php
	  $marcas = array();
	  try {
          $no_cliente=$_POST["cliente"];
		  $sql = "SELECT cve_marca, marca FROM marcas WHERE SUBSTR(no_cliente, 1, 4) =$no_cliente;";
		  include("../../common/conexion.php");
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
				array_push($marcas, array("cve_marca" => $row["cve_marca"], "marca" => utf8_encode($row["marca"])));  
			  }
		  }
		  $conexion->close();

		  echo json_encode(array("status" => "correcto", "msj" => $sql, "marcas" => $marcas));
	  }
	  catch (Exception $e) {
		  echo json_encode(array("status" => "error", "msj" => "Error en la base de datos: " . $e->getMessage()));
		  $conexion->close();
	  }
?>