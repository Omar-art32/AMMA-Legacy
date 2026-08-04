<?php
  include('../../common/conexion.php');
	$idElim=$_POST['idElim'];

	$sql = "SELECT documento FROM clientes_relaciones WHERE id='$idElim'";
	$query = $conexion->query($sql);
	$relD=0;
	while ($r = $query->fetch_assoc()) {
		$relD = $r['documento'];
		$sql_del="UPDATE documentos SET status=2 WHERE id=?";
		$st_del=$conexion->prepare($sql_del);
		$st_del->bind_param('i',$relD); 
		if(!$st_del->execute())throw new Exception('Ocurrio un error al intentar eliminar la relacion');
	}

    $sql_del="DELETE FROM clientes_relaciones WHERE id=?";
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