<?php
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
 




