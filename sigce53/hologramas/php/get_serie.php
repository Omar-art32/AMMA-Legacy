<?php
include('../../common/conexion.php');
//obtenemos los parametros de la busqueda
$client=utf8_decode ($_POST['cliente']);
$marca=utf8_decode ($_POST['marca']);

$sql_bus="select serie from marcas where no_cliente='$client' and cve_marca='$marca'";
$result=$conexion->query($sql_bus);
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
	$serie=trim($row[0]);
    echo $serie;
  }
  else
  {
	//echo "<p><font color='red'>Aun no hay marcas registradas para este cliente.</font></p>";
	echo $sql_bus; 
  }
}		 
?>
 




