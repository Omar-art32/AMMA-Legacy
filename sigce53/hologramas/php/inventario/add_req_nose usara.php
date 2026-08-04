<?php
$errores=0;
$mensaje="";
$user=utf8_decode($_POST['user']);
$no_pedido=utf8_decode($_POST['no_pedido']);
$total=$_POST['tot_list'];
$lista_req=json_decode($_POST['lista_req'],true);
$ids=array();
$item=array();
$l_arr=count($total);
$cad_values="";
$sep="";
$count_id=0; //para posiciones del arreglo
foreach($lista_req as $h => $id)
{
  $ids[$count_id]=$h;
  $count_id++;		
}
for($x=0;$x<$total;$x++)
{
	$item=$lista_req[$ids[$x]];
	//print_r($item);
	add_val($item);
	if($x==$total-1)
	{
		guarda_req();		
	}
}

function add_val($itm)
{
  global $user, $cad_values, $sep,$no_pedido;
  $cliente=utf8_decode ($itm['cte']);
  $marca=utf8_decode ($itm['marca']);
  $serie=utf8_decode ($itm['serie']);
  $cantidad=utf8_decode ($itm['cantidad']);
  $fini=utf8_decode ($itm['fini']);
  $ffin=utf8_decode ($itm['ffin']);  
  $fecha = date("Y-m-d H:i:s" );
  
  $cad_values.=$sep."('{$no_pedido}', '{$fecha}', '{$cliente}', '{$marca}', '{$serie}', '{$fini}', '{$ffin}', '{$cantidad}', '0', '{$user}')";
  $sep=",";
}
function guarda_req()
{
	
    require('mc_table.php');
	include('../../../common/conexion.php');
	global $cad_values;
	//echo $cad_values; 
	$sql_ins="INSERT INTO h_pedidos(no_pedido,fecha,no_cliente,marca,serie,fi,ff,cantidad,status,usr) VALUES ".$cad_values.";";
	$result=$conexion->query($sql_ins); 
	if($result==false)
	{ 
	  $mensaje=array('status' => 'error','msj'=> 'Ha ocurrido un error al generar la requisicion, imprima pantalla del error y comuniquelo al area de sistemas');
	} 
	else
	{   
	  crea_req();
	}
}
function crea_req()
{
	global $cad_values,$no_pedido,$ids,$lista_req,$total;
	$item_recibo=array();
	   //VARIABLES PARA GENERAR EL RECIBO
  include('../../../common/conexion.php');
  require_once('../../../common/cfg_server.php');
  
$pdf=new PDF_MC_Table();
$pdf->AliasNbPages();
$pdf->SetDisplayMode(100,'continuous');	
	$pdf->AddPage('P','Letter');
	  $pdf->SetAutoPageBreak (true , 1);
	        $pdf->SetTextColor(0,0,0);			
			$nb_marca=0;
			$next_y=0;
			$alto_fila=0;
			$gen=0;
			$f_ini_gen="";
			$f_fin_gen="";
			//INSERTAR LOGO
			$y=10;			
			//CABECERA
			$y=$y+10;
			$pdf->SetFont('Arial','B',9);
			$pdf->SetXY(67,$y);
			$pdf->Cell(90,5,'Consejo Mexicano Regulador de la Calidad del Mezcal',0,'','C');
			//TITULO AREA
			$y=$y+8;
	     	$pdf->SetFont('Arial','B',11);
			$pdf->SetXY(59,$y);
			$pdf->Cell(100,5,'R.F.C. CMR971212NG2',0,'','C');
			//TITULO DOCUMENTO
			$y=$y+6;
			$pdf->SetFont('Arial','B',10);
			$pdf->SetXY(65,$y);
			$pdf->Cell(90,5,'PEDIDO DE HOLOGRAMAS NO: '.$no_pedido,0,'','C');
			//datos recibo
			$y=$y+12;
			//-------ENCABEZADO DE LA TABLA-------
			$pdf->SetFillColor(125,166,93);
			$pdf->SetDrawColor(86,128,54);
	        $pdf->SetTextColor(255,255,255);
			//
			$pdf->SetFont('Arial','B',10);
			$pdf->SetXY(15,$y);
		    $pdf->Cell(60,12,'MARCA',1,1,'C',1);
			
			$pdf->Rect(75,$y,30,12,'DF');
			$pdf->SetXY(75,$y+1);
		    //$pdf->Cell(50,6,'NO SELLOS SOLICITADOS',1,'','C');
			$pdf->MultiCell(30,5,'NO SELLOS SOLICITADOS',0,'C'); 
			
			$pdf->SetXY(105,$y);
		    $pdf->Cell(100,12,'NOMENCLATURA',1,'','C',1);
			$y=$y+12;
			$pdf->SetTextColor(0,0,0);
			
			for($x=0;$x<$total;$x++)
			{
				$item_recibo=$lista_req[$ids[$x]];
				$marca=utf8_decode ($item_recibo['nom_marca']);
				$cantidad=utf8_decode ($item_recibo['cantidad']);
				$fini=utf8_decode ($item_recibo['fini']);
				$ffin=utf8_decode ($item_recibo['ffin']);
				$nb_marc=$pdf->NbLines(60,$marca);
			  $pdf->SetFont('Arial','',10);
				if($nb_marc>1)
			   {
				   $alto_fila=$nb_marc*5;	
				   $next_y=$nb_marc*5;
				$pdf->SetXY(15,$y);
				$pdf->Rect(15,$y,60,$alto_fila,'D');
				//$pdf->Cell(50,$alto_fila,$marca,0,'','C');  
				$pdf->MultiCell(60,5,$marca,1,'C',0); 
			   } 
			   else
			   {
				   $alto_fila=7;
				   $next_y=7;
				$pdf->SetXY(15,$y);
				$pdf->Cell(60,$alto_fila,$marca,1,'','C');		   
			   }
			  
			  	$pdf->SetXY(75,$y);
				$pdf->Cell(30,$alto_fila,number_format($cantidad),1,'','C');
				$pdf->SetFont('Arial','B',11);
			  	$pdf->SetXY(105,$y);
				$pdf->Cell(50,$alto_fila,$fini,1,'','C');
				
			  	$pdf->SetXY(155,$y);
				$pdf->Cell(50,$alto_fila,$ffin,1,'','C');
				
			 $y=$y+$next_y;					
			}
					
			$y=260;
			
			$pdf->line(20,$y,200,$y);
		
			$pdf->SetFont('Arial','',8);
			$pdf->SetXY(60,$y);
			$dir="Cofre de Perote No. 325 Col. Volcanes, Oaxaca, Oaxaca, C.P. 68020";
			$pdf->Cell(90,5,$dir,0,'','L');
			$y=$y+5;
			$pdf->SetFont('Arial','',10);
			$pdf->SetXY(20,$y);
			$dir2="www.crm.org.mx  atencionaclientes@crm.org.mx";
			$pdf->Cell(90,5,$dir2,0,'','L');
			
			$pdf->SetFont('Arial','',9);
			$pdf->SetXY(120,$y);
			$dir3=utf8_decode("Télefonos: 01(951) 517 45 79   y   01(951) 206 18 57");
			$pdf->Cell(90,5,$dir3,0,'','L');		
			
			
			$file = 'Pedido_'.$no_pedido.'.pdf';	
	$pdf->Output('../../tmp_req/'.$file, 'F');
    //Redirect
	$dir_file="http://".$svr_dir."/siig/hologramas/tmp_req/".$file;
	
	//eliminar temporales
	$sql_del="delete from h_tmp_pedido where no_pedido='$no_pedido'";
	$res_del=$conexion->query($sql_del);
	if($res_del==true)
	{
	  echo json_encode(array("status" => "OK","link_dir"=>$dir_file,"msj"=>"Todo ha ido bien"));
	} 
	else
	{
	  echo json_encode(array("status" => "OK","link_dir"=>$dir_file,"msj"=>"No se pudo eliminar la lista temporal"));
	}
    
	

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
			$m="Enero";
			break;
		}
		case '02':
		{
			$m="Febrero";
			break;
		}
		case '03':
		{
			$m="Marzo";
			break;
		}
		case '04':
		{
			$m="Abril";
			break;
		}
		case '05':
		{
			$m="Mayo";
			break;
		}
		case '06':
		{
			$m="Junio";
			break;
		}
		
		case '07':
		{
			$m="Julio";
			break;
		}
		case '08':
		{
			$m="Agosto";
			break;
		}
		case '9':
		{
			$m="Septiembre";
			break;
		}
		case '10':
		{
			$m="Octubre";
			break;
		}
		case '11':
		{
			$m="Noviembre";
			break;
		}
		case '12':
		{
			$m="Diciembre";
			break;
		}
		
	}
	$nfech=$dat[2]." de ".$m." de ".$dat[0];
		return $nfech;
}
?>