<?php
//$conexion = new mysqli("localhost","root","","amma");
$conexion = new mysqli(
    "mariadb",
    "root",
    "root",
    "amma"
);
if($conexion->connect_errno > 0){
    die('Unable to connect to database [' . $conexion->connect_error . ']');
}
?>
