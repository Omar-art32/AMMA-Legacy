<?php
//$con_rem=new mysqli("50.63.227.48","crmreg","","crmreg");
//$con_rem = new mysqli("localhost","root","MyCRMSql15","crmreg");
$con_rem = new mysqli("localhost","root","SIIGsql#2021v2","crmreg");
if($con_rem->connect_errno > 0){
    die(utf8_encode('No se pudo conectar a la base de datos remota. Motivo: '. $con_rem->connect_error));	
}
?>