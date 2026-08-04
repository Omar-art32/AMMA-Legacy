<?php
	//include('php/registro/conexion.php');
	include("../common/conexion.php");
	$conexion->set_charset("utf8");
	$sql="SELECT * FROM comun  c INNER JOIN especie e ON e.id_especie = c.id_especie WHERE status = 1 ORDER BY c.nombre";
	$result = $conexion->query($sql);
	if ($result->num_rows > 0){
	    $combobit="";
	    while ($row = $result->fetch_array(MYSQLI_ASSOC)){
	 	   $combobit .="\t<option value=\"".$row['id_comun']."\">".$row['nombre']." - ".$row['genespecie']." ".$row['variante']." </option>\n";
	    }
	}else{
		echo "No hubo resultados";
	}
	$conexion->close();
?>
