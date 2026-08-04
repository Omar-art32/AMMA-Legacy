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
            $clientes_conflicto = "'C9999','C0285','C0001','C0003','C0249'"; */

        $sql_conflicto = ($clientes_conflicto != "") ? " AND (paraje_vivero.id_cliente NOT IN ({$clientes_conflicto}) ) " : "";
    }
    // ---------------------------------------------------------------------------------------------------
    // ---------------------------------------------------------------------------------------------------

	$consulta = "SELECT paraje_vivero.id_paraje,paraje_vivero.id_cliente,paraje_vivero.constancia_vivero 
	FROM  paraje_vivero INNER JOIN constancias_vivero ON paraje_vivero.id_paraje = constancias_vivero.id_paraje 
	WHERE paraje_vivero.tipo=2 $sql_conflicto
	group by paraje_vivero.id_paraje";



	$registro=$conexion->query($consulta);
	$tabla = "";
	foreach ($registro as $row){



		$no_cliente="";
		$nombreCliente="";


		if($row['id_cliente']!=""){

		$cliente=$row['id_cliente'];
	    $strCliente = "SELECT clientes.no_cliente,clientes.nombre
					   from clientes 
					   where clientes.no_cliente='$cliente' ";



				   
	   $clientes= $conexion->query($strCliente);
	   $filaClientes = mysqli_fetch_array($clientes);

	   $no_cliente=$filaClientes['no_cliente'];
	   $nombreCliente=$filaClientes['nombre'];

	   } 


		$constancias = ($row["constancia_vivero"]!="")?'<div class=\"col-md-4\"> <a href=\"constancia/pdfConstanciaVivero/'.$row["constancia_vivero"].'\" target=\"_blank\" ><img width=\"35px\" src=\"images/pdf.svg\"></a></div>':'';
		$constancias .= '<div id=\"items_en_uso_vivero\" class=\"col-md-4\"><a href=\"#\" id=\"vivero_'.$row["id_paraje"].'\"><img width=\"35px\" src=\"images/exchange.svg\"></a></div>';
		$tabla.='{
			"paraje":"'.$row['id_paraje'].'",
			"cliente":"'.$no_cliente.'",
			"nombre":"'.$nombreCliente.'",
			"constancias":"'.$constancias.'"
		},';
	}
	//eliminamos la coma que sobra
	$tabla = substr($tabla,0, strlen($tabla) - 1);
	$conexion->close();
	//$conexion_remota->close();
	echo '{"data":['.$tabla.']}';	
?>