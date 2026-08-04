<?php
	/*include ("common/conexion.php");
	include('php/registro/conexion_remota.php');*/
	include("../common/conexion.php");
	$conexion->set_charset("utf8");
	
	$idus = (isset($_POST['idus']))?$_POST['idus']:0;
    // ---------------------------------------------------------------------------------------------------
    // ---------------------------------------------------------------------------------------------------
    $sql_conflicto = "";
    if($idus > 0 ){
        $usuario_solicita = $_POST['idus'];

        $conflicto_intereses = $conexion->prepare("SELECT getConflictoIntereses(?)");
        if (!$conflicto_intereses) 
            throw new Exception("ERROR AL CONSULTAR CONFLICTO (ERR:001)");
        $conflicto_intereses->bind_param("i", $usuario_solicita);
        if (!$conflicto_intereses->execute()) 
            throw new Exception("ERROR AL CONSULTAR CONFLICTO (ERR:002)");
        $conflicto_intereses->store_result();
        $conflicto_intereses->bind_result($clientes_conflicto);
        $conflicto_intereses->fetch();
        $conflicto_intereses->close();

        /**MODIFICAR CONSULTA DEACUERDO A LAS NECECIDADES 
         * LA VARIABLE $clientes_conflicto TRAE LOS CLIENTES EN EL SIGUIENTE FORMATO 'C9999','C9998'
        */
        /*if($usuario_solicita == 1)
            $clientes_conflicto = "'C9999','C9998','C0001','C0003','C0249'"; */

        $sql_conflicto = ($clientes_conflicto != "") ? " WHERE (id_cliente NOT IN ({$clientes_conflicto}) ) " : "";
    }
    // ---------------------------------------------------------------------------------------------------
    // ---------------------------------------------------------------------------------------------------

	$consulta="SELECT paraje.id_cliente,paraje.id_paraje,paraje.constancia_extracciones 
	from  paraje  
	inner join cextracciones on (cextracciones.id_paraje=paraje.id_paraje COLLATE utf8_general_ci) 
	$sql_conflicto 
	group by paraje.id_paraje order by paraje.id DESC";

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
		
			$clientes= $conexion->query($strCliente);
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
		
		// CHECAR GUÍAS 
		####################################################################################
		/*$cadenaSqlGO = "SELECT *
		from cextracciones c
		WHERE id_paraje = '".$row['id_paraje']."' AND id_extraccion IN (SELECT no_guia from historial_extraccion_verificadores) ";
		$sqlCountGO = $conexion->prepare($cadenaSqlGO);
		
		$sqlCountGO->store_result();
		$totalResGO = $sqlCountGO->num_rows; // cuenta el total de registros devueltos

		$cadenaSqlG = "SELECT * from cextracciones WHERE id_paraje = '".$row['id_paraje']."' ";
		$sqlCountG= $conexion->prepare($cadenaSqlG);
		
		$sqlCountG->store_result();
		$totalResG = $sqlCountG->num_rows; // cuenta el total de registros devueltos*/
		// ####################################################################################

		
		$agregar = '';
		$tabla.='{
			"paraje":"'.$row['id_paraje'].'",
			"cliente":"'.$no_cliente.'",
			"nombre":"'.$nombreCliente.'",
			"constancias":"'.$constancias.'",
			"opciones":"'.$agregar.'"
		},';
	}

	//VIVEROS
	/*$consulta="SELECT pv.id_cliente,pv.id_paraje,pv.constancia_extracciones 
	from  paraje_vivero pv 
	inner join cextracciones on (cextracciones.id_paraje=pv.id_paraje COLLATE utf8_general_ci) 
	$sql_conflicto 
	group by pv.id_paraje order by pv.id ASC; ";

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
   
	   $clientes= $conexion->query($strCliente);
	   $filaClientes = mysqli_fetch_array($clientes);

	   $no_cliente=$filaClientes['no_cliente'];
	   $nombreCliente=$filaClientes['nombre'];

	   }


		$id_paraje = "'".$row['id_paraje']."'";
		$no_cliente_s = "'".$no_cliente."'";
		$nombre_s = "'".$nombreCliente."'";
		$constancias = ($row["constancia_extracciones"]!="")?'<div class=\"col-md-4\"> <a href=\"constancia/pdfConstanciaExtraccion/'.$row["constancia_extracciones"].'\" target=\"_blank\"><img width=\"35px\" src=\"images/pdf.svg\"></a></div>':'';
		$constancias .= '<div id=\"items_en_uso_extraccionesv\" class=\"col-md-4\"> <a href=\"#\" id=\"extracciones_'.$row["id_paraje"].'\"><img width=\"35px\" src=\"images/exchange.svg\"></a></div>';
		$agregar = '<a href=\"\" title=\"Constancias\" class=\"btn btn-primary\" onclick=\"constancias('.$id_paraje.','.$no_cliente_s.','.$nombre_s.')\" data-toggle=\"modal\" data-target=\"#exampleModalCenter\"><span class=\"glyphicon glyphicon-plus\"></span></a>';
		$tabla.='{
			"paraje":"'.$row['id_paraje'].'",
			"cliente":"'.$no_cliente.'",
			"nombre":"'.$nombreCliente.'",
			"constancias":"'.$constancias.'",
			"opciones":"'.$agregar.'"
		},';
	}*/

	//eliminamos la coma que sobra
	$tabla = substr($tabla,0, strlen($tabla) - 1);
	$conexion->close();
	//$conexion_remota->close();
	echo '{"data":['.$tabla.']}';	
?>