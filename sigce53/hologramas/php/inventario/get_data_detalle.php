<?php
include('../../../common/conexion.php');
//obtenemos los parametros de la busqueda
$folio_sh_det=$_POST['folio'];
//$cantidad=$_POST['cantidad'];
$no_pedido=0;
//OBTENEMOS EL NUMERO DE PEDIDO ACTUAL
$sql_tmp=$sql="select if(max(no_pedido) is null,0,max(no_pedido))  from h_tmp_pedido";
$res_tmp=$conexion->query($sql_tmp);
if($res_tmp && $res_tmp->num_rows==1)
{
	$row_tmp=$res_tmp->fetch_row();
	$no_pedido=$row_tmp[0];
	if($no_pedido==0)
	{
		$sql="select if(max(no_pedido) is null,0,max(no_pedido))  from h_pedidos";
		$result=$conexion->query($sql);
		// Ahora comprobaremos que todo ha ido correctamente
		if($result && $result->num_rows==1)
		{
			$row=$result->fetch_row();
			$no_pedido=$row[0]+1;
		}
	}
}

//FIN OBTENER EL NO PEDIDO
$cliente="";
$edo="";
$marca="";
$nom_marca="";
$serie="";
$tipo=0;
$cantidad=0;
$urgente=0;
//variables para guardas los folios
$mto_existe=0;
$f_ini=0;
$f_fin=0;
$msj="";
$sql_detalle="SELECT solicitudes.fecha, sh_pedidos.no_cliente, sh_detalle.id_solicitud, sh_detalle.marca cve_marca,
marcas.marca, marcas.serie, sh_detalle.tipo, sh_detalle.edo, sh_detalle.cantidad, sh_detalle.urgente,
sh_detalle.importe,sh_detalle.status
FROM sh_pedidos
INNER JOIN solicitudes ON solicitudes.id=sh_pedidos.id_solicitud AND solicitudes.tipo=2
INNER JOIN sh_detalle ON sh_detalle.id_solicitud=sh_pedidos.id_solicitud
INNER JOIN marcas ON marcas.no_cliente=sh_pedidos.no_cliente AND marcas.cve_marca=sh_detalle.marca
where sh_detalle.id=$folio_sh_det";

$res_detalle=$conexion->query($sql_detalle);
if($res_detalle && $res_detalle->num_rows==1)
{
	$row=$res_detalle->fetch_assoc();
	$cliente=$row['no_cliente'];
	$edo=$row['edo'];
	$marca=$row['cve_marca'];
	$nom_marca=$row['marca'];
	$serie=$row['serie'];
	$tipo=$row['tipo'];
	$cantidad=$row['cantidad'];
	$urgente=$row['urgente'];
	//AHORA OBTENEMOS LOS ULTIMOS FOLIOS PEDIDOS O REGISTRADOS EN INVENTARIO
	$sql_tmp_pedido="Select fi,ff,cantidad from h_tmp_pedido where no_cliente='{$cliente}' and marca='{$marca}' and serie='{$serie}' order by ff desc limit 1";
	$res_tmp_pedido=$conexion->query($sql_tmp_pedido);
	if($res_tmp_pedido->num_rows>0)
	{
		  $row=$res_tmp_pedido->fetch_row();
		  $f_ini=trim($row[0]);
		  $f_fin=trim($row[1]);
		  $mto_existe=trim($row[2]);
		  $msj="Pedido en cola";
		  //echo json_encode(array('status' => 'correcto','msj'=> $msj,'mto'=> $mto_existe,'fini'=> $f_ini,'ffin'=> $f_fin));
	}
	else
	{
		$sql_pedidos="Select fi,ff,cantidad from h_pedidos where no_cliente='{$cliente}' and marca='{$marca}' and serie='{$serie}' order by ff desc limit 1";

		$res_pedidos=$conexion->query($sql_pedidos);
		if($res_pedidos->num_rows>0)
		{
			$row=$res_pedidos->fetch_row();
			$f_ini=trim($row[0]);
			$f_fin=trim($row[1]);
			$mto_existe=trim($row[2]);
			$msj="Ultimo pedido";
			//echo json_encode(array('status' => 'correcto','msj'=> 'Cantidad Viable'));
			//echo json_encode(array('status' => 'correcto','msj'=> $msj,'mto'=> $mto_existe,'fini'=> $f_ini,'ffin'=> $f_fin));
		}
		else
		{
			$sql_entradas="select fol_ini, fol_fin, cantidad from h_entradas where no_cliente='{$cliente}' and marca='{$marca}' and serie='{$serie}' order by fol_fin desc limit 1";
			$res_entradas=$conexion->query($sql_entradas);
			if($res_entradas->num_rows>0)
			{
				$row=$res_entradas->fetch_row();
				$f_ini=trim($row[0]);
				$f_fin=trim($row[1]);
				$mto_existe=trim($row[2]);
				$msj="Ultima Entrada";
				//echo json_encode(array('status' => 'correcto','msj'=> 'Cantidad Viable'));
				//echo json_encode(array('status' => 'correcto','msj'=> $msj,'mto'=> $mto_existe,'fini'=> $f_ini,'ffin'=> $f_fin));
			}
			else
			{
				$msj="Sin registros de entradas o pedidos";
				//echo json_encode(array('status' => 'error','msj'=> 'No haya registros de entradas ni pedidos','mto'=> '0','fini'=> '0','ffin'=> '0','ne'=> '0'));
			}//END IF res_entradas
		}//END IF res_pedidos
	}//END IF res_tmp_pedido
	if($no_pedido>0)
	{
		$f_ini=$f_fin+1;
		$f_fin=$f_fin+$cantidad;
		//echo $no_pedido;
	  echo json_encode(array('status' => 'OK','msj'=> $msj, 'folio_det'=> $folio_sh_det, 'no_pedido'=> $no_pedido, 'cliente'=> $cliente, 'edo'=> utf8_encode($edo), 'marca'=> $marca, 'nom_marca'=> utf8_encode($nom_marca), 'serie'=> $serie, 'tipo'=> $tipo, 'cantidad'=> $cantidad, 'urgente'=> $urgente,'mto'=> $mto_existe,'fini'=> $f_ini,'ffin'=>$f_fin));
	}
	else
	{
		echo json_encode(array('status' => 'error','msj'=> 'No se pudo obtener el numero de pedido'));
	}
}//END IF res_detalle
else
{
	echo json_encode(array('status' => 'error','msj'=> $sql_detalle));
}
?>
