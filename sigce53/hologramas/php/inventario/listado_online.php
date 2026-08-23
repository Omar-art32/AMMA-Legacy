<?php
    $page = $_POST['page'] ?? 1;  // Almacena el numero de pagina actual
    $limit = $_POST['rows'] ?? 10; // Almacena el numero de filas que se van a mostrar por pagina
    $sidx = $_POST['sidx'] ?? ''; // Almacena el indice por el cual se har· la ordenaci·n de los datos
    $sord = $_POST['sord'] ?? ''; // Almacena el modo de ordenaci·n
	$depto=$_POST['depto'] ?? '';
	$cargo=$_POST['cargo'] ?? '';
	$idus = $_POST['clvuser'] ?? '';
	$anio_b="";
	$fecha_ini="";
	$nivel = $_POST['nivel'] ?? '';
	include("../../../common/conexion.php");
	include('../../../common/cfg_server.php');
  mysqli_set_charset($conexion,"utf8");
	//REVISAMOS SI SE RECIBIO UN CAMPO PARA FILTRAR
  $condiciones =  "";
	if(isset($_POST['campo'])) {
		$clave=$_POST['valor'] ?? '';
		$campo=$_POST['campo'] ?? '';
		if($campo == "todos") {
			$clave1 = $_POST['valor1'] ?? '';
			$clave2 = $_POST['valor2'] ?? '';
			$clave3 = $_POST['valor3'] ?? '';
			$clave4 = $_POST['valor4'] ?? '';
			if($clave1 != "")
				$condiciones .= " shp.id_solicitud = '".$clave1."' ";
			if($clave2 != "") {
				$condiciones .= ($condiciones != "")?" AND ": "";
				$condiciones .= "  marcas.marca = '".$clave2."' ";
			}
			if($clave3 > 0) {
				$condiciones .= ($condiciones != "")?" AND ": "";
				$condiciones .=($clave3 == 4)? "  sh_detalle.status IN (4,5,6) ": "  sh_detalle.status = '".$clave3."' ";
			}
			if($clave4 != "") {
				$condiciones .= ($condiciones != "")?" AND ": "";
				$condiciones .= "  shp.no_cliente = '".$clave4."' ";
			}
		} else {
			$condiciones="shp.$campo='{$clave}'";
			if($campo=='marca')
				$condiciones="marcas.$campo='{$clave}'";
	        if($campo=='estatus') {
	        	if($clave == 4)
	        		$condiciones="sh_detalle.status IN (4,5,6) ";
	        	else
	            	$condiciones="sh_detalle.status='{$clave}'";
	        }
	    }
		if(!$sidx) $sidx =1;
		// Se crea la conexi·n a la base de datos
	    // Se hace una consulta para saber cuantos registros se van a mostrar
		$sql_cont="SELECT count(*) as count
		FROM sh_pedidos shp
		INNER JOIN solicitudes ON solicitudes.id=shp.id_solicitud
		INNER JOIN sh_detalle ON sh_detalle.id_solicitud=shp.id_solicitud
		INNER JOIN marcas ON marcas.no_cliente=shp.no_cliente AND marcas.cve_marca=sh_detalle.marca
		WHERE $condiciones";
                //echo $sql_cont;
		//echo $sql_cont;
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
		$consulta = "SELECT solicitudes.id, 				solicitudes.folio, 		  solicitudes.anio, 			sh_detalle.id id_det, shp.id_solicitud,
							date(solicitudes.fecha) fecha,  shp.no_cliente,    sh_detalle.marca cve_marca, 	marcas.marca, 		  sh_detalle.tipo,
							sh_detalle.edo, 				sh_detalle.urgente,		  sh_detalle.cantidad, 			sh_detalle.importe,	  sh_detalle.status,
							shp.comprobante,			sh_detalle.observaciones, shp.tipo_pago, 				shp.comprobante, 	  time(solicitudes.fecha) hora,
							sh_detalle.pago_opcion pago_opcion, sh_detalle.pago_promo
		FROM sh_pedidos shp INNER JOIN solicitudes ON solicitudes.id=shp.id_solicitud
		INNER JOIN sh_detalle ON sh_detalle.id_solicitud=shp.id_solicitud
		INNER JOIN marcas ON marcas.no_cliente = shp.no_cliente AND marcas.cve_marca=sh_detalle.marca
		where $condiciones  ORDER BY shp.id_solicitud desc limit $start , $limit";
		//echo $consulta;
		$result = $conexion->query($consulta);
		// Se agregan los datos de la respuesta del servidor
		$respuesta = new stdClass(); // PHP 8: debe inicializarse antes de asignar propiedades
		$respuesta->page[0] = $page;
		$respuesta->total[0] = $total_pages;
		$respuesta->records[0] = $count;
		$respuesta->sql[0] = $consulta;
		$i=0;
		$link="";
		$status="";
		$tipo_mez="";
		$prioridad="";
		$importe="";
		while( $fila = $result->fetch_assoc() ) {

			$pos1 = strstr($fila["tipo_pago"], "TRANSFERENCIA");
			$pos2 = strstr($fila["tipo_pago"], "CHEQUE");
			$pos3 = strstr($fila["tipo_pago"], "EFECTIVO");
			$pos4 = strstr($fila["tipo_pago"], "DEPOSIT (IN");
			$pos5 = strstr($fila["tipo_pago"], "DEPOSITO");
			$tpe = 0;
			if($pos1 != "") {
				$tp = "TF"; $tpe = 2;
			} elseif($pos2 != "") {
				$tp = "CH"; $tpe = 3;
			} elseif($pos3 != "" || $pos4 != "") {
				$tp = "EF"; $tpe = 1;
			} else {
				$tp = "OT"; $tpe = 4;
			}
			if($fila["comprobante"] != "")
				$comp = '<p title="'.$fila["tipo_pago"].'" ><a href="../../clientes/hologramas/php/files/'.$fila["comprobante"].'?'.uniqid().'" target="_blank" > '.$tp.' </a><p>';
			else
				$comp = '<p title="'.$fila["tipo_pago"].'" >'.$tp.'<p>';
			$fpago = ($cargo == 7 || $cargo == 12 || $cargo == 13 || $cargo == 14 || $cargo == 20 || $idus == 1)?$comp:'';

			$marca=$fila["cve_marca"].' - '.$fila["marca"];
			$pago_opcion = "PAGO NORMAL";
			switch($fila["pago_opcion"]) {
				case 1: {
					$pago_opcion = "PAGO NORMAL";
					break;
				}
				case 2: {
					$pago_opcion = "PAQUETE EMPRENDEDOR";
					break;
				}
				case 3: {
					$pago_opcion = "CARGO A ESTADO DE CUENTA";
					break;
				}
				case 4: {
					$pago_opcion = "SEFADER";
					break;
				}
				case 5: {
					$pago_opcion = "AUTORIZADO POR UT";
					break;
				}
			}
			switch($fila["status"])
			{
				case 1:
				{
					$status="REVISIÓN";
					break;
				}
				case 2:
				{
					$status="AUTORIZADO";
					break;
				}
				case 3:
				{
					$status="EN LISTA";
					break;
				}
				case 4:
				{
					$status="SOLICITADO A PROVEEDOR";
					break;
				}
				case 5:
				{
					$status="SOLICITADO A PROVEEDOR";
					break;
				}
				case 6:
				{
					$status="SOLICITADO A PROVEEDOR";
					break;
				}
				case 7:
				{
					$status="CANCELADO";
					break;
				}
			}
			$status = ($fila["status"] != 7 && $fila["status"] != 1) ? ("$status<br><span style='font-size: 8px;color:blue;'>$pago_opcion</span>"): $status;
			//.
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
			if($fila['urgente']==1)
			{
				$prioridad="URGENTE";
			}
			else
			{
				$prioridad="NORMAL";
			}
			$link="";
			if( ($depto=="DA" || $idus == '1' || $idus == '4') || $nivel == 1)
			{
				if($fila["status"]==1) {
				  $link='<button type="button"  name="btn_asignar" id="btn_asignar" class="btn btn-sm btn-success" onClick=confirma_pago_online_otro('.$fila["id_det"].','.$fila["id"].','.$tpe.','.$fila["urgente"].')><i class="fa fa-lg fa-usd"></i></button>';
				  if($fila["comprobante"]!="")
				  {
					  $url="'http://".$svr_dir."/clientes/hologramas/php/files/".$fila["comprobante"]."?".uniqid()."'";
					  $link.='&nbsp;<button type="button"  name="btn_asignar" id="btn_asignar" class="btn btn-sm btn-danger" onClick="ver_comprobante('.$url.')" style="padding-left:7px !important; padding-right:7px !important;"><i class="fa fa-lg fa-file" style="color:#fff"></i></button>';
				  }
				  $link.='&nbsp;&nbsp;<button type="button" name="btn_cancelar" id="btn_cancelar" class="btn btn-sm btn-warning" onclick="cancelar_solicitud('.$fila["id_det"].','.$fila["id"].')"><i class="fa fa-lg fa-close"></i></button>';
				  /*if($idus == '1' ) {
					$link.='&nbsp;&nbsp;<button type="button"  name="btn_asignar" id="btn_asignar" class="btn btn-sm btn-success" onClick=confirma_pago_online_otro('.$fila["id_det"].','.$fila["id"].','.$tpe.')><i class="fa fa-btc" aria-hidden="true"></i></button>';
				  }*/
				} else {
					if($fila["comprobante"]!="") {
						$url="'http://".$svr_dir."/clientes/hologramas/php/files/".$fila["comprobante"]."?".uniqid()."'";
						$link='<button type="button"  name="btn_asignar" id="btn_asignar" class="btn btn-sm btn-danger" onClick="ver_comprobante('.$url.')" style="padding-left:7px !important; padding-right:7px !important;"><i class="fa fa-lg fa-file" style="color:#fff"></i></button>';
				  	}
					if ($fila["pago_opcion"] === "5") {
						$link .= '&nbsp;&nbsp;<button type="button"  name="btn_asignar" id="btn_asignar" class="btn btn-sm btn-success" onClick="modifica_pago_online_otro('.$fila["id_det"].','.$fila["id"].','.$tpe.')"><i class="fa fa-lg fa-usd"></i></button>';
						/*if($idus == '1' ) {
							$link.='&nbsp;&nbsp;<button type="button"  name="btn_asignar" id="btn_asignar" class="btn btn-sm btn-success" onClick=modifica_pago_online_otro('.$fila["id_det"].','.$fila["id"].','.$tpe.','.$fila["pago_opcion"].')><i class="fa fa-btc" aria-hidden="true"></i></button>';
						}*/
					}

					/*if($idus == 1) {
						if($fila["status"]==2)
							$link='<button type="button"  name="btn_asignar" id="btn_asignar" class="btn btn-sm btn-success" onClick="get_data_online_tmp('.$fila["id_det"].','.$fila["cantidad"].')"><i class="fa fa-lg fa-cart-plus"></i></button>&nbsp;<button type="button"  name="btnEditPO" id="btnEditPO" class="btn btn-sm btn-primary" onClick="getEditarPO('.$fila["id_det"].')"><i class="fa fa-lg fa-edit"></i></button>&nbsp;&nbsp;<button type="button" name="btn_cancelar" id="btn_cancelar" class="btn btn-sm btn-warning" onclick="cancelar_solicitud('.$fila["id_det"].','.$fila["id"].')"><i class="fa fa-lg fa-close"></i></button>';
					}*/
				}
			}
			else if($depto=="OC" || $idus == 1)
			{
				if($fila["status"]==2)
					$link='<button type="button"  name="btn_asignar" id="btn_asignar" class="btn btn-sm btn-success" onClick="get_data_online_tmp('.$fila["id_det"].','.$fila["cantidad"].')"><i class="fa fa-lg fa-cart-plus"></i></button>&nbsp;<button type="button"  name="btnEditPO" id="btnEditPO" class="btn btn-sm btn-primary" onClick="getEditarPO('.$fila["id_det"].')"><i class="fa fa-lg fa-edit"></i></button>&nbsp;&nbsp;<button type="button" name="btn_cancelar" id="btn_cancelar" class="btn btn-sm btn-warning" onclick="cancelar_solicitud('.$fila["id_det"].','.$fila["id"].')"><i class="fa fa-lg fa-close"></i></button>';
				/*else
				$link="";*/
			}

			if($fila["observaciones"]!=""){

				$link.='&nbsp;<button type="button" name="btn_info" id="btn_info" class="btn btn-sm btn-info" onclick="get_observacion_pedido('.$fila["id_det"].')"><i class="fa fa-lg fa-info"></i></button>';

			}

			$divide = floatval($fila["importe"])/intval($fila["cantidad"]);
			//$txtdivide = ($divide == "0.9" || $divide == "1.35" || $fila["id"] == 1425) ? "<br><span style='font-size: 8px;color:red;'>BUEN FIN</span>": "";
			$txtdivide = ($fila["pago_promo"] == "1") ? "<br><span style='font-size: 8px;color:red;'>BUEN FIN</span>": "";
			$importe="$ ".number_format($fila["importe"], 2, '.', ',') . $txtdivide;
			$respuesta->rows[$i]["id"]=$fila["id_det"];
			$folio = ($fila["id"] < 33159) ? $fila["folio"] . "/" .substr($fila["anio"], -2, 2) : $fila["folio"];
			$respuesta->rows[$i]["cell"]=array($folio, $fila["fecha"]."  ". $fila["hora"], $fila["no_cliente"], $marca, $tipo_mez, $fila["edo"], number_format($fila["cantidad"],0),$importe , $prioridad, $status, $link, $fpago, $pago_opcion, $fila["status"]);
			$respuesta->rows[$i]["opera"] = $divide .":". floatval($fila["importe"]).":".intval($fila["cantidad"]);
			$i++;
		}
		// La respuesta se regresa como json
		echo  json_encode($respuesta);

	} else {

		if(!$sidx) $sidx =1;
		// Se crea la conexi·n a la base de datos
		// Se hace una consulta para saber cuantos registros se van a mostrar
		$result = $conexion->query("SELECT count(*) as count FROM sh_pedidos INNER JOIN solicitudes ON solicitudes.id=sh_pedidos.id_solicitud INNER JOIN sh_detalle ON sh_detalle.id_solicitud=sh_pedidos.id_solicitud
			INNER JOIN marcas ON marcas.no_cliente=sh_pedidos.no_cliente AND marcas.cve_marca=sh_detalle.marca");
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

		/*SELECT solicitudes.id, 				solicitudes.folio, 		  solicitudes.anio, 			sh_detalle.id id_det, shp.id_solicitud,
							date(solicitudes.fecha) fecha,  shp.no_cliente,    sh_detalle.marca cve_marca, 	marcas.marca, 		  sh_detalle.tipo,
							sh_detalle.edo, 				sh_detalle.urgente,		  sh_detalle.cantidad, 			sh_detalle.importe,	  sh_detalle.status,
							shp.comprobante,			sh_detalle.observaciones, shp.tipo_pago, 				shp.comprobante, 	  time(solicitudes.fecha) hora,
							sh_detalle.pago_opcion pago_opcion
		FROM sh_pedidos shp
		INNER JOIN solicitudes ON solicitudes.id=sh_pedidos.id_solicitud
		INNER JOIN sh_detalle ON sh_detalle.id_solicitud=sh_pedidos.id_solicitud
		INNER JOIN marcas ON marcas.no_cliente = sh_pedidos.no_cliente AND marcas.cve_marca=sh_detalle.marca
		where $condiciones  ORDER BY sh_pedidos.id_solicitud desc limit $start , $limit*/

		//Consulta que devuelve los registros de una sola pagina
		$consulta = "SELECT solicitudes.id, 				solicitudes.folio, 		  solicitudes.anio, 			sh_detalle.id id_det, shp.id_solicitud,
							date(solicitudes.fecha) fecha,  shp.no_cliente,    sh_detalle.marca cve_marca, 	marcas.marca, 		  sh_detalle.tipo,
							sh_detalle.edo, 				sh_detalle.urgente,		  sh_detalle.cantidad, 			sh_detalle.importe,	  sh_detalle.status,
							shp.comprobante,			sh_detalle.observaciones, shp.tipo_pago, 				shp.comprobante, 	  time(solicitudes.fecha) hora,
							sh_detalle.pago_opcion pago_opcion, sh_detalle.pago_promo
						FROM sh_pedidos shp INNER JOIN solicitudes ON solicitudes.id=shp.id_solicitud
						INNER JOIN sh_detalle ON sh_detalle.id_solicitud=shp.id_solicitud
						INNER JOIN marcas ON marcas.no_cliente = shp.no_cliente AND marcas.cve_marca=sh_detalle.marca
                        ORDER BY shp.id_solicitud desc limit $start , $limit";

		//echo $consulta;
		$result = $conexion->query($consulta);
		// Se agregan los datos de la respuesta del servidor
		$respuesta = new stdClass(); // PHP 8: debe inicializarse antes de asignar propiedades
		$respuesta->page[0] = $page;
		$respuesta->total[0] = $total_pages;
		$respuesta->records[0] = $count;
		$i=0;
		$link="";
		$status="";
		$tipo_mez="";
		$prioridad="";
		$importe="";
		while( $fila = $result->fetch_assoc() ) {

			$pos1 = strstr($fila["tipo_pago"], "TRANSFERENCIA");
			$pos2 = strstr($fila["tipo_pago"], "CHEQUE");
			$pos3 = strstr($fila["tipo_pago"], "EFECTIVO");
			$pos4 = strstr($fila["tipo_pago"], "DEPOSIT (IN");
			$pos5 = strstr($fila["tipo_pago"], "DEPOSITO");
			$tpe = 0;
			if($pos1 != "") {
				$tp = "TF"; $tpe = 2;
			} elseif($pos2 != "") {
				$tp = "CH"; $tpe = 3;
			} elseif($pos3 != "" || $pos4 != "") {
				$tp = "EF"; $tpe = 1;
			}else {
				$tp = "OT"; $tpe = 4;
			}
			if($fila["comprobante"] != "")
				$comp = '<p title="'.$fila["tipo_pago"].'" ><a href="../../clientes/hologramas/php/files/'.$fila["comprobante"].'?'.uniqid().'" target="_blank" > '.$tp.' </a><p>';
			else
				$comp = '<p title="'.$fila["tipo_pago"].'" >'.$tp.'<p>';
			$fpago = ($cargo == 7 || $cargo == 12 || $cargo == 13 || $cargo == 14 || $cargo == 20 || $idus == 1)?$comp:'';

			$marca=$fila["cve_marca"].' - '.$fila["marca"];
			$pago_opcion = "PAGO NORMAL";
			switch($fila["pago_opcion"]) {
				case 1: {
					$pago_opcion = "PAGO NORMAL";
					break;
				}
				case 2: {
					$pago_opcion = "PAQUETE EMPRENDEDOR";
					break;
				}
				case 3: {
					$pago_opcion = "CARGO A ESTADO DE CUENTA";
					break;
				}
				case 4: {
					$pago_opcion = "SEFADER";
					break;
				}
				case 5: {
					$pago_opcion = "AUTORIZADO POR UT";
					break;
				}
			}
			switch($fila["status"])
			{
				case 1:
				{
					$status="REVISIÓN";
					break;
				}
				case 2:
				{
					$status="AUTORIZADO";
					break;
				}
				case 3:
				{
					$status="EN LISTA";
					break;
				}
				case 4:
				{
					$status="SOLICITADO A PROVEEDOR";
					break;
				}
				case 5:
				{
					$status="SOLICITADO A PROVEEDOR";
					break;
				}
				case 6:
				{
					$status="SOLICITADO A PROVEEDOR";
					break;
				}
				case 7:
				{
					$status="CANCELADO";
					break;
				}
			}
			$status = ($fila["status"] != 7 && $fila["status"] != 1) ? ("$status<br><span style='font-size: 8px;color:blue;'>$pago_opcion</span>"): $status;
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
			if($fila['urgente']==1)
			{
				$prioridad="URGENTE";
			}
			else
			{
				$prioridad="NORMAL";
			}
			$link="";
			if(($depto=="DA"  || $idus == '1' || $idus == '4') || $nivel == 1)
			{
				if($fila["status"]==1)
				{
				  $link='<button type="button"  name="btn_asignar" id="btn_asignar" class="btn btn-sm btn-success" onClick=confirma_pago_online_otro('.$fila["id_det"].','.$fila["id"].','.$tpe.','.$fila["urgente"].')><i class="fa fa-lg fa-usd"></i></button>';
				  if($fila["comprobante"]!="")
				  {
					  $url="'http://".$svr_dir."/clientes/hologramas/php/files/".$fila["comprobante"]."?".uniqid()."'";
					  $link.='&nbsp;<button type="button"  name="btn_asignar" id="btn_asignar" class="btn btn-sm btn-danger" onClick="ver_comprobante('.$url.')" style="padding-left:7px !important; padding-right:7px !important;"><i class="fa fa-lg fa-file" style="color:#fff"></i></button>';
				  }
				  $link.='&nbsp;&nbsp;<button type="button" name="btn_cancelar" id="btn_cancelar" class="btn btn-sm btn-warning" onclick="cancelar_solicitud('.$fila["id_det"].','.$fila["id"].')"><i class="fa fa-lg fa-close"></i></button>';
				  /*if($idus == '1' ) {
					$link.='&nbsp;&nbsp;<button type="button"  name="btn_asignar" id="btn_asignar" class="btn btn-sm btn-success" onClick=confirma_pago_online_otro('.$fila["id_det"].','.$fila["id"].','.$tpe.')><i class="fa fa-btc" aria-hidden="true"></i></button>';
				  }*/
				} else {
				  if($fila["comprobante"]!="") {
					$url="'http://".$svr_dir."/clientes/hologramas/php/files/".$fila["comprobante"]."?".uniqid()."'";
					$link='<button type="button"  name="btn_asignar" id="btn_asignar" class="btn btn-sm btn-danger" onClick="ver_comprobante('.$url.')" style="padding-left:7px !important; padding-right:7px !important;"><i class="fa fa-lg fa-file" style="color:#fff"></i></button>';
				  }
				  if ($fila["pago_opcion"] === "5") {
					$link .= '&nbsp;&nbsp;<button type="button"  name="btn_asignar" id="btn_asignar" class="btn btn-sm btn-success" onClick="modifica_pago_online_otro('.$fila["id_det"].','.$fila["id"].','.$tpe.')"><i class="fa fa-lg fa-usd"></i></button>';
				  }

				  	/*if($idus == 1) {
						if($fila["status"]==2)
							$link='<button type="button"  name="btn_asignar" id="btn_asignar" class="btn btn-sm btn-success" onClick="get_data_online_tmp('.$fila["id_det"].','.$fila["cantidad"].')"><i class="fa fa-lg fa-cart-plus"></i></button>&nbsp;<button type="button"  name="btnEditPO" id="btnEditPO" class="btn btn-sm btn-primary" onClick="getEditarPO('.$fila["id_det"].')"><i class="fa fa-lg fa-edit"></i></button>&nbsp;&nbsp;<button type="button" name="btn_cancelar" id="btn_cancelar" class="btn btn-sm btn-warning" onclick="cancelar_solicitud('.$fila["id_det"].','.$fila["id"].')"><i class="fa fa-lg fa-close"></i></button>';
					}*/
				}
			}
			else if($depto=="OC" || $idus == 1)
			{
				if($fila["status"]==2)
					$link='<button type="button"  name="btn_asignar" id="btn_asignar" class="btn btn-sm btn-success" onClick="get_data_online_tmp('.$fila["id_det"].','.$fila["cantidad"].')"><i class="fa fa-lg fa-cart-plus"></i></button>&nbsp;<button type="button"  name="btnEditPO" id="btnEditPO" class="btn btn-sm btn-primary" onClick="getEditarPO('.$fila["id_det"].')"><i class="fa fa-lg fa-edit"></i></button>&nbsp;&nbsp;<button type="button" name="btn_cancelar" id="btn_cancelar" class="btn btn-sm btn-warning" onclick="cancelar_solicitud('.$fila["id_det"].','.$fila["id"].')"><i class="fa fa-lg fa-close"></i></button>';
				/*else
				$link="";*/
			}

			if($fila["observaciones"]!=""){

				$link.='&nbsp;<button type="button" name="btn_info" id="btn_info" class="btn btn-sm btn-info" onclick="get_observacion_pedido('.$fila["id_det"].')"><i class="fa fa-lg fa-info"></i></button>';

			}

			$divide = floatval($fila["importe"])/intval($fila["cantidad"]);
			//$txtdivide = ($divide == "0.9" || $divide == "1.35" || $fila["id"] == 1425) ? "<br><span style='font-size: 8px;color:red;'>BUEN FIN</span>": "";
			$txtdivide = ($fila["pago_promo"] == "1") ? "<br><span style='font-size: 8px;color:red;'>BUEN FIN</span>": "";
			$importe="$ ".number_format($fila["importe"], 2, '.', ',') . $txtdivide;
			//$importe .=

			$respuesta->rows[$i]["id"]=$fila["id_det"];
			$folio = ($fila["id"] < 33159) ? $fila["folio"] . "/" .substr($fila["anio"], -2, 2) : $fila["folio"];
			$respuesta->rows[$i]["cell"]=array($folio, $fila["fecha"]."  ". $fila["hora"], $fila["no_cliente"], $marca, $tipo_mez, $fila["edo"], number_format($fila["cantidad"],0),$importe , $prioridad, $status, $link, $fpago, $pago_opcion, $fila["status"]);
			$respuesta->rows[$i]["opera"] = $divide .":". floatval($fila["importe"]).":".intval($fila["cantidad"]);
			$i++;		}
		// La respuesta se regresa como json
		echo  json_encode($respuesta);
	}
?>