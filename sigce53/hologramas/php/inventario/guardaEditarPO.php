<?php
try {
	session_start();
	include('../../../common/conexion.php'); 
	$conexion->autocommit(FALSE);
	$campos=""; 
	$cambios="";
	$old_tipo="";
	$old_edo="";
	$cont_cambios=0;
	$sep="";
	$sql_up_detalle="UPDATE sh_detalle set ";
	$usr=$_POST["usr_up"];
	$id_detalle=$_POST["txtIdEditarPO"];
	$tipo=$_POST["cbo_tipoEPO"];
	$edo=$_POST["cbo_edosEPO"];
	$obs=$_POST["txtObsPO"];	
	$sql_detalle="SELECT tipo,edo from sh_detalle where id=$id_detalle limit 1";
	$res_det=$conexion->query($sql_detalle);
	if($res_det!=true) throw new Exception("No se encontraron datos.".$sql_detalle);
	$row_det=$res_det->fetch_assoc();
	if($row_det['tipo']!=$tipo)
	{
		$campos.="tipo";
		$sql_up_detalle.="tipo='{$tipo}'";		
		$old_tipo=$row_det['tipo'];	
		$sep=", ";
		$cont_cambios++;		
	}
	if($row_det['edo']!=$edo)
	{
		$campos.=$sep."estado";
		$sql_up_detalle.=$sep."edo='{$edo}'";
		$old_edo=$row_det['edo'];		
		$cont_cambios++;
	}
	if($cont_cambios==1)
	{
		$cambios="El campo ".$campos." fue modificado";
		
	}
	else if($cont_cambios==2)
	{
		$cambios="Los campos ".$campos." fueron modificados";
	}
	$sql_up_detalle.=" WHERE id=$id_detalle";
	$res_up_det=$conexion->query($sql_up_detalle);
	if($res_up_det!=true) throw new Exception("Error al actualizar el detalle del pedido .".$sql_up_detalle);
	
	$sql_ins_bit="INSERT INTO sh_up_detalle(id_detalle, old_tipo, old_edo, cambios, observaciones, usr_up,fecha_up)values($id_detalle, '{$old_tipo}', '{$old_edo}', '{$cambios}', '{$obs}', '{$usr}',NOW())";
	$res_ins_bit=$conexion->query($sql_ins_bit);
	if($res_ins_bit!=true) throw new Exception("Error al insertar detalles en la bitacora .".$sql_ins_bit);
	$conexion->commit();
	$conexion->close();
	echo json_encode(array("status" => "OK", "msj" => "Detalle actualizado correctamente"));
}
catch (Exception $e)
{
	$conexion->rollback();
	$conexion->close();
	echo json_encode(array("status" => "error", "msj" =>  $e->getMessage()));
}
?>