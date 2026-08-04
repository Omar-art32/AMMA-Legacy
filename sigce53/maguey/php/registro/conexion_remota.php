<?php
//$conexion = new mysqli("localhost","root","","siig");
//$conexion = new mysqli("localhost","root","MyCRMSql15","siige");
$conexion_remota = new mysqli("localhost","root","SIIGsql#2021v2","crmreg");
//$conexion = new mysqli("crmreg.db.11217162.hostedresource.com","crmreg","CrM#bd2016JL","crmreg");
if($conexion_remota->connect_errno > 0){
    die('Unable to connect to database [' . $conexion_remota->connect_error . ']');
}
?>
