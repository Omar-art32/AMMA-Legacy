<?php
try {			
	include('../../../common/conexion.php');
	$conexion->autocommit(FALSE);
	$idFilaPedido=$_POST['fila_pedido'];
	$fi_ing=$_POST['fi_ing'];
	$ff_ing=$_POST['ff_ing'];
	$usr=$_POST['usr'];
	//$sql = "SELECT intentos FROM usuarios_asociados WHERE usuario = ?";
	$sql_edo="SELECT max(fol_fin) f_fin FROM h_existencias INNER JOIN h_pedidos ON h_existencias.no_cliente=h_pedidos.no_cliente and h_existencias.marca=h_pedidos.marca and h_existencias.serie=h_pedidos.serie WHERE h_pedidos.id_row=? and h_existencias.edo=h_pedidos.edo and h_existencias.tipo=h_pedidos.tipo limit 1";	
	
	$ff_entrada=0;
	$tipo="";
	$ps = $conexion->prepare($sql_edo);
	$ps->bind_param("i", $idFilaPedido);
	if (!$ps->execute()) throw new Exception("Error en la base de datos");
	$ps->bind_result($ff_entrada);
	$ps->store_result();
	$ps->fetch();

	// VALIDAR SI LOS FOLIOS SON DE DE HOLOGRAMAS NUEVOS :: 310322 :: 070725
	$ifi_ing = intval($fi_ing);
	$iff_ing = intval($ff_ing);
	$sql_pedidos = $conexion->prepare("SELECT holograma FROM h_pedidos WHERE id_row = ?");
	if (!$sql_pedidos) throw new Exception("Ocurrio un error al obtener la información (ERROR:01) $conexion->error");
    $sql_pedidos->bind_param("i",$idFilaPedido);
    if (!$sql_pedidos->execute()) throw new Exception("Ocurrio un error al obtener la información (ERROR:02) $conexion->error");
    $sql_pedidos->store_result();
    $sql_pedidos->bind_result($holograma);
    $sql_pedidos->fetch();
	$holograma++;
	$version = $holograma;
	//
	if($ff_entrada== NULL)
	{
	 
		 //INSERTAMOS LA ENTRADA
		 $sql_ins="INSERT INTO h_entradas(no_cliente,marca,edo,tipo,serie,fol_ini,fol_fin,cantidad,version,fecha,usr)(SELECT no_cliente,marca,edo,tipo,serie,fi,ff,cantidad,$version,NOW(), ? FROM h_pedidos where h_pedidos.id_row=?)";
		 $ps_e = $conexion->prepare($sql_ins);
		 $ps_e->bind_param("si", $usr, $idFilaPedido);		 
		 if (!$ps_e->execute()) throw new Exception("Error al insertar la entrada");	
		 $sql_exs="INSERT INTO h_existencias(no_cliente,marca,edo,tipo,serie,fol_ini,fol_fin,existencias)(SELECT no_cliente,marca,edo,tipo,serie,fi,ff,cantidad FROM h_pedidos where h_pedidos.id_row=?)";
		 $ps_e = $conexion->prepare($sql_exs);
		 $ps_e->bind_param("i", $idFilaPedido);
		 if (!$ps_e->execute()) throw new Exception("Error al insertar la existencia [FREE]");
		 $sql_ped="UPDATE h_pedidos SET status=6 where id_row=?";
		 $ps_e = $conexion->prepare($sql_ped);
		 $ps_e->bind_param("i", $idFilaPedido);
		 if (!$ps_e->execute()) throw new Exception("Error al actualizar el estatus del pedido [FREE]");
		 $mensaje_p="Los folios se han agregado correctamente a las existencias1";		

	  
	}
	else
	{
	  //ACTUALIZAR EXISTENCIAS CORRESPONDIENTES AL ESTADO DEL PEDIDO y TIPO
	  if($fi_ing==($ff_entrada+1))
	  {
		 //INSERTAMOS LA ENTRADA
		 $sql_ins="INSERT INTO h_entradas(no_cliente,marca,edo,tipo,serie,fol_ini,fol_fin,cantidad,version,fecha,usr)(SELECT no_cliente,marca,edo,tipo,serie,fi,ff,cantidad,$version,NOW(), ? FROM h_pedidos where h_pedidos.id_row=?)";
		 $ps_e = $conexion->prepare($sql_ins);
		 $ps_e->bind_param("si", $usr, $idFilaPedido);		 
		 if (!$ps_e->execute()) throw new Exception("Error al insertar la entrada");





		 $sql_exs="UPDATE h_existencias INNER JOIN h_pedidos ON h_existencias.no_cliente=h_pedidos.no_cliente and h_existencias.marca=h_pedidos.marca and h_existencias.edo=h_pedidos.edo and h_existencias.serie=h_pedidos.serie and h_existencias.tipo=h_pedidos.tipo SET  h_existencias.fol_ini=if(h_existencias.existencias>0,h_existencias.fol_ini,h_pedidos.fi), h_existencias.fol_fin=h_pedidos.ff, h_existencias.existencias=h_existencias.existencias+h_pedidos.cantidad WHERE h_pedidos.id_row=? AND h_existencias.id_existencias=(SELECT id_existencias FROM (SELECT * FROM h_existencias) AS h_existencias_sub WHERE no_cliente=h_pedidos.no_cliente AND marca=h_pedidos.marca AND edo=h_pedidos.edo AND serie=h_pedidos.serie AND tipo=h_pedidos.tipo ORDER BY fol_fin desc limit 1)";

		 $ps_e = $conexion->prepare($sql_exs);
		 $ps_e->bind_param("i", $idFilaPedido);
		 if (!$ps_e->execute()) throw new Exception("Error al actualizar la existencia [ESTADOS]");
		 $sql_ped="UPDATE h_pedidos SET status=6 where id_row=?";
		 $ps_e = $conexion->prepare($sql_ped);
		 $ps_e->bind_param("i", $idFilaPedido);
		 if (!$ps_e->execute()) throw new Exception("Error al actualizar el estatus del pedido [ESTADOS]");
		 $mensaje_p="Los folios se han agregado correctamente a las existencias2";
  
	  } 
	  else
	  {
		  //INSERTAMOS LA ENTRADA
		  $sql_ins="INSERT INTO h_entradas(no_cliente,marca,edo,tipo,serie,fol_ini,fol_fin,cantidad,version,fecha,usr)(SELECT no_cliente,marca,edo,tipo,serie,fi,ff,cantidad,$version,NOW(), ? FROM h_pedidos where h_pedidos.id_row=?)";
		  $ps_e = $conexion->prepare($sql_ins);
		  $ps_e->bind_param("si", $usr, $idFilaPedido);		 
		  if (!$ps_e->execute()) throw new Exception("Error al insertar la entrada");
		   
		   
		   $sql_exs="INSERT INTO h_existencias(no_cliente,marca,edo,tipo,serie,fol_ini,fol_fin,existencias)(SELECT no_cliente,marca,edo,tipo,serie,fi,ff,cantidad FROM h_pedidos where h_pedidos.id_row=?)";
		   $ps_e = $conexion->prepare($sql_exs);
		   $ps_e->bind_param("i", $idFilaPedido);
		   if (!$ps_e->execute()) throw new Exception("Error al insertar la existencia [ESTADOS]");
		   $sql_ped="UPDATE h_pedidos SET status=6 where id_row=?";
		   $ps_e = $conexion->prepare($sql_ped);
		   $ps_e->bind_param("i", $idFilaPedido);
		   if (!$ps_e->execute()) throw new Exception("Error al actualizar el estatus del pedido [ESTADOS]");
		   $mensaje_p="Los folios se han agregado como un nuevo registro de existencias3";

	  }	  
	}

	$error=insertarNotificacion($idFilaPedido,1);
	if($error=="ERROR") throw new Exception("Error al registrar Notificacion");

	
	$conexion->commit();
	$conexion->close();
	echo json_encode(array("status" => "OK", "msj" => $mensaje_p));

	
}
catch (Exception $e) {
	$conexion->rollback();
	$conexion->close();
	echo json_encode(array("status" => "error", "msj" => "Error en la base de datos: " . $e->getMessage()));
}

