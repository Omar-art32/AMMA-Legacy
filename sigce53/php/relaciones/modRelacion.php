<?php
      include("../../common/conexion.php");


	  $msj="El registro se actualizo correctamente";
	  
	  try {
          
          $id=$_POST["id"];
          $obs=utf8_decode($_POST["obs"]);
          $tipo_vigencia=$_POST['tipo_vigencia'];
          $vigencia_ini=$_POST['vigencia_ini'];
          $vigencia_fin=$_POST['vigencia_fin'];


		  $sql = "UPDATE clientes_relaciones SET obs=?, tipo_vig=?, fecha_ini=?, fecha_fin= ? WHERE id=?";
		  $ps=$conexion->prepare($sql);
		  $ps->bind_param('sssss',$obs,$tipo_vigencia,$vigencia_ini,$vigencia_fin,$id);
		  if(!$ps->execute())throw new Exception('No se pudieron consultar los productores autorizados');			  		 
		  $ps->close();
		  
		  $conexion->close();
		  echo json_encode(array("status" =>"OK", "msj"=>$msj));
	  }
	  catch (Exception $e) {
		  echo json_encode(array("status" => "error", "msj" => "Error en la base de datos: " . $e->getMessage()));
		  $conexion->close();
	  }
?>