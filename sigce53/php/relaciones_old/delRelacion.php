<?php
  include('../../common/conexion.php');
    $idElim=$_POST['idElim']; 
    $sql_del="delete from clientes_relaciones where id=?";
	$st_del=$conexion->prepare($sql_del);
	$st_del->bind_param('i',$idElim); 
	if(!$st_del->execute())throw new Exception('Ocurrio un error al intentar eliminar la relacion');
	if($st_del->affected_rows>0)
	{ 
	  echo json_encode(array('status' => 'OK','msj'=> 'Relacion eliminada correctamente'));
	} 
	else
	{   
	  echo json_encode(array('status' => 'error','msj'=> 'No se pudo eliminar la relacion'));
	}
?>