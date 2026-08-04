<?php
	include("../common/conexion.php");
	$tipo = $_POST['tipo'];
	$exito = 0;
	$id = "";
	if($tipo == "P") {
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
	} elseif($tipo == "V") {
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
	} 

	$conexion->close(); //cerramos la conexión
	if ($id != "") {
		$resp["exito"] = "1";
		$resp["id"] = $id;
	} else {
		$resp["exito"] = "0";
		$resp["id"] = $id;
	}
    echo json_encode($resp);
?> 
