<?php
include('../../../common/conexion.php');
$usr=$_POST['user'];
$tipo=utf8_decode ($_POST['tipo']);
$serie="";
$marca=utf8_decode ($_POST['marca']);
$cliente=utf8_decode ($_POST['cliente']);

$ext_ini=utf8_decode ($_POST['ext_ini']);
$ext_fin=utf8_decode ($_POST['ext_fin']);
$ent_ini=utf8_decode ($_POST['ini_ent']);
$ent_fin=utf8_decode ($_POST['fin_ent']);
$existe_reg=utf8_decode ($_POST['existe_reg']);

$fi="";
$ff="";
				
$total=utf8_decode ($_POST['total']);
//$observ=utf8_decode ($_POST['observ']);
$fecha = date("Y-m-d H:i:s" );

//revisar el folio inicial de la existencia para saber cual sera el folio inicial de la entrada
if($ext_ini==0)
{
	$fi=$ent_ini;
}
else
{
	if($ent_ini>$ext_ini)
	{
		$fi=$ext_ini;
	}
	else
	{
		$fi=$ent_ini;
	}
}
//para folio final
if($ent_fin>$ext_fin)
{
	$ff=$ent_fin;
}
else
{
	$ff=$ext_fin;
}

if($tipo=='G')
{
  $sql_ent="INSERT INTO h_entradas(no_cliente, marca, serie, fol_ini, fol_fin, cantidad, fecha, usr) VALUES
  ('--', '--', '-', '{$ent_ini}', '{$ent_fin}', '{$total}','{$fecha}', '{$usr}');";
  if($existe_reg==0)
  {
	 $sql_existencias="insert into h_existencias (no_cliente,marca,serie,fol_ini,fol_fin,existencias) values('--','--','-',$fi,$ff,$total);";
  }
  else
  {
	$sql_existencias="update h_existencias set fol_ini=$fi, fol_fin=$ff, existencias=existencias+$total where no_cliente='--' and marca='--' and serie='-'";
  }
}
else if($tipo=='P')
{
  $serie=utf8_decode ($_POST['serie']);
  $sql_ent="INSERT INTO h_entradas(no_cliente, marca, serie, fol_ini, fol_fin, cantidad, fecha, usr, version) VALUES
  ('{$cliente}', '{$marca}', '{$serie}', '{$ent_ini}', '{$ent_fin}', '{$total}', '{$fecha}', '{$usr}', '$version');";
  if($existe_reg==0)
  {
	 $sql_existencias="insert into h_existencias (no_cliente,marca,serie,fol_ini,fol_fin,existencias) values('{$cliente}','{$marca}','{$serie}',$fi,$ff,$total);";
  }
  else
  {
	$sql_existencias="update h_existencias set fol_ini=$fi, fol_fin=$ff, existencias=existencias+$total where no_cliente='$cliente' and marca='$marca' and serie='$serie'";
  }
   
}
$result=$conexion->query($sql_ent);


// Ahora comprobaremos que todo ha ido correctamente
if($result==false)
{ 
  echo json_encode(array('status' => 'error','msj'=> 'Ha ocurrido un error al generar el recibo, imprima pantalla del error y comuniquelo al area de sistemas'));
} 
else
{ 
      $update=$conexion->query($sql_existencias);
	  if($update==false)
	  {
		 echo json_encode(array('status' => 'error','msj'=>'Ha ocurrido un error al actualizar existencias, imprima pantalla del error y comuniquelo al area de sistemas'.$sql_existencias.'--'.$sql_ent));
	  }
	  else
	  {
	 
        echo json_encode(array('status' => 'correcto','msj'=>'Entrada agregada correctamente'));
	  }
}

?>
 




