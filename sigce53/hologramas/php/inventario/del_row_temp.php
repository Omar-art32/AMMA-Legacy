<?php
  include('../../../common/conexion.php');
  $no_pedido=utf8_decode($_POST['no_pedido']);  
  $cliente=utf8_decode ($_POST['cliente']);
  $marca=utf8_decode ($_POST['marca']);
  $estado=utf8_decode ($_POST['estado']);

  $sql = "UPDATE sh_detalle sh INNER JOIN h_tmp_pedido ht ON sh.id=ht.id_sh_d AND ht.no_pedido=? AND ht.no_cliente= ? AND ht.marca= ? AND ht.edo= ? SET sh.status=2 WHERE sh.status=3;";
  $ps=$conexion->prepare($sql);
  $ps->bind_param('isss',$no_pedido,$cliente,$marca,$estado);
  if(!$ps->execute()){

  	echo json_encode(array('status' => 'error','msj'=> 'No se pudo actualizar estatus '.$sql));

  }

  else{

  $sql_del="delete from h_tmp_pedido where no_pedido=$no_pedido and no_cliente='$cliente' and marca='$marca' and edo='$estado'";
	$result=$conexion->query($sql_del); 
	if($conexion->affected_rows>0)
	{ 
	  echo json_encode(array('status' => 'OK','msj'=> 'Detalle eliminado correctamente'));
	} 
	else
	{   
	  echo json_encode(array('status' => 'error','msj'=> 'No se pudo eliminar el detalle de la lista, intente mas tarde'.$sql_del));
	}

  }
?>