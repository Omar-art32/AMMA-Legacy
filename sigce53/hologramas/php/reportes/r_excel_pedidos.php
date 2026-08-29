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
		$pos_ini="O6";
		$pos_fin="";
		$consultai = "SELECT h_pedidos.id_row, h_pedidos.no_pedido, DATE(h_pedidos.fecha) fecha, h_pedidos.no_cliente, h_pedidos.marca cve ,
        h_pedidos.serie, marcas.marca, h_pedidos.edo, h_pedidos.tipo, h_pedidos.fi, h_pedidos.ff, h_pedidos.cantidad, h_pedidos.status, h_pedidos.urgente,
        shp.tipo_pago, shp.comprobante, s.folio, h_pedidos.holograma
        FROM h_pedidos
        inner join marcas on marcas.no_cliente=h_pedidos.no_cliente and marcas.cve_marca=h_pedidos.marca
        INNER JOIN sh_detalle sh ON sh.id=h_pedidos.id_sh_d
        INNER JOIN sh_pedidos shp ON sh.id_solicitud = shp.id_solicitud
        INNER JOIN solicitudes s ON s.id = shp.id_solicitud ";

		$sql_sum="select if(h_salidas.marca='','GEN',h_salidas.marca) cve, h_salidas.no_cliente, if(marcas.marca is null,'GEN',marcas.marca) marca, if(h_salidas.serie='','GEN',h_salidas.serie) serie, sum(h_salidas.se1) suma from h_salidas left join marcas on marcas.no_cliente=h_salidas.no_cliente and marcas.cve_marca=h_salidas.marca";
		//$str_sql="select * from sellos_2013 order by fecha_entr";

		if(isset($_POST['fechaini'])){

			$fecha1 = $_POST['fechaini'];
			$fecha2 = $_POST['fechafin'];
			$nocliente = $_POST['nocliente'];
			$consulta = "";

			$he1 = "";
			$msj1 = "";
			$file_name = 'pedidos_' . random_int(0, mt_getrandmax()) . '.xlsx';
			$operador = " where ";
			$order = "";

			if(trim($fecha1) !== '' && trim($fecha2) !== '') {
				//echo $_POST['fechaini'];
				//echo $_POST['fechafin'];
				$consulta .= ($consulta !== "") ? " AND " : " WHERE ";
				$consulta .= " DATE(h_pedidos.fecha) BETWEEN '$fecha1' AND '$fecha2' "; 
				$order = " ORDER BY h_pedidos.no_cliente,h_pedidos.marca,h_pedidos.fi asc ";
				$periodo=fecha($fecha1).'  a  '.fecha($fecha2);
				$msj_per="Periodo:";
			} else if( trim($fecha1) !== '') {
				$consulta .= ($consulta !== "") ? " AND " : " WHERE ";
				$consulta .= " DATE(h_pedidos.fecha) = '$fecha1' ";
				$order = " ORDER BY h_pedidos.marca, h_pedidos.fi asc ";
				$periodo=fecha($fecha1);
				$msj_per="Fecha:";
			} 
			if ($nocliente != "") {
	            $consulta .= ($consulta !== "") ? " AND " : " WHERE ";
	            $consulta .= " h_pedidos.no_cliente IN ('".$nocliente."')  ";
	            $order = " ORDER BY h_pedidos.fecha asc ";
	        } 
			$consulta = $consultai . $consulta . $order;
			//echo $consulta;
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
						'bold' => false,
						/*'color'=>array('rgb'=>'ffffff'),*/
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
					 //$spreadsheet->getActiveSheet()->setCellValue('A3', $consulta);
					 $spreadsheet->getActiveSheet()->getStyle('D1')->getFont()->setSize(18);
					 $spreadsheet->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
					 $spreadsheet->getActiveSheet()->getStyle('D1:I1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
					 $spreadsheet->getActiveSheet()->getRowDimension(1)->setRowHeight(50);
					 $spreadsheet->getActiveSheet()->getRowDimension(2)->setRowHeight(30);
					 $spreadsheet->getActiveSheet()->mergeCells('D2:I2');
					 $spreadsheet->getActiveSheet()->setCellValue('D2', 'REPORTE DE HOLOGRAMAS SOLICITADOS A PROVEEDOR');
					 $spreadsheet->getActiveSheet()->getStyle('D2')->getFont()->setSize(14);
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
				$spreadsheet->getActiveSheet()->getStyle('A'.$r_h.':O'.$r_h)->applyFromArray($styleArray2);

				$spreadsheet->setActiveSheetIndex(0);
				$spreadsheet->setActiveSheetIndex(0)
				->setCellValue('A'.$r_h, 'Pedido')
				->setCellValue('B'.$r_h, 'Fecha')
				->setCellValue('C'.$r_h, 'No. de Control')
				->setCellValue('D'.$r_h, 'Clave')
				->setCellValue('E'.$r_h, 'Marca')
				->setCellValue('F'.$r_h, 'Estado')
				->setCellValue('G'.$r_h, 'Solicitud')
				->setCellValue('H'.$r_h, 'Categoría')
				->setCellValue('I'.$r_h, 'Versión')
				->setCellValue('J'.$r_h, 'Tipo de Pago')
				->setCellValue('K'.$r_h, 'Prioridad')
				->setCellValue('L'.$r_h, 'Estatus')
				->setCellValue('M'.$r_h, 'Folio Inicial')
				->setCellValue('N'.$r_h, 'Folio Final')
				->setCellValue('O'.$r_h, 'Cantidad');
				for($x=0;$x<=$t2;$x++) {
					$fil=$letras[$x].'2';
					$spreadsheet->getActiveSheet()->getStyle($fil)->getFont()->setBold(true);
				}

				$x=$st_r;
				$arr_fila=[];
				while($fila= $res->fetch_assoc()) {
					$arr_fila[0] = $fila["no_pedido"];
					 //NO_CLIENTE
					$new_index.=$fila['no_cliente'];
					/*if($fila['cve']=='')
						$new_index.='_GEN_';
					else
						$new_index.='_'.$fila['cve'].'_';*/
					$arr_fila[2] = str_pad($fila['no_cliente'], 5, "0", STR_PAD_LEFT);;
					$arr_fila[1] = $fila["fecha"];
					$arr_fila[3]=mb_convert_encoding($fila["cve"], 'UTF-8', 'ISO-8859-1');
					$arr_fila[4]=mb_convert_encoding($fila["marca"], 'UTF-8', 'ISO-8859-1');
					$arr_fila[5]=mb_convert_encoding($fila["edo"], 'UTF-8', 'ISO-8859-1');
					//SOLICITUD
					$arr_fila[6] = $fila["folio"];
					//CATEGORIA
					switch($fila["tipo"]) {
						case 0:
                            $arr_fila[7]="N/A";
                            break;
						case 1:
                            $arr_fila[7]="MEZCAL";
                            break;
						case 2:
                            $arr_fila[7]="ARTESANAL";
                            break;
						case 3:
                            $arr_fila[7]="ANCESTRAL";
                            break;

					}
				     $arr_fila[8] = match ($fila['holograma']) {
                         '0' => "GENÉRICO",
                         '1' => "VERSIÓN 1",
                         '2' => "VERSIÓN 2",
                         default => "OTRO",
                     };
					//$arr_fila[8] = $fila['holograma']=='0'?'GÉNERICO':'NUEVO';
					$arr_fila[9] = mb_convert_encoding($fila['tipo_pago'], 'UTF-8', 'ISO-8859-1');
                    $arr_fila[10] = mb_convert_encoding($fila['urgente'] == '1'? 'URGENTE': 'NORMAL', 'UTF-8', 'ISO-8859-1');
					
					$status = "";
                    if($fila['status'] == 0)
                        $status="SIN SOLICITAR";
                    elseif($fila['status'] == 1)
                        $status="SOLICITADO";
                    elseif($fila['status'] == 2)
                        $status="RECIBIDO";
                    elseif($fila['status'] == 3)
                        $status="PROCESANDO";
                    elseif($fila['status'] == 4)
                        $status="IMPRESO";
                    elseif($fila['status'] == 5)
                        $status="ENTREGADO";
                    elseif($fila['status'] == 6)
                        $status="EN INVENTARIO";
                    $arr_fila[11] = $status;

                    $folioi = $fila['no_cliente'].$fila['cve'].str_pad($fila['fi'], 7, "0", STR_PAD_LEFT).$fila['serie'];
                    $foliof = $fila['no_cliente'].$fila['cve'].str_pad($fila['ff'], 7, "0", STR_PAD_LEFT).$fila['serie'];

                    $arr_fila[12] = $folioi;
                    $arr_fila[13] = $foliof;
                    $arr_fila[14] = $fila['cantidad'];

				  /*if($new_index!=$last_index){
					  if($x>$st_r){
					  //agregar suma
					      $pos_fin='O'.($x-1);
					      $formu="=sum(".$pos_ini.":".$pos_fin.")";
						  $pos_formu='O'.($x);
						    $spreadsheet->setActiveSheetIndex(0)->setCellValue($pos_formu, $formu);
							$spreadsheet->getActiveSheet()->getStyle($pos_formu)->applyFromArray($styleArray3);
							$spreadsheet->getActiveSheet()->getStyle($pos_formu)->getNumberFormat()->setFormatCode("#,##0");
					      if($bandera_color==0){
							 $fill_color="DDEBF7";
							 $bandera_color=1;
						  } else{
							  $fill_color="F2F2F2";
							  $bandera_color=0;
					      }
						 //$x=$x+3;
						 $pos_ini='O'.$x;
					  } else {
					  //GUARDAMOS EL INICIO DE UNA NUEVA MARCA Y ESTABLECEMOS EL CAMBIO DE COLOR
					      $pos_ini="O5";
						  $fill_color="F2F2F2";
					  }//Endif $X>5;

				  }//ENDIF NEWINDEX VS LASTINDEX*/
				  for($i=1;$i<=15;$i++) {
					$c=$letras[$i-1].$x;
					$dato=$arr_fila[$i-1];

					if($letras[$i-1]!='B')
					  $spreadsheet->setActiveSheetIndex(0)->setCellValue($c, $dato);
					else
					  $spreadsheet->getActiveSheet()->setCellValueExplicit($c, $dato,DataType::TYPE_STRING);
					$spreadsheet->getActiveSheet()->getColumnDimension($letras[$i-1])->setAutoSize(true);
					if($i === 15)
						$spreadsheet->getActiveSheet()->getStyle($c)->getNumberFormat()->setFormatCode("#,##0");
				  }
				  $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(false);
				  $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(40);

				//$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(80);
				  $cell_ini='A'.$x;
				  $cell_fin='O'.$x;
				  $spreadsheet->getActiveSheet()->getStyle($cell_ini.":".$cell_fin)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
				  $spreadsheet->getActiveSheet()->getStyle($cell_ini.":".$cell_fin)->getBorders()->getAllBorders()->getColor()->setRGB('23719E');
				  $spreadsheet->getActiveSheet()->getStyle($cell_ini.":".$cell_fin)->getFill()->setFillType(Fill::FILL_SOLID);
				  //$spreadsheet->getActiveSheet()->getStyle($cell_ini.":".$cell_fin)->getFill()->getStartColor()->setARGB($fill_color); // COLOR NEGRO

				  $last_index=$new_index;
				  $new_index="";
				  $x++;
				  $cont_result++;
				}
				//ESCRIBIMOS LA FORMULA DE LA ULTIMA LISTA
			    $pos_fin='O'.($x-1);
				//$formu="=sum(".$pos_ini.":".$pos_fin.")";
				$formu="=sum(".$pos_ini.":".$pos_fin.")";
				$pos_formu='O'.($x);
				$spreadsheet->setActiveSheetIndex(0)->setCellValue($pos_formu, $formu);
				$spreadsheet->getActiveSheet()->getStyle($pos_formu)->applyFromArray($styleArray3);
				$spreadsheet->getActiveSheet()->getStyle($pos_formu)->getNumberFormat()->setFormatCode("#,##0");
				//INMOBILIZAR LOS ENCABEZADOS
				$spreadsheet->getActiveSheet()->freezePane('A7');
				// Rename worksheet
				$spreadsheet->getActiveSheet()->setTitle('PedidosHologramas');

				
				// Set active sheet index to the first sheet, so Excel opens this as the first sheet
				$spreadsheet->setActiveSheetIndex(0);
				// Redirect output to a client’s web browser (Excel2007)
				//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				//header('Content-Disposition: attachment;filename="ReporteGral.xlsx"');
				//header('Cache-Control: max-age=0');

				$objWriter = IOFactory::createWriter($spreadsheet, 'Xlsx');
				$objWriter->save('../../tmp_excel/'.$file_name);
				$dir_file="http://".$svr_dir."/hologramas/tmp_excel/".$file_name;
				echo json_encode(['status' => 'OK','msj'=>$dir_file]);
				exit;
				//FIN DEL SCRIPT PARA GENERAR EL ARCHIVO
			}//FIN ISSET CLIENTE
			else
				echo json_encode(['status' => 'error','msj'=>'datos vacios']);

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
