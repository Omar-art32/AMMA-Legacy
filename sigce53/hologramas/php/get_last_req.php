<?php
include(__DIR__ . '/../../../common/conexion.php');
//obtenemos los parametros de la busqueda
$tipo_h=mb_convert_encoding ($_POST['tipo_h'], 'ISO-8859-1');
$client=mb_convert_encoding ($_POST['cliente'], 'ISO-8859-1');
$client=substr($client,0,4);
$marca=mb_convert_encoding ($_POST['marca'], 'ISO-8859-1');
$serie=mb_convert_encoding ($_POST['serie'], 'ISO-8859-1');
//$cantidad=utf8_decode ($_POST['cantidad']);
$sql_tmp_pedido="Select fi,ff,cantidad from h_tmp_pedido where no_cliente=$client and marca='{$marca}' and serie='{$serie}' order by ff desc limit 1";
$res_tmp_pedido=$conexion->query($sql_tmp_pedido);
if($res_tmp_pedido->num_rows>0)
{	
	  $row=$res_tmp_pedido->fetch_row();
	  $f_ini=trim($row[0]);
	  $f_fin=trim($row[1]);
	  $mto_existe=trim($row[2]);
	  $msj="Pedido en cola";
	  //echo json_encode(array('status' => 'correcto','msj'=> 'Cantidad Viable'));
	  echo json_encode(['status' => 'correcto','msj'=> $msj,'mto'=> $mto_existe,'fini'=> $f_ini,'ffin'=> $f_fin]);	
}
else
{
	$sql_pedidos="Select fi,ff,cantidad from h_pedidos where no_cliente=$client and marca='{$marca}' and serie='{$serie}' order by ff desc limit 1";
	$res_pedidos=$conexion->query($sql_pedidos);
	if($res_pedidos->num_rows>0)
	{
		$row=$res_pedidos->fetch_row();
		$f_ini=trim($row[0]);
		$f_fin=trim($row[1]);
		$mto_existe=trim($row[2]);
		$msj="Pedido en proceso";
		//echo json_encode(array('status' => 'correcto','msj'=> 'Cantidad Viable'));
		echo json_encode(['status' => 'correcto','msj'=> $msj,'mto'=> $mto_existe,'fini'=> $f_ini,'ffin'=> $f_fin]);		
	}
	else
	{
		$sql_entradas="select fol_ini, fol_fin, cantidad from h_entradas where no_cliente='$client' and marca='$marca' and serie='$serie' order by fol_fin desc limit 1";
		$res_entradas=$conexion->query($sql_entradas);
		if($res_entradas->num_rows>0)
		{
			$row=$res_entradas->fetch_row();
			$f_ini=trim($row[0]);
			$f_fin=trim($row[1]);
			$mto_existe=trim($row[2]);
			$msj="Ultima Entrada";
			//echo json_encode(array('status' => 'correcto','msj'=> 'Cantidad Viable'));
			echo json_encode(['status' => 'correcto','msj'=> $msj,'mto'=> $mto_existe,'fini'=> $f_ini,'ffin'=> $f_fin]);		
		}
		else
		{
			echo json_encode(['status' => 'error','msj'=> 'No haya registros de entradas ni pedidos','mto'=> '0','fini'=> '0','ffin'=> '0','ne'=> '0']);
		}	
	}	
}
?>
 




