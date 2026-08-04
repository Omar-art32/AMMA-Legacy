<?php
	include ("php/registro/conexion.php");
	$num=$_POST['num'];
	 try{
		$conexion->autocommit(FALSE);
		$data = json_decode($_POST['tMagueys'], true);
		foreach($data as $value){
			$fechita = date('Y-m-d',strtotime($value[4]));
			ini_set('date.timezone', 'America/Mexico_City');
			$now=date("Y-m-d");
			$sqlplantas="('$num','$value[0]','$value[1]','$value[2]','$fechita','$value[3]','$now','1','$value[3]')";
			$sqlplantas="INSERT INTO existenciaplanta_vivero(id_paraje,regmaguey,origen,id_comun,fecha_siembra,cantidadini,fecha_registro,status,existenciaplantas) VALUES ".$sqlplantas;
			$ps=$conexion->query($sqlplantas);
			if($ps==false) throw new Exception('Error al realizar el registro '.$sqlplantas);
		}
		$conexion->commit();
		$conexion->close();
		echo 'Registro realizado correctamente';
	}catch (mysqli_sql_exception $e) {
		$conexion->rollback();
		$conexion->close();
		echo "Error en la base de datos: " . $e->getMessage();
	}
?>
