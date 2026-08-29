<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;

session_set_cookie_params(0, "/", $_SERVER["HTTP_HOST"], 0);

session_start();
$mod=1;
require_once(__DIR__ . "/../../../common/cfg_server.php");
$d_s=$_POST["id_s"];
if(isset($_SESSION[$d_s]))
{
	if($_SESSION[$d_s]["logged"] != "OK") {
		header("location: http://".$svr_dir."/acceso/login.php?mod=$mod");
	} else if($_SESSION[$d_s]["logged"] == "OK" &&  $_SESSION[$d_s]["seccion_1_4"]=="logged") {
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		date_default_timezone_set('Mexico/General');
		if (PHP_SAPI === 'cli')
		die('This example should only be run from a Web Browser');
		
		/** Cargar PhpSpreadsheet vía Composer */
		require_once __DIR__ . '/../../../vendor/autoload.php';
		include(__DIR__ . '/../../../common/conexion.php');
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
		$consulta="select h_salidas.id_recibo, h_salidas.anio_rcbo, h_salidas.no_cliente, if(h_salidas.marca='0','',h_salidas.marca) cve, if(marcas.marca is null,'',marcas.marca) marca, h_salidas.serie, h_salidas.edo, h_salidas.tipo, if(h_salidas.solicitud='','S/N',h_salidas.solicitud) solicitud, h_salidas.fecha_entr, h_salidas.destino, h_salidas.fi1, h_salidas.ff1, h_salidas.m1, h_salidas.motivo, h_salidas.m2, h_salidas.se1 from h_salidas left join marcas on marcas.no_cliente=h_salidas.no_cliente and marcas.cve_marca=h_salidas.marca where";
		$sql_sum="select if(h_salidas.marca='','GEN',h_salidas.marca) cve, if(marcas.marca is null,'GEN',marcas.marca) marca, if(h_salidas.serie='','GEN',h_salidas.serie) serie, sum(h_salidas.se1) suma 
		from h_salidas 
		left join marcas on marcas.no_cliente=h_salidas.no_cliente and marcas.cve_marca=h_salidas.marca 
		where";
		//$str_sql="select * from sellos_2013 order by fecha_entr";
		
		if(isset($_POST['cliente'])) {
			$tipo=$_POST['tipo'];
			$cliente=$_POST['cliente'];
			$cliente=substr($cliente,0,5);
			$marca = ($_POST['marca'] != "" && $_POST['marca'] != "undefined")? $_POST['marca']: "";
			$marca = ($marca != "") ? " and h_salidas.marca = '{$marca}' " : "";
			$tipo_m=$_POST['tipo_m'];
			$fecha1=$_POST['fecha1'];
			$fecha2=$_POST['fecha2'];
		    $estado=$_POST['estado'];
	        $categoria=$_POST['categoria'];
			switch($tipo) {
				case 1:
                    $consulta .= " h_salidas.no_cliente like '%$cliente%'";
                    $sql_sum.=" h_salidas.no_cliente like '%$cliente%'";
                    $he1="Hologramas entregados al cliente: ".$cliente;
                    $msj1="";
                    $file_name=$cliente.'_gral.xlsx';
                    break;
				case 2:
                    $consulta .= " h_salidas.no_cliente like '%$cliente%' and h_salidas.serie=''";
                    $sql_sum.=" h_salidas.no_cliente like '%$cliente%' and h_salidas.serie=''";
                    $he1="Hologramas entregados al cliente: ".$cliente;
                    $msj1="";
                    $file_name=$cliente.'_gen.xlsx';
                    break;
				case 3:
                    $consulta .= " h_salidas.no_cliente like '%$cliente%' and h_salidas.serie!=''";
                    $sql_sum.="  h_salidas.no_cliente like '%$cliente%' and h_salidas.serie!=''";
                    $he1="Hologramas entregados al cliente: ".$cliente;
                    $msj1="";
                    $file_name=$cliente.'_per.xlsx';
                    break;
				case 4:
                    switch($tipo_m) {
						case 'T':
                            $consulta .= " h_salidas.no_cliente like '%$cliente%' {$marca} ";
                            $sql_sum.="  h_salidas.no_cliente like '%$cliente%' {$marca} ";
                            $he1="Relación de hologramas entregados al cliente: ".$cliente;
                            $msj1="Para la marca: ";
                            $file_name=$cliente.'_gral_mca.xlsx';
                            break;
						case 'G':
                            $consulta .= " h_salidas.no_cliente like '%$cliente%' {$marca} and h_salidas.serie=''";
                            $sql_sum.="  h_salidas.no_cliente like '%$cliente%' {$marca} and h_salidas.serie=''";
                            $he1="Relación de hologramas entregados al cliente: ".$cliente;
                            $msj1="Para la marca: ";
                            $file_name=$cliente.'_gen_mca.xlsx';
                            break;
						case 'P':
                            $consulta .= " h_salidas.no_cliente like '%$cliente%' {$marca} and h_salidas.serie!=''";
                            $sql_sum.="  h_salidas.no_cliente like '%$cliente%' {$marca} and h_salidas.serie!=''";
                            $he1="Relación de hologramas entregados al cliente: ".$cliente;
                            $msj1="Para la marca: ";
                            $file_name=$cliente.'_per_mca.xlsx';
                            break;
					}
                    break;
			}


			if($estado!="T" && empty($estado)) {

				$consulta.=" and h_salidas.edo='$estado'";
				$sql_sum.=" and h_salidas.edo='$estado'";
			}

			if($categoria!="" && $categoria!="T" && empty($estado) ) {
				$consulta.=" and h_salidas.tipo=$categoria";
				$sql_sum.=" and h_salidas.tipo=$categoria";
			}


			if(trim($fecha1) !== ''&&trim($fecha2) !== '') {
				$consulta.=" and h_salidas.fecha_entr between '$fecha1' and '$fecha2' ORDER BY h_salidas.marca,h_salidas.fi1 asc";
				$sql_sum.=" and h_salidas.fecha_entr between '$fecha1' and '$fecha2' group by h_salidas.marca, h_salidas.serie ORDER BY h_salidas.marca,h_salidas.fi1 asc";
				$periodo=fecha($fecha1).'  a  '.fecha($fecha2);
				$msj_per="Periodo:";
			}
			else if(trim($fecha1) !== '') {
				$consulta.=" and fecha_entr='$fecha1' ORDER BY h_salidas.marca,h_salidas.fi1 asc";
				$sql_sum.=" and fecha_entr='$fecha1' group by h_salidas.marca, h_salidas.serie ORDER BY h_salidas.marca,h_salidas.fi1 asc";
				$periodo=fecha($fecha1);
				$msj_per="Fecha:";
			} else {
				$consulta.="  ORDER BY h_salidas.marca,h_salidas.fi1 asc";
				$sql_sum.="  group by h_salidas.marca, h_salidas.serie ORDER BY h_salidas.marca,h_salidas.fi1 asc";
			}
			$consulta .= "  ";
			$res=$conexion->query($consulta);
			$tot=$res->num_rows;
			$t2=$res->field_count;
			$letras=['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC'];
			// Crear nuevo objeto Spreadsheet
			$spreadsheet = new Spreadsheet();
			// Set document properties
			$spreadsheet->getProperties()->setCreator("NJGC")
			->setLastModifiedBy("AMMA")
			->setTitle("REPORTES")
			->setSubject("REPORTES")
			->setDescription("REPORTE GENERAL")
			->setKeywords("office 2007 openxml php")
			->setCategory("REPORTE");
			$styleArray = [
				'font' => [
					'bold' => true,
					],
				'alignment' => [
					'horizontal' => Alignment::HORIZONTAL_CENTER,
					],
				'borders' => [
					'allborders' => [
						'style' => Border::BORDER_THIN,
						],
					],
				'fill' => [
					'type' => Fill::FILL_GRADIENT_LINEAR,'rotation' => 90,
					'startcolor' => [
						'argb' => 'FFA0A0A0',
					],
					'endcolor' => [
						'argb' => 'FFFFFFFF',
					],
				],		
			];
			$styleArray2 = [
				'font' => [
					'bold' => true,
					'color'=>['rgb'=>'ffffff'],
				],
				'borders' => [
					'allborders' => [
						'style' => Border::BORDER_THIN,
						'color' => ['rgb' => '9DB2B3'],
						],
					],
				'alignment' => [
					'horizontal' => Alignment::HORIZONTAL_CENTER,
					'vertical' => Alignment::VERTICAL_CENTER,
				],
				'fill' => [
					'type' => Fill::FILL_SOLID,
					'color' => ['rgb'=>'23719E'],
				],
			];
			$styleArray3 = [
				'font' => [
					'bold' => true,
					'color'=>['rgb'=>'ffffff'],
				],
				'borders' => [
					'allborders' => [
						'style' => Border::BORDER_THIN,
						'color' => ['rgb' => '6A8696'],
						],
					],
				'alignment' => [
					'horizontal' => Alignment::HORIZONTAL_RIGHT,
					'vertical' => Alignment::VERTICAL_CENTER,
				],
				'fill' => [
					'type' => Fill::FILL_SOLID,
					'color' => ['rgb'=>'2E966D'],
				],
			];
				
			//HEADER
			$spreadsheet->getActiveSheet()->mergeCells('D1:I1');
			$spreadsheet->getActiveSheet()->setCellValue('D1', 'ASOCIACIÓN DE MAGUEY Y MEZCAL ARTESANAL');
			$spreadsheet->getActiveSheet()->getStyle('D1')->getFont()->setSize(18);
			$spreadsheet->getActiveSheet()->getStyle('D1')->getFont()->setBold(true); 
			$spreadsheet->getActiveSheet()->getStyle('D1:I1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			$spreadsheet->getActiveSheet()->getRowDimension(1)->setRowHeight(50);
			$spreadsheet->getActiveSheet()->getRowDimension(2)->setRowHeight(30);
			$spreadsheet->getActiveSheet()->mergeCells('D2:I2');
			$spreadsheet->getActiveSheet()->setCellValue('D2', 'Reporte de Entrega de Hologramas');
			$spreadsheet->getActiveSheet()->getStyle('D2')->getFont()->setSize(13);
			$spreadsheet->getActiveSheet()->getStyle('D2')->getFont()->setBold(true); 
			$spreadsheet->getActiveSheet()->getStyle('D2:I2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			$spreadsheet->getActiveSheet()->getStyle('D2:I2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
			$spreadsheet->getActiveSheet()->mergeCells('D3:I3');
			$spreadsheet->getActiveSheet()->setCellValue('D3', $he1);
			$spreadsheet->getActiveSheet()->getStyle('D3')->getFont()->setSize(12);
			$spreadsheet->getActiveSheet()->getStyle('D3')->getFont()->setBold(true); 
			$spreadsheet->getActiveSheet()->getStyle('D3:I3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			//logotipo       
			$spreadsheet->getDefaultStyle()->getFont()->setName('Calibri');
			$spreadsheet->getDefaultStyle()->getFont()->setSize(11);     
			$spreadsheet->getActiveSheet()->mergeCells('B1:C2');
			$objDrawing = new Drawing();
			$objDrawing->setName('logo');
			$objDrawing->setDescription('Logo AMMA');
			$objDrawing->setPath('../../../images/logo_amma.jpg');       // filesystem reference for the image file
			$objDrawing->setHeight(80);                 // sets the image height to 36px (overriding the actual image height); 
			$objDrawing->setCoordinates('B1');    // pins the top-left corner of the image to cell D24
			$objDrawing->setOffsetX(10);                // pins the top left corner of the image at an offset of 10 points horizontally to the right of the top-left corner of the cell
			$objDrawing->setWorksheet($spreadsheet->getActiveSheet());
			//AGREGAR ENCABEZADO DE LA TABLA
			$spreadsheet->getActiveSheet()->getStyle('A'.$r_h.':N'.$r_h)->applyFromArray($styleArray2);
				
			$spreadsheet->setActiveSheetIndex(0);     
			$spreadsheet->setActiveSheetIndex(0)
			->setCellValue('A'.$r_h, 'Recibo')
			->setCellValue('B'.$r_h, 'Marca')
			->setCellValue('C'.$r_h, 'Serie')
			->setCellValue('D'.$r_h, 'Estado')
			->setCellValue('E'.$r_h, 'Categoria')
			->setCellValue('F'.$r_h, 'Solicitud')
			->setCellValue('H'.$r_h, 'Destino')
			->setCellValue('H'.$r_h, 'Fecha Entrega')
			->setCellValue('I'.$r_h, 'Folio Inicial')
			->setCellValue('J'.$r_h, 'Folio Final')
			->setCellValue('K'.$r_h, 'Mermas Entrega')
			->setCellValue('L'.$r_h, 'Motivo')
			->setCellValue('M'.$r_h, 'Mermas Reportadas')
			->setCellValue('N'.$r_h, 'Sellos Entregados'); 
			for($x=0;$x<=$t2;$x++) {
				$fil=$letras[$x].'2';
				$spreadsheet->getActiveSheet()->getStyle($fil)->getFont()->setBold(true);   
			}
				
			$x=$st_r;
			$arr_fila=[];
			while($fila= $res->fetch_assoc()) {
				if($fila["anio_rcbo"]==0) {
					$arr_fila[0]='----';
				} else {
					$arr_fila[0]='AR'.str_pad($fila["id_recibo"],4,'0',STR_PAD_LEFT).'/'.$fila["anio_rcbo"];
				}
				//MARCA
				$arr_fila[1]=mb_convert_encoding($fila["marca"], 'UTF-8', 'ISO-8859-1');	  
				if($arr_fila[1]=="") {
					$arr_fila[1]="N/A";
				}	 
				//CLAVE
				if($fila['cve']=='') {
					$new_index='GEN_';
				} else {
					$new_index=$fila['cve'].'_';
				}
				//SERIE
				$arr_fila[2]=$fila["serie"];					  
				if($arr_fila[2]=='') {
					$arr_fila[2] ="N/A";					
				} 		
				//SOLICITUD
				$arr_fila[3]=$fila["solicitud"];
				if($arr_fila[3]=='') {
					$arr_fila[3] ="S/N";
				}
				//ESTADO
				
				$arr_fila[4]=$fila["edo"];
				if($arr_fila[4]=="") {
				$arr_fila[4]="N/A";
				}	

				//CATEGORIA
				switch($fila["tipo"]) {
					case 0:
                        $arr_fila[5]="N/A";
                        break;
					case 1:
                        $arr_fila[5]="MEZCAL";
                        break;
					case 2:
                        $arr_fila[5]="ARTESANAL";
                        break;
					case 3:
                        $arr_fila[5]="ANCESTRAL";
                        break;
					
				}
				//DESTINO
				$arr_fila[6]=$fila["destino"];
				$arr_fila[7]=$fila["fecha_entr"];
				//FOLIOS
				if($fila["serie"]!='') {
				$arr_fila[8]=$fila["no_cliente"].$fila["cve"].str_pad($fila["fi1"], 7,'0',STR_PAD_LEFT).$fila["serie"];
				$arr_fila[9]=$fila["no_cliente"].$fila["cve"].str_pad($fila["ff1"], 7,'0',STR_PAD_LEFT).$fila["serie"];
				} else {
				$arr_fila[8]=$fila["fi1"];
				$arr_fila[9]=$fila["ff1"];
				}			
				//MERMAS ENTREGA
				//$arr_fila[8]=$fila["m1"];
				//$arr_fila[9]=$fila["motivo"];
				$arr_fila[10]=$fila["m1"];
				$arr_fila[11]=$fila["motivo"];
				//MERMAS REPORTADAS
				$arr_fila[12]=$fila["m2"];
				$arr_fila[13]=$fila["se1"];
				/*prueba
				$arr_fila[10]=$new_index;
				$arr_fila[11]=$last_index;*/	
					
					
				if($new_index !== $last_index) {
					if($x>$st_r) {
					//agregar suma 
						$pos_fin='N'.($x-1);
						$formu="=sum(".$pos_ini.":".$pos_fin.")";
						//$formu="";
						$pos_formu='N'.($x);
						$spreadsheet->setActiveSheetIndex(0)->setCellValue($pos_formu, $formu);
						$spreadsheet->getActiveSheet()->getStyle($pos_formu)->applyFromArray($styleArray3);
						$spreadsheet->getActiveSheet()->getStyle($pos_formu)->getNumberFormat()->setFormatCode("#,##0");
						if($bandera_color === 0){
							$fill_color="DDEBF7"; 
							$bandera_color=1;
						} else{
							$fill_color="F2F2F2";
							$bandera_color=0;
						}
						$x += 3; 
						$pos_ini='N'.$x;
					} else {   
					//GUARDAMOS EL INICIO DE UNA NUEVA MARCA Y ESTABLECEMOS EL CAMBIO DE COLOR
						$pos_ini="N5";
						$fill_color="F2F2F2";
					}//Endif $X>5;
					
				}//ENDIF NEWINDEX VS LASTINDEX			 
				for($i=1;$i<=14;$i++) {
					$c=$letras[$i-1].$x;
					$dato=$arr_fila[$i-1];
					$spreadsheet->setActiveSheetIndex(0)->setCellValue($c, $dato);
					$spreadsheet->getActiveSheet()->getColumnDimension($letras[$i-1])->setAutoSize(true);
					if($i === 14) {
						$spreadsheet->getActiveSheet()->getStyle($c)->getNumberFormat()->setFormatCode("#,##0");
					}
				}	
				$spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(false);
				$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(40);
					
			//$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(80);
				$cell_ini='A'.$x;
				$cell_fin='N'.$x;
				$spreadsheet->getActiveSheet()->getStyle($cell_ini.":".$cell_fin)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
				$spreadsheet->getActiveSheet()->getStyle($cell_ini.":".$cell_fin)->getBorders()->getAllBorders()->getColor()->setRGB('23719E');
				$spreadsheet->getActiveSheet()->getStyle($cell_ini.":".$cell_fin)->getFill()->setFillType(Fill::FILL_SOLID); 
				$spreadsheet->getActiveSheet()->getStyle($cell_ini.":".$cell_fin)->getFill()->getStartColor()->setARGB($fill_color);
				
				$last_index=$new_index;
				$x++;	
				$cont_result++;
			}
				//ESCRIBIMOS LA FORMULA DE LA ULTIMA LISTA
			$pos_fin='N'.($x-1);
			$formu = "=sum(".$pos_ini.":".$pos_fin.")";
			//$formu="";
			$pos_formu='N'.($x);
			$spreadsheet->setActiveSheetIndex(0)->setCellValue($pos_formu, $formu);
			$spreadsheet->getActiveSheet()->getStyle($pos_formu)->applyFromArray($styleArray3);
			$spreadsheet->getActiveSheet()->getStyle($pos_formu)->getNumberFormat()->setFormatCode("#,##0");
			//INMOBILIZAR LOS ENCABEZADOS
			$spreadsheet->getActiveSheet()->freezePane('A7');
			// Rename worksheet
			$spreadsheet->getActiveSheet()->setTitle('SellosEntregados');
				
			//PARA EL RESUMEN
			if($_POST['resumen']=='SI') {
				$bandera_color=1;
				$spreadsheet->createSheet(1);
				$spreadsheet->setActiveSheetIndex(1);
				$spreadsheet->getActiveSheet()->setTitle('Resumen');
				//HEADER
				$spreadsheet->getActiveSheet()->mergeCells('C1:I1');
				$spreadsheet->getActiveSheet()->setCellValue('C1', 'ASOCIACIÓN DE MAGUEY Y MEZCAL ARTESANAL');
				$spreadsheet->getActiveSheet()->getStyle('C1')->getFont()->setSize(18);
				$spreadsheet->getActiveSheet()->getStyle('C1')->getFont()->setBold(true); 
				$spreadsheet->getActiveSheet()->getStyle('C1:I1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
				$spreadsheet->getActiveSheet()->getRowDimension(1)->setRowHeight(50);
				$spreadsheet->getActiveSheet()->getRowDimension(2)->setRowHeight(30);
				$spreadsheet->getActiveSheet()->mergeCells('C2:I2');
				$spreadsheet->getActiveSheet()->setCellValue('C2', 'Resumen de Entrega de Hologramas');
				$spreadsheet->getActiveSheet()->getStyle('C2')->getFont()->setSize(13);
				$spreadsheet->getActiveSheet()->getStyle('C2')->getFont()->setBold(true); 
				$spreadsheet->getActiveSheet()->getStyle('C2:I2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
				$spreadsheet->getActiveSheet()->getStyle('C2:I2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
				$spreadsheet->getActiveSheet()->mergeCells('C3:I3');
				$spreadsheet->getActiveSheet()->setCellValue('C3', $he1);
				$spreadsheet->getActiveSheet()->getStyle('C3')->getFont()->setSize(12);
				$spreadsheet->getActiveSheet()->getStyle('C3')->getFont()->setBold(true); 
				$spreadsheet->getActiveSheet()->getStyle('C3:I3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
				//logotipo       
				$spreadsheet->getDefaultStyle()->getFont()->setName('Calibri');
				$spreadsheet->getDefaultStyle()->getFont()->setSize(11);
				$objDrawing = new Drawing();
				$objDrawing->setName('logo');
				$objDrawing->setDescription('Logo AMMA');
				$objDrawing->setPath('../../../images/logo_amma.jpg');       // filesystem reference for the image file
				$objDrawing->setHeight(80);                 // sets the image height to 36px (overriding the actual image height); 
				$objDrawing->setCoordinates('B1');    // pins the top-left corner of the image to cell D24
				$objDrawing->setOffsetX(10);                // pins the top left corner of the image at an offset of 10 points horizontally to the right of the top-left corner of the cell
				$objDrawing->setWorksheet($spreadsheet->getActiveSheet());
				//AGREGAR ENCABEZADO DE LA TABLA
				$spreadsheet->setActiveSheetIndex(1);
				$r_h=6;
				$spreadsheet->getActiveSheet()->getStyle('B'.$r_h.':D'.$r_h)->applyFromArray($styleArray2);
				$spreadsheet->setActiveSheetIndex(1);     
				$spreadsheet->setActiveSheetIndex(1)->setCellValue('B'.$r_h, 'Marca')->setCellValue('C'.$r_h, 'Serie')->setCellValue('D'.$r_h, 'Cantidad'); 				
					
				$res_sum=$conexion->query($sql_sum);
				//echo $sql_sum;
				if($res_sum->num_rows>0)
				{
					$x=7;
					while($row_sum=$res_sum->fetch_array())
					{
						if($bandera_color === 0){
							$fill_color="DDEBF7"; 
							$bandera_color=1;
						} else {
							$fill_color="F2F2F2";
							$bandera_color=0;
						}
						for($i=2;$i<=4;$i++) {
							$c=$letras[$i-1].$x;
							$dato=mb_convert_encoding($row_sum[$i-1], 'UTF-8', 'ISO-8859-1');
							$spreadsheet->setActiveSheetIndex(1)->setCellValue($c, $dato);
							$spreadsheet->getActiveSheet()->getColumnDimension($letras[$i-1])->setAutoSize(true);
							if($i === 4) {
								$spreadsheet->getActiveSheet()->getStyle($c)->getNumberFormat()->setFormatCode("#,##0");
							}
						}
						$cell_ini='B'.$x;
						$cell_fin='D'.$x;
						$spreadsheet->getActiveSheet()->getStyle($cell_ini.":".$cell_fin)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
						$spreadsheet->getActiveSheet()->getStyle($cell_ini.":".$cell_fin)->getBorders()->getAllBorders()->getColor()->setRGB('23719E');
						$spreadsheet->getActiveSheet()->getStyle($cell_ini.":".$cell_fin)->getFill()->setFillType(Fill::FILL_SOLID); 
						$spreadsheet->getActiveSheet()->getStyle($cell_ini.":".$cell_fin)->getFill()->getStartColor()->setARGB($fill_color);
						$x++;
					}
					$pos_ini='D7';
					$pos_fin='D'.($x-1);
					$formu="=sum(".$pos_ini.":".$pos_fin.")";
					//$formu="";
					$pos_formu='D'.($x);
					$spreadsheet->setActiveSheetIndex(1)->setCellValue($pos_formu, $formu);
					$spreadsheet->getActiveSheet()->getStyle($pos_formu)->applyFromArray($styleArray3);
					$spreadsheet->getActiveSheet()->getStyle($pos_formu)->getNumberFormat()->setFormatCode("#,##0");
				}
				   
			}
			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$spreadsheet->setActiveSheetIndex(0);
			// Redirect output to a client’s web browser (Excel2007)
			//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			//header('Content-Disposition: attachment;filename="ReporteGral.xlsx"');
			//header('Cache-Control: max-age=0');
			$objWriter = IOFactory::createWriter($spreadsheet, 'Xlsx');
			$objWriter->save('../../tmp_excel/'.$file_name);
			$dir_file="http://".$svr_dir."/hologramas/tmp_excel/".$file_name;				
			echo json_encode(['status' => 'OK','msj'=>$dir_file, 'sql'=>$consulta]);
			exit;
				
			//FIN DEL SCRIPT PARA GENERAR EL ARCHIVO
		} else {	//FIN ISSET CLIENTE
			echo json_encode(['status' => 'error','msj'=>'datos vacios']);
		}
	}
	else
	{
	  header("location: http://".$svr_dir."/acceso/login.php?mod=$mod");
	}
}
else {
  header("location: http://".$svr_dir."/acceso/login.php?mod=$mod");	
}

function fecha($fech)
{
	$dat=explode('-',$fech);
	$m='';
	switch($dat[1])
	{
		case '01':
            $m="Ene";
            break;
		case '02':
            $m="Feb";
            break;
		case '03':
            $m="Mar";
            break;
		case '04':
            $m="Abr";
            break;
		case '05':
            $m="May";
            break;
		case '06':
            $m="Jun";
            break;
		
		case '07':
            $m="Jul";
            break;
		case '08':
            $m="Ago";
            break;
		case '9':
            $m="Sep";
            break;
		case '10':
            $m="Oct";
            break;
		case '11':
            $m="Nov";
            break;
		case '12':
            $m="Dic";
            break;
		
	}
		return $dat[2]."-".$m."-".$dat[0];
}
?>