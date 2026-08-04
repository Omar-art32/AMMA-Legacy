<?php
include ("registro/conexion.php");
$noparaje=$_POST['noparaje'];
$nocliente=$_POST['nocliente'];
$nomcliente=$_POST['nomcliente'];
$cant=$_POST['cant'];
$fechaActual = date('Y-m-d');
$status=1;

		try {

			//consulta para obtener los datos de status y constancias de la tablan cextraccione.
		     $consultamodal= "SELECT cextracciones.status, cextracciones.constancia, cextracciones.id_paraje FROM cextracciones INNER JOIN historial_cextracciones ON historial_cextracciones.id_paraje=cextracciones.id_paraje WHERE historial_cextracciones.id_paraje='$noparaje'";

		    $conMod=$conexion->query($consultamodal);
		    $res=mysqli_fetch_array($conMod);

		    //asignamos los valores de status y constancias a unas variable.
		    // $status=$res['status'];
		    $constancia=$res['constancia'];

			//valores a insertar
			$datoHCE="('','$noparaje','$nocliente','$nomcliente','$cant','$fechaActual')";
		 	$datocextraciones="('','$noparaje','$status','$fechaActual','$constancia')";

			// insertar en la tabla historial_cextracciones
				$sqlparaje="INSERT INTO historial_cextracciones (id_hcextraccion,id_paraje,no_cliente,nombre,cantidad,fecha) VALUES ".$datoHCE;
			    $result=$conexion->query($sqlparaje);	

		    //ALERT 
			    if($result==false){
			    	throw new Exception("Error al insertar en historial_cextraciones ".$sqlparaje);
			    }else{
			    	echo "Su registro constancias de creado exitosamente";
			    }
		    
			//insertar la cantidad de los datos en la tabla cextraciones
			for ($i=1; $i <=$cant; $i++) { 
				$sqlparaje="INSERT INTO cextracciones (id_extraccion,id_paraje,status,fecha,constancia) VALUES ".$datocextraciones;
				$result=$conexion->query($sqlparaje);
			}

			 $conexion->commit();
		     $conexion->close();
			
		} catch (mysqli_sql_exception $e) {
			 $conexion->rollback();
		   	 $conexion->close();
		   	 echo "Error en la base de datos: " . $e->getMessage();
		}

?>