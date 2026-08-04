<?php
try
 {
	include('../../../common/conexion.php');
	//include('../../../common/conexion_remota.php');
	$conexion->autocommit(FALSE); 

	// CARGA DE ARCHIVO
	if (!empty($_FILES['file'])) {
			/*echo json_encode(['error'=>'No hay archivos para Importar.']);
			// or you can throw an exception
			return; // terminate
		}*/
		// get the files posted
		$images = $_FILES['file'];
		// get user id posted
		$userid = empty($_POST['user']) ? '' : $_POST['user'];
		// a flag to see if everything is ok
		$success = null;
		// file paths to store
		$paths= [];
		// get file names
		$filenames = $images['name'];
		$idSolicitud = $_POST['id_s'];


		//$rtCliente = DIRECTORY_SEPARATOR . "php" . DIRECTORY_SEPARATOR . "reportes" . DIRECTORY_SEPARATOR . "pdf_acuses" ;
		//$rtCliente = DIRECTORY_SEPARATOR . "pdf_acuses";
		$rtCliente = "pdf_acuses";
		$arrext = explode(".", $filenames);
		$ext = array_pop($arrext);

		$sql = "SELECT no_cliente, id_solicitud, total, tipo_pago FROM sh_pedidos WHERE id_solicitud = ?";
		$ps = $conexion->prepare($sql);
		$ps->bind_param("i", $idSolicitud);
		$ps->execute();
		$result = $ps->get_result();
		if ($row = $result->fetch_assoc()) {
			//$numRecibo = str_pad($row['id_recibo'], 4, "0", STR_PAD_LEFT);
			$nombreArchivo = "FOL_".$idSolicitud.".".$ext;
			
			$target = ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "clientes/hologramas/php/files/".  $nombreArchivo;
			if(move_uploaded_file($images['tmp_name'], $target)) {
				$success = true;
				$paths[] = $target;
			} else
				$success = false;
		} 

		if($success) {
			$sql = $conexion->prepare("UPDATE sh_pedidos SET comprobante = '".$nombreArchivo."' WHERE id_solicitud = '".$idSolicitud."'");
			if ($sql) { 
				if($sql->execute())
						$success = true;
				else
					$success = false;
			} else
				$success = false;
		}
	} else {
		$success = true;
	}
	// check and process based on successful status
	if ($success === true) {		
		$idSolicitud = $_POST['id_s'];
		$sql = "SELECT no_cliente, id_solicitud, total, tipo_pago FROM sh_pedidos WHERE id_solicitud = ?";
		$sql = $conexion->prepare($sql);
		$sql->bind_param("i", $idSolicitud);
		if (!$sql->execute()) throw new Exception("Error al consultar hologramas (ERR:002) $conexion->error");
		$sql->store_result();
		$sql->bind_result($no_cliente, $id_solicitud, $total, $tipo_pago);
		$sql->fetch();
		// 
		$sql = "SELECT status FROM sh_detalle WHERE id_solicitud = ?";
		$sql = $conexion->prepare($sql);
		$sql->bind_param("i", $idSolicitud);
		if (!$sql->execute()) throw new Exception("Error al consultar hologramas (ERR:002) $conexion->error");
		$sql->store_result();
		$sql->bind_result($status);
		$sql->fetch();
		
		//if($success) {
		
			//$output = [];
		// for example you can get the list of files uploaded this way
		// $output = ['uploaded' => $paths];
			//$con_rem->autocommit(FALSE);
			$folio=$_POST['folio'];  	
			$id_s=$_POST['id_s']; 
			$user=$_POST['user'];  
			$pago_opcion = $_POST['pago_opcion'];
			if(isset($_POST['forma_pago'])) {
				$forma_pago = ($_POST['forma_pago'] === '1') ? "EFECTIVO (EN OFICINA AMMA)" : "TRANSFERENCIA BANCARIA";
			} else {
				$forma_pago = "";
			}
			//ACTUALIZAMOS EN LA BD LOCAL
			if(isset($_POST['modificar']) && $_POST['modificar'] === "1") {
				$sql_pag_local="update sh_detalle set pago_opcion = $pago_opcion, usr_pago='$user' where id=$folio";
			} else {
				if($tipo_pago !== $forma_pago && $forma_pago !== "") {
					$sql_pag_local="update sh_pedidos set tipo_pago = ? where id_solicitud = ?";
					$sqlActualiza = $conexion->prepare($sql_pag_local);
					if (!$sqlActualiza) throw new Exception(json_encode(array("codigo" => "0009","error" => $conexion->error)));
					$sqlActualiza->bind_param("si", $forma_pago,				$id_s);
					if (!$sqlActualiza->execute()) throw new Exception(json_encode(array("codigo" => "0010","error" => $conexion->error)));
					$sqlActualiza->close();
				}
				if($status == "3" || $status == "4" || $status == "5") {
					$sql_pag_local="update sh_detalle set pago_opcion = $pago_opcion, usr_pago='$user' where id=$folio";
				} else {
					$sql_pag_local="update sh_detalle set status=2,fecha_pago=NOW(), sinc_up=2, pago_opcion = $pago_opcion, usr_pago='$user' where id=$folio";
				}
			}
			$res_up_pago_local=$conexion->query($sql_pag_local);	
			if($res_up_pago_local!=true) throw new Exception("Error al actualizar el estatus de Sincronizacion en la tabla sh_pedidos: ".$sql_pag_local);
			if($conexion->affected_rows<1) throw new Exception("No se actualizo ningun registro: ".$sql_pag_local);
			//AHORA ACTUALIZAMOS EN LA BD REMOTA
			/*$sql_pag_rem="update sh_detalle set status=2 where id=$folio";
			$res_up_pago_rem=$con_rem->query($sql_pag_rem);	
			if($res_up_pago_rem!=true) throw new Exception("Error al actualizar el estatus de Sincronizacion en la tabla sh_pedidos: ".$sql_pag_rem);
			if($con_rem->affected_rows<1) throw new Exception("No se actualizo ningun registro: ".$sql_pag_rem);*/
			//CREAMOS LA NOTIFICACION
			$sql_alert="INSERT INTO g_alertas (id_solicitud,id_referencia,id_msj,fecha)VALUES($id_s,$folio,1,NOW())";
			$res_alert=$conexion->query($sql_alert);
			if($res_alert!=true) throw new Exception("NO se pudo guardar la alerta: ".$sql_alert);
			$conexion->commit();
			//$con_rem->commit();
			echo json_encode(array("status" => "OK", "msj" => "Se actualizó el status a Pagado" . $status));	
		/*} else {
			$msj = "Error al Crear Carpeta del Cliente, ";
			$output = ['error'=> "Error al Actualizar el registro del Producto, contacte con su Administrador de Sistemas."];
		}*/
	} elseif ($success === false) {
		$msj = 'Error al Subir el Archivo, ';
		$output = ['error'=> $msj."contacte con su Administrador de Sistemas."];
	} else {
		$output = ['error'=>'No hay archivos para Importar.'];
	}

	//

	
 }
 catch (Exception $e) {
		$conexion->rollback();
		//$con_rem->rollback();
		echo json_encode(array("status" => "error", "msj" => "Error en la base de datos: " . $e->getMessage()));
		//$con_rem->close();
		$conexion->close();
	}
?>