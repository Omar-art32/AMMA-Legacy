<?php 
//include('../php/registro/conexion.php');
include("../../common/conexion.php");
    $conexion->set_charset("utf8");
    $consulta="SELECT MAX(id)+1 as id FROM localidades WHERE 1";
    $consultaid = $conexion->query($consulta);
    if($consultaid==false) throw new Exception("Error al obtener id localidades");
    if ($consultaid->num_rows > 0){
        $id="";
        while ($row = $consultaid->fetch_array(MYSQLI_ASSOC)){
          $id .=$row['id']; //concatenamos el los options para luego ser insertado en el HTML
        }
    } 
$listado="INSERT INTO localidades(id,MunicipioID,localidad) VALUES ('$id', '$_POST[id_municipio]','$_POST[localidad]')";
$listar= $conexion->query($listado);

?>
