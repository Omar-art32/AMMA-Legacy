<?php
include('../../../common/conexion.php');
$usr=$_POST['user'];
$tipo = mb_convert_encoding($_POST['tipo'] ?? '', 'ISO-8859-1', 'UTF-8');
$serie="";
$marca = mb_convert_encoding($_POST['marca'] ?? '', 'ISO-8859-1', 'UTF-8');
$cliente = mb_convert_encoding($_POST['cliente'] ?? '', 'ISO-8859-1', 'UTF-8');

$ext_ini = mb_convert_encoding($_POST['ext_ini'] ?? '', 'ISO-8859-1', 'UTF-8');
$ext_fin = mb_convert_encoding($_POST['ext_fin'] ?? '', 'ISO-8859-1', 'UTF-8');
$ent_ini = mb_convert_encoding($_POST['ini_ent'] ?? '', 'ISO-8859-1', 'UTF-8');
$ent_fin = mb_convert_encoding($_POST['fin_ent'] ?? '', 'ISO-8859-1', 'UTF-8');
$existe_reg = mb_convert_encoding($_POST['existe_reg'] ?? '', 'ISO-8859-1', 'UTF-8');

$fi="";
$ff="";

$total = mb_convert_encoding($_POST['total'] ?? '', 'ISO-8859-1', 'UTF-8');
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
	 $sql_existencias="insert into h_existencias (no_cliente,marca,serie,edo,tipo,fol_ini,fol_fin,existencias) values('--','--','-','OAXACA',0,$fi,$ff,$total);";
  }
  else
  {
	$sql_existencias="update h_existencias set fol_ini=$fi, fol_fin=$ff, existencias=existencias+$total where no_cliente='--' and marca='--' and serie='-'";
  }
}
else if($tipo=='P')
{
  $serie = mb_convert_encoding($_POST['serie'] ?? '', 'ISO-8859-1', 'UTF-8');
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