function recuperarFolioNot($anioActual)
{

	try {

		include('../../../common/conexion.php');
		$conexion->autocommit(FALSE);

		$numNotificacion = 1;
		$sql = "SELECT MAX(numero) AS max_notificacion FROM notificaciones.notificaciones WHERE anio = ?";
		$ps = $conexion->prepare($sql);
		$ps->bind_param("i", $anioActual);
		if (!$ps->execute()) throw new Exception("Error al recuperar numero de Notiticacion");
		$ps->bind_result($max_notificacion);
		$ps->fetch();
		$numNotificacion = ($max_notificacion != null) ? $max_notificacion + 1 : $numNotificacion;
		$ps->close();

		

		$conexion->commit();
		$conexion->close();
		return $numNotificacion;

	} catch (Exception $e) {
		$conexion->rollback();
		$conexion->close();
		return "ERROR";
	}
}


function guardarError($id_solicitud, $no_cliente, $mensaje_error, $tipo_notificacion)
{


	try {

		include('../../../common/conexion.php');
		$conexion->autocommit(FALSE);

		$sql = "INSERT INTO notificaciones.notificaciones_pendientes(id_solicitud, no_cliente, mensaje_error, tipo, fecha ) VALUES (?, ?, ?, ?, NOW())";
		$ps = $conexion->prepare($sql);
		$ps->bind_param("isii", $id_solicitud, $no_cliente, $mensaje_error, $tipo_notificacion);
		if (!$ps->execute()) throw new Exception("Error al registrar error de notificacion $conexion->error");
		$ps->close();

		$conexion->commit();
		$conexion->close();
		return "OK";
	} catch (Exception $e) {
		$conexion->rollback();
		$conexion->close();
		return "ERROR";
	}
}

