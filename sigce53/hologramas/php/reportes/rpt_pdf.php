<?php
// FPDF se carga automáticamente vía Composer (setasign/fpdf) dentro de mc_table.php
require(__DIR__ . '/mc_table.php');
require_once(__DIR__ . '/../../../common/cfg_server.php');
include(__DIR__ . '/../../../common/conexion.php');


$fecha = date("Y-m-d" );
$msj1="";
$file_name="";
$periodo="";
$msj_per="";
$arr_sum=[];
$array_resumen=[];
$consulta="select h_salidas.id_salidas, h_salidas.id_recibo, h_salidas.anio_rcbo, h_salidas.no_cliente, if(h_salidas.marca='0','',h_salidas.marca) cve, if(marcas.marca is null,'',marcas.marca) marca, h_salidas.serie, h_salidas.edo, h_salidas.tipo, if(h_salidas.solicitud='','S/N',h_salidas.solicitud) solicitud, h_salidas.fecha_entr, h_salidas.destino, h_salidas.fi1, h_salidas.ff1, h_salidas.se1 from h_salidas left join marcas on marcas.no_cliente=h_salidas.no_cliente and marcas.cve_marca=h_salidas.marca where";
$sql_sum="select if(h_salidas.marca='','GEN',h_salidas.marca) cve, if(marcas.marca is null,'GEN',marcas.marca) marca, if(h_salidas.serie='','GEN',h_salidas.serie) serie, sum(h_salidas.se1) suma 
from h_salidas 
left join marcas on marcas.no_cliente=h_salidas.no_cliente and marcas.cve_marca=h_salidas.marca 
where";
if(isset($_POST['cliente']))
{
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

		switch($tipo)
		{
			case 1:
                $consulta .= " h_salidas.no_cliente like '%$cliente%'";
                $sql_sum.=" h_salidas.no_cliente like '%$cliente%'";
                $he1="Relación de hologramas entregados al cliente: ".$cliente;
                $msj1="";
                $file_name=$cliente.'_gral.pdf';
                break;
			case 2:
                $consulta .= " h_salidas.no_cliente like '%$cliente%' and h_salidas.serie=''";
                $sql_sum.=" h_salidas.no_cliente like '%0194%' and h_salidas.serie=''";
                $he1="Relación de hologramas entregados al cliente: ".$cliente;
                $msj1="";
                $file_name=$cliente.'_gen.pdf';
                break;
			case 3:
                $consulta .= " h_salidas.no_cliente like '%$cliente%' and h_salidas.serie!=''";
                $sql_sum.="  h_salidas.no_cliente like '%$cliente%' and h_salidas.serie!=''";
                $he1="Relación de hologramas entregados al cliente: ".$cliente;
                $msj1="";
                $file_name=$cliente.'_per.pdf';
                break;
			case 4:
                switch($tipo_m)
				{
					case 'T':
                        $consulta .= " h_salidas.no_cliente like '%$cliente%' {$marca} ";
                        $sql_sum.="  h_salidas.no_cliente like '%$cliente%' {$marca} ";
                        $he1="Relación de hologramas entregados al cliente: ".$cliente;
                        $msj1="Para la marca: ";
                        $file_name=$cliente.'_gral_mca.pdf';
                        break;
					case 'G':
                        $consulta .= " h_salidas.no_cliente like '%$cliente%' {$marca} and h_salidas.serie=''";
                        $sql_sum.="  h_salidas.no_cliente like '%$cliente%' {$marca} and h_salidas.serie=''";
                        $he1="Relación de hologramas entregados al cliente: ".$cliente;
                        $msj1="Para la marca: ";
                        $file_name=$cliente.'_gen_mca.pdf';
                        break;
					case 'P':
                        $consulta .= " h_salidas.no_cliente like '%$cliente%' {$marca} and h_salidas.serie!=''";
                        $sql_sum.="  h_salidas.no_cliente like '%$cliente%' {$marca} and h_salidas.serie!=''";
                        $he1="Relación de hologramas entregados al cliente: ".$cliente;
                        $msj1="Para la marca: ";
                        $file_name=$cliente.'_per_mca.pdf';
                        break;
				}
                break;
		}

		if($estado!="T" && empty($estado))
		{

			$consulta.=" and h_salidas.edo='$estado'";
			$sql_sum.=" and h_salidas.edo='$estado'";
		}


		

		if($categoria!="" && $categoria!="T" && empty($estado) )
		{
			$consulta.=" and h_salidas.tipo=$categoria";
			$sql_sum.=" and h_salidas.tipo=$categoria";
		}



		if(trim($fecha1) !== ''&&trim($fecha2) !== '')
		{
			$consulta.=" and h_salidas.fecha_entr between '$fecha1' and '$fecha2' ORDER BY h_salidas.marca,h_salidas.fi1 asc";
			$sql_sum.=" and h_salidas.fecha_entr between '$fecha1' and '$fecha2' group by h_salidas.marca, h_salidas.serie ORDER BY h_salidas.marca,h_salidas.fi1 asc";
			$periodo=fecha($fecha1).'  a  '.fecha($fecha2);
			$msj_per="Periodo:";
		}
		else if(trim($fecha1) !== '')
		{
			$consulta.=" and fecha_entr='$fecha1' ORDER BY h_salidas.marca,h_salidas.fi1 asc";
			$sql_sum.=" and fecha_entr='$fecha1' group by h_salidas.marca, h_salidas.serie ORDER BY h_salidas.marca,h_salidas.fi1 asc";
			$periodo=fecha($fecha1);
			$msj_per="Fecha:";
		}
		else
		{
		$consulta.="  ORDER BY h_salidas.marca,h_salidas.fi1 asc";
		$sql_sum.="  group by h_salidas.marca, h_salidas.serie ORDER BY h_salidas.marca,h_salidas.fi1 asc";
		}


//OBTENER LA SUMA DE LOS TOTALES
$res_sum=$conexion->query($sql_sum);
if($res_sum->num_rows>0)
{
	$ind_r=0;
	while($row_sum=$res_sum->fetch_assoc())
	{
		$index_sum=$row_sum['cve'].'_'.$row_sum['serie'];
		$arr_sum[$index_sum]=$row_sum['suma'];
		
		if($row_sum['marca']=='GEN')
		{
			$marca_hol='GENERICOS';
		}
		else
		{
			$marca_hol=$row_sum['marca'];
		}
	    if($row_sum['serie']=='GEN')
		{
			$tipo_hol='GENERICOS';
		}
		else
		{
			$tipo_hol='PERSONALIZADOS';
		}
		$array_resumen[$ind_r]=[$marca_hol,$tipo_hol,$row_sum['suma']];
		$ind_r++;
	}
}//FIN OBTENER TOTALES

$cont_rxp=1;
$cont_rows=0;
$bandera=1;
$fist_row=1;
$new_pag=0;
$indice_marca="";
$y=30;//la posicion incial de y	
$alto_fila=7;
$last_index="";
$new_index="";
$temp_index="";

$result = $conexion->query($consulta);
$num_res=$result->num_rows;
//iniciamos la creacion del pdf
//$file_name=$cliente.'_per_mca.pdf';	
//$num_res=0;
$pdf=new PDF_MC_Table();
$pdf->AliasNbPages();
$pdf->SetDisplayMode(100,'continuous');	
$pdf->AddPage('L','Letter');  
$pdf->SetXY(35,22);
$pdf->SetDrawColor(214,214,214);
$y=42;

  if($num_res>0)
  {
	  while($fila = $result->fetch_assoc())
	  {
	  //---------------INICIO DE VARIABLES-------------------
	  if($fila["anio_rcbo"]==0)
	  {
		  $recibo='----';
	  }
	  else
	  {
	  $recibo='AR'.str_pad($fila["id_recibo"],4,'0',STR_PAD_LEFT).'/'.$fila["anio_rcbo"];
	  }
	  //MARCA
	  $marca=mb_convert_encoding($fila["marca"], 'UTF-8', 'ISO-8859-1');	  
	  if($marca === "")
	  {
		  $marca="N/A";
	  }	 
	  //CLAVE 
	  if($fila["cve"]=="")
	  {		  
		  $new_index.="GEN_";
	  }
	  else
	  {
		  $new_index.=$fila["cve"]."_";
	  }
	  //FOLIOS
	  if($fila["serie"]!='')
	  {
	  $fol_ini=$fila["no_cliente"].$fila["cve"].str_pad($fila["fi1"], 7,'0',STR_PAD_LEFT).$fila["serie"];
	  $fol_fin=$fila["no_cliente"].$fila["cve"].str_pad($fila["ff1"], 7,'0',STR_PAD_LEFT).$fila["serie"];
	  }
	  else
	  {
	  $fol_ini=$fila["fi1"];
	  $fol_fin=$fila["ff1"];
	  }			
	  //$no_cliente=$fila["no_cliente"];
	  $serie=$fila["serie"];
	  if($serie=='')
	  {
		  $serie ="N/A";
		  $new_index.="GEN";
	  }
	  else
	  {
		  $new_index.=$serie;
	  }

	  $edo=$fila["edo"];

	  if($edo=="" || $edo=="T"){
				$edo="N/A";
	  }

	  switch($fila["tipo"])
	  
	  {
						case 0:
                            $cat="N/A";
                            break;
						case 1:
                            $cat="MEZCAL";
                            break;
						case 2:
                            $cat="ARTESANAL";
                            break;
						case 3:
                            $cat="ANCESTRAL";
                            break;
						
	}	



	  $cantidad=$fila["se1"];
	  $solicitud=$fila["solicitud"];
	  if($solicitud=='')
	  {
		  $solicitud ="S/N";
	  }
	  $f_entrega=$fila["fecha_entr"];
	  //------------FIN VARIABLES---------------------
	  
	 if(strlen($marca)>24||strlen($solicitud)>11)
	 {
		 $alto_fila=10;	
		 $next_y=10;	  
	 } 
	 else
	 {
		 $alto_fila=7;
		 $next_y=7;
	 }
	  //agregar el indice inicial
	    if($fist_row !== 1)
		{
			if($new_index !== $last_index)
			{
			$pdf->SetFillColor(91,91,91);
	        $pdf->SetTextColor(255,255,255);
			$pdf->SetFont('Arial','B',10);
			$pdf->SetXY(225,$y);					
			$pdf->Cell(25,7,number_format($arr_sum[$last_index]),1,0,'C',1);
			$y += 12;
			  if($y+7>170)
			  {
				$y=171;
			  }
			}			  
		}
		else
		{			 
			$fist_row=0;
		}		  
		//REVISAR SI LA PAGINA ESTA LLENA Y AGREGAR OTRA 	
		if($y>170&&$cont_rxp<$num_res-1)
		{
			$pdf->AddPage('L','Letter');
			$y=42;
			$new_pag=0;
			$fin=18;
			$pdf->SetXY(130,$y);
		}
		//AGREGA LOS ENCABEZADOS  
		$pdf->SetTextColor(0,0,0);
		if($new_pag === 0)
		{ 
			//mensajes del encabezado
			$pdf->SetFont('Arial','B',12);
			$pdf->SetXY(58,20);					
			$pdf->Cell(150,7,mb_convert_encoding($he1, 'ISO-8859-1'),0,0,'C');
			
				
			$pdf->SetXY(53,27);
			$pdf->SetFont('Arial','B',11);
			$pdf->Cell(150,7,mb_convert_encoding($msj1, 'ISO-8859-1'),0,0,'C');
			
			if($msj1 !== '')//MSJ PARA MARCA
			{
			  $pdf->SetXY(53,32);
			  $pdf->SetFont('Arial','',9);
			  $pdf->Cell(150,7,mb_convert_encoding($marca, 'ISO-8859-1'),0,0,'C');
			  //MSJ SI EXISTE FECHA O PERIODO
			  if($periodo!="")
			  {
				$pdf->SetXY(211,27);
				$pdf->SetFont('Arial','B',11);
				$pdf->Cell(50,7,$msj_per,0,0,'C');
				$pdf->SetXY(211,32);
				$pdf->SetFont('Arial','',10);
				$pdf->Cell(50,7,$periodo,0,0,'C');
			  }
			}
			else
			{
			  if($periodo!="")
			  {
				$pdf->SetXY(53,27);
				$pdf->SetFont('Arial','B',11);
				$pdf->Cell(150,7,$msj_per,0,0,'C');
				$pdf->SetXY(53,32);
				$pdf->SetFont('Arial','',10);
				$pdf->Cell(150,7,$periodo,0,0,'C');
			  } 
			}
			//
			$pdf->SetFillColor(146,202,103);
			$pdf->SetFont('Arial','B',9);
			$pdf->SetXY(20,$y);					
			$pdf->Cell(20,7,'ID Recibo',1,0,'C',1);
			//marca
			$pdf->SetXY(40,$y);
			$pdf->Cell(40,7,'Marca',1,0,'C',1);
			//serie
			$pdf->SetXY(80,$y);
			$pdf->Cell(10,7,'Serie',1,0,'C',1);
		    //estado
			$pdf->SetXY(90,$y);
			$pdf->Cell(15,7,'Estado',1,0,'C',1);
			//categoria
			$pdf->SetXY(105,$y);
			$pdf->Cell(15,7,'Categoria',1,0,'C',1);
			//solicitud
			$pdf->SetXY(120,$y);
			$pdf->Cell(15,7,'Solicitud',1,0,'C',1);
			//F. Entrega
			$pdf->SetXY(135,$y);
			$pdf->Cell(20,7,'F. Entrega',1,0,'C',1);
			//F. Inicial
			$pdf->SetXY(155,$y);
			$pdf->Cell(35,7,'F. Inicial',1,0,'C',1);
			//F. Final
			$pdf->SetXY(190,$y);
			$pdf->Cell(35,7,'F. Final',1,0,'C',1);
			//Cantidad
			$pdf->SetXY(225,$y);
			$pdf->Cell(25,7,'Cantidad',1,0,'C',1);
			$y += 7;
			$new_pag=1;
		}
		  if($bandera === 1)
		  {
			  $pdf->SetFillColor(242,242,242);
			  $bandera=0;
		  }
		  else
		  {
			  $pdf->SetFillColor(255,255,255);
			  $bandera=1;
		  }
		  //INICIA LA IMPRESION DE LOS DATOS
		  $pdf->SetFont('Arial','B',10);
		  $pdf->SetXY(20,$y);					
		  $pdf->Cell(20,$alto_fila,$recibo,1,0,'C',1);					
		  $pdf->SetFont('Arial','',10);
		  
		  //marca
		  
		  $pdf->SetXY(40,$y);
		   if(strlen($marca)>24)
		   {
			$marca.='';
			$pdf->SetFont('Arial','',8); 
			$pdf->MultiCell(40,5,mb_convert_encoding($marca, 'ISO-8859-1'),1,'L',1); 
		   } 
		   else
		   {
			 $pdf->SetFont('Arial','',8);
			 $pdf->Cell(40,$alto_fila,mb_convert_encoding($marca, 'ISO-8859-1'),1,0,'L',1);  
		   }
		  $pdf->SetFont('Arial','',7);
		  //serie
		  $pdf->SetXY(80,$y);
		  $pdf->Cell(10,$alto_fila,mb_convert_encoding($serie, 'ISO-8859-1'),1,0,'C',1);
		  //estado
		  $pdf->SetXY(90,$y);
		  $pdf->Cell(15,$alto_fila,mb_convert_encoding($edo, 'ISO-8859-1'),1,0,'C',1);
		  //categoria
		  $pdf->SetXY(105,$y);
		  $pdf->Cell(15,$alto_fila,mb_convert_encoding($cat, 'ISO-8859-1'),1,0,'C',1);
		  //solicitud
		  $pdf->SetXY(120,$y);
		  //$pdf->Cell(25,$alto_fila,utf8_decode($solicitud),1,0,'C',1);
		   if(strlen($solicitud)>11)
		   {
			
			$pdf->SetFont('Arial','',5); 
			$pdf->MultiCell(15,5,mb_convert_encoding($solicitud, 'ISO-8859-1'),1,'C',1); 
		   } 
		   else
		   {
			 $pdf->SetFont('Arial','',5);
			 $pdf->Cell(15,$alto_fila,mb_convert_encoding($solicitud, 'ISO-8859-1'),1,0,'C',1);  
		   }
		  //f entrega
		  $pdf->SetXY(135,$y);
		  $pdf->Cell(25,$alto_fila,fecha($f_entrega),1,0,'C',1);
		  $pdf->SetFont('Arial','',9); 
		  //f inicial
		  $pdf->SetXY(155,$y);
		  $pdf->Cell(35,$alto_fila,mb_convert_encoding($fol_ini, 'ISO-8859-1'),1,0,'C',1);
		  //f final
		  $pdf->SetXY(190,$y);
		  $pdf->Cell(35,$alto_fila,mb_convert_encoding($fol_fin, 'ISO-8859-1'),1,0,'C',1);
			  //f final
		  $pdf->SetXY(225,$y);
		  $pdf->Cell(25,$alto_fila,number_format($cantidad),1,0,'C',1);
		    if($cont_rows==($num_res-1))
			  {			  
			  $pdf->SetFillColor(91,91,91);
	          $pdf->SetTextColor(255,255,255);
			  $pdf->SetFont('Arial','B',10);
			  $pdf->SetXY(225,$y+$alto_fila);	
			  //imprimir el resultado:				
			  $pdf->Cell(25,7,number_format($arr_sum[$new_index]),1,0,'C',1);
			  }
		   $last_index=$new_index;
		   $new_index="";
		   $y += $next_y;
		   $cont_rxp++;
		   $cont_rows++;
	  }
  }//fin if num_res>0
  //---RESUMEN PAR LOS CASOS
  if($_POST['resumen']=='SI')
  {
	 $filas_sum=  count($array_resumen);
	 $y2=42;
	 $fill_c=1;
	 $pdf->SetTextColor(0,0,0);
	 $pdf->AddPage('L','Letter');
	 //INICIA ENCABEZADO
	 $he_RES='Resumen de Hologramas entregados al cliente:'.$cliente;
	 
	 $pdf->SetFont('Arial','B',12);
			$pdf->SetXY(58,20);					
			$pdf->Cell(150,7,mb_convert_encoding($he_RES, 'ISO-8859-1'),0,0,'C');
			
				
			$pdf->SetXY(53,27);
			$pdf->SetFont('Arial','B',11);
			$pdf->Cell(150,7,mb_convert_encoding($msj1, 'ISO-8859-1'),0,0,'C');
			
			if($msj1 !== '')//MSJ PARA MARCA
			{
			  $pdf->SetXY(53,32);
			  $pdf->SetFont('Arial','',9);
			  $pdf->Cell(150,7,mb_convert_encoding($marca, 'ISO-8859-1'),0,0,'C');
			  //MSJ SI EXISTE FECHA O PERIODO
			  if($periodo!="")
			  {
				$pdf->SetXY(211,27);
				$pdf->SetFont('Arial','B',11);
				$pdf->Cell(50,7,$msj_per,0,0,'C');
				$pdf->SetXY(211,32);
				$pdf->SetFont('Arial','',10);
				$pdf->Cell(50,7,$periodo,0,0,'C');
			  }
			}
			else
			{
			  if($periodo!="")
			  {
				$pdf->SetXY(53,27);
				$pdf->SetFont('Arial','B',11);
				$pdf->Cell(150,7,$msj_per,0,0,'C');
				$pdf->SetXY(53,32);
				$pdf->SetFont('Arial','',10);
				$pdf->Cell(150,7,$periodo,0,0,'C');
			  } 
			}
			//
			$pdf->SetFillColor(146,202,103);
			$pdf->SetFont('Arial','B',10);
			$pdf->SetXY(50,$y2);					
			$pdf->Cell(100,7,'Marca',1,0,'C',1);
			//marca
			$pdf->SetXY(150,$y2);
			$pdf->Cell(50,7,'Tipo Holograma',1,0,'C',1);
			//serie
			$pdf->SetXY(200,$y2);
			$pdf->Cell(25,7,'Cantidad',1,0,'C',1);
			
			$y2 += 7;
	 
	 
	 //$pdf->SetWidths(array(50,40,40));
	 $gran_total=0;
	 foreach ($array_resumen as $sumData) 
	 {	  	 
	     if($fill_c === 1)
		  {
			  $pdf->SetFillColor(242,242,242);
			  $fill_c=0;
		  }
		  else
		  {
			  $pdf->SetFillColor(255,255,255);
			  $fill_c=1;
		  }
		 $num_line=$pdf->NbLines(100,$sumData[0]);
		 $alto_fila2=$num_line*7;
		 $pdf->Rect(50,$y2,100,$alto_fila2,'DF');	
		 $pdf->SetXY(50,$y2+(1*$num_line));			
		 $pdf->MultiCell(100,5,$sumData[0],0,'L',0); 
	     //$pdf->Cell(120,7,$sumData[0],1,0,'L',1);
		 
		 $pdf->SetXY(150,$y2);			
	     $pdf->Cell(50,$alto_fila2,$sumData[1],1,0,'C',1);
		 
		 $pdf->SetXY(200,$y2);			
	     $pdf->Cell(25,$alto_fila2,number_format($sumData[2]),1,0,'C',1);
		 $y2 += $alto_fila2;
		 $gran_total+=$sumData[2];
	  	 /*$num_line=$pdf->NbLines(30,$sumData[0]);
		 $pdf->Row(array($sumData[0],$sumData[1],$num_line)); */
	}     
	     $pdf->SetFillColor(91,91,91);
	     $pdf->SetTextColor(255,255,255);
	     $pdf->SetXY(150,$y2);			
	     $pdf->Cell(50,$alto_fila2,'Total',1,0,'C',1);
		 
		 $pdf->SetXY(200,$y2);			
	     $pdf->Cell(25,$alto_fila2,number_format($gran_total),1,0,'C',1);
	     $pdf->SetTextColor(0,0,0);
  }

      $files = glob('../../tmp_pdf/*'); // get all file names
	  foreach($files as $file)
	  { // iterate files
		if(is_file($file))
		  @unlink($file); // delete file
      }
//$file_name = 'rpt_pdf.pdf';	
$pdf->Output('../../tmp_pdf/'.$file_name, 'F');
/** @var string $svr_dir */
$dir_file = "http://" . $svr_dir . "/hologramas/tmp_pdf/" . $file_name;
echo json_encode(['status' => 'correcto','msj'=>$dir_file]);
}//FIN ISSET CLIENTE
else
{
	echo json_encode(['status' => 'error','msj'=>'datos vacios']);
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
