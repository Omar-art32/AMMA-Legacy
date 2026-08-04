<?php
include('../../common/conexion.php');
$client=utf8_decode ($_POST['cliente']);
$client=$client;
$funcion=$_POST['funcion'];
$id=$_POST['id'];
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
	$cbo='<select name="'.$id.'" id="'.$id.'" class="cbo-medium form-control" style="float:left;" onChange="'.$funcion.'();"><option  value="0">Seleccionar</option>';
	while($row=$result->fetch_row())
	{
	  $cve=$row[0];
	  $marca=utf8_encode($row[1]);
	  $cbo.= "<option  value={$cve}>{$cve} - {$marca}</option>";
	}
	$cbo.="</select>&nbsp;<button type='button' name='btnAddMarca' id='btnAddMarca' class='btn btn-success btn-xs' style='vertical-align:top;' onClick='addMarca()'>
                                        <span class='glyphicon glyphicon-plus'></span>
                                       </button>";
	echo json_encode(array('status' => 'correcto','cbo'=> $cbo, 'SQL'=>$sql));
  }
  else
  {
	  $msj="<font color='#990000'>Sin Marcas&nbsp;&nbsp;</font>
	  <button type='button' name='btnAddMarca' id='btnAddMarca' class='btn btn-success btn-xs' onClick='addMarca()'>
                                        <span class='glyphicon glyphicon-plus'></span>
                                       </button>";
	echo json_encode(array('status' => 'error','msj'=> $msj));
  }
}		 

?>
 




