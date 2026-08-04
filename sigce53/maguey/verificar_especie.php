<?php
//include ("php/registro/conexion.php");
include("../common/conexion.php");
$especie=$_POST['especie'];

$id_comun= $especie;
//$id_especie= $dato[1];
//$usr=$_POST['user'];
//$fecha = date("Y-m-d H:i:s" );
$sql="SELECT * FROM `comun` WHERE id_comun='$id_comun' AND status = 1";
$result=$conexion->query($sql);
// Ahora comprobaremos que todo ha ido correctamente
if($result==false)
{ 
  $msj="No se ha podido consultar el registro";
  echo json_encode(array('status' => 'error','msj'=> $msj));
} 
else
{ 
  $tot=$result->num_rows;
  if($tot>0)
  {
	$row=$result->fetch_row();
	echo json_encode(array('status' => 'correcto','valido'=> 'si'));
  }
  else
  {
	echo json_encode(array('status' => 'correcto','valido'=> 'no'));
  }
}

$conexion->close(); 	
?>
 




