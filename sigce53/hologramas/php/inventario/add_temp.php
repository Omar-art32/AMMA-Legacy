<?php
try
{
  include('../../../common/conexion.php');
  //include('../../../common/conexion_remota.php');
  $conexion->autocommit(FALSE);
  //$con_rem->autocommit(FALSE);
  
  $user=utf8_decode($_POST['user']);
  $no_pedido=utf8_decode($_POST['no_pedido']);

  $datos = json_decode($_POST['datos'],true);  
  $cliente=utf8_decode ($datos['cte']);
  $marca=utf8_decode ($datos['marca']);
  $edo=utf8_decode ($datos['edo']);
  $serie=utf8_decode ($datos['serie']);
  $tipo=utf8_decode ($datos['tipo']);
  $cantidad=utf8_decode ($datos['cantidad']);
  $pagado=utf8_decode ($datos['pagado']);
  $urgente=utf8_decode ($datos['urgente']);
  $fini=utf8_decode ($datos['fini']);
  $ffin=utf8_decode ($datos['ffin']);  
  $fecha = date("Y-m-d H:i:s" );  
  $folio_det=0;
  if(isset($datos['folio_det']))
  {
	 $folio_det=$datos['folio_det'];
	 $sql_ins="INSERT INTO h_tmp_pedido(id_sh_d,no_pedido, fecha, no_cliente, edo, marca, serie, tipo, fi, ff, cantidad, pagado, urgente, status,usr) VALUES ($folio_det,'{$no_pedido}', '{$fecha}', '{$cliente}','{$edo}', '{$marca}', '{$serie}', '{$tipo}', '{$fini}', '{$ffin}', '{$cantidad}', '{$pagado}', '{$urgente}', '0', '{$user}')";  
  }
  else
  {	 
	$sql_ins="INSERT INTO h_tmp_pedido(no_pedido, fecha, no_cliente, edo, marca, serie, tipo, fi, ff, cantidad, pagado, urgente, status,usr) VALUES ('{$no_pedido}', '{$fecha}', '{$cliente}','{$edo}', '{$marca}', '{$serie}', '{$tipo}', '{$fini}', '{$ffin}', '{$cantidad}', '{$pagado}', '{$urgente}', '0', '{$user}')";  
  }
  $result=$conexion->query($sql_ins); 
  if ($result!=true) throw new Exception("Error al agregar el pedido a la lista temporal: ".$sql_ins);
  if($folio_det!=0)
  {
	  $sql_up_det="update sh_detalle set status=3, fecha_lista=NOW(),usr_lista='$user' where id=$folio_det";
	  $res_up_local=$conexion->query($sql_up_det);
	  if ($res_up_local!=true) throw new Exception("No se pudo actualizar el estatus del pedido en la base local: ".$sql_up_det);
	  //$res_up_rem=$con_rem->query($sql_up_det);
	  //if ($res_up_rem!=true) throw new Exception("No se pudo actualizar el estatus del pedido en la base remota: ".$sql_up_det);
  }
   //$con_rem->commit();
   $conexion->commit();
    //OBTENEMOS EL NUMERO DE PEDIDO ACTUAL
    $sql_tmp=$sql="select max(id_row) maxid from h_tmp_pedido";
    $res_tmp=$conexion->query($sql_tmp);
    if($res_tmp && $res_tmp->num_rows==1) {
      $row_tmp=$res_tmp->fetch_row();
      $id_row = $row_tmp[0];
    }

   echo json_encode(array("status" => "OK", "msj" => "Se agrego correctamente al temporal", "id_row" => @$id_row));
}
catch (Exception $e) {
		//$con_rem->rollback();
		$conexion->rollback();
		echo json_encode(array("status" => "error", "msj" => "Error en la base de datos: " . $e->getMessage()));
		$conexion->close();
	}
?>                                                                                                                                                                                         