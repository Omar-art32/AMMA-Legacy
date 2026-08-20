<?php
//  Escudo para evitar que advertencias o avisos de PHP 8 ensucien el JSON
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', '0');

// Definir encabezado de respuesta JSON
header('Content-Type: application/json; charset=utf-8');

include('../../../common/conexion.php');
include('../../../common/conexion.php');
//$usr=$_POST['user'];
$anio = date('y');
$sql="select if(max(id_recibo) is null,0,max(id_recibo))  from h_salidas where anio_rcbo=$anio";
$result=$conexion->query($sql);
// Ahora comprobaremos que todo ha ido correctamente
if($result==false)
{ 
  echo "<p><font color='red'>Disculpe ha ocurrido un error, intente mas tarde</font></p>";
} 
else
{ 
  $tot=$result->num_rows;
  if($tot==1)
  {
	$row=$result->fetch_row();
	echo json_encode(array('status' => 'correcto','n_rcbo'=> $row[0]));
	$conexion->close();
  }
  else
  {
	echo json_encode(array('status' => 'error','n_rcbo'=> 'na'));
  }
}		 

?>
 




