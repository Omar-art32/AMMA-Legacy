<?php
	try{
		/*$servidor = "localhost";
		$basedatos = "siig";
		$usuario = "root";
		$contrasena = "";*/

		/*$servidor = "localhost";
		$basedatos = "siige";
		$usuario = "root";
		$contrasena = "MyCRMSql15";*/

		$servidor = "localhost";
		$basedatos = "amma";
		$usuario = "root";
		$contrasena = "";

		/*$servidor = "crmreg.db.11217162.hostedresource.com";
		$basedatos = "crmreg";
		$usuario = "crmreg";
		$contrasena = "CrM#bd2016JL";*/

		$conexion = new PDO("mysql:host=$servidor;dbname=$basedatos",$usuario,$contrasena,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		return $conexion;
	}
		catch (PDOException $e){
		die ("No se puede conectar a la base de datos". $e->getMessage());
	}
?>
