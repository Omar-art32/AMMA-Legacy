<?php
	include("../common/conexion.php");
	
	$consulta="SELECT SUBSTR(id_paraje,2,length(id_paraje)) id FROM paraje_vivero WHERE id = (SELECT MAX(id) FROM paraje_vivero) ORDER BY id DESC";
	$consultaid = $conexion->query($consulta);
	if ($consultaid->num_rows > 0){
		$id="V";
		while ($row = $consultaid->fetch_array(MYSQLI_ASSOC)){
			if($row['id'] > 0) 
				$id .=($row['id'] + 1); //concatenamos el los options para luego ser insertado en el HTML
			else
				$id .= 1;
		}
	}else{
		$id="V1";
		//echo "No hubo resultados";
	}
	$conexion->close(); //cerramos la conexión
?> 
