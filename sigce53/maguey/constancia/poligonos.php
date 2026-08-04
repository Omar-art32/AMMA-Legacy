<?php

include_once("Polyline.php");
/*include_once $_SERVER["DOCUMENT_ROOT"] . "/fpdf/fpdf.php";*/
include_once ("/fpdf/fpdf.php");

$conexion = new mysqli("localhost","root","","parajes");
if($conexion->connect_errno > 0){
	die("Error al conectarse a la base de datos [$conexion->connect_error]");
}

try {
	$sql = "SELECT AsBinary(poligono), lat, lng FROM paraje";
	$result = $conexion->query($sql);


	if ($result) {

		if($row = $result->fetch_row()) {

			$clat = $row[1];
			$clng = $row[2];

			$geo = unpack("Corder/Ltype/Lnum", $row[0]);

			//echo "Tipo: " . $geo["type"] . "<br>";
			//echo "Numero: " . $geo["num"] . "<br>";

			if ($geo["type"] == 3) {
				$num = $geo["num"];
				$offset = 9;

				$puntos = array();

				for($i=0; $i < $num; $i++) {
					$h = unpack("@" . $offset . "/Lnumpts", $row[0]);
					$numpts = $h["numpts"];

					$offset += 4;

					$nump = $numpts * 2;

					$pts = unpack("@" . $offset . "/d" . $nump, $row[0]);

					$lat = 0;
					$lon = 0;
					$esLongitud = true;
					foreach ($pts as $value) {
						$esLongitud ? $lon = $value : $lat = $value;

						if (!$esLongitud) {
							array_push($puntos, array($lat, $lon));
						}

						$esLongitud = !$esLongitud;

					}

					$offset += ($nump*8);
				}

			}

			$puntosCodificados = Polyline::Encode($puntos);

			$urlGoogle1 = "http://maps.googleapis.com/maps/api/staticmap?zoom=16" .
						"&size=640x640&maptype=satellite&sensor=false&path=color:red|weight:1|fillcolor:red|enc:" . $puntosCodificados;
			$urlGoogle2 = "http://maps.googleapis.com/maps/api/staticmap?zoom=14" .
						"&size=640x640&maptype=hybrid&sensor=false&path=color:red|weight:1|fillcolor:red|enc:" . $puntosCodificados;
			

			$pdf=new FPDF();
			//Primera página
			$pdf->AddPage();
			$pdf->SetFont("Arial","",15);
			$pdf->Cell(40,20);
			$pdf->Write(5,"Localizacion ");
			$pdf->Image($urlGoogle1, 10, 22, 80, 80, "PNG");

			$pdf->Image($urlGoogle2, 100, 22, 80, 80, "PNG");
			
			$pdf->Output();
			
			$pdf=new FPDF();
			
		}

	}
	else {
		echo "Error al recuperar el poligono.";
	}

	$conexion->close();
}
catch (mysqli_sql_exception $e) {
	echo "Error al conectarse a la base de datos.";
	$conexion->close();
}



?>