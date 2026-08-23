<?php
    $page = $_POST['page'] ?? 0;  // Almacena el numero de pagina actual
    $limit = $_POST['rows'] ?? 0; // Almacena el numero de filas que se van a mostrar por pagina
    $sidx = $_POST['sidx'] ?? ''; // Almacena el indice por el cual se har· la ordenaci·n de los datos
    $sord = $_POST['sord'] ?? ''; // Almacena el modo de ordenaci·n
	$depto=$_POST['depto'] ?? '';
	$cargo=$_POST['cargo'] ?? '';
	$idus = $_POST['clvuser'] ?? '';
	$anio_b="";
	$fecha_ini="";
	//REVISAMOS SI SE RECIBIO UN CAMPO PARA FILTRAR
	if(isset($_POST['campo']))
		{
		$clave=$_POST['clave'];
		$campo=$_POST['campo'];
		if(!$sidx) $sidx =1;
		// Se crea la conexi·n a la base de datos
		//$conexion = new mysqli("localhost","root","SIIGsql#2021v2","siig");
		include("../../../common/conexion.php");
    	mysqli_set_charset($conexion,"utf8");

		// Se hace una consulta para saber cuantos registros se van a mostrar
		$result = $conexion->query("SELECT COUNT(*) AS count FROM h_pedidos where $campo='{$clave}'");
		if($campo=='marca')
		{
			$result = $conexion->query("SELECT COUNT(*) AS count FROM h_pedidos inner join marcas on marcas.no_cliente=h_pedidos.no_cliente and marcas.cve_marca=h_pedidos.marca where marcas.marca='{$clave}'");
		}
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
		$start = max(0, $limit*$page - $limit); // Bug fix: evita LIMIT negativo cuando no hay registros

		//Consulta que devuelve los registros de una sola pagina

		$consulta = "
		SELECT h_pedidos.id_row, h_pedidos.no_pedido, DATE(h_pedidos.fecha) fecha, h_pedidos.no_cliente, h_pedidos.marca cve,
		h_pedidos.serie, marcas.marca, h_pedidos.edo, h_pedidos.tipo, h_pedidos.fi, h_pedidos.ff, h_pedidos.cantidad, h_pedidos.status, h_pedidos.urgente,
		shp.tipo_pago, shp.comprobante, s.folio, h_pedidos.holograma
		FROM h_pedidos
		inner join marcas on marcas.no_cliente=h_pedidos.no_cliente and marcas.cve_marca=h_pedidos.marca
		INNER JOIN sh_detalle sh ON sh.id=h_pedidos.id_sh_d
		INNER JOIN sh_pedidos shp ON sh.id_solicitud = shp.id_solicitud
		INNER JOIN solicitudes s ON s.id=shp.id_solicitud
		 where h_pedidos.$campo='{$clave}' ORDER BY $sidx $sord limit $start , $limit";

		if($campo=='marca')
		{
			$consulta = "
			SELECT h_pedidos.id_row, h_pedidos.no_pedido, DATE(h_pedidos.fecha) fecha, h_pedidos.no_cliente, h_pedidos.marca cve, h_pedidos.serie,
			marcas.marca, h_pedidos.edo, h_pedidos.tipo, h_pedidos.fi, h_pedidos.ff, h_pedidos.cantidad, h_pedidos.status , h_pedidos.urgente,
			shp.tipo_pago, shp.comprobante, s.folio, h_pedidos.holograma
			FROM h_pedidos
			inner join marcas on marcas.no_cliente=h_pedidos.no_cliente and marcas.cve_marca=h_pedidos.marca
			INNER JOIN sh_detalle sh ON sh.id=h_pedidos.id_sh_d
			INNER JOIN sh_pedidos shp ON sh.id_solicitud = shp.id_solicitud
			INNER JOIN solicitudes s ON s.id=shp.id_solicitud
			where marcas.marca='{$clave}' ORDER BY $sidx $sord limit $start , $limit";
		}
		$result = $conexion->query($consulta);
	    //echo $consulta;
		// Se agregan los datos de la respuesta del servidor
		$respuesta = new stdClass(); // PHP 8: debe inicializarse antes de asignar propiedades
		$respuesta->page[0] = $page;
		$respuesta->total[0] = $total_pages;
		$respuesta->records[0] = $count;
		$i=0;
		$old_value="";
		$new_value="";
		$tipo_mez="";
		$acciones="";
		$status="";
		if($result) {
			while( $fila = $result->fetch_assoc() ) {

				$pos1 = strstr($fila["tipo_pago"], "TRANSFERENCIA");
				$pos2 = strstr($fila["tipo_pago"], "CHEQUE");
				$pos3 = strstr($fila["tipo_pago"], "EFECTIVO");
				$pos4 = strstr($fila["tipo_pago"], "DEPOSIT (IN");
				$pos5 = strstr($fila["tipo_pago"], "DEPOSITO");
				if($pos1 != "")
					$tp = "TF";
				elseif($pos2 != "")
					$tp = "CH";
				elseif($pos3 != "" || $pos4 != "")
					$tp = "EF";
				else
					$tp = "OT";
				if($fila["comprobante"] != "")
					$comp = '<p title="'.$fila["tipo_pago"].'" ><a href="../../clientes/hologramas/php/files/'.$fila["comprobante"].'?'.uniqid().'" target="_blank" > '.$tp.' </a><p>';
				else
					$comp = '<p title="'.$fila["tipo_pago"].'" >'.$tp.'<p>';
				$fpago = ($cargo == 7 || $cargo == 12 || $cargo == 13 || $cargo == 20 || $idus == 1)?$comp:'';

				$new_value=$fila["no_pedido"];
				$acciones="";
				$status="";
				if($new_value!=$old_value)
				{
					if($fila['status']==0&&$cargo==14)
					{
						$acciones="<button type='button' name='btnReenviar' id='btnReenviar' class='btn btn-success btn-md' style='margin-top:0;' onClick='re_enviar($new_value);'>
	                                        <span class='glyphicon glyphicon-upload'></span>
	                                       </button>";
					}
				}
				else
				{
					$acciones="";
				}
				switch($fila["tipo"])
				{
					case 0:
					{
						$tipo_mez="N/A";
						break;
					}
					case 1:
					{
						$tipo_mez="MEZCAL";
						break;
					}
					case 2:
					{
						$tipo_mez="ARTESANAL";
						break;
					}
					case 3:
					{
						$tipo_mez="ANCESTRAL";
						break;
					}

				}
				switch($fila["status"])
				{
					case 0:
					{
						$status="SIN SOLICITAR";
						break;
					}
					case 1:
					{
						$status="SOLICITADO";
						break;
					}
					case 2:
					{
						$status="RECIBIDO";
						break;
					}
					case 3:
					{
						$status="PROCESANDO";
						break;
					}
					case 4:
					{
						$status="IMPRESO";
						break;
					}
					case 5:
					{
						$status="ENTREGADO";
						$id_fila=$fila["id_row"];
						if($cargo==12 || $cargo==13 || $cargo==14 || $idus == 1)
						{
						  $acciones="<button type='button' name='btnIngresar' id='btnIngresar' class='btn btn-primary btn-md class-botones-ingresar' style='margin-top:0;' onClick='ingresarPedido($id_fila);'><i class='fa fa-lg fa-sign-in' aria-hidden='true'></i></button>";
						}
						break;
					}
					case 6:
					{
						$status="EN INVENTARIO";
						break;
					}
				}
				if($fila['urgente']==1)
				{
					$prioridad="URGENTE";
				}
				else
				{
					$prioridad="NORMAL";
				}
				$old_value=$new_value;
				$f_ini=$fila["no_cliente"].$fila["cve"].str_pad($fila["fi"],7,'0',STR_PAD_LEFT).$fila["serie"];
				$f_fin=$fila["no_cliente"].$fila["cve"].str_pad($fila["ff"],7,'0',STR_PAD_LEFT).$fila["serie"];
				$holograma = ($fila["holograma"] == '1')?"NUEVO V1":(($fila["holograma"] == '2')?"NUEVO V2":"GENÉRICO");
				$respuesta->rows[$i]["id"]=$fila["id_row"];
				$respuesta->rows[$i]["cell"]=array($fila["no_pedido"], $fila["folio"],$fila["fecha"],$fila["no_cliente"],$fila["marca"],$fila["edo"],$tipo_mez,$f_ini,$f_fin,$fila["cantidad"], $holograma,$prioridad,$status,$acciones, $fpago);
				$i++;
			}
		}
		// La respuesta se regresa como json
		echo  json_encode($respuesta);
	}
	else
	{
		if(!$sidx) $sidx =1;
		// Se crea la conexi·n a la base de datos
		//$conexion = new mysqli("localhost","root","SIIGsql#2021v2","siig");
		include("../../../common/conexion.php");
    	mysqli_set_charset($conexion,"utf8");
		// Se hace una consulta para saber cuantos registros se van a mostrar
		$result = $conexion->query("SELECT COUNT(*) AS count FROM h_pedidos");
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
		$start = max(0, $limit*$page - $limit); // Bug fix: evita LIMIT negativo cuando no hay registros
		//
		$consultanr = "SELECT h_pedidos.id_row, h_pedidos.no_pedido, DATE(h_pedidos.fecha) fecha, h_pedidos.no_cliente, h_pedidos.marca cve ,
		h_pedidos.serie, marcas.marca, h_pedidos.edo, h_pedidos.tipo, h_pedidos.fi, h_pedidos.ff, h_pedidos.cantidad, h_pedidos.status, h_pedidos.urgente,
		shp.tipo_pago, shp.comprobante, s.folio, h_pedidos.holograma
		FROM h_pedidos
		inner join marcas on marcas.no_cliente=h_pedidos.no_cliente and marcas.cve_marca=h_pedidos.marca
		INNER JOIN sh_detalle sh ON sh.id=h_pedidos.id_sh_d
		INNER JOIN sh_pedidos shp ON sh.id_solicitud = shp.id_solicitud
		INNER JOIN solicitudes s ON s.id = shp.id_solicitud";
		$resultnr = $conexion->query($consultanr);
		//Consulta que devuelve los registros de una sola pagina
		$consulta = "SELECT h_pedidos.id_row, h_pedidos.no_pedido, DATE(h_pedidos.fecha) fecha, h_pedidos.no_cliente, h_pedidos.marca cve ,
		h_pedidos.serie, marcas.marca, h_pedidos.edo, h_pedidos.tipo, h_pedidos.fi, h_pedidos.ff, h_pedidos.cantidad, h_pedidos.status, h_pedidos.urgente,
		shp.tipo_pago, shp.comprobante, s.folio, h_pedidos.holograma
		FROM h_pedidos
		inner join marcas on marcas.no_cliente=h_pedidos.no_cliente and marcas.cve_marca=h_pedidos.marca
		INNER JOIN sh_detalle sh ON sh.id=h_pedidos.id_sh_d
		INNER JOIN sh_pedidos shp ON sh.id_solicitud = shp.id_solicitud
		INNER JOIN solicitudes s ON s.id = shp.id_solicitud
		ORDER BY $sidx $sord limit $start , $limit";
		$result = $conexion->query($consulta);
		// Se agregan los datos de la respuesta del servidor
		$respuesta = new stdClass(); // PHP 8: debe inicializarse antes de asignar propiedades
		$respuesta->page[0] = $page;
		$respuesta->total[0] = $total_pages;
		$respuesta->records[0] = $count;
		$i=0;
		$old_value="";
		$new_value="";
		$tipo_mez="";
		$acciones="";
		$status="";
		//
		$row_cnt = $resultnr->num_rows;
		if($row_cnt > 0) {
			while( $fila = $result->fetch_assoc() ) {

				$new_value=$fila["no_pedido"];
				$acciones="";
				$status="";
				if($new_value!=$old_value)
				{
					if($fila['status']==0&&$cargo==14)
					{
						$acciones="<button type='button' name='btnReenviar' id='btnReenviar' class='btn btn-success btn-md' style='margin-top:0;' onClick='re_enviar($new_value);'>
										<span class='glyphicon glyphicon-upload'></span>
									   </button>";
					}
				}
				else
				{
					$acciones="";
				}
				switch($fila["tipo"])
				{
					case 0:
					{
						$tipo_mez="N/S";
						break;
					}
					case 1:
					{
						$tipo_mez="MEZCAL";
						break;
					}
					case 2:
					{
						$tipo_mez="ARTESANAL";
						break;
					}
					case 3:
					{
						$tipo_mez="ANCESTRAL";
						break;
					}

				}
				switch($fila["status"])
				{
					case 0:
					{
						$status="SIN SOLICITAR";
						break;
					}
					case 1:
					{
						$status="SOLICITADO";
						break;
					}
					case 2:
					{
						$status="RECIBIDO";
						break;
					}
					case 3:
					{
						$status="PROCESANDO";
						break;
					}
					case 4:
					{
						$status="IMPRESO";
						break;
					}
					case 5:
					{
						$status="ENTREGADO";
						$id_fila=$fila["id_row"];
						if($cargo==12 || $cargo==13 || $cargo==14 || $idus == 1)
						{
						  $acciones="<button type='button' name='btnIngresar' id='btnIngresar' class='btn btn-primary btn-md class-botones-ingresar' style='margin-top:0;' onClick='ingresarPedido($id_fila);'><i class='fa fa-lg fa-sign-in' aria-hidden='true'></i></button>";
						}
						break;
					}
					case 6:
					{
						$status="EN INVENTARIO";
						break;
					}
				}
				if($fila['urgente']==1)
				{
					$prioridad="URGENTE";
				}
				else
				{
					$prioridad="NORMAL";
				}
				$old_value=$new_value;
				$f_ini=$fila["no_cliente"].$fila["cve"].str_pad($fila["fi"],7,'0',STR_PAD_LEFT).$fila["serie"];
				$f_fin=$fila["no_cliente"].$fila["cve"].str_pad($fila["ff"],7,'0',STR_PAD_LEFT).$fila["serie"];

				$pos1 = strstr($fila["tipo_pago"], "TRANSFERENCIA");
				$pos2 = strstr($fila["tipo_pago"], "CHEQUE");
				$pos3 = strstr($fila["tipo_pago"], "EFECTIVO");
				$pos4 = strstr($fila["tipo_pago"], "DEPOSIT (IN");
				$pos5 = strstr($fila["tipo_pago"], "DEPOSITO");
				if($pos1 != "")
					$tp = "TF";
				elseif($pos2 != "")
					$tp = "CH";
				elseif($pos3 != "" || $pos4 != "")
					$tp = "EF";
				else
					$tp = "OT";
				if($fila["comprobante"] != "")
					$comp = '<p title="'.$fila["tipo_pago"].'" ><a href="../../clientes/hologramas/php/files/'.$fila["comprobante"].'?'.uniqid().'" target="_blank" > '.$tp.' </a><p>';
				else
					$comp = '<p title="'.$fila["tipo_pago"].'" >'.$tp.'<p>';
				$fpago = ($cargo == 7 || $cargo == 12 || $cargo == 13 || $cargo == 20 || $idus == 1)?$comp:'';

				$holograma = ($fila["holograma"] == '1')?"NUEVO V1":(($fila["holograma"] == '2')?"NUEVO V2":"GENÉRICO");
				$respuesta->rows[$i]["id"]=$fila["id_row"];
				$respuesta->rows[$i]["cell"]=array($fila["no_pedido"], $fila["folio"],$fila["fecha"],$fila["no_cliente"],$fila["marca"],$fila["edo"],$tipo_mez,$f_ini,$f_fin,$fila["cantidad"],$holograma,$prioridad,$status,$acciones,$fpago);
				$i++;
			}
		}
		// La respuesta se regresa como json
		echo  json_encode($respuesta);
	}
?>