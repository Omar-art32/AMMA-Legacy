<?php
// Ocultar errores/avisos en pantalla para evitar corromper la salida JSON
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', '0');

header('Content-Type: application/json; charset=utf-8');

include('../../../common/conexion.php');

if (isset($_GET['term'])){
    $return_arr = array();
    $busca = $_GET['term'];

    $result = $conexion->query("SELECT no_cliente, nombre FROM clientes WHERE nombre!='--' AND no_cliente LIKE '%{$busca}%' LIMIT 10");

    while($row = $result->fetch_array()) {
        $row_array['value'] = $row['no_cliente'];
        
        // Reemplazo de utf8_encode por mb_convert_encoding
        $row_array['asociado'] = mb_convert_encoding($row['nombre'], 'UTF-8', 'ISO-8859-1');

        array_push($return_arr, $row_array);
    }

    echo json_encode($return_arr);
    exit;
}
?>