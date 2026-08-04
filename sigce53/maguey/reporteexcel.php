<?php
  session_start();
  include("../common/conexion.php");
  /*include('php/registro/conexion.php');
  include('php/registro/conexion_remota.php');*/
  $conexion->set_charset("utf8");
  //$conexion_remota->set_charset("utf8");


	$consulta = "SELECT
	paraje.id_paraje,
	paraje.id_cliente,
	paraje.nombrep,
	existenciaplanta.regmaguey AS regmaguey,
	LPAD(constancias.id_constancia,4,'0') as constancia,
	CONCAT('P',LPAD(SUBSTR(paraje.id_paraje, 2, LENGTH(paraje.id_paraje)),4,'0')) as parajes,
	Date_format(constancias.fecha,'%y') as anio,
	constancias.id_constancia as numeroconstancia,
	municipios.nombre as nombrem,
	estados.nombre as nombree,
	localidades.localidad,
	paraje.paraje,
	comun.nombre,genespecie,existenciaplantas,
	existenciaplanta.edad,usufruto,tenencia,superficie,lng,lat,dis_planmetros,dis_surcometros,fecha_paraje,rcampo,cantidadini, paraje.numa, au.login 
from estados
inner join municipios on municipios.estado=estados.clave
inner join localidades on localidades.MunicipioID=municipios.id
inner join paraje on localidades.id=paraje.id_localidad
inner join constancias on constancias.id_paraje=paraje.id_paraje
inner join existenciaplanta on paraje.id_paraje=existenciaplanta.id_paraje
Inner Join comun ON comun.id_comun= existenciaplanta.id_comun
Inner Join especie ON comun.id_especie = especie.id_especie 
INNER JOIN a_usuarios au ON paraje.id_us = au.id_us 
order by paraje.id ";

	$filas = 0;
	$idus = $_GET['aleat'];

	if($resultado = $conexion->query($consulta))
  		$filas = $resultado->num_rows;
  //$idus = $_SESSION[$d_s]['id_us'];
  // clvuser == 23 || clvuser == 34 || clvuser == 117 || clvuser == 21) {
	if($filas > 0 && ($idus == 1 || $idus == 23 || $idus == 34 || $idus == 48 || $idus == 117 || $idus == 21 || $idus == 148 )){




		date_default_timezone_set('America/Mexico_City');

		if (PHP_SAPI == 'cli')
			die('Este archivo solo se puede ver desde un navegador web');

		/** Se agrega la libreria PHPExcel */
		require_once 'libs/phpExcel/PHPExcel.php';

		// Se crea el objeto PHPExcel
		$objPHPExcel = new PHPExcel();

		// Se asignan las propiedades del libro
		$objPHPExcel->getProperties()->setCreator("NJGC") //Autor
							 ->setLastModifiedBy("NJGC") //Ultimo usuario que lo modificó
							 ->setTitle("REPORTE DE PREDIOS AMMA")
							 ->setSubject("REPORTE DE PREDIOS AMMA")
							 ->setDescription("REPORTE DE PREDIOS AMMA")
							 ->setKeywords("REPORTE DE PREDIOS AMMA")
							 ->setCategory("REPORTE DE PREDIOS AMMA");

		$tituloReporte = "REPORTE DE PREDIOS DE MAGUEY";
		$titulosColumnas = array('NO_PARAJE','NO_CLIENTE','NOMBRE DEL CLIENTE','NOMBRE DE PRODUCTOR','SITUACIÓN DE MANEJO','NO.CONSTANCIA','LOCALIDAD','MUNICIPIO','ESTADO','NOMBRE DEL PARAJE','NOMBRE COMÚN (ESPECIE)','NOMBRE CIENTIFICO (ESPECIE)','CANTIDAD INICIAL','CANTIDAD DE EXISTENCIA PLANTAS','EDAD','USUFRUTO','TENENCIA','SUPERFICIE','LONGITUD','LATITUD','DISTANCIA ENTRE PLANTAS (METROS)','DISTANCIA ENTRE SURCOS (METROS)','FECHA DE REGISTRO','REPRESENTANTE EN CAMPO','ORIGEN','REGISTRÓ');

		$objPHPExcel->setActiveSheetIndex(0)
        		    ->mergeCells('A1:Z1');

		// Se agregan los titulos del reporte
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1',$tituloReporte)
        		    ->setCellValue('A2',  $titulosColumnas[0])
		            ->setCellValue('B2',  $titulosColumnas[1])
        		    ->setCellValue('C2',  $titulosColumnas[2])
            		->setCellValue('D2',  $titulosColumnas[3])
					->setCellValue('E2',  $titulosColumnas[4])
		            ->setCellValue('F2',  $titulosColumnas[5])
        		    ->setCellValue('G2',  $titulosColumnas[6])
            		->setCellValue('H2',  $titulosColumnas[7])
					->setCellValue('I2',  $titulosColumnas[8])
		            ->setCellValue('J2',  $titulosColumnas[9])
        		    ->setCellValue('K2',  $titulosColumnas[10])
            		->setCellValue('L2',  $titulosColumnas[11])
				    ->setCellValue('M2',  $titulosColumnas[12])
		            ->setCellValue('N2',  $titulosColumnas[13])
        		    ->setCellValue('O2',  $titulosColumnas[14])
            		->setCellValue('P2',  $titulosColumnas[15])
					->setCellValue('Q2',  $titulosColumnas[16])
					->setCellValue('R2',  $titulosColumnas[17])
					->setCellValue('S2',  $titulosColumnas[18])
					->setCellValue('T2',  $titulosColumnas[19])
					->setCellValue('U2',  $titulosColumnas[20])
					->setCellValue('V2',  $titulosColumnas[21])
					->setCellValue('W2',  $titulosColumnas[22])
					->setCellValue('X2',  $titulosColumnas[23])
					->setCellValue('Y2',  $titulosColumnas[24])
					->setCellValue('Z2',  $titulosColumnas[25]);

		//Se agregan los datos de los alumnos
		$i = 3;
		while ($registro= $resultado->fetch_array())
		{


			$cliente=$registro['id_cliente'];
	 	    $strCliente = "SELECT clientes.no_cliente,clientes.nombre as clientenombre
					   from clientes
					   where clientes.no_cliente='$cliente'";


	        $clientes= $conexion->query($strCliente);
	        $filaClientes = mysqli_fetch_array($clientes);

	        $origen = "";
	        $origen = ($registro['numa'] > 0) ? "EXTERNO": "AMMA";

			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValueExplicit('A'.$i, $registro['parajes'], PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValueExplicit('B'.$i, $filaClientes['no_cliente'], PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValue('C'.$i, $filaClientes['clientenombre'])
			->setCellValue('D'.$i, $registro['nombrep'])
			->setCellValue('E'.$i, $registro['regmaguey'])
			->setCellValueExplicit('F'.$i, strtoupper($registro['constancia']).$registro['parajes'].$registro['anio'], PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValue('G'.$i, $registro['localidad'])
			->setCellValue('H'.$i, $registro['nombrem'])
			->setCellValue('I'.$i, $registro['nombree'])
			->setCellValue('J'.$i, $registro['paraje'])
			->setCellValue('K'.$i, $registro['nombre'])
			->setCellValue('L'.$i, $registro['genespecie'])
			->setCellValue('M'.$i, $registro['cantidadini'])
			->setCellValue('N'.$i, $registro['existenciaplantas'])
			->setCellValue('O'.$i, $registro['edad'])
			->setCellValue('P'.$i, $registro['usufruto'])
			->setCellValue('Q'.$i, $registro['tenencia'])
			->setCellValue('R'.$i, $registro['superficie'])
			->setCellValue('S'.$i, $registro['lng'])
			->setCellValue('T'.$i, $registro['lat'])
			->setCellValue('U'.$i, $registro['dis_planmetros'])
			->setCellValue('V'.$i, $registro['dis_surcometros'])
			->setCellValue('W'.$i, $registro['fecha_paraje'])
			->setCellValue('X'.$i, $registro['rcampo'])
			->setCellValue('Y'.$i, $origen)
			->setCellValue('Z'.$i, $registro['login'])
			;
	 		$i++;
		}

		$estiloTituloReporte = array(
        	'font' => array(
	        	'name'      => 'Verdana',
    	        'bold'      => true,
        	    'italic'    => false,
                'strike'    => false,
               	'size' =>16,
	            	'color'     => array(
    	            	'rgb' => 'FFFFFF'
        	       	)
            ),
	        'fill' => array(
				'type'	=> PHPExcel_Style_Fill::FILL_SOLID,
				'color'	=> array('argb' => '1C7F33')
			),
            'borders' => array(
               	'allborders' => array(
                	'style' => PHPExcel_Style_Border::BORDER_NONE
               	)
            ),
            'alignment' =>  array(
        			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        			'rotation'   => 0,
        			'wrap'          => TRUE
    		)
        );

		$estiloTituloColumnas = array(
            'font' => array(
                'name'      => 'Arial',
                'bold'      => true,
				'size' =>11,
                'color'     => array(
                    'rgb' => 'FFFFFF'
                )
            ),
            'fill' 	=> array(
				'type'		=> PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
				'rotation'   => 90,
        		'startcolor' => array(
            		'rgb' => '4AE66F'
        		),
        		'endcolor'   => array(
            		'argb' => 'FF431a5d'
        		)
			),
            'borders' => array(
            	'top'     => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
                    'color' => array(
                        'rgb' => '53DA73'
                    )
                ),
                'bottom'     => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
                    'color' => array(
                        'rgb' => '29D551'
                    )
                )
            ),
			'alignment' =>  array(
        			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        			'wrap'          => TRUE
    		));

		$estiloInformacion = new PHPExcel_Style();
		$estiloInformacion->applyFromArray(
			array(
           		'font' => array(
               	'name'      => 'Arial',
				'size' =>9,
               	'color'     => array(
                   	'rgb' => '000000'
               	)
           	),
           	'fill' 	=> array(
				'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
				//'color'		=> array('argb' => 'FFd9b7f4')
			),
           	'borders' => array(
               	'left'     => array(
                   	'style' => PHPExcel_Style_Border::BORDER_THIN ,
	                'color' => array(	'rgb' => '#B2E5CB'
                   	)
               	)
           	)
        ));

		$objPHPExcel->getActiveSheet()->getStyle('A1:Z1')->applyFromArray($estiloTituloReporte);
		$objPHPExcel->getActiveSheet()->getStyle('A2:Z2')->applyFromArray($estiloTituloColumnas);
		$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A3:Z".($i-1));

		for($i = 'A'; $i <= 'X'; $i++){
			$objPHPExcel->setActiveSheetIndex(0)
				->getColumnDimension($i)->setAutoSize(TRUE);
		}

		// Se asigna el nombre a la hoja
		$objPHPExcel->getActiveSheet()->setTitle('PREDIOS');

		// Se activa la hoja para que sea la que se muestre cuando el archivo se abre
		$objPHPExcel->setActiveSheetIndex(0);
		// Inmovilizar paneles
		//$objPHPExcel->getActiveSheet(0)->freezePane('A4');
		$objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0,3);

		// Se manda el archivo al navegador web, con el nombre que se indica (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Reportedepredios.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;

	}
	else{
		print_r('No hay resultados para mostrar');
	}

	$conexion->close();
	//$conexion_remota->close();
?>
