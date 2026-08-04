<?php
  include('../../../common/conexion.php');
  $no_pedido=utf8_decode($_POST['no_pedido']);  
  $cliente=utf8_decode ($_POST['no_cliente']);
  $marca=utf8_decode ($_POST['marca']);
  $estado=utf8_decode ($_POST['estado']);
  $sql_pag="update h_tmp_pedido set pagado=1 where no_pedido=$no_pedido and no_cliente='$cliente' and marca='$marca' and edo='$estado'";
	$result=$conexion->query($sql_pag); 
	if($conexion->affected_rows>0)
	{ 
	  echo json_encode(array('status' => 'OK','msj'=> 'Estatus de pago Actualizado'));
	} 
	else
	{   
	  echo json_encode(array('status' => 'error','msj'=> 'NO se puede actualizar el estatus de pago'.$sql_pag));
	}
?>