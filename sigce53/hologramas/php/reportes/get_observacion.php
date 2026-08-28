<?php
      include(__DIR__ . "/../../../common/conexion.php");


	  $msj="";
	  
	  try {
          $id=$_POST["id"];


		  $sql = "SELECT m1,fol_m1,obs_ent,DATE(fecha_entr),usr FROM h_salidas WHERE id_salidas= ?";
		  $ps=$conexion->prepare($sql);
		  $ps->bind_param('s',$id);
		  if(!$ps->execute())throw new Exception('No se pudo encontrar las observaciones');	
		  $ps->store_result();
		  $ps->bind_result($mermas,$folio_mermas,$observaciones,$fecha_captura,$usuario);		  		 
		  while($ps->fetch())
		  {
			  	
			  	$msj.='<p><b> <span class="glyphicon glyphicon-asterisk"> </span> Mermas  </b><p>';
			  	$msj.='<p>Cantidad: <b>'.$mermas.'</b><p>';
				$msj.='<p>Folios : <b>'.$folio_mermas.'</b><p>';
				

				$msj.='<p><b> <span class="glyphicon glyphicon-asterisk"> </span> Entregó </b><p>';
				$msj.='<p>Usuario: <b>'.$usuario.'</b><p>';
				$msj.='<p>Fecha : <b>'.$fecha_captura.'</b><p>';
				

				$msj.='<p><b> <span class="glyphicon glyphicon-asterisk"> </span> Observaciones  </b><p>';
				$msj.='<p><b>'.mb_convert_encoding($observaciones, 'UTF-8', 'ISO-8859-1').'</b><p>';
				

			  
			  
		  }
		  $ps->close();
		  


		  $conexion->close();
		  echo json_encode(["status" =>"OK", "msj"=>$msj]);
	  }
	  catch (Exception $e) {
		  echo json_encode(["status" => "error", "msj" => "Error en la base de datos: " . $e->getMessage()]);
		  $conexion->close();
	  }
?>