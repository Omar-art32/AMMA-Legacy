<?php
    $page = $_POST['page'];  // Almacena el numero de pagina actual
    $limit = $_POST['rows']; // Almacena el numero de filas que se van a mostrar por pagina
    $sidx = $_POST['sidx'];  // Almacena el indice por el cual se har� la ordenaci�n de los datos
    $sord = $_POST['sord'];  // Almacena el modo de ordenaci�n

	$tipo=$_POST['tipo'];
	$cliente = ($_POST['cliente'] == '0') ? "": $_POST['cliente'];

	//$cliente=substr($cliente,0,4);

	$orden = $_POST['orden'];



	$marca="";
	$tipo_m="";
	$fecha1="";
	$fecha2="";
    $estado="";
    $categoria="";


	if(isset($_POST['marca']))
	{
	  $marca=$_POST['marca'];
	}
	if(isset($_POST['tipo_m']))
	{
	  $tipo_m=$_POST['tipo_m'];
	}
	if(isset($_POST['fecha1']))
	{
	  $fecha1=$_POST['fecha1'];
	}
	if(isset($_POST['fecha2']))
	{
	  $fecha2=$_POST['fecha2'];
	}
	if(isset($_POST['estado']))
	{
	  $estado=$_POST['estado'];
	}
	if(isset($_POST['categoria']))
	{
	  $categoria=$_POST['categoria'];
	}


    $consulta="select h_salidas.id_salidas, h_salidas.id_recibo, h_salidas.anio_rcbo, h_salidas.no_cliente, if(h_salidas.marca='0','',h_salidas.marca) cve, if(marcas.marca is null,'',marcas.marca) marca, 
	h_salidas.serie, h_salidas.edo, h_salidas.tipo, if(h_salidas.solicitud='','S/N',h_salidas.solicitud) solicitud, h_salidas.fecha_entr, h_salidas.destino, 
	h_salidas.fi1, h_salidas.ff1, h_salidas.se1 
	from h_salidas 
	left join marcas on marcas.no_cliente=h_salidas.no_cliente and marcas.cve_marca=h_salidas.marca where";
	$sql_cont="SELECT COUNT(*) AS count FROM  h_salidas where";

	 if(isset($_POST['cliente']))
	{

		switch($tipo)
		{
			case 1:
                $consulta .= " h_salidas.no_cliente like '%$cliente%'";
                $sql_cont.=" h_salidas.no_cliente like '%$cliente%'";
                break;
			case 2:
                $consulta .= " h_salidas.no_cliente like '%$cliente%' and h_salidas.serie=''";
                $sql_cont.=" h_salidas.no_cliente like '%$cliente%' and h_salidas.serie=''";
                break;
			case 3:
                $consulta .= " h_salidas.no_cliente like '%$cliente%' and h_salidas.serie!=''";
                $sql_cont.="  h_salidas.no_cliente like '%$cliente%' and h_salidas.serie!=''";
                break;
			case 4:
                switch($tipo_m)
				{
					case 'T':
                        $consulta .= " h_salidas.no_cliente like '%$cliente%' and  h_salidas.marca='{$marca}'";
                        $sql_cont.="  h_salidas.no_cliente like '%$cliente%' and  h_salidas.marca='{$marca}'";
                        break;
					case 'G':
                        $consulta .= " h_salidas.no_cliente like '%$cliente%' and  h_salidas.marca='{$marca}' and h_salidas.serie=''";
                        $sql_cont.="  h_salidas.no_cliente like '%$cliente%' and  h_salidas.marca='{$marca}' and h_salidas.serie=''";
                        break;
					case 'P':
                        $consulta .= " h_salidas.no_cliente like '%$cliente%' and  h_salidas.marca='{$marca}' and h_salidas.serie!=''";
                        $sql_cont.="  h_salidas.no_cliente like '%$cliente%' and  h_salidas.marca='{$marca}' and h_salidas.serie!=''";
                        break;
				}
                break;
		}

		if($estado != "T" && $estado != "" ) {
			$consulta.=" and h_salidas.edo='$estado'";
			$sql_cont.=" and h_salidas.edo='$estado'";
		}




		if($categoria!="" && $categoria!="T" )
		{
			$consulta.=" and h_salidas.tipo=$categoria";
			$sql_cont.=" and h_salidas.tipo=$categoria";
		}


		$order = "";
		if($orden != "") 
			$order = "  ORDER BY h_salidas.$orden,h_salidas.fecha_entr,h_salidas.fi1 asc";
		else 
			$order = "  ORDER BY h_salidas.marca,h_salidas.fecha_entr,h_salidas.fi1 asc";


		if(trim($fecha1) !== ''&&trim($fecha2) !== '') {
			$consulta.=" and fecha_entr between '$fecha1' and '$fecha2' $order";
			$sql_cont.=" and fecha_entr between '$fecha1' and '$fecha2' $order";
		} else if(trim($fecha1) !== '') {
			$consulta.=" and fecha_entr='$fecha1' $order";
			$sql_cont.=" and fecha_entr='$fecha1' $order";
		} else {
			$consulta.= $order;
			$sql_cont.= $order;
		}



		if(!$sidx) $sidx =1;
		// Se crea la conexi�n a la base de datos
		//$conexion = new mysqli("localhost","root","MyCRMSql15","siig");
		include(__DIR__ . '/../../../common/conexion.php');

		// Se hace una consulta para saber cuantos registros se van a mostrar

		$result = $conexion->query($sql_cont);

		// Se obtiene el resultado de la consulta
		$fila = $result->fetch_array();
		$count = $fila['count'];

		//En base al numero de registros se obtiene el numero de paginas
		if( $count >0 ) {
		$total_pages = ceil($count/$limit);
		} else {
		$total_pages = 0;
		}
		if ($page > $total_pages)
			$page=$total_pages;

		//Almacena numero de registro donde se va a empezar a recuperar los registros para la pagina
		$start = $limit*$page - $limit;
		if($total_pages==0)
		{
			$start=0;
		}
		//Consulta que devuelve los registros de una sola pagina

		//$consulta = "SELECT id_inf_lab, id_cliente, nombre, laboratorio, id_analisis, tipo FROM uv_inf_lab where $campo='{$clave}' ORDER BY $sidx $sord";

	   $consulta.=" limit $start , $limit";
		$result = $conexion->query($consulta);

		// Se agregan los datos de la respuesta del servidor
		$respuesta->page[0] = $page;
		$respuesta->total[0] = $total_pages;
		$respuesta->records[0] = $count;
		$i=0;
		while( $fila = $result->fetch_assoc() ) {
			$recibo='AR'.str_pad($fila["id_recibo"],4,'0',STR_PAD_LEFT).'/'.$fila["anio_rcbo"];
			$marca=mb_convert_encoding($fila["marca"], 'UTF-8', 'ISO-8859-1');
			if($marca === "")
			{
				$marca="N/A";
			}
			if($fila["serie"]!='')
			{
			$fol_ini=$fila["no_cliente"].$fila["cve"].str_pad($fila["fi1"], 7,'0',STR_PAD_LEFT).$fila["serie"];
			$fol_fin=$fila["no_cliente"].$fila["cve"].str_pad($fila["ff1"], 7,'0',STR_PAD_LEFT).$fila["serie"];
			}
			else
			{
		    $fol_ini=$fila["fi1"];
			$fol_fin=$fila["ff1"];
			}
			//$no_cliente=$fila["no_cliente"];
			$serie=$fila["serie"];
			$edo=$fila["edo"];

			if($edo==""){
				$edo="N/A";
			}


			if($serie=='')
			{
				$serie ="N/A";
			}
			$cantidad=$fila["se1"];
			$solicitud=$fila["solicitud"];
			if($solicitud=='')
			{
				$solicitud ="S/N";
			}
			$f_entrega=$fila["fecha_entr"];

			switch($fila["tipo"])
			{
				case 0:
                    $tipo_mez="N/A";
                    break;
				case 1:
                    $tipo_mez="MEZCAL";
                    break;
				case 2:
                    $tipo_mez="ARTESANAL";
                    break;
				case 3:
                    $tipo_mez="ANCESTRAL";
                    break;

			}
			$respuesta->rows[$i]["id"]=$fila["id_salidas"];
			$respuesta->rows[$i]["cell"]=[$recibo,$marca,$serie,$edo,$tipo_mez,mb_convert_encoding($solicitud, 'UTF-8', 'ISO-8859-1'),$f_entrega,$fol_ini,$fol_fin,$cantidad];
			$i++;
		}
    //$respuesta->rows[0]["sql"] = $consulta;
	$conexion->close();
		// La respuesta se regresa como json
		echo  json_encode($respuesta);
	}
?>
