<?php
	include ("php/registro/conexion.php");
	$conexion->set_charset("utf8");
	ini_set('date.timezone', 'America/Mexico_City');
  	//$fech=date("Y-m-d"); //fecha actial
	$num=$_POST['num']; // numero de predio
	$cboxGuiaM=$_POST['cboxGuiaM']; // guia
	$supe=$_POST['superficieM']; //superficie del predio
	$cboxMSP=$_POST['cboxMSP'];// estatus del predio 
	

    $pol=null;
    $rut = $_FILES["poligonoM"]["tmp_name"];
    if (file_exists($rut)){
    $kml = file_get_contents($rut);
    $coordinates = "<coordinates>";
    $coordinates2 = "</coordinates>";
    if(strpos($kml,$coordinates)!==false){
        $pos=strpos($kml,$coordinates);
        $a=substr($kml,$pos+strlen($coordinates));
        if(strpos($a,$coordinates2)!==false){
          $npos=strpos($a,$coordinates2);
          $b=substr($a,0,$npos);
          $poligono = trim($b);
        }else{
          $poligono = trim($a);
        }
    }
 

    //cambio del poligono
    $resultado = str_replace(",0 ", "*", $poligono);
    $resultado = str_replace(",", " ", $resultado);
    $resultado = str_replace("*", ",", $resultado);
    if(substr($resultado, -1)==","){
    $resultado = substr($resultado, 0, -1);
    }else if(substr($resultado, -1)=="0"){
     $resultado = substr($resultado, 0, -2);
    }
    $pol=$resultado;//poligono
    }


	//$fech="'".$fech."'";
    $fech="NOW()";
	try{
		$conexion->autocommit(FALSE);
		$data = json_decode($_POST['tMagueys'], true); //arreglo de amgueys agregados

		if($cboxMSP==1){

	    $sql="UPDATE paraje SET superficie = '$supe' WHERE id_paraje= '$num'";
		$ps=$conexion->query($sql);
		if($ps==false) throw new Exception('Error al actualizar superfecie de predio. '.$sql);

		if($pol!=null){

	    $sql="UPDATE paraje SET poligono = GEOMFROMTEXT('POLYGON(($pol))') WHERE id_paraje= '$num'";
		$ps=$conexion->query($sql);
		if($ps==false) throw new Exception('Error al actualizar superfecie de predio. '.$sql);

		}

	   }


		foreach($data as $value){
			$sqlplanta="($num,'$value[0]','$value[1]','$value[2]','$value[3]','$value[4]','$value[5]',$fech,'1','$value[4]')";
			$sqlplantas="INSERT INTO `existenciaplanta`(`id_paraje`, `regmaguey`, `dis_surcometros`, `dis_planmetros`, `id_comun`, `cantidadini`, `edad`, `fecha_registro`, `status`, `existenciaplantas`) VALUES ".$sqlplanta;
			$ps=$conexion->query($sqlplantas);
			if($ps==false) throw new Exception('Error al realizar el registro '.$sqlplantas);
		}
		if($cboxGuiaM==1){
			$datoextracc="('$num','1',$fech,' ')";
			$datoextracc="INSERT INTO cextracciones (id_paraje,status,fecha,constancia) VALUES".$datoextracc;
			$resextrac=$conexion->query($datoextracc);
			if($resextrac==false) throw new Exception('Error al realizar el registro '.$datoextracc);
		}else{
			foreach($data as $value){
				if ($value[5]>4){
					$datoextracc="('$num','1',$fech,' '),('$num','1',$fech,' '),('$num','1',$fech,' '),('$num','1',$fech,' '),('$num','1',$fech,' ')";
					$datoextracc="INSERT INTO cextracciones (id_paraje,status,fecha,constancia) VALUES".$datoextracc;
					$resextrac=$conexion->query($datoextracc);
					if($resextrac==false) throw new Exception('Error al realizar el registro '.$datoextracc);
				}
			}
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