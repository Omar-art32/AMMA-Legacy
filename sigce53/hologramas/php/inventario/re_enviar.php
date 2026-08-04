<?php
    include('../../../common/conexion.php');
    $no_pedido=$_POST['no_pedido'];  
	/*
	$user=utf8_decode($_POST['user']); 
	$fecha = date("Y-m-d H:i:s" );
	$sql_remota="";
	$val_pedido="";
	*/
    $sql_check="SELECT * FROM h_pedidos WHERE no_pedido=$no_pedido";
	error_reporting(0);
    $con_rem=new mysqli("50.63.227.48","crmreg","CrM#bd2016JL","crmreg");
	$result=$con_rem->query($sql_check); 
	if($result->num_rows==0)
	{ 
	   //INSERTAR EN LA BD REMOTA	  	  
	    $sql_pedido="SELECT no_pedido,fecha,no_cliente,marca,serie,fi,ff,cantidad,status,usr FROM h_pedidos WHERE no_pedido=$no_pedido";
	    $sql_remota="INSERT INTO h_pedidos (no_pedido,fecha,no_cliente,marca,serie,fi,ff,cantidad,status,usr)VALUES";
	    $res_p=$conexion->query($sql_pedido);
	    $sep="";
		while($row=$res_p->fetch_assoc())
		{
		   $sql_remota.=$sep."('{$row['no_pedido']}','{$fecha}','{$row['no_cliente']}','{$row['marca']}','{$row['serie']}','{$row['fi']}','{$row['ff']}','{$row['cantidad']}',1,'{$user}')";
		   $sep=",";
		}
		$sql_remota.=";";
		$in_rem=$con_rem->query($sql_remota);
		if($in_rem==true)
		{  //ACTUALIZAR EL STATUS A ENVIADO
		   $sql_up="update h_pedidos set status=1 where no_pedido=$no_pedido";
		   $res_up=$conexion->query($sql_up);		   
		   if($res_up==true)
		   {
			 echo json_encode(array('status' => 'OK','msj'=> 'Se ha re-enviado correctamente la requisicion'));
		   }
		   else
		   {
			  echo json_encode(array('status' => 'OK','msj'=> 'No se pudo actualizar la informacion del re-envío intente mas tarde'));  
		   }
		}
		else
		{
		  echo json_encode(array('status' => 'OK','msj'=> 'No se ha podido re-enviar la requisición')); 			 
		}
	} 
	else 
	{   
	   $sql_up="update h_pedidos set status=1 where no_pedido=$no_pedido";
	   $res_up=$conexion->query($sql_up);		   
	   if($res_up==true)
	   {
		  echo json_encode(array('status' => 'OK','msj'=> 'Se ha re-enviado correctamente la requisicion'));
	   }
	   else
	   {
		  echo json_encode(array('status' => 'OK','msj'=> 'No se pudo actualizar la informacion del re-envío intente mas tarde'));  
	   }
	}
?>