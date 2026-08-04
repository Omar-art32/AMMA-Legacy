<?php
      include("../../common/conexion.php");


	  $msj="";
	  
	  try {
          $id=$_POST["id"];


		  $sql = "SELECT cr.cte_prov,cr.obs,cr.tipo_rel, cr.tipo_vig,cr.fecha_ini,cr.fecha_fin from clientes_relaciones cr WHERE cr.id=?";
		  $ps=$conexion->prepare($sql);
		  $ps->bind_param('s',$id);
		  if(!$ps->execute())throw new Exception('No se pudieron consultar los productores autorizados');	
		  $ps->store_result();
		  $ps->bind_result($cliente_prov,$obs,$tipo_rel,$tipo_vig,$fecha_ini,$fecha_fin);		  		 
		  while($ps->fetch())
		  {
			  
			  



				if($tipo_vig==1){
					$msj.='<p>Tipo vigencia: <b>Finita</b><p>';
					$msj.='<p>Fecha Inicio: <b>'.$fecha_ini.'</b><p>';
					$msj.='<p>Fecha Fin: <b>'.$fecha_fin.'</b><p>';
				}

				else if($tipo_vig==2){
					 $msj.='<p>Tipo vigencia: <b>Indefinida</b><p>';
					 $msj.='<p>Fecha Inicio: <b>'.$fecha_ini.'</b><p>';
				}
				
				$msj.='<p>Observación: <b>'.utf8_encode($obs).'</b><p>';
			  
			  
		  }
		  $ps->close();
		  


		  $conexion->close();
		  echo json_encode(array("status" =>"OK", "msj"=>$msj, "cliente_prov"=>$cliente_prov, "obs"=>utf8_encode($obs), "tipo_vig"=>$tipo_vig, "fecha_ini"=>$fecha_ini, "fecha_fin"=>$fecha_fin));
	  }
	  catch (Exception $e) {
		  echo json_encode(array("status" => "error", "msj" => "Error en la base de datos: " . $e->getMessage()));
		  $conexion->close();
	  }
?>