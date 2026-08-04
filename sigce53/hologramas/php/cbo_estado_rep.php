<?php
include('../../common/conexion.php');
$client=utf8_decode ($_POST['cliente']);
$client=substr($client,0,4);
//$usr=$_POST['user'];
//$fecha = date("Y-m-d H:i:s" );
$sql="select edo from h_salidas where substr(no_cliente,1,4)='$client' group by edo";

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
	$cbo='<select name="cbo_edo" class="form-control" id="cbo_edo" onChange="busc_xedo();"><option  value="T">-----TODOS-----</option>';
	while($row=$result->fetch_row())
	{
	  $edo=$row[0];

	  if($edo==""){
	  $cbo.= "<option  value='{$edo}'>N/A</option>";
	  }	

	  else{
	  $cbo.= "<option  value='{$edo}'>{$edo}</option>";
	  }
	}
	$cbo.="</select>";
	echo json_encode(array('status' => 'correcto','cbo'=> $cbo));
  }
  else
  {
	  $msj="<font color='#990000'>Sin Registros&nbsp;&nbsp;</font>";
	echo json_encode(array('status' => 'error','msj'=> $msj));
  }
}		 

?>
 




