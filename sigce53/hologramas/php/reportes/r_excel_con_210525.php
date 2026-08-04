<?php
session_start();
session_set_cookie_params(0, "/", $_SERVER["HTTP_HOST"], 0);
$mod=1;
require_once("../../../common/cfg_server.php");
$d_s=$_POST["id_s"];
if(isset($_SESSION[$d_s]))
{
	if($_SESSION[$d_s]["logged"] != "OK")
	{
		header("location: http://".$svr_dir."/sigce/acceso/login.php?mod=$mod");
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
		require_once '../../../libs/phpExcel/PHPExcel.php';
		include('../../../common/conexion.php');
		/** DECLARACION DE VARIABLES */
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
		//$arr_sum=array();
		//$array_resumen=array();
		//$colors=array('FCE4D6','DDEBF7','E2EFDA','FFF2CC','EFEFEF','DDF0F2','EFE7F3','DBEFD3');
		//$colors=array('FCE4D6','DDEBF7','E2EFDA','EFE7F3','EFEFEF','FFF2CC','DDF0F2','DBEFD3');
		$fill_color="";
		$bandera_color=0;
		$pos_ini="";
		$pos_fin="";
		$consulta="SELECT hs.id_recibo, 
		IF(hs.anio_rcbo ='0','----',CONCAT('AR',LPAD(hs.id_recibo,4,'0'),'/',hs.anio_rcbo)) anio_rcbo,
		hs.no_cliente, 
		IF(hs.marca = '', 'N/A', hs.marca) marca,
		IF(hs.marca='0','',hs.marca) cve, 
		IF(m.marca is null,'',m.marca) marca, 
		IF(hs.serie = '', 'N/A', hs.serie) serie,
		IF(hs.edo = '', 'N/A', hs.edo) edo,
		IF(hs.tipo = '0', 'N/A', IF(hs.tipo = '1', 'MEZCAL', IF(hs.tipo = '2', 'ARTESANAL', IF(hs.tipo = '3', 'ANCESTRAL', hs.tipo)))) tipo, 
		IF(hs.solicitud='','S/N',hs.solicitud) solicitud, 
		hs.fecha_entr, hs.destino, 
		IF(hs.serie != '', CONCAT(hs.no_cliente, hs.marca, LPAD(hs.fi1,7,'0'), hs.serie), hs.fi1 ) fi1, 
		IF(hs.serie != '', CONCAT(hs.no_cliente, hs.marca, LPAD(hs.ff1,7,'0'), hs.serie), hs.ff1 ) ff1, 
		hs.m1, hs.motivo, hs.m2, hs.se1, hs.usr 
		FROM h_salidas hs 
		LEFT JOIN marcas m ON m.no_cliente=hs.no_cliente and m.cve_marca=hs.marca";

		$sql_sum="select if(hs.marca='','GEN',hs.marca) cve, hs.no_cliente, if(m.marca is null,'GEN',m.marca) marca, if(hs.serie='','GEN',hs.serie) serie, sum(hs.se1) suma from h_salidas hs left join marcas m on m.no_cliente=hs.no_cliente and m.cve_marca=hs.marca";
		//$str_sql="select * from sellos_2013 order by fecha_entr";

		if(isset($_POST['tipo_con'])) {
			$tipo=$_POST['tipo_con'];
			$fecha1=$_POST['fecha1'];
			$fecha2=$_POST['fecha2'];

			switch($tipo)
			{
				case 'T':
				{
					$he1="Mixto Concentrado";
					$msj1="";
					$operador=" where ";
					$file_name='mixto_concentrado.xlsx';
					break;
				}
				case 'G':
				{
					$consulta .= " where hs.serie=''";
					$sql_sum.="  where hs.serie=''";
					$he1="Genéricos";
					$msj1="";
					$operador=" and ";
					$file_name='genericos_concentrado.xlsx';
					break;
				}
				case 'P':
				{
					$consulta .= " where hs.serie!=''";
					$sql_sum.="  where hs.serie!=''";
					$he1="Personalizados";
					$msj1="";
					$operador=" and ";
					$file_name='personalizado_concentrado.xlsx';
					break;
				}
			}

			if(trim($fecha1)!=''&&trim($fecha2)!='') {
				$consulta.=$operador."hs.fecha_entr between '$fecha1' and '$fecha2' ORDER BY hs.no_cliente,hs.marca,hs.fi1 asc";
				$sql_sum.=$operador."hs.fecha_entr between '$fecha1' and '$fecha2' GROUP BY concat(hs.no_cliente,hs.marca), hs.serie ORDER BY hs.no_cliente, hs.marca,   hs.fi1 asc ";
				$periodo=fecha($fecha1).'  a  '.fecha($fecha2);
				$msj_per="Periodo:";
			} else if(trim($fecha1)!='') {
				$consulta.=$operador."fecha_entr='$fecha1' ORDER BY hs.marca,hs.fi1 asc";
				$sql_sum.=$operador."fecha_entr='$fecha1' GROUP BY concat(hs.no_cliente,hs.marca), hs.serie ORDER BY hs.no_cliente, hs.marca,hs.fi1 asc";
				$periodo=fecha($fecha1);
				$msj_per="Fecha:";
			} else {
				$consulta.="  ORDER BY hs.no_cliente, hs.marca, hs.fi1 asc";
				$sql_sum.="  GROUP BY concat(hs.no_cliente,hs.marca), hs.serie ORDER BY hs.no_cliente,hs.marca,hs.fi1 asc";
			}
			
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
					'bold' => true,
					'color'=>array('rgb'=>'ffffff'),
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
			$objPHPExcel->getActiveSheet()->mergeCells('D1:I1');
			$objPHPExcel->getActiveSheet()->setCellValue('D1', 'ASOCIACIÓN DE MAGUEY Y MEZCAL ARTESANAL');
			$objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setSize(18);
			$objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('D1:I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(50);
			$objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(30);
			$objPHPExcel->getActiveSheet()->mergeCells('D2:I2');
			$objPHPExcel->getActiveSheet()->setCellValue('D2', 'REPORTE DE ENTREGA DE HOLOGRAMAS');
			$objPHPExcel->getActiveSheet()->getStyle('D2')->getFont()->setSize(14);
			$objPHPExcel->getActiveSheet()->getStyle('D2')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('D2:I2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('D2:I2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->mergeCells('D3:I3');
			$objPHPExcel->getActiveSheet()->setCellValue('D3', $he1);
			$objPHPExcel->getActiveSheet()->getStyle('D3')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('D3')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('D3:I3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//logotipo
			$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
			$objPHPExcel->getDefaultStyle()->getFont()->setSize(11);
			$objPHPExcel->getActiveSheet()->mergeCells('B1:C2');
			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('logo');
			$objDrawing->setDescription('PHPExcel logo');
			$objDrawing->setPath('../../../images/logo_amma.jpg');       // filesystem reference for the image file
			$objDrawing->setHeight(80);                 // sets the image height to 36px (overriding the actual image height);
			$objDrawing->setCoordinates('B1');    // pins the top-left corner of the image to cell D24
			$objDrawing->setOffsetX(10);                // pins the top left corner of the image at an offset of 10 points horizontally to the right of the top-left corner of the cell
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
			//AGREGAR ENCABEZADO DE LA TABLA
			$objPHPExcel->getActiveSheet()->getStyle('A'.$r_h.':O'.$r_h)->applyFromArray($styleArray2);

			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$r_h, 'Recibo')
			->setCellValue('B'.$r_h, 'No Control')
			->setCellValue('C'.$r_h, 'Marca')
			->setCellValue('D'.$r_h, 'Serie')
			->setCellValue('E'.$r_h, 'Estado')
			->setCellValue('F'.$r_h, 'Categoria')
			->setCellValue('G'.$r_h, 'Solicitud')
			->setCellValue('H'.$r_h, 'Destino')
			->setCellValue('I'.$r_h, 'Entregó')
			->setCellValue('J'.$r_h, 'Fecha Entrega')
			->setCellValue('K'.$r_h, 'Folio Inicial')
			->setCellValue('L'.$r_h, 'Folio Final')
			->setCellValue('M'.$r_h, 'Mermas Entrega')
			->setCellValue('N'.$r_h, 'Motivo')
			->setCellValue('O'.$r_h, 'Mermas Reportadas')
			->setCellValue('P'.$r_h, 'Sellos Entregados');
			for($x=0;$x<=$t2;$x++) {
				$fil=$letras[$x].'2';
				$objPHPExcel->getActiveSheet()->getStyle($fil)->getFont()->setBold(true);
			}

			$x=$st_r;
			$arrCampos = array("anio_rcbo", "no_cliente", "marca",	    "serie",      "edo",
								"tipo",		"solicitud",  "destino",    "fecha_entr", "fi1", 		
								"ff1",		"m1",		  "motivo",     "m2",		  "se1");
			while($fila= $res->fetch_assoc()) {
				$new_index .= $fila['no_cliente'];
				$new_index .= ($fila['cve'] == '') ? '_GEN_': '_'.$fila['cve'].'_';
				$new_index .= ($fila["serie"] == 'N/A') ? 'GEN': $fila["serie"];

				if($new_index!=$last_index) {
					if($x>$st_r){
					//agregar suma
						$pos_fin='O'.($x-1);
						$formu="=sum(".$pos_ini.":".$pos_fin.")";
						$pos_formu='O'.($x);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue($pos_formu, $formu);
						$objPHPExcel->getActiveSheet()->getStyle($pos_formu)->applyFromArray($styleArray3);
						$objPHPExcel->getActiveSheet()->getStyle($pos_formu)->getNumberFormat()->setFormatCode("#,##0");
						if($bandera_color==0){
							$fill_color="DDEBF7";
							$bandera_color=1;
						} else{
							$fill_color="F2F2F2";
							$bandera_color=0;
						}
						$x=$x+3;
						$pos_ini='O'.$x;
					} else {
					//GUARDAMOS EL INICIO DE UNA NUEVA MARCA Y ESTABLECEMOS EL CAMBIO DE COLOR
						$pos_ini="O5";
						$fill_color="F2F2F2";
					}//Endif $X>5;

				}//ENDIF NEWINDEX VS LASTINDEX
				
				$letra = "A";
				foreach($arrCampos as $campo) {
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($letra.$x, $fila[$campo]);
					$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
					$letra++;
				}
				$objPHPExcel->getActiveSheet()->getStyle($letra)->getNumberFormat()->setFormatCode("#,##0");
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(false);
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
				$cell_ini='A'.$x;
				$cell_fin='O'.$x;
				$objPHPExcel->getActiveSheet()->getStyle($cell_ini.":".$cell_fin)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle($cell_ini.":".$cell_fin)->getBorders()->getAllBorders()->getColor()->setRGB('23719E');
				$objPHPExcel->getActiveSheet()->getStyle($cell_ini.":".$cell_fin)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
				$objPHPExcel->getActiveSheet()->getStyle($cell_ini.":".$cell_fin)->getFill()->getStartColor()->setARGB($fill_color);

				$last_index=$new_index;
				$new_index="";
				$x++;
				$cont_result++;
				/*if($cont_result > 1800) {
					break;
				}*/
			}
			//ESCRIBIMOS LA FORMULA DE LA ULTIMA LISTA
			$pos_fin='O'.($x-1);
			$formu="=sum(".$pos_ini.":".$pos_fin.")";
			$pos_formu='O'.($x);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($pos_formu, $formu);
			$objPHPExcel->getActiveSheet()->getStyle($pos_formu)->applyFromArray($styleArray3);
			$objPHPExcel->getActiveSheet()->getStyle($pos_formu)->getNumberFormat()->setFormatCode("#,##0");
			//INMOBILIZAR LOS ENCABEZADOS
			$objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0,6);
			// Rename worksheet
			$objPHPExcel->getActiveSheet()->setTitle('SellosEntregados');

				//PARA EL RESUMEN
			if($_POST['resumen']=='SI')
			{
				$bandera_color=1;
				$objPHPExcel->createSheet(1);
				$objPHPExcel->setActiveSheetIndex(1);
				$objPHPExcel->getActiveSheet()->setTitle('Resumen');
				//HEADER
				$objPHPExcel->getActiveSheet()->mergeCells('C1:I1');
				$objPHPExcel->getActiveSheet()->setCellValue('C1', 'ASOCIACIÓN DE MAGUEY Y MEZCAL ARTESANAL');
				$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setSize(18);
				$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('C1:I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(50);
				$objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(30);
				$objPHPExcel->getActiveSheet()->mergeCells('C2:I2');
				$objPHPExcel->getActiveSheet()->setCellValue('C2', 'Resumen de Entrega de Hologramas');
				$objPHPExcel->getActiveSheet()->getStyle('C2')->getFont()->setSize(13);
				$objPHPExcel->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('C2:I2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('C2:I2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$objPHPExcel->getActiveSheet()->mergeCells('C3:I3');
				$objPHPExcel->getActiveSheet()->setCellValue('C3', $he1);
				$objPHPExcel->getActiveSheet()->getStyle('C3')->getFont()->setSize(12);
				$objPHPExcel->getActiveSheet()->getStyle('C3')->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('C3:I3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				//logotipo
				$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
				$objPHPExcel->getDefaultStyle()->getFont()->setSize(11);
				$objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('logo');
				$objDrawing->setDescription('PHPExcel logo');
				$objDrawing->setPath('../../../images/logo_amma.jpg');       // filesystem reference for the image file
				$objDrawing->setHeight(80);                 // sets the image height to 36px (overriding the actual image height);
				$objDrawing->setCoordinates('B1');    // pins the top-left corner of the image to cell D24
				$objDrawing->setOffsetX(10);                // pins the top left corner of the image at an offset of 10 points horizontally to the right of the top-left corner of the cell
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
				//AGREGAR ENCABEZADO DE LA TABLA
				$objPHPExcel->setActiveSheetIndex(1);
				$r_h=6;
				$objPHPExcel->getActiveSheet()->getStyle('B'.$r_h.':E'.$r_h)->applyFromArray($styleArray2);
				$objPHPExcel->setActiveSheetIndex(1);
				$objPHPExcel->setActiveSheetIndex(1)
				->setCellValue('B'.$r_h, 'No Control')
				->setCellValue('C'.$r_h, 'Marca')
				->setCellValue('D'.$r_h, 'Serie')
				->setCellValue('E'.$r_h, 'Cantidad');

				
				$res_sum=$conexion->query($sql_sum);
				if($res_sum->num_rows>0) {
					$x=7;
					while($row_sum=$res_sum->fetch_array())
					{
						if($bandera_color==0){
						$fill_color="DDEBF7";
						$bandera_color=1;
						} else {
							$fill_color="F2F2F2";
							$bandera_color=0;
						}
						for($i=2;$i<=5;$i++) {
							$c=$letras[$i-1].$x;
							$dato=utf8_encode($row_sum[$i-1]);
							$objPHPExcel->setActiveSheetIndex(1)->setCellValue($c, $dato);
							$objPHPExcel->getActiveSheet()->getColumnDimension($letras[$i-1])->setAutoSize(true);
							if($i==5) {
								$objPHPExcel->getActiveSheet()->getStyle($c)->getNumberFormat()->setFormatCode("#,##0");
							}
						}
						$cell_ini='B'.$x;
						$cell_fin='E'.$x;
						$objPHPExcel->getActiveSheet()->getStyle($cell_ini.":".$cell_fin)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($cell_ini.":".$cell_fin)->getBorders()->getAllBorders()->getColor()->setRGB('23719E');
						$objPHPExcel->getActiveSheet()->getStyle($cell_ini.":".$cell_fin)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
						$objPHPExcel->getActiveSheet()->getStyle($cell_ini.":".$cell_fin)->getFill()->getStartColor()->setARGB($fill_color);
						$x++;
					}
					$pos_ini='E7';
					$pos_fin='E'.($x-1);
					$formu="=sum(".$pos_ini.":".$pos_fin.")";
					$pos_formu='E'.($x);
					$objPHPExcel->setActiveSheetIndex(1)->setCellValue($pos_formu, $formu);
					$objPHPExcel->getActiveSheet()->getStyle($pos_formu)->applyFromArray($styleArray3);
					$objPHPExcel->getActiveSheet()->getStyle($pos_formu)->getNumberFormat()->setFormatCode("#,##0");
				}

			}
			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel->setActiveSheetIndex(0);
			// Redirect output to a client’s web browser (Excel2007)
			//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			//header('Content-Disposition: attachment;filename="ReporteGral.xlsx"');
			//header('Cache-Control: max-age=0');

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save('../../tmp_excel/'.$file_name);
			$dir_file="http://".$svr_dir."/sigce/hologramas/tmp_excel/".$file_name;
			echo json_encode(array('status' => 'OK','msj'=>$dir_file));
			exit;
			//FIN DEL SCRIPT PARA GENERAR EL ARCHIVO
			
		} else {
			echo json_encode(array('status' => 'error','msj'=>'datos vacios'));
		}
	} else {
	  header("location: http://".$svr_dir."/sigce/acceso/login.php?mod=$mod");
	}
} else {
  header("location: http://".$svr_dir."/sigce/acceso/login.php?mod=$mod");
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
