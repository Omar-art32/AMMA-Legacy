<?php
include('../../../common/conexion.php');
//$usr=$_POST['user'];
$anio = date('y');
$sql_tmp=$sql="select if(max(no_pedido) is null,0,max(no_pedido))  from h_tmp_pedido";
$res_tmp=$conexion->query($sql_tmp);
if($res_tmp==false)
{ 
  echo "<p><font color='red'>Disculpe ha ocurrido un error, intente mas tarde</font></p>";
} 
else
{ 
  $tot_tmp=$res_tmp->num_rows;
  if($tot_tmp==1)
  {
	$row_tmp=$res_tmp->fetch_row();
	$no_tmp=$row_tmp[0];
	if($no_tmp!=0)
	{
	  echo json_encode(array('status' => 'correcto','tmp' => 'si','no_pedido'=> $no_tmp ));
	  $conexion->close();
	}
	else
	{
		$sql="select if(max(no_pedido) is null,0,max(no_pedido))  from h_pedidos";
		$result=$conexion->query($sql);
		// Ahora comprobaremos que todo ha ido correctamente
		if($result==false)
		{ 
		  echo "<p><font color='red'>Disculpe ha ocurrido un error, intente mas tarde</font></p>";
		} 
		else
		{ 
		  $tot=$result->num_rows;
		  if($tot==1)
		  {
			$row=$result->fetch_row();
			$no_pedido=$row[0]+1;
			echo json_encode(array('status' => 'correcto','tmp' => 'no','no_pedido'=> $no_pedido ));
			$conexion->close();
		  }
		  else
		  {
			echo json_encode(array('status' => 'error','n_rcbo'=> 'na'));
		  }
		}
	}
  }
  else
  {
	echo json_encode(array('status' => 'error','n_rcbo'=> 'na'));
  }
}	
?>
 




