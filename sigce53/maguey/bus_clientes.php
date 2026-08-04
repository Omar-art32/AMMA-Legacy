<?php
//include('php/registro/conexion.php');
//include('php/registro/conexion_remota.php');
include("../common/conexion.php");
$conexion->set_charset("utf8");
if (isset($_GET['term'])){
	$return_arr = array();
    $busca=$_GET['term'];
    $idus = (isset($_GET['idus']))?$_GET['idus']:0;
    // ---------------------------------------------------------------------------------------------------
    // ---------------------------------------------------------------------------------------------------
    $sql_conflicto = "";
    if($idus > 0 ){
    	$usuario_solicita = $_GET['idus'];

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
	        $clientes_conflicto = "'C9999','C9998','C0001','C0003','C0249'";*/

	    $sql_conflicto = ($clientes_conflicto != "") ? " AND (no_cliente NOT IN ({$clientes_conflicto}) )" : "";
    }
    // ---------------------------------------------------------------------------------------------------
    // ---------------------------------------------------------------------------------------------------
	$result = $conexion->query("SELECT * FROM clientes WHERE no_cliente like '%".$_GET['term']."%' and nombre !='--' " . $sql_conflicto);
	// (magueyero = '1' ) AND || no_cliente = 'C0393'
    // Se obtiene el resultado de la consulta
    while($row = $result->fetch_array()) {
	    //$row_array['id'] = $row['id'];
		$row_array['value'] = $row['no_cliente'];
		$row_array['abbrev'] = $row['nombre'];
		$row_array['cliente_crm'] = $row['registro_crm'];
		//$row_array['abbre'] = $row['tipo_persona'];
		array_push($return_arr,$row_array);
	}
    /* Toss back results as json encoded array. */
    echo json_encode($return_arr);
}

$conexion->close(); 


?>

