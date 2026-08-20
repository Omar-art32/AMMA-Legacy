<?php
// Ocultar avisos/deprecated para evitar corromper la salida JSON
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', '0');

header('Content-Type: application/json; charset=utf-8');

include('../../common/conexion.php');

// Recibir variables de forma segura
$client_raw = $_POST['cliente'] ?? '';
$client     = mb_convert_encoding($client_raw, 'ISO-8859-1', 'UTF-8');

$funcion = $_POST['funcion'] ?? '';
$id      = $_POST['id'] ?? '';

$sql    = "SELECT cve_marca, marca FROM marcas WHERE no_cliente = '$client' GROUP BY cve_marca";
$result = $conexion->query($sql);

if ($result == false) { 
    echo json_encode(array(
        'status' => 'error', 
        'msj'    => '<p><font color="red">Disculpe, ha ocurrido un error, intente más tarde</font></p>'
    ));
    exit;
}

$tot = $result->num_rows;

if ($tot > 0) {
    $cbo = '<select name="' . $id . '" id="' . $id . '" class="cbo-medium form-control" style="float:left;" onChange="' . $funcion . '();"><option value="0">Seleccionar</option>';
    
    while ($row = $result->fetch_row()) {
        $cve   = $row[0];
        $marca = mb_convert_encoding($row[1] ?? '', 'UTF-8', 'ISO-8859-1');
        $cbo  .= "<option value=\"{$cve}\">{$cve} - {$marca}</option>";
    }
    
    $cbo .= "</select>&nbsp;<button type='button' name='btnAddMarca' id='btnAddMarca' class='btn btn-success btn-xs' style='vertical-align:top;' onClick='addMarca()'>
                <span class='glyphicon glyphicon-plus'></span>
             </button>";
             
    echo json_encode(array('status' => 'correcto', 'cbo' => $cbo, 'SQL' => $sql));
} else {
    $msj = "<font color='#990000'>Sin Marcas&nbsp;&nbsp;</font>
            <button type='button' name='btnAddMarca' id='btnAddMarca' class='btn btn-success btn-xs' onClick='addMarca()'>
                <span class='glyphicon glyphicon-plus'></span>
            </button>";
            
    echo json_encode(array('status' => 'error', 'msj' => $msj));
}
exit;
?>