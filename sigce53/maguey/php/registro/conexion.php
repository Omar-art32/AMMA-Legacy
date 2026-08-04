<?php
$conexion = new mysqli("localhost","root","AMMAsq1#2023v3","crmreg");
if($conexion->connect_errno > 0){
    die('Unable to connect to database [' . $conexion->connect_error . ']');
}

?>
