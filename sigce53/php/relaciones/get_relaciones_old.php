<?php
      include("../../common/conexion.php");
	  $cad_prod="";
	  $cad_env="";
	  $link="";
	  
	  try {
          $no_cliente=$_POST["cliente"];
		  $tipo_c=$_POST["tipoConsulta"];
		  $sql_p = "SELECT cr.id,cr.cte_prov, c.nombre from clientes_relaciones cr INNER JOIN clientes c ON c.no_cliente=cr.cte_prov WHERE cr.cte_rec=? AND cr.tipo_rel='P'";
		  $ps_p=$conexion->prepare($sql_p);
		  $ps_p->bind_param('s',$no_cliente);
		  if(!$ps_p->execute())throw new Exception('No se pudieron consultar los productores autorizados');	
		  $ps_p->store_result();
          $nr=$ps_p->num_rows;
		  $ps_p->bind_result($id,$prov,$nom_prov);		  		 
		  while($ps_p->fetch())
		  {
			  if($tipo_c=='E'){
				  $link='<td><button type="button"  name="btnEliminarRelacion" id="btnEliminarRelacion" class="btn btn-sm btn-danger" onClick="confirm_elim('.$id.')"><i class="fa fa-minus-circle"></i></button></td>';
			  }
			  $cad_prod.="<tr id='rowRel".$id."'><td>".$prov."</td><td>".utf8_encode($nom_prov)."</td>".$link."</tr>";
			  //array_push($productores, array("cve_marca" => $row["cve_marca"], "marca" => utf8_encode($row["marca"])));  
		  }
		  $ps_p->close();
		  
		  $sql_e = "SELECT cr.id,cr.cte_prov, c.nombre,cr.marca cve,m.marca from clientes_relaciones cr INNER JOIN clientes c ON c.no_cliente=cr.cte_prov INNER JOIN marcas m ON m.no_cliente=cr.cte_rec AND m.cve_marca=cr.marca WHERE cr.cte_rec=? AND cr.tipo_rel='E'";
		  $ps_e=$conexion->prepare($sql_e);
		  $ps_e->bind_param('s',$no_cliente);
		  if(!$ps_e->execute())throw new Exception('No se pudieron consultar los proveedores autorizados');	
		  $ps_e->store_result();		 
          $nr_e=$ps_e->num_rows;		  
		  $ps_e->bind_result($id_e,$prov_e,$nom_prov_e,$cve,$n_marca);		  		 
		  while($ps_e->fetch())
		  {
			  if($tipo_c=='E'){
				  $link='<td><button type="button"  name="btnEliminarRelacion" id="btnEliminarRelacion" class="btn btn-sm btn-danger" onClick="confirm_elim('.$id_e.')"><i class="fa fa-minus-circle"></i></button></td>';
			  }
			  $cad_env.="<tr id='rowRel".$id_e."'><td>".$prov_e."</td><td>".utf8_encode($nom_prov_e)."</td><td>".$cve.' - '.utf8_encode($n_marca)."</td>".$link."</tr>";
			  //array_push($productores, array("cve_marca" => $row["cve_marca"], "marca" => utf8_encode($row["marca"])));  
		  }	
		   
		  $ps_e->close();			  		  
		  $conexion->close();
		  echo json_encode(array("status" =>"OK", "n_prod"=>$nr, "prod" => $cad_prod, "n_env"=>$nr_e, "env"=> $cad_env));
	  }
	  catch (Exception $e) {
		  echo json_encode(array("status" => "error", "msj" => "Error en la base de datos: " . $e->getMessage()));
		  $conexion->close();
	  }
?>