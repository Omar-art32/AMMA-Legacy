<?php
  include('../../../common/conexion.php');
   $sql_del="delete from h_tmp_pedido where 1";
	$result=$conexion->query($sql_del); 
	if($conexion->affected_rows>0)
	{ 
	  echo json_encode(array('status' => 'OK','msj'=> 'Detalle eliminado correctamente'));
	} 
	else
	{   
	  echo json_encode(array('status' => 'error','msj'=> 'No hay registros que eliminar'));
	}
?>