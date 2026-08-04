<?php
include('../../common/conexion.php');
//obtenemos los parametros de la busqueda
$tipo_h=utf8_decode ($_POST['tipo_h']);
$client=utf8_decode ($_POST['cliente']);
//$client=substr($client,0,4);
$marca=utf8_decode ($_POST['marca']);
$serie=utf8_decode ($_POST['serie']);
//$cantidad=utf8_decode ($_POST['cantidad']);
if($tipo_h=='G')
{
	$sql_bus="select existencias, fol_ini, fol_fin from h_existencias where no_cliente='--' and marca='--' and serie='-'";
}
else if($tipo_h=='P')
{
$sql_bus="select existencias, fol_ini, fol_fin from h_existencias where no_cliente='$client' and marca='$marca' and serie='$serie'";
}
$result=$conexion->query($sql_bus);
// Ahora comprobaremos que todo ha ido correctamente
if($result==false)
{ 
  echo json_encode(array('status' => 'error','msj'=> 'Disculpe ha ocurrido un error, intente mas tarde'));
} 
else
{ 
  $tot=$result->num_rows;
  if($tot==1)
  {
	$row=$result->fetch_row();
	$mto_existe=trim($row[0]);
	$f_ini=trim($row[1]);
	$f_fin=trim($row[2]);
	//echo json_encode(array('status' => 'correcto','msj'=> 'Cantidad Viable'));
	if($mto_existe>0)
	{
	echo json_encode(array('status' => 'correcto','msj'=> 'Existencias Viables','mto'=> $mto_existe,'fini'=> $f_ini,'ffin'=> $f_fin));
	}
	else
	{
		echo json_encode(array('status' => 'error','msj'=> 'Inventario vacio'));
	}
	
  }
  else
  {
	echo json_encode(array('status' => 'error','msj'=> 'No se tienen registros de hologramas de esta MARCA'.$sql_bus));
  }
}		 
?>
 




