<?php
include('../../../common/conexion.php');
//obtenemos los parametros de la busqueda
$tipo_h=utf8_decode ($_POST['tipo_h']);
$client=utf8_decode ($_POST['cliente']);
$client=substr($client,0,4);
$marca=utf8_decode ($_POST['marca']);
$serie=utf8_decode ($_POST['serie']);
//$cantidad=utf8_decode ($_POST['cantidad']);
if($tipo_h=='G')
{
  $sql_bus="select fol_ini, fol_fin, cantidad from h_entradas where no_cliente='--' and marca='--' and serie='-' order by fol_fin desc limit 1";
}
else if($tipo_h=='P')
{
  $sql_bus="select fol_ini, fol_fin, cantidad from h_entradas where no_cliente='$client' and marca='$marca' and serie='$serie' order by fol_fin desc limit 1";
}
$result=$conexion->query($sql_bus);
// Ahora comprobaremos que todo ha ido correctamente
if($result==false)
{ 
  echo json_encode(array('status' => 'error','msj'=> 'No se pudo ejecutar la consulta','ne'=>'1'));
} 
else
{ 
  $tot=$result->num_rows;
  if($tot==1)
  {
	$row=$result->fetch_row();
	$f_ini=trim($row[0]);
	$f_fin=trim($row[1]);
	$mto_existe=trim($row[2]);
	//echo json_encode(array('status' => 'correcto','msj'=> 'Cantidad Viable'));
	echo json_encode(array('status' => 'correcto','msj'=> 'Existencia Actual','mto'=> $mto_existe,'fini'=> $f_ini,'ffin'=> $f_fin));
	
  }
  else
  {
	echo json_encode(array('status' => 'error','msj'=> 'No se tienen entradas de hologramas de esta MARCA','ne'=>'0'));
  }
}		 
?>
 




