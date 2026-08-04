<?php
 
//Configuracion de la conexion a base de datos
  $bd_host = "localhost"; 
  $bd_usuario = "root"; 
  $bd_password = ""; 
  $bd_base = "parajes"; 
 
$con = mysqli_connect($bd_host, $bd_usuario, $bd_password); 
	mysqli_select_db($con,$bd_base); 
 
//variables POST
  $id=$_POST['id_paraje'];
  $local=$_POST['id_localidad'];
  $state=$_POST['id_cliente'];
  $paraje=$_POST['paraje'];
  $lat=$_POST['lat'];
  $lng=$_POST['lng'];
  $poligono=$_POST['poligono'];
  $tenencia=$_POST['tenencia'];
  $superficie=$_POST['superficie'];
  $archivo=$_POST['docpro'];
  $referencia=$_POST['referencia'];
  $usufruto=$_POST['usufruto'];
  $fecha=$_POST['fecha'];
  $referencia2=$_POST['nombrep'];
  //aqui empieza otro 
  $sc=$_POST['sc'];
  $sm=$_POST['sm'];
  $especie=$_POST['plantas'];
  $edad=$_POST['edad'];
  
//registra los datos del empleados
  $sql="INSERT INTO paraje (id_paraje,id_localidad,id_cliente,paraje,lat,lng,poligono,tenencia,superficie,docpro,referencia,usufruto,fecha,nombrep) VALUES ('','$local', '$state','$paraje','$lat','$lng','$poligono','$tenencia','$superficie','$archivo','$referencia','$usufruto','$fecha','$referencia2')";
mysqli_query($con,$sql) or die('Error. '.mysqli_error());
?>