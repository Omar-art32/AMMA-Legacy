<?php
	include("../common/conexion.php");
	$tipo = $_POST['tipo'];
	$exito = 0;
	$id = "";

	$consulta="SELECT SUBSTR(id_paraje,2,length(id_paraje)) id FROM paraje WHERE id = (SELECT MAX(id) FROM paraje) ORDER BY id DESC";
	$consultaid = $conexion->query($consulta);
	if ($consultaid->num_rows > 0){
		$id="P";
		while ($row = $consultaid->fetch_array(MYSQLI_ASSOC)){
			if($row['id'] > 0) 
				$id .=($row['id'] + 1); //concatenamos el los options para luego ser insertado en el HTML
			else
				$id .= 1;
		}
	}else
		$id="P1";

	return $id;
?> 
