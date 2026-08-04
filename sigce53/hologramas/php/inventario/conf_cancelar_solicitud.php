<?php
try
 {
	include('../../../common/conexion.php');
	//include('../../../common/conexion_remota.php');
	$conexion->autocommit(FALSE); 
	//$con_rem->autocommit(FALSE);
	$folio=$_POST['folio'];  	
	$id_s=$_POST['id_s']; 
	$user=$_POST['user'];  
	$sql_pag_local="update sh_detalle set status=7,fecha_cancelar=NOW(), sinc_up=7, usr_cancelar='$user' where id=$folio";
	$res_up_pago_local=$conexion->query($sql_pag_local);	
	if($res_up_pago_local!=true) throw new Exception("Error al actualizar el estatus de Sincronizacion en la tabla sh_pedidos: ".$sql_pag_local);
	if($conexion->affected_rows<1) throw new Exception("No se actualizo ningun registro: ".$sql_pag_local);
	//AHORA ACTUALIZAMOS EN LA BD REMOTA
	/*$sql_pag_rem="update sh_detalle set status=2 where id=$folio";
	$res_up_pago_rem=$con_rem->query($sql_pag_rem);	
	if($res_up_pago_rem!=true) throw new Exception("Error al actualizar el estatus de Sincronizacion en la tabla sh_pedidos: ".$sql_pag_rem);
	if($con_rem->affected_rows<1) throw new Exception("No se actualizo ningun registro: ".$sql_pag_rem);*/
	//CREAMOS LA NOTIFICACION
	$sql_alert="INSERT INTO g_alertas (id_solicitud,id_referencia,id_msj,fecha)VALUES($id_s,$folio,7,NOW())";
	$res_alert=$conexion->query($sql_alert);
	if($res_alert!=true) throw new Exception("NO se pudo guardar la alerta: ".$sql_alert);
	$conexion->commit();
	//$con_rem->commit();
	echo json_encode(array("status" => "OK", "msj" => "Se actualizó el status a Pendiente de pago"));	
 }
 catch (Exception $e) {
		$conexion->rollback();
		//$con_rem->rollback();
		echo json_encode(array("status" => "error", "msj" => "Error en la base de datos: " . $e->getMessage()));
		//$con_rem->close();
		$conexion->close();
	}
?>