<?php
session_start();
session_set_cookie_params(0, "/", $_SERVER["HTTP_HOST"], 0);
$mod=1;
require_once("../../common/cfg_server.php");
//$d_s=$_POST["id_s"];
/*if(isset($_SESSION[$d_s]))
{*/
	/*if($_SESSION[$d_s]["logged"] != "OK")
	{
		header("location: http://".$svr_dir."/acceso/login.php?mod=$mod");
	}
	else if($_SESSION[$d_s]["logged"] == "OK" &&  $_SESSION[$d_s]["seccion_1_4"]=="logged")
	{*/
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
		$fecha = date("Y-m-d" );
		$msj1="";
		$file_name="";
		$periodo="";
		$msj_per="";

		$new_index="";
		$last_index="";
		$cont_result=1;
		$r_h=3; // es el numero de fila del encabezado
		$st_r=6; //es el numero a partir del cual se agregaran los datos
		//$arr_sum=array();
		//$array_resumen=array();
		//$colors=array('FCE4D6','DDEBF7','E2EFDA','FFF2CC','EFEFEF','DDF0F2','EFE7F3','DBEFD3');
		//$colors=array('FCE4D6','DDEBF7','E2EFDA','EFE7F3','EFEFEF','FFF2CC','DDF0F2','DBEFD3');
		$fill_color="";
		$bandera_color=0;
		$pos_ini="";
		$pos_fin="";
		

		//if(isset($_POST['fechaini'])){

			/*$fecha1 = $_POST['fechaini'];
			$fecha2 = $_POST['fechafin'];**/

			$he1 = "";
			$msj1 = "";
			$file_name = 'guias_' . rand() . '.xlsx';
			$operador = " where ";

			$sqlP = "select *from cextracciones";
			if ($resultado = $conexion->query($sqlP))
				$infoCampo = $resultado->fetch_fields();
			$sqlP = "select *from historial_extraccion_verificadores";
			if ($resultado = $conexion->query($sqlP))
				$infoCampo2 = $resultado->fetch_fields();
			
			$cadenaSql = "SELECT c.id_extraccion cid_extraccion, p.id_paraje, p.paraje p_paraje, 
			p.id_cliente pid_cliente, c.fecha c_fecha, hev.no_cliente_recibe, hev.no_cliente_envia, 
			p.maguey_con_registro, p.servicio, pe.no_pinas_agave, pe.tapada, pe.lts_producidos
			FROM paraje p 
			INNER JOIN cextracciones c ON p.id_paraje = c.id_paraje
			LEFT JOIN historial_extraccion_verificadores hev ON c.id_extraccion = hev.no_guia 
			LEFT JOIN rv_produccion_entrada pe ON c.id_extraccion = pe.no_guia 
			WHERE p.status = '1' ORDER BY p.id ASC";
			
			//echo $cadenaSql;
				$res=$conexion->query($cadenaSql);
				/*$tot=$res->num_rows;
				$t2=$res->field_count;*/
				$letras=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC');
				// Create new PHPExcel object
				$objPHPExcel = new PHPExcel();
				// Set document properties
				$objPHPExcel->getProperties()->setCreator("NJGC")
				->setLastModifiedBy("AMMA")
				->setTitle("REPORTES")
				->setSubject("REPORTES")
				->setDescription("REPORTE GENERAL GUÍAS")
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
					 $objPHPExcel->getActiveSheet()->mergeCells('D1:I1');
					 $objPHPExcel->getActiveSheet()->setCellValue('D1', 'ASOCIACIÓN DE MAGUEY Y MEZCAL ARTESANAL');
					 //$objPHPExcel->getActiveSheet()->setCellValue('A3', $consulta);
					 $objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setSize(18);
					 $objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
					 $objPHPExcel->getActiveSheet()->getStyle('D1:I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					 $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(50);
					 $objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(30);
					 $objPHPExcel->getActiveSheet()->mergeCells('D2:I2');
					 $objPHPExcel->getActiveSheet()->setCellValue('D2', 'REPORTE DE GUÍAS DOCUMENTALES EXCLUSIVAS');
					 $objPHPExcel->getActiveSheet()->getStyle('D2')->getFont()->setSize(14);
					 $objPHPExcel->getActiveSheet()->getStyle('D2')->getFont()->setBold(true);
					 $objPHPExcel->getActiveSheet()->getStyle('D2:I2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					 $objPHPExcel->getActiveSheet()->getStyle('D2:I2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					 //$objPHPExcel->getActiveSheet()->mergeCells('D3:I3');
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
					$objDrawing->setPath('../../images/logo_amma.jpg');       // filesystem reference for the image file
					$objDrawing->setHeight(80);                 // sets the image height to 36px (overriding the actual image height);
					$objDrawing->setCoordinates('B1');    // pins the top-left corner of the image to cell D24
					$objDrawing->setOffsetX(10);                // pins the top left corner of the image at an offset of 10 points horizontally to the right of the top-left corner of the cell
					$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
			    //AGREGAR ENCABEZADO DE LA TABLA
				$objPHPExcel->getActiveSheet()->getStyle('A'.$r_h.':K'.$r_h)->applyFromArray($styleArray2);

				$objPHPExcel->setActiveSheetIndex(0);
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A'.$r_h, '# Guía')
				->setCellValue('B'.$r_h, 'Paraje')
				->setCellValue('C'.$r_h, 'Nombre de Paraje')
				->setCellValue('D'.$r_h, '# Cliente')
				->setCellValue('E'.$r_h, 'Fecha')
				->setCellValue('F'.$r_h, 'Estado')
				->setCellValue('G'.$r_h, 'Tipo de Guía')
				->setCellValue('H'.$r_h, '# Cliente Recibe')
				->setCellValue('I'.$r_h, 'Tapada')
				->setCellValue('J'.$r_h, 'Extracción')
				->setCellValue('K'.$r_h, 'Litros Producidos');
				
				
				/*for($x=0;$x<=$t2;$x++)
				{
				$fil=$letras[$x].'2';
				$objPHPExcel->getActiveSheet()->getStyle($fil)->getFont()->setBold(true);
				}*/

				$x=$st_r;
				$arr_fila=array();
				$filan = 4;
				foreach($res as $row) {
					$letrita = "A";
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($letrita.$filan, $row["cid_extraccion"]);
					$letrita++;
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($letrita.$filan, $row["id_paraje"]);
					$letrita++;
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($letrita.$filan, $row["p_paraje"]);
					$letrita++;
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($letrita.$filan, $row["pid_cliente"]);
					$letrita++;
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($letrita.$filan, $row["c_fecha"]);
					$letrita++;
					$estado = ($row["no_cliente_envia"] != "") ? "USADA": "DISPONIBLE";
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($letrita.$filan, $estado);
					$letrita++;
					$tguia = "";
					if($row["maguey_con_registro"] == 2 && $row["servicio"] == "EXCLUSIVO") 
						$tguia = "DOCUMENTAL EXCLUSIVA";
					elseif($row["maguey_con_registro"] == 2 && $row["servicio"] == "NORMAL") 
						$tguia = "DOCUMENTAL NORMAL";
					else
						$tguia = "EN SITIO";
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($letrita.$filan, $tguia);
					$letrita++;
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($letrita.$filan, $row["no_cliente_recibe"]);
					$letrita++;
					$tapada = ""; $lts_producidos = "";
					// VALIDAR DONDE FUE USADA LA GUÍA
					if($row["tapada"] != "") {
						$tapada = $row["tapada"];
						$lts_producidos = $row["lts_producidos"];
						$no_pinas_agave = $row["no_pinas_agave"];
					} else {
						$sqlt = $conexion->prepare("SELECT pe.tapada, pe.lts_producidos, pen.no_pinas_agave 
						FROM rv_produccion_entrada pe 
						 INNER JOIN rv_produccion_ensamble pen ON pe.id_produccion_entrada = pen.id_produccion_entrada 
						 WHERE pen.no_guia = '".$row["cid_extraccion"]."' LIMIT 1 ");
						if ($sqlt) { /*si la conexion esta preparada*/
							$sqlt->execute(); /* ejecutar la consulta */
							$resultSetT = $sqlt->get_result();
							$resultT = $resultSetT->fetch_all(MYSQLI_ASSOC);
							$no_pinas_agave = 0;
							foreach($resultT as $rowt) {
								$tapada = $rowt["tapada"];
								$lts_producidos = $rowt["lts_producidos"];
								$no_pinas_agave += $rowt["no_pinas_agave"];
							}
						}
					}
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($letrita.$filan, $tapada);
					$letrita++;
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($letrita.$filan, $no_pinas_agave);
					$letrita++;
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($letrita.$filan, $lts_producidos);
					
					$filan++;
				} //
				
				$objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0,6);
				// Rename worksheet
				$objPHPExcel->getActiveSheet()->setTitle('GuíasGeneradas');

				
				// Set active sheet index to the first sheet, so Excel opens this as the first sheet
				$objPHPExcel->setActiveSheetIndex(0);
				// Redirect output to a client’s web browser (Excel2007)
				//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				//header('Content-Disposition: attachment;filename="ReporteGral.xlsx"');
				//header('Cache-Control: max-age=0');

				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				$objWriter->save('../tmp_excel/'.$file_name);
				$dir_file="http://".$svr_dir."/maguey/tmp_excel/".$file_name;
				//echo json_encode(array('status' => 'OK','msj'=>$dir_file));
				exit;
				//FIN DEL SCRIPT PARA GENERAR EL ARCHIVO
			/*}//FIN ISSET CLIENTE
			else
				echo json_encode(array('status' => 'error','msj'=>'datos vacios'));*/

	/*}
	else
	{
	  header("location: http://".$svr_dir."/acceso/login.php?mod=$mod");
	}
}
else
{
  header("location: http://".$svr_dir."/acceso/login.php?mod=$mod");
}*/

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
