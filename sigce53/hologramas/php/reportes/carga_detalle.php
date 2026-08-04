<?php
   	$clave="";
    $respuesta=array();
    // Se crea la conexión a la base de datos
    include('../../../common/conexion.php');
	
  
		$clave=$_POST['clave'];
		$consulta = "select h_salidas.id_salidas, h_salidas.id_recibo, h_salidas.anio_rcbo, h_salidas.no_cliente, h_salidas.marca cve, marcas.marca, h_salidas.serie, h_salidas.solicitud, h_salidas.fecha_entr, h_salidas.destino, h_salidas.fi1, h_salidas.ff1, h_salidas.se1 from h_salidas left join marcas on marcas.no_cliente=h_salidas.no_cliente and marcas.cve_marca=h_salidas.marca where h_salidas.linea=0 and h_salidas.id_salidas=$clave order by h_salidas.id_recibo asc limit 1";
   
     $result = $conexion->query($consulta);
     $i=0;
     while( $fila = $result->fetch_assoc() ) {
		
		$recibo='AR'.str_pad($fila["id_recibo"],4,'0',STR_PAD_LEFT).'/'.$fila["anio_rcbo"];
		echo json_encode(array('status' => 'OK','id_recibo'=> $fila["id_recibo"],'id_salidas'=> $fila["id_salidas"],'anio_recibo'=> $fila["anio_rcbo"],'no_cliente'=> $fila["no_cliente"],'cve_marca'=> $fila["cve"],'marca'=> utf8_encode($fila["marca"]),'serie'=> $fila["serie"],'solicitud'=> $fila["solicitud"],'fecha_e'=> $fila["fecha_entr"],'destino'=> $fila["destino"],'fi1'=> $fila["fi1"],'ff1'=> $fila["ff1"],'se1'=> $fila["se1"]));
		
    }
	// La respuesta se regresa como json
   
?>