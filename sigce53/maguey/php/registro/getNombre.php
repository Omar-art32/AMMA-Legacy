<?php
include('conexion.php');
$client=utf8_decode ($_POST['cliente']);
$client=substr($client,0,4);
//$usr=$_POST['user'];
//$fecha = date("Y-m-d H:i:s" );
$sql="select nombre from clientes where substr(no_cliente,1,4)='$client' and nombre!='--'";
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
  $cbo="";
  if($tot==1)
  {
	$row=$result->fetch_row();
	$nombre=utf8_encode($row[0]);
	echo json_encode(array('status' => 'correcto','nombre'=> $nombre));
  }
  else
  {
	$msj="No se tiene registro";
	echo json_encode(array('status' => 'error','msj'=> $msj));
  }
}	
$conexion->close(); 	 

?>
 




