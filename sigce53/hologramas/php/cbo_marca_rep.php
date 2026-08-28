<?php
include(__DIR__ . '/../../common/conexion.php');
$client=mb_convert_encoding ($_POST['cliente'], 'ISO-8859-1');
//$client=substr($client,0,4);
//$usr=$_POST['user'];
//$fecha = date("Y-m-d H:i:s" );
$sql="select cve_marca,marca from marcas where no_cliente='$client' group by cve_marca";
$result=$conexion->query($sql);
// Ahora comprobaremos que todo ha ido correctamente
if($result==false)
{ 
  echo "<p><font color='red'>Disculpe ha ocurrido un error, intente mas tarde</font></p>";
} 
else
{ 
  $tot=$result->num_rows;
  $cbo="";
  if($tot>0)
  {
	$cbo='<select name="cbo_marcas" class="form-control" id="cbo_marcas" onChange="busc_xmar();"><option  value="TODOS">-----TODOS-----</option><option  value="GENERICOS">--GENERICOS--</option><option  value="PERSON">--PERSONALIZADOS--</option>';
	while($row=$result->fetch_row())
	{
	  $cve=$row[0];
	  $marca=mb_convert_encoding($row[1], 'UTF-8', 'ISO-8859-1');
	  $cbo.= "<option  value={$cve}>{$cve} - {$marca}</option>";
	}
	$cbo.="</select>";
	echo json_encode(['status' => 'correcto','cbo'=> $cbo]);
  }
  else
  {
	  $msj="<font color='#990000'>Sin Marcas&nbsp;&nbsp;</font>";
	echo json_encode(['status' => 'error','msj'=> $msj]);
  }
}		 

?>
 




