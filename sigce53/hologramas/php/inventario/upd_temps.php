<?php
include('../../../common/conexion.php');
$lista_req=array();
$valor = $_POST['valor'];
$idrow = (isset($_POST['idrow']) && $_POST['idrow'] > 0) ? $_POST['idrow']: 0 ;
if($idrow == 0) {
    echo json_encode(array('status' => 'Error','msj'=> 'No se puede actualizar el registro, actualice la página e intente de nuevo'));
} else {
    $sql_upd="UPDATE h_tmp_pedido SET holograma='$valor' WHERE id_row = $idrow";
    $result=$conexion->query($sql_upd);
    if($result==false)
     	echo json_encode(array('status' => 'Error','msj'=> 'Error al realizar el registro'.$sql_upd));
    else
    	echo json_encode(array('status' => 'OK', 'msj' => 'Bien'));
}

?>
