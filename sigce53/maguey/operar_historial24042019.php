<?php
	$ban = true;
	include ("common/conexion.php");
	$sql="SELECT * FROM cextracciones WHERE cextracciones.id_paraje = ".$_POST["datos"]["id_paraje"];
	$result = $conexion->query($sql);
	foreach ($result as $value){
		$constancia = $value["constancia"];
		echo $constancia;
		$insert="INSERT INTO historial_cextracciones (id_paraje,no_cliente,nombre,cantidad,fecha) VALUES ('".$_POST["datos"]["id_paraje"]."','".$_POST["datos"]["no_cliente"]."','".$_POST["datos"]["nombre"]."','".$_POST["datos"]["cantidad"]."','".date('Y-m-d')."')";
		$result_b=$conexion->query($insert);
		if($result_b){
			for($i=0; $i<$_POST["datos"]["cantidad"]; $i++){
				$insert="INSERT INTO cextracciones (id_paraje,status,fecha,constancia) VALUES ('".$_POST["datos"]["id_paraje"]."','1','".date('Y-m-d')."','".$constancia."')";
				$result_c=$conexion->query($insert);
				if(!$result_c){
					$ban = false;
				}
			}
		}else{
			$ban = false;
		}
		if($ban){
			print_r("Proceso realizado exitosamente");
			break;
		}else{
			throw new Exception("Error! ".$sql);
		}
	}
?>