<?php
	include ("common/conexion.php");
	include('php/registro/conexion_remota.php');
	$conexion->set_charset("utf8");
	$conexion_remota->set_charset("utf8");
	/*$consulta = "SELECT paraje.id_paraje,clientes.no_cliente,clientes.nombre,paraje.constancia_extracciones FROM clientes INNER JOIN paraje ON clientes.no_cliente = paraje.id_cliente INNER JOIN constancias ON paraje.id_paraje = constancias.id_paraje WHERE paraje.poligono IS NOT NULL AND paraje.tipo = 1 GROUP BY paraje.id_paraje";*/
	$consulta="SELECT paraje.id_cliente,paraje.id_paraje,paraje.constancia_extracciones, cextracciones.id_extraccion from  paraje  inner join cextracciones on cextracciones.id_paraje=paraje.id_paraje group by paraje.id_paraje";

	$registro=$conexion->query($consulta);
	$tabla = "";
	foreach ($registro as $row){


		$no_cliente="";
		$nombreCliente="";

		if($row['id_cliente']!=""){

		$cliente=$row['id_cliente'];
	    $strCliente = "SELECT clientes.no_cliente,clientes.nombre
					   from clientes 
					   where clientes.no_cliente='$cliente'";
   
	   $clientes= $conexion_remota->query($strCliente);
	   $filaClientes = mysqli_fetch_array($clientes);

	   $no_cliente=$filaClientes['no_cliente'];
	   $nombreCliente=$filaClientes['nombre'];

	   }


		$id_paraje = "'".$row['id_paraje']."'";
		$no_cliente_s = "'".$no_cliente."'";
		$nombre_s = "'".$nombreCliente."'";
		$constancias = ($row["constancia_extracciones"]!="")?'<div class=\"col-md-4\"> <a href=\"constancia/pdfConstanciaExtraccion/'.$row["constancia_extracciones"].'\" target=\"_blank\"><img width=\"35px\" src=\"images/pdf.svg\"></a></div>':'';
		$constancias .= '<div id=\"items_en_uso_extracciones\" class=\"col-md-4\"> <a href=\"#\" id=\"extracciones_'.$row["id_paraje"].'\"><img width=\"35px\" src=\"images/exchange.svg\"></a></div>';
		$agregar = '<a href=\"\" title=\"Constancias\" class=\"btn btn-primary\" onclick=\"constancias('.$id_paraje.','.$no_cliente_s.','.$nombre_s.')\" data-toggle=\"modal\" data-target=\"#exampleModalCenter\"><span class=\"glyphicon glyphicon-plus\"></span></a>';
		$tabla.='{
			"paraje":"'.$row['id_paraje'].'",
                        "noguia":"'.$row['id_extraccion'].'",
			"cliente":"'.$no_cliente.'",
			"nombre":"'.$nombreCliente.'",
			"constancias":"'.$constancias.'",
			"opciones":"'.$agregar.'"
		},';
	}
	//eliminamos la coma que sobra
	$tabla = substr($tabla,0, strlen($tabla) - 1);
	$conexion->close();
	$conexion_remota->close();
	echo '{"data":['.$tabla.']}';	
?>