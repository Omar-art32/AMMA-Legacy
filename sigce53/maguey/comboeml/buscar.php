<?php
require_once("funciones.php");

if(isset($_POST['estado'])){
	
	$municipios = dameMunicipio($_POST['estado']);
	
	$html = "<option value=''>- Seleccione un Municipio -</option>";
	foreach($municipios as $indice => $registro){
		$html .= "<option value='".$registro['id']."'>".$registro['nombre']."</option>";
	}
	
	$respuesta = array("html"=>$html);
	echo json_encode($respuesta);
}

if(isset($_POST['municipio'])){
	
	$localidades= dameLocalidad($_POST['municipio']);
	
	$html = "<option value=''>- Seleccione una Localidad -</option>";
	foreach($localidades as $indice => $registro){
		$html .= "<option value='".$registro['id']."'>".$registro['localidad']."</option>";
	}
	
	$respuesta = array("html"=>$html);
	echo json_encode($respuesta);
}

?>