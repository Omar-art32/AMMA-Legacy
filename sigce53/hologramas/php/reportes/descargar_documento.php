<?php
//session_start();
if (isset($_GET["tipo"])) {
    include("../../../common/conexion.php");
        
    if ($_GET["tipo"] == "R") {
        $id = hexdec($_GET["id"])^1337;
		try {
			$sql = "SELECT id_recibo, anio_rcbo FROM h_salidas WHERE id_salidas = ?";
			$ps = $conexion->prepare($sql);
			$ps->bind_param("i", $id);
			$ps->execute();
			$result = $ps->get_result();

			if ($row = $result->fetch_assoc()) {
				$numRecibo = str_pad($row['id_recibo'], 4, "0", STR_PAD_LEFT);
	      		$nombreArchivo = "AR".$numRecibo."_".$row['anio_rcbo'].".pdf";
	      		$nombre = ".." . DIRECTORY_SEPARATOR . "recibos" . DIRECTORY_SEPARATOR . "pdf_recibos" . DIRECTORY_SEPARATOR . $nombreArchivo;
	      		//echo $nombre;
				$fp = fopen($nombre, 'rb');
				header("Content-Type: application/pdf");
				header("Content-Length: " . filesize($nombre));
				header("Content-Disposition: inline; filename=recibo".$numRecibo.$row['anio_rcbo'].".pdf");
				fpassthru($fp);
			}

			$ps->close();
			$conexion->close();

		}
		catch (mysqli_sql_exception $e) {
			$conexion->close();
		}
		exit;
    } elseif ($_GET["tipo"] == "AR") {
        $id = hexdec($_GET["id"])^1337;
		try {
			$sql = "SELECT nombreAcuse, anio_rcbo FROM h_salidas WHERE id_salidas = ?";
			//echo $sql;
			$ps = $conexion->prepare($sql);
			$ps->bind_param("i", $id);
			$ps->execute();
			$result = $ps->get_result();
			if ($row = $result->fetch_assoc()) {
	      		$nombreAcuse = $row['nombreAcuse'];
	      		$nombre = "pdf_acuses" . DIRECTORY_SEPARATOR . $nombreAcuse;
				$fp = fopen($nombre, 'rb');
				header("Content-Type: application/pdf");
				header("Content-Length: " . filesize($nombre));
				header("Content-Disposition: inline; filename=".$nombre);
				fpassthru($fp);
			}

			$ps->close();
			$conexion->close();

		}
		catch (mysqli_sql_exception $e) {
			$conexion->close();
		}
		exit;
    } elseif ($_GET["tipo"] == "A") {
        //$id = hexdec($_GET["id_salida"])^1337;
        $id = $_GET["id_salida"];
		try {
			$sql = "SELECT nombreAcuse, anio_rcbo FROM h_salidas WHERE id_salidas = ?";
			//echo $sql;
			$ps = $conexion->prepare($sql);
			$ps->bind_param("i", $id);
			$ps->execute();
			$result = $ps->get_result();
			if ($row = $result->fetch_assoc()) {
				//$numRecibo = str_pad($row['id_recibo'], 4, "0", STR_PAD_LEFT);
	      		//$nombreArchivo = "ACUSE".$numRecibo."_".$row['anio_rcbo'].".pdf";
	      		$nombreAcuse = $row['nombreAcuse'];
	      		$nombre = "pdf_acuses" . DIRECTORY_SEPARATOR . $nombreAcuse;
	      		//echo $nombre;
				$fp = fopen($nombre, 'rb');
				header("Content-Type: application/pdf");
				header("Content-Length: " . filesize($nombre));
				header("Content-Disposition: inline; filename=".$nombre);
				fpassthru($fp);
			}

			$ps->close();
			$conexion->close();

		}
		catch (mysqli_sql_exception $e) {
			$conexion->close();
		}
		exit;
    } 
}
