<?php
 
//Configuracion de la conexion a base de datos
  $bd_host = "localhost"; 
  $bd_usuario = "root"; 
  $bd_password = ""; 
  $bd_base = "parajes"; 
 
	$con = mysqli_connect($bd_host, $bd_usuario, $bd_password); 
	mysqli_select_db($con,$bd_base); 
 
//consulta todos los empleados
$sql=mysqli_query($con,"SELECT * FROM paraje");
?>
<table style="color:#000099;width:400px;">
	<tr style="background:#9BB;">
		<td>localidad</td>
		<td>cliente</td>
		<td>paraje</td>
	</tr>
<?php
  while($row = mysqli_fetch_array($sql)){
  echo "<tr>";
  	echo "<td>".$row['id_localidad']."</td>";
  	echo "<td>".$row['id_cliente']."</td>";
  	echo "<td>".$row['paraje']."</td>";
  	echo "</tr>";
  }
?>
</table>