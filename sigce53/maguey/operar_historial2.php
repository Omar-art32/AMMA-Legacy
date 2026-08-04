<?php
	$ban = true;
	include ("common/conexion.php");
	include('php/registro/conexion_remota.php');


	$sql="SELECT * FROM paraje WHERE paraje.id_paraje = ".$_POST["datos"]["id_paraje"];
	$result = $conexion->query($sql);
	foreach ($result as $value){
		$constancia = "";
		
		$no_cliente="";
		$nombreCliente="";

		if($value['id_cliente']!=""){

		$cliente=$value['id_cliente'];
	    $strCliente = "SELECT clientes.no_cliente,clientes.nombre
					   from clientes 
					   where clientes.no_cliente=$cliente";
   
	   $clientes= $conexion_remota->query($strCliente);
	   $filaClientes = mysqli_fetch_array($clientes);

	   $no_cliente=$filaClientes['no_cliente'];
	   $nombreCliente=$filaClientes['nombre'];

	   }


		$insert="INSERT INTO historial_cextracciones (id_paraje,no_cliente,nombre,cantidad,fecha) VALUES ('".$_POST["datos"]["id_paraje"]."','".$no_cliente."','".$nombreCliente."','".$_POST["datos"]["cantidad"]."','".date('Y-m-d')."')";
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

	
	$conexion->close();

	
?>