<?php
    include('../../../common/conexion.php');
	//include('../../../common/conexion_remota.php');
	$cont_err=0; 
	$arr_err=array();  
	//$user=$_POST['user'];
	//$fecha=date("Y-m-d H:i:s");
	$sql_pendientes="SELECT * FROM h_pedidos WHERE status>1 and status!=sinc_down";
	//$get_pendientes=$con_rem->query($sql_pendientes);
	$get_pendientes=$conexion->query($sql_pendientes);
	$sep="";
	$ids="";
	
	if($get_pendientes->num_rows>0)
	{		
		while($row=$get_pendientes->fetch_assoc())
		{
			$id_row=$row['id_row'];
			$id_pe=$row['no_pedido'];
			$status=$row['status'];
			$no_c=$row['no_cliente'];
			$mca=$row['marca'];
			$edo=$row['edo'];
			$fi=$row['fi'];
			$sql_up="update h_pedidos set status=$status where no_pedido=$id_pe and no_cliente='${no_c}' and marca='{$mca}' and edo='{$edo}' and fi=$fi";	
			$res_up=$conexion->query($sql_up);
			if($res_up==false)
			{
				$cont_err++;
				$arr_err[$cont_err]=$sql_up;
				//echo 'errorres';
			}
			else
			{
				$ids.=$sep.$id_row;
				$sep=',';
			}			
		}
		if($cont_err==0)
		{
			$ids;
			$up_down="update h_pedidos set sinc_down=status where id_row in ($ids)";
			$res_down=$conexion->query($up_down);
			if($res_down==true)
			{
				echo json_encode(array('status'=>'OK','msj'=>'La sincronizacion se ha realizado correctamente','tipo_r'=>'2'));
			}
		}
		
	}
	else
	{
		echo json_encode(array('status'=>'OK','msj'=>'Sin registros para sincronizar','tipo_r'=>'2'));
	}
?>