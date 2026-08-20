<?php

// Ocultar avisos/deprecated para evitar corromper la salida JSON
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', '0');

header('Content-Type: application/json; charset=utf-8');

include('../../common/conexion.php');
$arr_let=array('A','B','C','D','E','F','G','H','I','J','K','L');
// Reemplazo de utf8_decode por mb_convert_encoding
$client_raw = $_POST['cliente'] ?? '';
$client     = mb_convert_encoding($client_raw, 'ISO-8859-1', 'UTF-8');
$client=substr($client,0,4);
$c_mar=0;
//$usr=$_POST['user'];
//$fecha = date("Y-m-d H:i:s" );
$sql="select cve_marca,marca from marcas where substr(no_cliente,1,4)='$client' group by cve_marca order by cve_marca asc";
$result=$conexion->query($sql);
// Ahora comprobaremos que todo ha ido correctamente
if($result==false)
{ 
  echo json_encode(array('status' => 'error','msj'=> 'Ha ocurrido un error, Intente mas tarde'));
} 
else
{ 
  $tot=$result->num_rows;
  $list_marcas="";
  if($tot>0)
  {
	$c_mar=0;
	$list_marcas='<p style="line-height:25px;">';
	while($row=$result->fetch_row())
	{
	  $cve=$row[0];
	  // Reemplazo de utf8_encode por mb_convert_encoding
    $marca = mb_convert_encoding($row[1] ?? '', 'UTF-8', 'ISO-8859-1');
	  $list_marcas.= "$cve - $marca <br>";
	  $c_mar++;
	}
	$list_marcas.="</p>";
  }
  else
  {
	$list_marcas.="Sin marcas previas";
	$c_mar=0; 
  }
  
  // Obtener la siguiente letra de forma segura si supera el índice del arreglo
  $next = $arr_let[$c_mar] ?? '';
  echo json_encode(array('status' => 'correcto','lista'=> $list_marcas,'next'=> $next));
}	


?>