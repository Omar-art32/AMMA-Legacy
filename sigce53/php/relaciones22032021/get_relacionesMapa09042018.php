<?php
      include("../../common/conexion.php");
      $conexion->set_charset("utf8");

	  $cad_prod="";
	  $cad_env="";
	  $link="";
    $arrProductores = array();
    $arrProveedores = array();
    $arrEnvasadores = array();
    $arrComercializadores = array();
    $arrCliente = array();

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
        array_push($arrProductores, array("asociado" => $prov, "empresa" => utf8_encode($nom_prov)));
		  }
		  $ps_p->close();

      // ***************SECCION PARA OBTENER A LOS PROVEEDORES
      $sql_v = "SELECT cr.id,cr.cte_prov, c.nombre from clientes_relaciones cr INNER JOIN clientes c ON c.no_cliente=cr.cte_prov WHERE cr.cte_rec=? AND cr.tipo_rel='V'";
      $ps_v=$conexion->prepare($sql_v);
      $ps_v->bind_param('s',$no_cliente);
      if(!$ps_v->execute())throw new Exception('No se pudieron consultar los proveedores autorizados'); 
      $ps_v->store_result();
      $np=$ps_v->num_rows;
      $ps_v->bind_result($id_v,$prov_v,$nom_prov_v);           
      while($ps_v->fetch())
      {
        array_push($arrProveedores, array("asociado" => $prov_v, "empresa" => utf8_encode($nom_prov_v)));
      }
      $ps_v->close();



      $sql_cli = "SELECT magueyero,envasador,comercializador,nombre from clientes  WHERE no_cliente=?";
		  $ps_cli=$conexion->prepare($sql_cli);
		  $ps_cli->bind_param('s',$no_cliente);
		  if(!$ps_cli->execute())throw new Exception('No se pudieron consultar los datos del asociado');
		  $ps_cli->store_result();
		  $ps_cli->bind_result($esProductor,$esEnvasador,$esComercializador,$nom_emp);
      $ps_cli->fetch();
		  $ps_cli->close();

      if ($esProductor == 1) {
        array_push($arrProductores,array("asociado" => $no_cliente, "empresa" => utf8_encode($nom_emp)));
      }


      $sql_marcas = "SELECT m.cve_marca,m.marca,c.nombre FROM marcas m INNER JOIN clientes c ON c.no_cliente = m.no_cliente WHERE m.no_cliente = ?;";
		  $ps_marcas=$conexion->prepare($sql_marcas);
		  $ps_marcas->bind_param('s',$no_cliente);
		  if(!$ps_marcas->execute())throw new Exception('No se pudieron consultar las marcas autorizadas');
		  $ps_marcas->store_result();
      $nr_m=$ps_marcas->num_rows;
		  $ps_marcas->bind_result($clave_marca,$Nommarca,$nombreEmpresa);
      $count =0;
		  while($ps_marcas->fetch())
		  {
        $arrEnvasadores[$count] =  array();
        $arrComercializadores[$count] =  array();

        // ***************SECCION PARA OBTENER A LOS ENVASADORES
       $sql_e = "SELECT cr.id,cr.cte_prov, c.nombre,cr.marca cve,m.marca from clientes_relaciones cr INNER JOIN clientes c ON c.no_cliente=cr.cte_prov INNER JOIN marcas m ON m.no_cliente=cr.cte_rec AND m.cve_marca=cr.marca WHERE cr.cte_rec=? AND cr.marca = ? AND cr.tipo_rel='E'";
       $ps_e=$conexion->prepare($sql_e);
       $ps_e->bind_param('ss',$no_cliente,$clave_marca);
       if(!$ps_e->execute())throw new Exception('No se pudieron consultar los proveedores autorizados');
       $ps_e->store_result();
       $nr_e=$ps_e->num_rows;
       $ps_e->bind_result($id_e,$prov_e,$nom_prov_e,$cve,$n_marca);
       $env_count = 0;
       while($ps_e->fetch())
       {
         if ($env_count == 0 && $esEnvasador == 1) {
            array_push($arrEnvasadores[$count],array("asociado" => $no_cliente, "empresa" => utf8_encode($nom_emp),"marca"=>utf8_encode($n_marca)));
         }
          array_push($arrEnvasadores[$count],array("asociado" => $prov_e, "empresa" => utf8_encode($nom_prov_e),"marca"=>utf8_encode($n_marca)));
          $env_count++;
       }
       $ps_e->close();


       // ***************SECCION PARA OBTENER A LOS COMERCIALIZADORES
       $sql_comer = "SELECT c.no_cliente,m.cve_marca,m.marca,c.nombre FROM marcas m INNER JOIN clientes c ON c.no_cliente = m.no_cliente WHERE m.marca = ?";
       $ps_coner=$conexion->prepare($sql_comer);
       $ps_coner->bind_param('s',$Nommarca);
       if(!$ps_coner->execute())throw new Exception('No se pudieron consultar los comercializadores autorizados');
       $ps_coner->store_result();
       $nr_comer=$ps_coner->num_rows;
       $ps_coner->bind_result($cliente_comer,$clv_marca,$marca_comer,$nom_empresa);
       while($ps_coner->fetch())
       {
          array_push($arrComercializadores[$count], array("asociado" => $cliente_comer, "empresa" => utf8_encode($nom_empresa),"marca"=>utf8_encode($marca_comer)));
       }
       $ps_coner->close();
      array_push($arrCliente, array("marca" => $marca_comer, "productores" => $arrProductores,"envasadores"=>$arrEnvasadores[$count],"comercializadores"=>$arrComercializadores[$count],"proveedores"=>$arrProveedores));
       $count ++;
		  }

		  $ps_marcas->close();

      if (count($arrCliente) == 0) {
        $arrCliente = [];
      }
		  $conexion->close();
      echo json_encode(array("status" =>"OK", "marcas"=>$arrCliente)/*"comer_obj"=>$arrComercializadores,"prod_obj"=>$arrProductores,"env_obj"=>$arrEnvasadores,"esProductor"=>($esProductor=='1')?true:false,"esEnvasador"=>($esEnvasador=='1')?true:false,"esComercializador"=>($esComercializador=='1')?true:false)*/);
	  }
	  catch (Exception $e) {
		  echo json_encode(array("status" => "error", "msj" => "Error en la base de datos: " . $e->getMessage()));
		  $conexion->close();
	  }
?>
