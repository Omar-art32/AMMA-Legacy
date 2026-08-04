<?php
  try
  {
	error_reporting(0);
    include('../../../common/conexion.php');
	//include('../../../common/conexion_remota.php');
	//$con_rem->autocommit(FALSE);
	$conexion->autocommit(FALSE);
	
    $no_pedido=$_POST['no_pedido'];  
	$user=utf8_decode($_POST['user']); 
	$fecha = date("Y-m-d H:i:s" );
	$sql_remota="";
	$val_pedido="";
    $sql_ins="INSERT INTO h_pedidos(id_sh_d,no_pedido,fecha,no_cliente,edo,marca,serie,tipo,holograma,fi,ff,cantidad,pagado,urgente,status,usr)(SELECT id_sh_d, no_pedido, '$fecha', no_cliente, edo, marca, serie, tipo, holograma, fi, ff, cantidad, pagado, urgente,0,'$user' FROM h_tmp_pedido WHERE no_pedido=$no_pedido and pagado=1);";
	$result=$conexion->query($sql_ins); 		
	if ($result!=true) throw new Exception("Error al agregar los datos en la tabla pedidos: ".$sql_ins);

	
	//INSERTAR EN LA BD REMOTA
    /*$sql_pedido="select id_sh_d,no_pedido,fecha,no_cliente,edo,marca,serie,tipo,fi,ff,cantidad,pagado,urgente,status,usr from h_pedidos where no_pedido=$no_pedido";
    $sql_remota="INSERT INTO h_pedidos (id_sh_d,no_pedido,fecha,no_cliente,edo,marca,serie,tipo,fi,ff,cantidad,pagado,urgente,status,usr)VALUES";
    $res_p=$conexion->query($sql_pedido);
    $sep="";
	$ids_alert="";
    while($row=$res_p->fetch_assoc())
    {
	   $sql_remota.=$sep."('{$row['id_sh_d']}','{$row['no_pedido']}','{$fecha}','{$row['no_cliente']}','{$row['edo']}','{$row['marca']}','{$row['serie']}','{$row['tipo']}', '{$row['fi']}', '{$row['ff']}', '{$row['cantidad']}', '{$row['pagado']}', '{$row['urgente']}', 1,'{$user}')";
	   if($row['id_sh_d']!=0)
	   {
		 $ids_alert.=$sep.$row['id_sh_d'];
	   }
	   $sep=",";
    }
	$sql_remota.=";";
	$in_rem=$con_rem->query($sql_remota);
	if ($in_rem!=true) throw new Exception("Error al agregar los datos en la tabla pedidos REMOTA: ".$sql_remota);*/

	//ACTUALIZAR EL ESTATUS A ENVIADO
	$sql_up="update h_pedidos set status=1,sinc_up=1 where no_pedido=$no_pedido";
    $res_up=$conexion->query($sql_up);
	if ($res_up!=true) throw new Exception("Error al actualizar el estatus en la tabla pedidos local 1: ".$sql_up);
	//GENERAR LAS ALERTAS
	if($ids_alert!="")
	{
       $sql_alertas="insert into g_alertas(id_solicitud,id_referencia,id_msj,fecha)(select id_solicitud,id_referencia,2, NOW() from g_alertas where id_referencia in ($ids_alert))";
	   $res_alertas=$conexion->query($sql_alertas);
	   if ($res_alertas!=true) throw new Exception("Error al actualizar el estatus en la tabla pedidos local 2: ".$sql_alertas);
	}
	//ELIMINAR EL PEDIDO TEMPORAL
	$del_temp="DELETE from h_tmp_pedido where no_pedido=$no_pedido and pagado=1;";
    $res_del=$conexion->query($del_temp);
	if($res_del!=true) throw new Exception("Se envío la requisición, pero no se han eliminado los archivos temporales, por favor informe a sistemas: ".$del_temp);
	$up_numpedido="update h_tmp_pedido set no_pedido=no_pedido+1  where no_pedido=$no_pedido";
	$res_up_numpedido=$conexion->query($up_numpedido);
	if($res_up_numpedido!=true) throw new Exception("Error actualizar el No de Pedido temporal LOCAL: ".$up_numpedido);
	$conexion->commit();
	//$con_rem->commit();
	echo json_encode(array("status" => "OK", "msj" => "El pedido se envio correctamente"));
  }
  catch(Exception $e)
  {
	  $conexion->rollback();
		//$con_rem->rollback();
		echo json_encode(array("status" => "error", "msj" => "Error en la base de datos: " . $e->getMessage()));
		$conexion->close();
  }
?>