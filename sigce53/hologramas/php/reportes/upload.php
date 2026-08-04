<?php
include("../../../common/conexion.php");
include('../../../common/ExceptionCRM.php');
mysqli_set_charset($conexion,"utf8");
// upload.php

// 'images' refers to your file input name attribute
if (empty($_FILES['input-id'])) {
    echo json_encode(['error'=>'No hay archivos para Importar.']);
    // or you can throw an exception
    return; // terminate
}
// get the files posted
$images = $_FILES['input-id'];
// get user id posted
$userid = empty($_POST['userid']) ? '' : $_POST['userid'];
// a flag to see if everything is ok
$success = null;
// file paths to store
$paths= [];
// get file names
$filenames = $images['name'];
$idSalida = $_POST['id_salida'];


//$rtCliente = DIRECTORY_SEPARATOR . "php" . DIRECTORY_SEPARATOR . "reportes" . DIRECTORY_SEPARATOR . "pdf_acuses" ;
//$rtCliente = DIRECTORY_SEPARATOR . "pdf_acuses";
$rtCliente = "pdf_acuses";
$arrext = explode(".", $filenames);
$ext = array_pop($arrext);

$sql = "SELECT id_recibo, anio_rcbo, no_cliente FROM h_salidas WHERE id_salidas = ?";
$ps = $conexion->prepare($sql);
$ps->bind_param("i", $idSalida);
$ps->execute();
$result = $ps->get_result();
if ($row = $result->fetch_assoc()) {
    $numRecibo = str_pad($row['id_recibo'], 4, "0", STR_PAD_LEFT);
    $nombreArchivo = "ACUSE".$numRecibo."_".$row['anio_rcbo'].".".$ext;
    
    $target = $rtCliente . DIRECTORY_SEPARATOR . $nombreArchivo;
    if(move_uploaded_file($images['tmp_name'], $target)) {
        $success = true;
        $paths[] = $target;
    } else
        $success = false;
}

// check and process based on successful status
if ($success === true) {
    /*$sql = $conexion->prepare("SELECT ie.estatus estatus FROM inf_et_productos iep
           INNER JOIN inf_etiquetas ie ON iep.num_informe = ie.num_informe
           WHERE id_producto = "."'".$idProd."' ");
    if ($sql) {
        $sql->execute();
        $resultSet = $sql->get_result();
        while ($result = $resultSet->fetch_assoc())
            $estatus = $result["estatus"];
    }*/
    $sql = $conexion->prepare("UPDATE h_salidas SET acuse = '1', nombreAcuse = '".$nombreArchivo."' WHERE id_salidas = '".$idSalida."'");
    
    if ($sql) { 
        if($sql->execute())
            $success = true;
        else
            $success = false;
    } else
        $success = false;
    if($success) {
    
        $output = [];
    // for example you can get the list of files uploaded this way
    // $output = ['uploaded' => $paths];
    } else {
        $msj = "Error al Crear Carpeta del Cliente, ";
        $output = ['error'=> "Error al Actualizar el registro del Producto, contacte con su Administrador de Sistemas."];
    }
} elseif ($success === false) {
    $msj = 'Error al Subir el Archivo, ';
    $output = ['error'=> $msj."contacte con su Administrador de Sistemas."];
} else {
    $output = ['error'=>'No hay archivos para Importar.'];
}

// return a json encoded response for plugin to process successfully
echo json_encode($output);




    function crearCarpeta($numCliente){
        include('../../../common/conexion.php');
        $sql = $conexion->prepare("SELECT no_cliente,carpetaUnica FROM clientes WHERE no_cliente="."'".$numCliente."' ");
        $carpetaUnicaO = ""; $noCliente = "";
        if ($sql) { /*si la conexion esta preparada*/
            $sql->execute();
            $resultSet = $sql->get_result();
            while ($result = $resultSet->fetch_assoc()) {
                $carpetaUnicaO = $result["carpetaUnica"];
                $noCliente = $result["no_cliente"];
            }
        }
        $resp = "";
        $rtCliente = ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "documentos" . DIRECTORY_SEPARATOR . $carpetaUnicaO;
        if ($carpetaUnicaO == "" || $carpetaUnicaO == NULL) {
            $carpetaUnica = uniqid();
            $rtCliente = ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "documentos" . DIRECTORY_SEPARATOR . $carpetaUnica;
            //$resp = crearCarpeta($rtCliente);
        } else
            $resp = "1b";
        // CREAR RUTA

        //$resp = crearCarpeta($rtCliente);
        $resp = ""; $dir = $rtCliente;
        if (!file_exists($dir)) {
            if(mkdir($dir, 0777, true))
                $resp = '1a';
            else
                $resp = '0a';
        } else
            $resp = '1a';

        if($resp == "1a") {
            if(chmod($dir, 0777))
                $resp = '1b';
            else
                $resp = '0b';
        }
        //
        if($resp == "1b") {
            if ($carpetaUnicaO == "" || $carpetaUnicaO == NULL) {
                //Actualizar carpetaUnica en clientes
                $sql="UPDATE clientes SET carpetaUnica=? WHERE no_cliente=?";
                $ps = $conexion->prepare($sql);
                $ps->bind_param("ss", $carpetaUnica,$noCliente);
                if (!$ps->execute())
                    throw new Exception("Error al actualizar carpeta");
                $ps->close();
                $carpetaUnicaO = $carpetaUnica;
            }
        }
        $arr["resp"] = $resp;
        $arr["CU"] = $carpetaUnicaO;
        return $arr;
    }
?>
