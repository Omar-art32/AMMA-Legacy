<?php
session_start();
session_set_cookie_params(0, "/", $_SERVER["HTTP_HOST"], 0);
$mod=1;
require_once("../../common/cfg_server.php");
$d_s=$_POST["id_s"];
if(isset($_SESSION[$d_s]))
{
	if($_SESSION[$d_s]["logged"] != "OK")
	{
		header("location: http://".$svr_dir."/acceso/login.php?mod=$mod");
	}
	else if($_SESSION[$d_s]["logged"] == "OK" &&  $_SESSION[$d_s]["seccion_1_4"]=="logged")
	{
		
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		date_default_timezone_set('Mexico/General');
		if (PHP_SAPI == 'cli')
		die('This example should only be run from a Web Browser');

		/** Include PHPExcel */
		require_once '../../libs/phpExcel/PHPExcel.php';
		include('../../common/conexion.php');
		/** DECLARACION DE VARIABLES */
		$conexion->set_charset("utf8");
		$fecha = date("Y-m-d" );
		$msj1="";
		$file_name="";
		$periodo="";
		$msj_per="";

		$new_index="";
		$last_index="";
		$cont_result=1;
		$r_h=5; // es el numero de fila del encabezado
		$st_r=6; //es el numero a partir del cual se agregaran los datos
		
		$fill_color="";
		$bandera_color=0;
		$pos_ini="";
		$pos_fin="";
		
		$consulta = "SELECT YEAR(cex.fecharegistro) yfr,MONTH(cex.fecharegistro) mfr, p.id_cliente nc, p.superficie s, c.nombre nomcli, p.maguey_con_registro mcr
		from paraje p 
		INNER JOIN cextracciones cex ON p.id_paraje = cex.id_paraje 
		INNER JOIN clientes c ON p.id_cliente = c.no_cliente 
		WHERE (p.maguey_con_registro = 2 && p.servicio = 'EXCLUSIVO' )
		ORDER BY YEAR(cex.fecharegistro),MONTH(cex.fecharegistro),p.id_cliente;";
      
	  // $where  $sql_conflicto ";

		if(isset($_POST)){

			$fecha1 = $_POST['fecha1'];
			$fecha2 = $_POST['fecha2'];

			$he1 = "";
			$msj1 = "";
			$file_name = 'predios_' . rand() . '.xlsx';
			$operador = " where ";

			if(trim($fecha1) != '' && trim($fecha2) != '') {
				//echo $_POST['fechaini'];
				//echo $_POST['fechafin'];
				//$consulta.=$operador."h_salidas.fecha_entr between '$fecha1' and '$fecha2' ORDER BY h_salidas.no_cliente,h_salidas.marca,h_salidas.fi1 asc";
				//$sql_sum.=$operador."h_salidas.fecha_entr between '$fecha1' and '$fecha2' GROUP BY concat(h_salidas.no_cliente,h_salidas.marca), h_salidas.serie ORDER BY h_salidas.no_cliente, h_salidas.marca,   h_salidas.fi1 asc ";
				$periodo=fecha($fecha1).'  a  '.fecha($fecha2);
				$msj_per="Periodo:";
			} else if( trim($fecha1) != '') {
				//$consulta.=$operador."fecha_entr='$fecha1' ORDER BY h_salidas.marca,h_salidas.fi1 asc";
				//$sql_sum.=$operador."fecha_entr='$fecha1' GROUP BY concat(h_salidas.no_cliente,h_salidas.marca), h_salidas.serie ORDER BY h_salidas.no_cliente, h_salidas.marca,h_salidas.fi1 asc";
				$periodo=fecha($fecha1);
				$msj_per="Fecha:";
			} else {
				//$consulta.="  ORDER BY h_salidas.no_cliente, h_salidas.marca, h_salidas.fi1 asc";
				//$sql_sum.="  GROUP BY concat(h_salidas.no_cliente,h_salidas.marca), h_salidas.serie ORDER BY h_salidas.no_cliente,h_salidas.marca,h_salidas.fi1 asc";
			}
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
			$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
			$objPHPExcel->getActiveSheet()->setCellValue('A1', 'ASOCIACIÓN DE MAGUEY Y MEZCAL ARTESANAL');
			//$objPHPExcel->getActiveSheet()->setCellValue('A3', $consulta);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(50);
			$objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(30);
			$objPHPExcel->getActiveSheet()->mergeCells('A2:G2');
			$objPHPExcel->getActiveSheet()->setCellValue('A2', 'REPORTE DE PREDIOS');
			$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(14);
			$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A2:G2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A2:G2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->mergeCells('A3:G3');
			//logotipo
			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('logo');
			$objDrawing->setDescription('PHPExcel logo');
			$objDrawing->setPath('../../images/logo_amma.jpg');       // filesystem reference for the image file
			$objDrawing->setHeight(80);                 // sets the image height to 36px (overriding the actual image height);
			$objDrawing->setCoordinates('B1');    // pins the top-left corner of the image to cell D24
			$objDrawing->setOffsetX(10);                // pins the top left corner of the image at an offset of 10 points horizontally to the right of the top-left corner of the cell
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
			//AGREGAR ENCABEZADO DE LA TABLA
			$objPHPExcel->getActiveSheet()->getStyle('A'.$r_h.':G'.$r_h)->applyFromArray($styleArray2);

			// SOCIOS | ASOCIADOS | CLIENTES
			// REGISTRO DOCUMENTAL: 200|300|600 :: HASTA 2 HECTÁREAS DE SUPERFICIE, LO DEMÁS SERÁ PROPORCIONAL 
			// REGISTRO EN SITIO:   230|330|990 :: HASTA 2 HECTÁREAS DE SUPERFICIE, LO DEMÁS SERÁ PROPORCIONAL
			// TABLA clientes
			// CAMPO asociado 0: CLIENTE  :: REGISTRO DOCUMENTAL 600(0.03 m2) 	::	
			// CAMPO asociado 1: ASOCIADO :: REGISTRO DOCUMENTAL 300(0.015 m2)	::	
			// CAMPO asociado 2: SOCIO 	  :: REGISTRO DOCUMENTAL 200(0.01 m2)	::	

			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$r_h, 'AÑO')
			->setCellValue('B'.$r_h, 'MES')
			->setCellValue('C'.$r_h, 'GUÍAS')
			->setCellValue('D'.$r_h, 'NO. CONTROL')
			->setCellValue('E'.$r_h, 'NOMBRE')
			->setCellValue('F'.$r_h, 'SUPERFICIE')
			->setCellValue('G'.$r_h, 'MONTO DE SERVICIOS');
			/*$letra = "A";
			for($x=0;$x<=$t2;$x++) {
				$objPHPExcel->getActiveSheet()->getStyle($letra."2")->getFont()->setBold(true);
				$letra++;
			}*/

			$x=$st_r;
			$arr_fila=array();
			$n = 6;
			$arrRPredios = array();
			// YEAR(p.fecharegistro) yfr,MONTH(p.fecharegistro) mfr, p.id_cliente nc, p.superficie s, c.asociado tc, c.nombre nomcli

			while($row = $res->fetch_assoc()) {
				$comb = $row["yfr"]."-".$row["mfr"]."-".$row["nc"];
				$nc = $row["nc"];
				$arrRPredios[$comb]["Y"] = $row["yfr"];
				$arrRPredios[$comb]["M"] = $row["mfr"];
				$arrRPredios[$comb]["C"] = (!isset($arrRPredios[$comb]["C"])) ? 0: $arrRPredios[$comb]["C"];
				$arrRPredios[$comb]["C"]++;
				$clienteGC[$nc] = (!isset($clienteGC[$nc])) ? 0: $clienteGC[$nc];
				$clienteGC[$nc]++;
				$arrRPredios[$comb]["NC"] = $row["nc"];
				$arrRPredios[$comb]["N"] = $row["nomcli"];
				$arrRPredios[$comb]["S"] = (!isset($arrRPredios[$comb]["S"])) ? 0: $arrRPredios[$comb]["S"];
				$arrRPredios[$comb]["S"] += $row["s"];
				$arrRPredios[$comb]["MS"] = (!isset($arrRPredios[$comb]["MS"])) ? 0: $arrRPredios[$comb]["MS"];
				$montoSuma = $arrRPredios[$comb]["MS"];
				// OBTENER MONTO DE GUÍA
				if($clienteGC[$nc] > 0 && $clienteGC[$nc] < 6)  //$200.00
					$montoSuma += 200;
				elseif($clienteGC[$nc] > 5 && $clienteGC[$nc] < 11)  //$400.00
					$montoSuma += 400;
				elseif($clienteGC[$nc] > 10 )  //$600.00
					$montoSuma += 600;
				$arrRPredios[$comb]["MS"] = $montoSuma;				
			}

			foreach ($arrRPredios as $row) {
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A".$n, $row["Y"]);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B".$n, $row["M"]);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C".$n, $row["C"]);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D".$n, $row["NC"]);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue("E".$n, $row["N"]);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue("F".$n, $row["S"]);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue("G".$n, $row["MS"]);
				$objPHPExcel->getActiveSheet()->getStyle('F'.$n)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
				$objPHPExcel->getActiveSheet()->getStyle('G'.$n)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
				$n++;

				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
			}

			
			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			//$objPHPExcel->setActiveSheetIndex(0);
			// Redirect output to a client’s web browser (Excel2007)
			//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			//header('Content-Disposition: attachment;filename="ReporteGral.xlsx"');
			//header('Cache-Control: max-age=0');

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
