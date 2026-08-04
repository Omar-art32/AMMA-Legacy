<?php

function dameEstado(){
	include("conexion.php");
	$resultado = false;
	$consulta = "SELECT * FROM estados WHERE dom=1";
	
	//$conexion = conectaBaseDatos();
	$sentencia = $conexion->prepare($consulta);
	
	try {
		if(!$sentencia->execute()){
			print_r($sentencia->errorInfo());
		}
		$resultado = $sentencia->fetchAll();
		//$resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
		$sentencia->closeCursor();
	}
	catch(Exception $e){
		echo "Error al ejecutar la sentencia: \n";
			print_r($e->getMessage());
	}
	
	return $resultado;

}

function dameMunicipio($estado = ''){
	include("conexion.php");
	$resultado = false;
	$consulta = "SELECT * FROM municipios ";
	
	if($estado != '')
		$consulta .= " WHERE estado = :estado " ;
	
	$consulta .= " ORDER BY nombre ASC";
	//$conexion = conectaBaseDatos();
	$sentencia = $conexion->prepare($consulta);
	$sentencia->bindParam('estado',$estado);
	
	try {
		if(!$sentencia->execute()){
			print_r($sentencia->errorInfo());
		}
		$resultado = $sentencia->fetchAll();
		//$resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
		$sentencia->closeCursor();
	}
	catch(Exception $e){
		echo "Error al ejecutar la sentencia: \n";
			print_r($e->getMessage());
	}
	
	return $resultado;
	
	
}

function dameLocalidad($municipio = ''){
	include("conexion.php");
	$resultado = false;
	$consulta = "SELECT * FROM localidades ";
	
	if($municipio != '')
		$consulta .= " WHERE MunicipioID = :municipio ";	
	$consulta .= " ORDER BY localidad ASC";
	//$conexion = conectaBaseDatos();
	$sentencia = $conexion->prepare($consulta);
	$sentencia->bindParam('municipio',$municipio);
	//$sentencia->bindParam('estado',$estado);
	
	try {
		if(!$sentencia->execute()){
			print_r($sentencia->errorInfo());
		}
		$resultado = $sentencia->fetchAll();
		//$resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
		$sentencia->closeCursor();
	}
	catch(Exception $e){
		echo "Error al ejecutar la sentencia: \n";
			print_r($e->getMessage());
	}
	
	return $resultado;
}


?>