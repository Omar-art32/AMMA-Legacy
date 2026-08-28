<?php
include(__DIR__ . '/../../common/conexion.php');
$client=mb_convert_encoding ($_POST['cliente'], 'ISO-8859-1');
//$client=substr($client,0,5);
//$usr=$_POST['user'];
//$fecha = date("Y-m-d H:i:s" );
$sql="select tipo from h_salidas where no_cliente='$client' group by tipo";

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
	$cbo='<select name="cbo_cat" class="form-control" id="cbo_cat" onChange="busc_xcat();"><option  value="T">-----TODOS-----</option>';
	while($row=$result->fetch_row())
	{
	  $tipo=$row[0];
	  
	  switch($tipo)
		{
				case 0:
                    $tipo_mez="N/A";
                    break;
				case 1:
                    $tipo_mez="MEZCAL";
                    break;
				case 2:
                    $tipo_mez="ARTESANAL";
                    break;
				case 3:
                    $tipo_mez="ANCESTRAL";
                    break;
				
		}

	  if($tipo==0){
	  $cbo.= "<option  value='{$tipo}'>N/A</option>";
	  }	

	  else{
	  $cbo.= "<option  value='{$tipo}'>{$tipo_mez}</option>";
	  }
	}
	$cbo.="</select>";
	echo json_encode(['status' => 'correcto','cbo'=> $cbo, 'sql'=>$sql]);
  }
  else
  {
	  $msj="<font color='#990000'>Sin Registros&nbsp;&nbsp;</font>";
	echo json_encode(['status' => 'error','msj'=> $msj]);
  }
}		 

?>
 




