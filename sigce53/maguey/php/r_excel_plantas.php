<?php
session_start();
session_set_cookie_params(0, "/", $_SERVER["HTTP_HOST"], 0);
$mod=1;
require_once("../../common/cfg_server.php");
$d_s=$_POST["id_s"];
if(isset($_SESSION[$d_s]))
{
	if($_SESSION[$d_s]["logged"] != "OK") {
		header("location: http://".$svr_dir."/acceso/login.php?mod=$mod");
	} else if($_SESSION[$d_s]["logged"] == "OK" &&  $_SESSION[$d_s]["seccion_4_5"]=="logged") {
		
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		date_default_timezone_set('Mexico/General');
		if (PHP_SAPI == 'cli')
		die('This example should only be run from a Web Browser');

		require_once '../../libs/phpExcel/PHPExcel.php';
		include('../../common/conexion.php');
		mysqli_set_charset($conexion,"utf8");
		$fecha = date("Y-m-d" );
		$msj1="";
		$file_name="";
		$periodo="";
		$msj_per="";

		$new_index="";
		$last_index="";
		$cont_result=1;
		$r_h=5; 
		$st_r=6; 
		$fill_color="";
		$bandera_color=0;
		$pos_ini="";
		$pos_fin="";
		
		$consulta = "SELECT ep.*, DATE(p.fecharegistro) fecharegistro, c.nombre comun, cli.nombre nombrecli,
		l.localidad local, m.nombre municipio, e.nombre mestado, p.paraje, p.id_cliente, p.superficie 
		FROM existenciaplanta ep
		INNER JOIN paraje p ON ep.id_paraje = p.id_paraje 
		INNER JOIN comun c ON ep.id_comun = c.id_comun 
		INNER JOIN clientes cli ON p.id_cliente = cli.no_cliente 
		INNER JOIN localidades l ON p.id_localidad = l.id
		INNER JOIN municipios m ON l.MunicipioID = m.id 
		INNER JOIN estados e ON m.estado = e.clave 
		";

		if(isset($_POST)){
			$search = $_POST['busca'];
			$fecha1 = $_POST['fecha1'];
			$fecha2 = $_POST['fecha2'];

			$he1 = "";
			$msj1 = "";
			$file_name = 'plantas_' . rand() . '.xlsx';
			// CUADRO DE BÚSQUEDA
			if($search != "" && $search != "undefined") {
				$consulta .= " WHERE (p.id_paraje LIKE '%$search%' || p.paraje LIKE '%$search%' || p.lat LIKE '%$search%' || p.lng LIKE '%$search%' || p.id_cliente LIKE '%$search%' 
							|| p.nombrep LIKE '%$search%' || p.rcampo LIKE '%$search%' || c.nombre LIKE '%$search%' || ep.regmaguey LIKE '%$search%' ) ";
			} 
			// FECHAS
			if(trim($fecha1) != '' && trim($fecha2) != '') {
				$consulta .= (($search != "") ? " AND ": " WHERE ") . " DATE(p.fecharegistro) between '$fecha1' and '$fecha2' ";
				$periodo=fecha($fecha1).'  a  '.fecha($fecha2);
				$msj_per="Periodo:";
			} else if( trim($fecha1) != '') {
				$consulta .= (($search != "") ? " AND ": " WHERE ") . " DATE(p.fecharegistro) = '$fecha1' ";
				$periodo=fecha($fecha1);
				$msj_per="Fecha:";
			}
			// CLIENTE 
			$clientesel= (!isset($_POST['cliente'])) ? "": $_POST['cliente'];
			if($clientesel != "" && $clientesel != "0") {
				$consulta .= (trim($fecha1) != '' || trim($fecha2) != '' || ($search != "" && $search != "undefined")) ? " AND ": " WHERE "; 
				$consulta .= " (p.id_cliente IN ('$clientesel')) ";
			}
			$consulta .= "  ORDER BY p.fecharegistro ASC";
			//echo $consulta;	
			$res=$conexion->query($consulta);
			$tot=$res->num_rows;
			$t2=$res->field_count;
			$letras=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC');
			// Create new PHPExcel object
			$objPHPExcel = new PHPExcel();
			// Set document properties
			$objPHPExcel->getProperties()->setCreator("NJGC")
			->setLastModifiedBy("AMMA")
			->setTitle("REPORTES")
			->setSubject("REPORTES")
			->setDescription("REPORTE GENERAL")
			->setKeywords("office 2007 openxml php")
			->setCategory("REPORTE");
			$styleArray = array(
				'font' => array(
					'bold' => true,
					),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					),
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						),
					),
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,'rotation' => 90,
					'startcolor' => array(
						'argb' => 'FFA0A0A0',
					),
					'endcolor' => array(
						'argb' => 'FFFFFFFF',
					),
				),
			);
			$styleArray2 = array(
				'font' => array(
					'bold' => true,
					'color'=>array('rgb'=>'ffffff'),
				),
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array('rgb' => '9DB2B3'),
						),
					),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				),
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb'=>'23719E'),
				),
			);
			$styleArray3 = array(
				'font' => array(
					'bold' => false,
					/*'color'=>array('rgb'=>'ffffff'),*/
				),
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array('rgb' => '6A8696'),
						),
					),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				),
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb'=>'2E966D'),
				),
			);

			//HEADER
			$objPHPExcel->getActiveSheet()->mergeCells('C1:O1');
			$objPHPExcel->getActiveSheet()->setCellValue('C1', 'ASOCIACIÓN DE MAGUEY Y MEZCAL ARTESANAL');
			//$objPHPExcel->getActiveSheet()->setCellValue('A3', $consulta);
			$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setSize(18);
			$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('C1:O1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(50);
			$objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(30);
			$objPHPExcel->getActiveSheet()->mergeCells('C2:O2');
			$objPHPExcel->getActiveSheet()->setCellValue('C2', 'REPORTE DE PLANTAS');
			$objPHPExcel->getActiveSheet()->getStyle('C2')->getFont()->setSize(14);
			$objPHPExcel->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('C2:O2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('C2:O2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			
			//logotipo
			$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
			$objPHPExcel->getDefaultStyle()->getFont()->setSize(11);
			$objPHPExcel->getActiveSheet()->mergeCells('A1:B2');
			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('logo');
			$objDrawing->setDescription('PHPExcel logo');
			$objDrawing->setPath('../../images/logo_amma.jpg');       // filesystem reference for the image file
			$objDrawing->setHeight(80);                 // sets the image height to 36px (overriding the actual image height);
			$objDrawing->setCoordinates('A1');    // pins the top-left corner of the image to cell D24
			$objDrawing->setOffsetX(10);                // pins the top left corner of the image at an offset of 10 points horizontally to the right of the top-left corner of the cell
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
			//AGREGAR ENCABEZADO DE LA TABLA
			$objPHPExcel->getActiveSheet()->getStyle('A'.$r_h.':O'.$r_h)->applyFromArray($styleArray2);

			$objPHPExcel->setActiveSheetIndex(0);

			$arrTitsCampos = array(
				"FECHA"				 =>"fecharegistro",
				"PREDIO"			 =>"id_paraje",
				"NOMBRE PREDIO"		 =>"paraje",
				"NO. CONTROL"		 =>"id_cliente",
				"NOMBRE"			 =>"nombrecli",
				"COMÚN" 			 =>"comun",
				"EDAD(AÑOS)" 	  	 =>"edad",
				"EXISTENCIA INICIAL" =>"cantidadini",
				"EXISTENCIA ACTUAL"  =>"existenciaplantas",
				"REGISTRO DE MAGUEY" =>"regmaguey",
				"HECTÁREAS" =>"superficie",
				"PARAJE" =>"paraje",
				"LOCALIDAD" =>"local",
				"MUNICIPIO" =>"municipio",
				"ESTADO" =>"mestado"
			);
			$letra = "A";
			foreach($arrTitsCampos as $index => $elem) {
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($letra.$r_h, $index);
				$letra++;
			}
			$letra = "A";
			for($x=0;$x<=$t2;$x++) {
				$objPHPExcel->getActiveSheet()->getStyle($letra."2")->getFont()->setBold(true);
				$letra++;
			}
			$x=$st_r;
			$arr_fila=array();
			$n = 6;
			while($row = $res->fetch_assoc()) {
				$letra = "A";
				foreach($arrTitsCampos as $index => $obj) {
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue("$letra".$n, $row[$obj]);
					$letra++;
				}

				$letra = "A";
				foreach($arrTitsCampos as $index => $obj) {
					$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
					$letra++;
				}
				$n++;
			}
			
			$objPHPExcel->getActiveSheet(0)->setTitle("PLANTAS");

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save('../tmp_excel/'.$file_name);
			$dir_file="http://".$svr_dir."/maguey/tmp_excel/".$file_name;
			echo json_encode(array('status' => 'OK','msj'=>$dir_file));
			exit;
			//FIN DEL SCRIPT PARA GENERAR EL ARCHIVO
		}//FIN ISSET CLIENTE
		else
			echo json_encode(array('status' => 'error','msj'=>'datos vacios'));

	}
	else
	{
	  header("location: http://".$svr_dir."/acceso/login.php?mod=$mod");
	}
}
else
{
  header("location: http://".$svr_dir."/acceso/login.php?mod=$mod");
}

function fecha($fech)
{
	$dat=array();
	$dat=explode('-',$fech);
	$m='';
	switch($dat[1])
	{
		case '01':
		{
			$m="Ene";
			break;
		}
		case '02':
		{
			$m="Feb";
			break;
		}
		case '03':
		{
			$m="Mar";
			break;
		}
		case '04':
		{
			$m="Abr";
			break;
		}
		case '05':
		{
			$m="May";
			break;
		}
		case '06':
		{
			$m="Jun";
			break;
		}

		case '07':
		{
			$m="Jul";
			break;
		}
		case '08':
		{
			$m="Ago";
			break;
		}
		case '9':
		{
			$m="Sep";
			break;
		}
		case '10':
		{
			$m="Oct";
			break;
		}
		case '11':
		{
			$m="Nov";
			break;
		}
		case '12':
		{
			$m="Dic";
			break;
		}

	}
	$nfech=$dat[2]."-".$m."-".$dat[0];
		return $nfech;
}
?>
