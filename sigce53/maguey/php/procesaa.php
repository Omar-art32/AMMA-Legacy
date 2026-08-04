<?php
if(isset($_POST["clienteno"])) {
    $criterio = '<option value="0"> Elige un Predio</option>';

    include('registro/conexion.php');
    //include("../../common/conexion.php");
    $conexion->set_charset("utf8");
    $strConsulta="
	SELECT p.id_paraje, p.paraje, p.tipo 
	FROM paraje p
	INNER JOIN existenciaplanta ep on p.id_paraje = ep.id_paraje AND ep.existenciaplantas > 0
	WHERE id_cliente = '".$_POST["clienteno"]."' AND tipo= 1
	GROUP BY p.id_paraje
	";
    $result = $conexion->query($strConsulta);


    while( $fila = $result->fetch_array() )
    {
            $tipo=($fila["tipo"]==1)?'Predio':'Vivero';
            //$opciones.='<option value="'.$fila["id_paraje"].'">'.$fila["id_paraje"].' - '.$fila["paraje"].'</option>';
            $criterio.='<option value="'.$fila["id_paraje"].'-'.$fila["tipo"].'">'.$fila["paraje"].'('.$fila["id_paraje"].'-'.$tipo.')</option>';

    }

    echo $criterio;
} elseif (isset($_GET['term'])){
    include("../../common/conexion.php");
    $conexion->set_charset("utf8");
    $return_arr = array();
    $busca=$_GET['term'];
    $result = $conexion->query("SELECT p.id_paraje, p.paraje, p.tipo, p.id_cliente, 
        c.no_cliente, c.nombre 
        FROM paraje p INNER JOIN clientes c ON c.no_cliente = p.id_cliente 
        WHERE id_paraje LIKE '%".$_GET['term']."%' ");
    while($row = $result->fetch_array()) {
        $row_array['value'] = $row['id_paraje'];
        $row_array['paraje'] = $row['paraje'];
        $row_array['nocliente'] = $row['id_cliente'];
        $row_array['nombrec'] = $row['nombre'];
        $row_array['tipo'] = $row['tipo'];
        array_push($return_arr,$row_array);
    }
    echo json_encode($return_arr);
} elseif (isset($_GET['guia'])){
    include("../../common/conexion.php");
    $conexion->set_charset("utf8");
    $return_arr = array();
    $busca=$_GET['guia'];
    $result = $conexion->query("SELECT p.id_cliente, p.id_paraje, p.constancia_extracciones, 
        p.paraje, ce.id_extraccion, p.nombrep, p.tipo, c.no_cliente, c.nombre
    FROM paraje p 
    INNER JOIN cextracciones ce on ce.id_paraje = p.id_paraje
    INNER JOIN clientes c ON c.no_cliente = p.id_cliente 
    WHERE ce.id_extraccion LIKE '%".$_GET['guia']."%' ");
    
    while($row = $result->fetch_array()) {
        $row_array['value'] = $row['id_extraccion'];
        $row_array['id_paraje'] = $row['id_paraje'];
        $row_array['paraje'] = $row['paraje'];
        $row_array['nocliente'] = $row['id_cliente'];
        $row_array['nombrec'] = $row['nombre'];//$row['nombre'];
        $row_array['tipo'] = $row['tipo'];
        array_push($return_arr,$row_array);
    }
    echo json_encode($return_arr);
} elseif (isset($_POST['funcion'])){
    $respuesta = new stdClass();
    $idUs = $_POST['idUs'];
    $nopredio = $_POST['nopredio'];
    include("../../common/conexion.php");
    $conexion->set_charset("utf8");
    $msjQ = "";
    $sqlC = ""; 
    $inicial = 0; $limite = 9;
    if($_POST['funcion'] == "guardarAtributosI") {
        $valID = $_POST['valID'];
        $inicial = $valID; $limite = ($valID+1);
    }
    for($i = $inicial; $i < $limite; $i++) {
        $result = $conexion->query("
            SELECT *
            FROM parajes_atributos 
            WHERE id_paraje = '$nopredio' AND atributo = '$i' ");
        $numrows = mysqli_num_rows($result);
        if($numrows > 0 && $_POST["indicador".$i] != "") {
            $fila = $result->fetch_object();
            $sqlI = "UPDATE parajes_atributos SET fecha = NOW(), idUs = " . $idUs . ",";
            $indicador = $_POST["indicador".$i];
            if($fila->valor != $indicador || $fila->observaciones != $_POST["txt".$i]) {
                $sqlC .= $sqlI . " valor = '$indicador', observaciones = '". $_POST["txt".$i] . "'  WHERE id_paraje = '$nopredio' AND atributo = '$i'; ";
                $sqlN = $sqlI . " valor = '$indicador', observaciones = '". $_POST["txt".$i] . "'  WHERE id_paraje = '$nopredio' AND atributo = '$i'; ";
                if(!$conexion->query($sqlN))
                    $msjQ .= " Error en Consulta: " . $sqlN;
            }   
        } elseif($numrows == 0 && $_POST["indicador".$i] != "") {
            $sqlI = "INSERT INTO parajes_atributos (fecha, id_paraje, status, atributo, valor, observaciones, idUs) VALUES ";
            $indicador = $_POST["indicador".$i];
            $sqlC .= $sqlI. "(NOW(),".$nopredio.",'1',$i,'".$indicador ."','". $_POST["txt".$i] . "', ".$idUs."); ";
            $sqlN = $sqlI. "(NOW(),".$nopredio.",'1',$i,'".$indicador ."','". $_POST["txt".$i] . "', ".$idUs."); ";
            if(!$conexion->query($sqlN))
                $msjQ .= " Error en Consulta: " . $sqlN;
        }
    }
    if($msjQ == "") {
        $respuesta->status = '1';
        $respuesta->text = 'Registro Guardado';
    } else {
        $respuesta->status = '0';
        $respuesta->text = 'Error al Guardar o Actualizar Parámetros: ' . $msjQ;
    }
        
    echo json_encode($respuesta);
}

$conexion->close(); 
?>
