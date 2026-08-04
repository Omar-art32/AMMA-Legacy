<?php
include('../../common/conexion.php');
$client=utf8_decode ($_POST['cliente']);
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
	  $marca=utf8_encode($row[1]);
	  $cbo.= "<option  value={$cve}>{$cve} - {$marca}</option>";
	}
	$cbo.="</select>";
	echo json_encode(array('status' => 'correcto','cbo'=> $cbo));
  }
  else
  {
	  $msj="<font color='#990000'>Sin Marcas&nbsp;&nbsp;</font>";
	echo json_encode(array('status' => 'error','msj'=> $msj));
  }
}		 

?>
 




