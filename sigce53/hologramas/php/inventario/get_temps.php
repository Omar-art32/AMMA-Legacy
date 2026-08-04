<?php
$no_pedido=$_POST['no_pedido'];
include('../../../common/conexion.php');
$lista_req=array();
$sql_busca="SELECT h_tmp_pedido.no_pedido,h_tmp_pedido.no_cliente,h_tmp_pedido.marca cve,marcas.marca, h_tmp_pedido.edo, h_tmp_pedido.serie,h_tmp_pedido.tipo,h_tmp_pedido.fi,h_tmp_pedido.ff,h_tmp_pedido.cantidad,h_tmp_pedido.pagado,h_tmp_pedido.urgente,h_tmp_pedido.id_row,h_tmp_pedido.holograma FROM h_tmp_pedido left join marcas on marcas.no_cliente=h_tmp_pedido.no_cliente and marcas.cve_marca=h_tmp_pedido.marca WHERE no_pedido='$no_pedido' order by fecha asc";
$result=$conexion->query($sql_busca);
if($result==false)
{
 echo json_encode(array('status' => 'Error','msj'=> 'Error al realizar el registro'.$sql_busca));
}		
else
{ 
 if($result->num_rows>0)
  {
	 while($r = mysqli_fetch_assoc($result)) 
	 {
	  $lista_req[] =$r;
	 }
	  $lista_req=utf8_converter($lista_req);
	  
	echo json_encode(array('status' => 'OK','lista'=> $lista_req));	
  }
  else
  {
	echo json_encode(array('status' => 'Error','msj'=> 'No se encontraron registros'.$sql_busca));  
  }
}
function utf8_converter($array)
{
  array_walk_recursive($array, function(&$item, $key){
	  if(!mb_detect_encoding($item, 'utf-8', true)){
			  $item = utf8_encode($item);
	  }
  }); 
  return $array;
}
?>
 




