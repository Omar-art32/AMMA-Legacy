<?php
include(__DIR__ . "/../../../common/conexion.php");
include(__DIR__ . '/../../../common/ExceptionCRM.php');
mysqli_set_charset($conexion,"utf8");
// upload.php
// 'images' refers to your file input name attribute
// get file names
$idSalida = $_POST['key'];

// LEER CARPETA ÚNICA
// 
//$rtCliente = ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "documentos" . DIRECTORY_SEPARATOR . $carpetaUnica;
/*$target = $rtCliente . DIRECTORY_SEPARATOR . $nombreArchivo;//"etiqueta_".$idProd . ".pdf";
$existe = (is_file( $target ))? 1: 0;
if($existe == '1') {
    if(unlink($target)) 
        $success = true;
    else 
        $success = false;
} else
    $success = true;

//if ($success === true) {*/
    $sql = $conexion->prepare("UPDATE h_salidas SET acuse = '', nombreAcuse = '' WHERE id_salidas= '".$idSalida."'");
    if ($sql) { 
        if($sql->execute()) {
            $success = true;
        } else
            $success = false;
    } else
        $success = false;
    if ($success) 
        $output = ['msj'=> "Archivo Eliminado."];
    else
        $output = ['error'=> "No fue posible actualizar el registro en la Base de Datos. Contacte a su Administrador"];
/*} elseif ($success === false) 
    $output = ['error'=> "No fue posible eliminar el archivo. Contacte a su Administrador"];*/

echo json_encode($output);
?>
