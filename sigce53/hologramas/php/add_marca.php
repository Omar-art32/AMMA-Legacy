<?php
include('../../common/conexion.php');
include('../../common/conexion_remota.php');
$client=utf8_decode ($_POST['nc_marca']);
$client=substr($client,0,4);
//$cod_c=substr($client,0,4);
$letra=utf8_decode ($_POST['letra']);
$marca=utf8_decode ($_POST['marca_new']);
$marca=strtoupper($marca);
$str_ins="";
$respuesta=array();
//revisar si la marca no existe
$sql_rev="select * from marcas where no_cliente='{$client}' and cve_marca='{$letra}'";
$revisa_ex=$conexion->query($sql_rev);
$existe_m=$revisa_ex->num_rows;
if($existe_m==0)
{
	//insertar en la base de datos local
	$str_ins="insert into marcas(no_cliente,cve_marca,marca,serie,sinc) values('$client','$letra','$marca','A',0)";
	$inserta=$conexion->query($str_ins);		 
	if($inserta==false)
	{ 
	  $respuesta=array('status' => 'Error','msj'=> 'Error agregar marca  -LOCAL-','remoto'=> '','msj_remoto'=> '','upmarc'=> '','msj_upmarc'=> '');
	} 
	else
	{
	  $respuesta=array('status' => 'OK','msj'=> 'Marca Agregada -LOCAL-','remoto'=> '','msj_remoto'=> '','upmarc'=> '','msj_upmarc'=> '');
	}		
	//------------GUARDAR EN LA BD REMOTA------------
	//$conexion->close();	
	//error_reporting(0);
	//$remota_con=new mysqli("50.63.227.48","crmreg","Finisterra#1","crmreg");
	//$remota_con = new mysqli("localhost","root","MyCRMSql15","crmreg"); //USADA PARA PRUEBAS LOCALES
	$sep="";
	$sql_no_sinc="SELECT no_cliente,cve_marca,marca,serie,sinc FROM marcas where sinc=0";
	$get_pendientes=$conexion->query($sql_no_sinc);
	$sql_ins_pendientes="insert into marcas_tmp(no_cliente,cve_marca,marca,serie,sinc) values";
	while($row=$get_pendientes->fetch_assoc())
			{
				$sql_ins_pendientes.=$sep."('{$row['no_cliente']}', '{$row['cve_marca']}', '{$row['marca']}', '{$row['serie']}', '0')";
				$sep=",";
			}
	
	$sql_ins_pendientes.=";";
	//ELIMINAR TEMPORALES
	$sql_temps="delete from marcas_tmp";
	$res_temps=$con_rem->query($sql_temps);	
	//FIN ELIMINAR TEMPORALES
	$remoto_ins=$con_rem->query($sql_ins_pendientes);
	if($remoto_ins==false)
	 { 
	   $respuesta['remoto']='Error';
	   $respuesta['msj_remoto']='Error agregar marca  -REMOTO-';
	 } 
	 else
	 {
		 $sql_ins_final="insert into marcas(no_cliente,cve_marca,marca,serie,sinc)(select no_cliente,cve_marca,marca,serie,sinc from marcas_tmp where concat(no_cliente,cve_marca) not in(select concat(no_cliente,cve_marca) from marcas))";
		 $res_ins_final=$con_rem->query($sql_ins_final);
	    //$remota_con->close();
	    //------ACTUALIZAR LA BD LOCAL PARA SABER QUE LA MARCA SE SINCRONIZO
	    //$conexion = new mysqli("localhost","root","MyCRMSql15","siig"); 
	    $up_marca="update marcas set sinc=1 where sinc=0";
	    $res_up= $conexion->query($up_marca);
	     if($res_up==false)
		 {
		   $respuesta['upmarc']='Error';
		   $respuesta['msj_upmarc']='Error actualizar marca -LOCAL-'.$up_marca; 
		 }
		 else
		 {
		   $respuesta['upmarc']='OK';
		   $respuesta['msj_upmarc']='Marca en Linea -LOCAL-'; 
		 }
	     $respuesta['remoto']='OK';
	     $respuesta['msj_remoto']='Marca Agregada -REMOTO-';
	 }
	}

else
{
  $respuesta=array('status' => 'Error','msj'=> 'Esta marca ya existe','remoto'=> '','msj_remoto'=> '','upmarc'=> '','msj_upmarc'=> '');	
}
echo json_encode($respuesta);
?>
 