function insertarNotificacion($idFilaPedido,$tipo_notificacion)
{

	try {

		include('../../../common/conexion.php');
		$conexion->autocommit(FALSE);
		$conexion->set_charset("utf8");

		$anioActual = intval(date("Y"));
		$anioAct    = date("y");


		// ********************************OBTENEMOS LOS DATOS DE LA SOLICITUD
		$sql = "SELECT s.id,s.folio,hp.no_cliente FROM h_pedidos hp INNER JOIN sh_detalle sh ON hp.id_sh_d=sh.id INNER JOIN solicitudes s ON sh.id_solicitud=s.id WHERE hp.id_row=? ";
		$solicitud = $conexion->prepare($sql);
		$solicitud->bind_param("i", $idFilaPedido);
		if (!$solicitud->execute()) throw new Exception("Error al recuperar numero de  folio Solicitud");
		$solicitud->bind_result($id_solicitud, $folioSolicitud, $no_cliente);
		$solicitud->fetch();
		$solicitud->close();


		// ********************************OBTENEMOS LOS DATOS DE LOS TELEFONOS
		$telefonos = $conexion->prepare(" 
		SELECT t.numero 
		FROM siig.clientes c
		INNER JOIN siig.clientes_telefonos ct ON ct.cliente=c.no_cliente
		INNER JOIN siig.telefonos t ON ct.telefono=t.id AND t.tipo=0 AND t.status=2 AND t.sms=1
		WHERE c.no_cliente= ?
		");

		if (!$telefonos) throw new Exception("Ocurrio un error al actualizar la solicitud, REF:NOTIFICACIONES1" . $conexion->error); //$conexion->error
		$telefonos->bind_param("s", $no_cliente);
		if (!$telefonos->execute()) throw new Exception("Ocurrio un error al actualizar la solicitud, REF:NOTIFICACIONES2" . $conexion->error); //$conexion->error
		$telefonos->store_result();
		$row_cnt = $telefonos->num_rows;
		$telefonos->bind_result($telefono);

		if ($row_cnt > 0) {

			// ********************************OBTENEMOS LOS DATOS DE LA NOTIFICACION
			$numConsecutivoNot = recuperarFolioNot($anioActual);
			if ($numConsecutivoNot == "ERROR") throw new Exception("Error al recuperar numero de notificacion $conexion->error");
			$claveNot = str_pad($numConsecutivoNot, 5, "0", STR_PAD_LEFT);
			$folioNot = 'NOT-' . $claveNot . '/' . $anioAct;
			$mensaje = "AMMA " . $folioNot . ": " . $folioSolicitud . " hologramas impresos. NOTA: Este numero no admite mensajes de respuesta. http://www.amma.org.mx/m.php";

			while ($telefonos->fetch()) {



				// ********************************INSERTAR NOTIFICACION
				$notificaciones = $conexion->prepare("INSERT INTO notificaciones.`notificaciones`(
																								`numero`,
																								`folio`,
																								`anio`,
																								`fecha`,
																								`no_cliente`,
																									`mensaje`,
																									`tipo`,
																									`telefono`,
																									`id_solicitud`,
																										`estatus`
																									)VALUES(?,?,?,NOW(),?,?,?,?,?,1)");


				if (!$notificaciones) throw new Exception("Error al registrar la notificacion REF-1 $conexion->error");
				$notificaciones->bind_param("isissisi", $numConsecutivoNot, $folioNot, $anioActual, $no_cliente, $mensaje, $tipo_notificacion, $telefono, $id_solicitud);
				if (!$notificaciones->execute()) throw new Exception("Error al registrar la notificacion REF-2 $conexion->error");
				$notificaciones->close();
			}
		} else {

			$error = guardarError($id_solicitud, $no_cliente, 1, $tipo_notificacion);
			if ($error == "ERROR") throw new Exception("Error al registrar error de notificacion $conexion->error");
		}

		$telefonos->close();

		$error = send_push();
        if ($error == "ERROR") {
            throw new Exception("Error al enviar push Notificacion");
        }

		$conexion->commit();
		$conexion->close();
		return "OK";

	} catch (Exception $e) {

		$conexion->rollback();
		$conexion->close();
		return "ERROR";
	}
}

function send_push(){

    try
    {
		include('../../../common/conexion.php');
      $conexion->autocommit(FALSE);
    
      $id_token = 1;
      $tokens= $conexion->prepare(" 
      SELECT token
      FROM notificaciones.tokens t 
      WHERE t.id = ?
      ");
      if (!$tokens) throw new Exception("Ocurrio un error al actualizar la solicitud, REF:NOTIFICACIONES1".$conexion->error);//$conexion->error
      $tokens->bind_param("i", $id_token);
      if (!$tokens->execute()) throw new Exception("Ocurrio un error al consultar la solicitud, REF:NOTIFICACIONES2".$conexion->error);//$conexion->error
      $tokens->store_result();
      $row_cnt = $tokens->num_rows;
      $tokens->bind_result($token);
      $tokens->fetch();
      $tokens->close();
    
      $url = "https://fcm.googleapis.com/fcm/send";
      $serverKey = 'AAAAiw1XPSg:APA91bE6kKiniQylZO1LI5Z1iHxmrQYEupI8POrC0dLtYdrnxjvhp3oaiahyPLq5POfBxV-eYyFmEnvDRtO1Hmc7f5v0gyIcii-yYGubOg87UJk9DjThQSSmPaTVphPridEvsQ4658rY';
      $title = "Notificación CRM";
      $body = "Enviar Notificación";
      $notification = array('title' => $title, 'text' => $body, 'sound' => 'default', 'badge' => '1');
    
    
                    if($row_cnt == 1){
    
                    $fcmRegIds = array();
                    array_push($fcmRegIds, $token);
    
                    ignore_user_abort();
                        ob_start();
    
    
                    $fields = array(
                        'registration_ids' => $fcmRegIds ,
                        'data' => $notification,
                    );
    
                    
    
                    $headers = array(
                        'Authorization:key='.$serverKey,
                        'Content-Type: application/json'
                    );      
    
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
                    $result = curl_exec($ch);
    
                    curl_close($ch);
                    ob_flush();
    
                    }             
    
              $conexion->commit();
              $conexion->close();
              return "OK";
    
    }
    catch (Exception $e) {
      
      $conexion->rollback();
      $conexion->close();
      return "ERROR";
    }

}