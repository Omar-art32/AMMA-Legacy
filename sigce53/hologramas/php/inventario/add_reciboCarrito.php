<?php
$errores=0;
$mensaje="";
$recibos=json_decode($_POST['recibos'],true);
$ids=array();
$item1=array();
$item2=array();
$item3=array();
$l_arr=count($recibos);

$count_id=0; //para posiciones del arreglo
foreach($recibos as $h => $id)
{
	if($h!='head')
	{
		$ids[$count_id]=$h;
		$count_id++;
	}	
}
$head=$recibos['head'];
//obtenemos los datos de la primer salida
$item1=$recibos[$ids[0]];
//mandamos a generar la salida
inserta($head,$item1);
//obtenemos los datos de la segunda salida
$item2=$recibos[$ids[1]];
if($errores==0) //comprobamos si hubo errores en la primera salida
{
  inserta($head,$item2);//si no hubo errores generamos al segunda salida
  if($l_arr==4) //comprobamos si se enviaron 3 salidas
  {
	$item3=$recibos[$ids[2]]; // obtenemos los datos de la tercera salida
	if($errores==0)//comprobamos si hubo errores en la segunda salida
	  {
		inserta($head,$item3);//si no hubo errores generamos la tercera salida
		recibo_mul();
		//echo json_encode($mensaje);	//imprimimos el mensaje obtenido despues ultima salida	 
	  }
	  else
	  {
		  echo json_encode($mensaje);//mensaje de error de la segunda salida
	  }
  }
  else
  {
	  recibo_mul();
	 //echo json_encode($mensaje);	//imprimimos el mensaje si solo hubo dos entradas 
  }

}
else
{
	echo json_encode($mensaje);//mensaje de errorde la primera salida
}

function inserta($hd,$itm)
{
  include('../../../common/conexion.php');
  global $errores, $mensaje;
  $usr=$hd['user'];
  $tipo=utf8_decode ($itm['tipo']);
  $recibo=utf8_decode ($hd['recibo']);
  $anio_r=utf8_decode ($hd['anio_r']);
  $id_recibo='AR'.str_pad($recibo, 4,'0',STR_PAD_LEFT).'/'.$anio_r;
  $empresa="";
  $serie="";
  $n_marca="--";
  $marca=utf8_decode ($itm['marca']);	
  if($marca=='0')
  {
	  $marca='';
  }
  $n_marca=utf8_decode ($itm['n_marca']);
  if($n_marca=='Seleccionar')
  {
	  $n_marca='--';
  }
  $cliente=utf8_decode ($hd['cliente']);
  $sol=utf8_decode ($itm['solicitud']);
  $fecha_e=utf8_decode ($hd['entrega']);
  $destino=utf8_decode ($itm['destino']);
  $fi=utf8_decode ($itm['fini']);
  $ff=utf8_decode ($itm['ffin']);				
  $subtotal=utf8_decode ($itm['cantidad']);
  $total=$subtotal;
  //mermas
  $mermas=utf8_decode ($itm['no_mermas']);
  $motivo_merma=utf8_decode ($itm['mtvo_merma']);
  $fol_m1=utf8_decode ($itm['fol_merma']);
  $fol_m1_num=utf8_decode ($itm['fol_merma_nums']);
  if($mermas>0)
  {
	  $total=$itm['total_sellos'];
  }
  $obs_ent = utf8_decode($itm['obs_ent']);

  $fecha = date("Y-m-d H:i:s" );
  
  if($tipo=='G')
  {
	$sql_ins="INSERT INTO h_salidas (id_recibo, anio_rcbo, no_cliente, marca, serie, solicitud, destino, fecha_entr, fi1, ff1, m1, fol_m1, fol_m1_num, motivo, obs_ent, se1, f_cap, linea, usr) VALUES
  ('{$recibo}', '{$anio_r}', '{$cliente}', '{$marca}', '', '{$sol}', '{$destino}', '{$fecha_e}', '{$fi}', '{$ff}','{$mermas}', '{$fol_m1}', '{$fol_m1_num}', '{$motivo_merma}', '{$obs_ent}', '{$total}', '{$fecha}','0', '{$usr}');";
	$sql_existencias="update h_existencias set fol_ini=if($ff<fol_fin,$ff+1,0), fol_fin=if($ff<fol_fin,fol_fin,0), existencias=existencias-$subtotal where no_cliente='--' and marca='--' and serie='-'";
  }
  else if($tipo=='P')
  {
	$serie=utf8_decode ($itm['serie']);
   $sql_ins="INSERT INTO h_salidas (id_recibo, anio_rcbo, no_cliente, marca, serie, solicitud, destino, fecha_entr, fi1, ff1, m1, fol_m1, fol_m1_num, motivo, obs_ent, se1, f_cap, linea, usr) VALUES
  ('{$recibo}', '{$anio_r}', '{$cliente}', '{$marca}', '{$serie}', '{$sol}', '{$destino}', '{$fecha_e}', '{$fi}', '{$ff}','{$mermas}', '{$fol_m1}', '{$fol_m1_num}', '{$motivo_merma}', '{$obs_ent}', '{$total}', '{$fecha}', '0',  '{$usr}');";
	 $sql_existencias="update h_existencias set fol_ini=if($ff<fol_fin,$ff+1,0), fol_fin=if($ff<fol_fin,fol_fin,0), existencias=existencias-$subtotal where no_cliente='$cliente' and marca='$marca' and serie='$serie'";
  }
  $result=$conexion->query($sql_ins);
  
  
  // Ahora comprobaremos que todo ha ido correctamente
  if($result==false)
  { 
    $errores++;
	$mensaje=array('status' => 'error','msj'=> 'Ha ocurrido un error al generar el recibo, imprima pantalla del error y comuniquelo al area de sistemas');
  } 
  else
  { 
	$update=$conexion->query($sql_existencias);
	if($update==false)
	{
	   $errores++;
	   $mensaje=array('status' => 'error','msj'=>'Ha ocurrido un error al actualizar existencias, imprima pantalla del error y comuniquelo al area de sistemas');
	}
	else
	{
		$mensaje=array('status' => 'OK','msj'=>'TODO HA IDO BIEN');
	}
  }
}
function recibo_mul()
{
	   //VARIABLES PARA GENERAR EL RECIBO
  include('../../../common/conexion.php');
  require('../../../libs/fpdf/fpdf.php');
  require_once('../../../common/cfg_server.php');
  class PDF extends FPDF
  {
	  var $col=0;
  }
  include('../conexion.php');
  global $item1, $item2, $item3,$head,$l_arr;
  
  $cliente=utf8_decode ($head['cliente']);
  $recibo=utf8_decode ($head['recibo']);
  $anio_r=utf8_decode ($head['anio_r']);
  $fecha_e=utf8_decode ($head['entrega']);
  $id_recibo='AR'.str_pad($recibo, 4,'0',STR_PAD_LEFT).'/'.$anio_r;
  $empresa="";
  //OBTENER LOS NOMBRES DE LA EMPRESA
       $busca_clie=str_pad($cliente, 4,'0',STR_PAD_LEFT);
	  $sql_empresa="select nombre from clientes where substr(no_cliente,1,4)='$busca_clie' and nombre!='--'";
	  $res_empresa=$conexion->query($sql_empresa);
	  // Ahora comprobaremos que todo ha ido correctamente
	  if($res_empresa==false)
	  { 
		$empresa="--";
	  } 
	  else
	  { 
		$tot=$res_empresa->num_rows;
		if($tot==1)
		{
		  $row_empresa=$res_empresa->fetch_row();
		  $empresa=trim($row_empresa[0]);
		}
		else
		{
		  $empresa="--";
		}
	  }//FIN OBTENER LOS DATOS DE LA EMPRESA
	  
//----HOLOGRAMAS ITEM1---------------  
  $serie="";
  $n_marca="--";
  $folios="";
  $tipo=utf8_decode ($item1['tipo']);
  $marca=utf8_decode ($item1['marca']);	
  $serie=utf8_decode ($item1['serie']);
  if($marca=='0')
  {
	  $marca='';
  }
  $n_marca=utf8_decode ($item1['n_marca']);
  if($n_marca=='Seleccionar')
  {
	  $n_marca='--';
  }
  $sol=utf8_decode ($item1['solicitud']);
  $destino=utf8_decode ($item1['destino']);
  $fi=utf8_decode ($item1['fini']);
  $ff=utf8_decode ($item1['ffin']);				
  $total=utf8_decode ($item1['cantidad']);
  //mermas
  $mermas=utf8_decode ($item1['no_mermas']);
  $fol_m1=utf8_decode ($item1['fol_merma']);
  if($mermas>0)
  {
	  $total=$item1['total_sellos'];
  }
  
  //GENERAR LOS FOLIOS  
  if($tipo=='G')
  {
	$folios=$fi.' - '.$ff;
  }
  else if($tipo=='P')
  {
  $folios=$cliente.$marca.str_pad($fi, 7,'0',STR_PAD_LEFT).$serie." - ".$cliente.$marca.str_pad($ff, 7,'0',STR_PAD_LEFT).$serie;
  }//FIN HOLOGRAMAS ITEM1
//----HOLOGRAMAS ITEM2---------------  
  $serie2="";
  $n_marca2="--";
  $folios2="";
  $tipo2=utf8_decode ($item2['tipo']);
  $marca2=utf8_decode ($item2['marca']);
  $serie2=utf8_decode ($item2['serie']);	
  if($marca2=='0')
  {
	  $marca2='';
  }
  $n_marca2=utf8_decode ($item2['n_marca']);
  if($n_marca2=='Seleccionar')
  {
	  $n_marca2='--';
  }
  $sol2=utf8_decode ($item2['solicitud']);
  $destino2=utf8_decode ($item2['destino']);
  $fi2=utf8_decode ($item2['fini']);
  $ff2=utf8_decode ($item2['ffin']);				
  $total2=utf8_decode ($item2['cantidad']);
  $mermas2=utf8_decode ($item2['no_mermas']);
  $fol_m2=utf8_decode ($item2['fol_merma']);
  if($mermas2>0)
  {
	  $total2=$item2['total_sellos'];
  }
  //$observ=utf8_decode ($_POST['observ']);
 
  //GENERAR LOS FOLIOS  
  if($tipo2=='G')
  {
	$folios2=$fi2.' - '.$ff2;
  }
  else if($tipo2=='P')
  {
  $folios2=$cliente.$marca2.str_pad($fi2, 7,'0',STR_PAD_LEFT).$serie2." - ".$cliente.$marca2.str_pad($ff2, 7,'0',STR_PAD_LEFT).$serie2;
  }//FIN HOLOGRAMAS ITEM1
  if($l_arr==4)//----HOLOGRAMAS ITEM3---------------  
  {
	$serie3="";
	$n_marca3="--";
	$folios3="";
	$tipo3=utf8_decode ($item3['tipo']);
	$marca3=utf8_decode ($item3['marca']);
	$serie3=utf8_decode ($item3['serie']);	
	if($marca3=='0')
	{
		$marca3='';
	}
	$n_marca3=utf8_decode ($item3['n_marca']);
	if($n_marca3=='Seleccionar')
	{
		$n_marca3='--';
	}
	$sol3=utf8_decode ($item3['solicitud']);
	$destino3=utf8_decode ($item3['destino']);
	$fi3=utf8_decode ($item3['fini']);
	$ff3=utf8_decode ($item3['ffin']);				
	$total3=utf8_decode ($item3['cantidad']);
	$mermas3=utf8_decode ($item3['no_mermas']);
	$fol_m3=utf8_decode ($item3['fol_merma']);
	if($mermas3>0)
	{
		$total3=$item3['total_sellos'];
	}
	//$observ=utf8_decode ($_POST['observ']);
   
	//GENERAR LOS FOLIOS  
	if($tipo3=='G')
	{
	  $folios3=$fi3.' - '.$ff3;
	}
	else if($tipo3=='P')
	{
	$folios3=$cliente.$marca3.str_pad($fi3, 7,'0',STR_PAD_LEFT).$serie3." - ".$cliente.$marca3.str_pad($ff3, 7,'0',STR_PAD_LEFT).$serie3;
	}//FIN HOLOGRAMAS ITEM1  
  }
  
  
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->SetDisplayMode(100,'continuous');	
	$pdf->AddPage('P','Letter');
	  $pdf->SetAutoPageBreak (true , 1);
	        $pdf->SetTextColor(0,0,0);
			
			
			//---------------RECIBO 1--------------------------------------------
			//INSERTAR LOGO
			$y=10;
			$pdf->Image('../../images/logo.jpg',12,$y,45,19);
			//$pdf->Image('../../images/logo2.jpg',171,$y,32,17);
			
			$pdf->SetFont('Arial','B',16);
			$pdf->SetXY(67,$y);
			$pdf->Cell(90,5, 'Consejo Regulador del Mezcal','0','','C');
			//CABECERA
			$y=$y+8;
			$pdf->SetFont('Arial','B',9);
			$pdf->SetXY(67,$y);
			$pdf->Cell(90,5,'Consejo Mexicano Regulador de la Calidad del Mezcal',0,'','C');
			//TITULO AREA
			$y=$y+8;
	     	$pdf->SetFont('Arial','B',11);
			$pdf->SetXY(65,$y);
			$pdf->Cell(100,5,'O R G A N I S M O  D E  C E R T I F I C A C I O N',0,'','C');
			//TITULO DOCUMENTO
			$y=$y+6;
			$pdf->SetFont('Arial','B',10);
			$pdf->SetXY(65,$y);
			$pdf->Cell(90,5,'ACUSE DE RECIBO DE HOLOGRAMAS',0,'','C');
			//datos recibo
			
			$pdf->SetFont('Arial','B',10);
			$pdf->SetXY(171,$y-4);
			$pdf->Cell(6,5,'No.',0,'','C');
			
			$pdf->SetFont('Arial','B',10);
			$pdf->SetXY(178,$y-4);
			$pdf->Cell(20,5,$id_recibo,0,0,'R');
					
			$pdf->SetFont('Arial','B',10);
			$pdf->SetXY(148,$y+1);
			$pdf->Cell(50,5,fecha($fecha_e),0,0,'R');
		
			//CUERPO RECIBO Y=42
			$y=$y+8;
			$pdf->SetFont('Arial','',9);
			$pdf->SetXY(20,$y);
			$pdf->Cell(23,5,'EMPRESA:',0,'','L');
	
			$pdf->SetFont('Arial','B',9);
			$pdf->SetXY(42,$y);
			$pdf->MultiCell(160,4.5,$empresa,0,'L',0);
			
			//----------HOLOGRAMAS ITEM1------------------------
		    //MARCA
			$y=$y+9; //y=50
			$pdf->SetFont('Arial','',9);
			$pdf->SetXY(20,$y);
			$pdf->Cell(23,4.5,'MARCA:',0,'','L');
			
			$pdf->SetFont('Arial','B',9);
			$pdf->SetXY(42,$y);
			$pdf->Cell(110,4.5,$n_marca,0,'','L');
			//FOLIOS
			$y=$y+4.5;
			$pdf->SetFont('Arial','',9);
			$pdf->SetXY(20,$y);
			$pdf->Cell(23,4.5,'FOLIOS:',0,'','L');
			
			$pdf->SetFont('Arial','B',10);
			$pdf->SetXY(42,$y);
			$pdf->Cell(110,4.5,$folios,0,'','L');
		    //CANTIDAD
		    $pdf->SetFont('Arial','',9);
			$pdf->SetXY(120,$y);
			$pdf->Cell(23,4.5,'PIEZAS:',0,'','L');
			
			$pdf->SetFont('Arial','B',10);
			$pdf->SetXY(140,$y);
			$pdf->Cell(110,4.5,$total,0,'','L');
			
			$y=$y+4.5;
			if($mermas>0)
			{
			$pdf->SetFont('Arial','',9);
			$pdf->SetXY(20,$y);
			$pdf->Cell(23,4.5,'MERMAS:',0,'','L');
			
			$pdf->SetFont('Arial','B',9);
			$pdf->SetXY(42,$y);
			$pdf->Cell(110,4.5,$fol_m1,0,'','L');	
			$y=$y+4.5;
			}
			//SOLICITUD
			$pdf->SetFont('Arial','',9);
			$pdf->SetXY(20,$y);
			$pdf->Cell(23,4.5,'SOLICITUD:',0,'','L');
			
			$pdf->SetFont('Arial','B',9);
			$pdf->SetXY(42,$y);
			$pdf->Cell(110,4.5,$sol,0,'','L');
			//DESTINO
			$pdf->SetFont('Arial','',9);
			$pdf->SetXY(120,$y);
			$pdf->Cell(23,4.5,'DESTINO:',0,'','L');
			
			$pdf->SetFont('Arial','B',9);
			$pdf->SetXY(140,$y);
			$pdf->Cell(110,4.5,$destino,0,'','L');
			//-----FIN HOLOGRAMAS ITEM1------------
			
			//----------HOLOGRAMAS ITEM2------------	
			  //MARCA	
			$y=$y+5.5; //y=50
			$pdf->SetFont('Arial','',9);
			$pdf->SetXY(20,$y);
			$pdf->Cell(23,4.5,'MARCA:',0,'','L');
			
			$pdf->SetFont('Arial','B',9);
			$pdf->SetXY(42,$y);
			$pdf->Cell(110,4.5,$n_marca2,0,'','L');
			//FOLIOS
			$y=$y+4.5;
			$pdf->SetFont('Arial','',9);
			$pdf->SetXY(20,$y);
			$pdf->Cell(23,4.5,'FOLIOS:',0,'','L');
			
			$pdf->SetFont('Arial','B',10);
			$pdf->SetXY(42,$y);
			$pdf->Cell(110,4.5,$folios2,0,'','L');
		    //CANTIDAD
		    $pdf->SetFont('Arial','',9);
			$pdf->SetXY(120,$y);
			$pdf->Cell(23,4.5,'PIEZAS:',0,'','L');
			
			$pdf->SetFont('Arial','B',10);
			$pdf->SetXY(140,$y);
			$pdf->Cell(110,4.5,$total2,0,'','L');
			
			$y=$y+4.5;
			//MERMAS
			if($mermas2>0)
			{
			$pdf->SetFont('Arial','',9);
			$pdf->SetXY(20,$y);
			$pdf->Cell(23,4.5,'MERMAS:',0,'','L');
			
			$pdf->SetFont('Arial','B',9);
			$pdf->SetXY(42,$y);
			$pdf->Cell(110,4.5,$fol_m2,0,'','L');	
			$y=$y+4.5;
			}
			//SOLICITUD
			$pdf->SetFont('Arial','',9);
			$pdf->SetXY(20,$y);
			$pdf->Cell(23,4.5,'SOLICITUD:',0,'','L');
			
			$pdf->SetFont('Arial','B',9);
			$pdf->SetXY(42,$y);
			$pdf->Cell(110,4.5,$sol2,0,'','L');
			//DESTINO
			$pdf->SetFont('Arial','',9);
			$pdf->SetXY(120,$y);
			$pdf->Cell(23,4.5,'DESTINO:',0,'','L');
			
			$pdf->SetFont('Arial','B',9);
			$pdf->SetXY(140,$y);
			$pdf->Cell(110,4.5,$destino2,0,'','L');
			//-----FIN HOLOGRAMAS ITEM2------------
			if($l_arr==4)//----HOLOGRAMAS ITEM3---------------  
            {
				//MARCAS
			  $y=$y+5.5; //y=50
			  $pdf->SetFont('Arial','',9);
			  $pdf->SetXY(20,$y);
			  $pdf->Cell(23,4.5,'MARCA:',0,'','L');
			  
			  $pdf->SetFont('Arial','B',9);
			  $pdf->SetXY(42,$y);
			  $pdf->Cell(110,4.5,$n_marca3,0,'','L');
			  //FOLIOS
			  $y=$y+4.5;
			  $pdf->SetFont('Arial','',9);
			  $pdf->SetXY(20,$y);
			  $pdf->Cell(23,4.5,'FOLIOS:',0,'','L');
			  
			  $pdf->SetFont('Arial','B',10);
			  $pdf->SetXY(42,$y);
			  $pdf->Cell(110,4.5,$folios3,0,'','L');
			  //CANTIDAD
			  $pdf->SetFont('Arial','',9);
			  $pdf->SetXY(120,$y);
			  $pdf->Cell(23,4.5,'PIEZAS:',0,'','L');
			  
			  $pdf->SetFont('Arial','B',10);
			  $pdf->SetXY(140,$y);
			  $pdf->Cell(110,4.5,$total3,0,'','L');
			  $y=$y+4.5;
			  //MERMAS
			  if($mermas3>0)
			  {
			  $pdf->SetFont('Arial','',9);
			  $pdf->SetXY(20,$y);
			  $pdf->Cell(23,4.5,'MERMAS:',0,'','L');
			  
			  $pdf->SetFont('Arial','B',9);
			  $pdf->SetXY(42,$y);
			  $pdf->Cell(110,4.5,$fol_m3,0,'','L');	
			  $y=$y+4.5;
			  }
			  //SOLICITUD
			  $pdf->SetFont('Arial','',9);
			  $pdf->SetXY(20,$y);
			  $pdf->Cell(23,4.5,'SOLICITUD:',0,'','L');
			  
			  $pdf->SetFont('Arial','B',9);
			  $pdf->SetXY(42,$y);
			  $pdf->Cell(110,4.5,$sol3,0,'','L');
			  
			  $pdf->SetFont('Arial','',9);
			  $pdf->SetXY(120,$y);
			  $pdf->Cell(23,4.5,'DESTINO:',0,'','L');
			  
			  $pdf->SetFont('Arial','B',9);
			  $pdf->SetXY(140,$y);
			  $pdf->Cell(110,4.5,$destino3,0,'','L');
			}
			
			$y=112;
			$pdf->SetFont('Arial','',9);
			$pdf->SetXY(20,$y);
			$pdf->Cell(23,5,'RECIBE:',0,'','L');
					
			$pdf->SetFont('Arial','',9);
			$pdf->SetXY(129,$y);
			$pdf->Cell(23,5,'FIRMA:',0,'','L');
		    $y=$y+4;
			$pdf->line(42,$y,120,$y);		
			$pdf->line(143,$y,185,$y);
			
			$y=$y+3;
			$pdf->SetFont('Arial','',9);
			$pdf->SetXY(184,$y);
			$pdf->Cell(23,5,'FQ-171/03',0,'','L');
			$y=$y+5;
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
			
			$pdf->SetFont('Arial','',10);
			$pdf->SetXY(5,135);
			$linea='.........................................................................................................';
			$pdf->Cell(180,5,$linea.$linea,0,'','L');
			
			//---------------RECIBO 2--------------------------------------------
			$y=148;
			$pdf->Image('../../images/logo.jpg',12,$y,45,19);
			//$pdf->Image('../../images/logo2.jpg',171,$y,32,17);
			
			$pdf->SetFont('Arial','B',16);
			$pdf->SetXY(67,$y);
			$pdf->Cell(90,5, 'Consejo Regulador del Mezcal','0','','C');
			//CABECERA
			$y=$y+8;
			$pdf->SetFont('Arial','B',9);
			$pdf->SetXY(67,$y);
			$pdf->Cell(90,5,'Consejo Mexicano Regulador de la Calidad del Mezcal',0,'','C');
			//TITULO AREA
			$y=$y+8;
	     	$pdf->SetFont('Arial','B',11);
			$pdf->SetXY(65,$y);
			$pdf->Cell(100,5,'O R G A N I S M O  D E  C E R T I F I C A C I O N',0,'','C');
			//TITULO DOCUMENTO
			$y=$y+6;
			$pdf->SetFont('Arial','B',10);
			$pdf->SetXY(65,$y);
			$pdf->Cell(90,5,'ACUSE DE RECIBO DE HOLOGRAMAS',0,'','C');
			//datos recibo
			
			$pdf->SetFont('Arial','B',10);
			$pdf->SetXY(171,$y-4);
			$pdf->Cell(6,5,'No.',0,'','C');
			
			$pdf->SetFont('Arial','B',10);
			$pdf->SetXY(178,$y-4);
			$pdf->Cell(20,5,$id_recibo,0,0,'R');
					
			$pdf->SetFont('Arial','B',10);
			$pdf->SetXY(148,$y+1);
			$pdf->Cell(50,5,fecha($fecha_e),0,0,'R');
		
			//CUERPO RECIBO Y=42
			$y=$y+8;
			$pdf->SetFont('Arial','',9);
			$pdf->SetXY(20,$y);
			$pdf->Cell(23,5,'EMPRESA:',0,'','L');
	
			$pdf->SetFont('Arial','B',9);
			$pdf->SetXY(42,$y);
			$pdf->MultiCell(160,4.5,$empresa,0,'L',0);
			
			//----------HOLOGRAMAS ITEM1------------------------
		    //MARCA
			$y=$y+9; //y=50
			$pdf->SetFont('Arial','',9);
			$pdf->SetXY(20,$y);
			$pdf->Cell(23,4.5,'MARCA:',0,'','L');
			
			$pdf->SetFont('Arial','B',9);
			$pdf->SetXY(42,$y);
			$pdf->Cell(110,4.5,$n_marca,0,'','L');
			//FOLIOS
			$y=$y+4.5;
			$pdf->SetFont('Arial','',9);
			$pdf->SetXY(20,$y);
			$pdf->Cell(23,4.5,'FOLIOS:',0,'','L');
			
			$pdf->SetFont('Arial','B',10);
			$pdf->SetXY(42,$y);
			$pdf->Cell(110,4.5,$folios,0,'','L');
		    //CANTIDAD
		    $pdf->SetFont('Arial','',9);
			$pdf->SetXY(120,$y);
			$pdf->Cell(23,4.5,'PIEZAS:',0,'','L');
			
			$pdf->SetFont('Arial','B',10);
			$pdf->SetXY(140,$y);
			$pdf->Cell(110,4.5,$total,0,'','L');
			
			$y=$y+4.5;
			if($mermas>0)
			{
			$pdf->SetFont('Arial','',9);
			$pdf->SetXY(20,$y);
			$pdf->Cell(23,4.5,'MERMAS:',0,'','L');
			
			$pdf->SetFont('Arial','B',9);
			$pdf->SetXY(42,$y);
			$pdf->Cell(110,4.5,$fol_m1,0,'','L');	
			$y=$y+4.5;
			}
			//SOLICITUD
			$pdf->SetFont('Arial','',9);
			$pdf->SetXY(20,$y);
			$pdf->Cell(23,4.5,'SOLICITUD:',0,'','L');
			
			$pdf->SetFont('Arial','B',9);
			$pdf->SetXY(42,$y);
			$pdf->Cell(110,4.5,$sol,0,'','L');
			//DESTINO
			$pdf->SetFont('Arial','',9);
			$pdf->SetXY(120,$y);
			$pdf->Cell(23,4.5,'DESTINO:',0,'','L');
			
			$pdf->SetFont('Arial','B',9);
			$pdf->SetXY(140,$y);
			$pdf->Cell(110,4.5,$destino,0,'','L');
			//-----FIN HOLOGRAMAS ITEM1------------
			
			//----------HOLOGRAMAS ITEM2------------	
			  //MARCA	
			$y=$y+5.5; //y=50
			$pdf->SetFont('Arial','',9);
			$pdf->SetXY(20,$y);
			$pdf->Cell(23,4.5,'MARCA:',0,'','L');
			
			$pdf->SetFont('Arial','B',9);
			$pdf->SetXY(42,$y);
			$pdf->Cell(110,4.5,$n_marca2,0,'','L');
			//FOLIOS
			$y=$y+4.5;
			$pdf->SetFont('Arial','',9);
			$pdf->SetXY(20,$y);
			$pdf->Cell(23,4.5,'FOLIOS:',0,'','L');
			
			$pdf->SetFont('Arial','B',10);
			$pdf->SetXY(42,$y);
			$pdf->Cell(110,4.5,$folios2,0,'','L');
		    //CANTIDAD
		    $pdf->SetFont('Arial','',9);
			$pdf->SetXY(120,$y);
			$pdf->Cell(23,4.5,'PIEZAS:',0,'','L');
			
			$pdf->SetFont('Arial','B',10);
			$pdf->SetXY(140,$y);
			$pdf->Cell(110,4.5,$total2,0,'','L');
			
			$y=$y+4.5;
			//MERMAS
			if($mermas2>0)
			{
			$pdf->SetFont('Arial','',9);
			$pdf->SetXY(20,$y);
			$pdf->Cell(23,4.5,'MERMAS:',0,'','L');
			
			$pdf->SetFont('Arial','B',9);
			$pdf->SetXY(42,$y);
			$pdf->Cell(110,4.5,$fol_m2,0,'','L');	
			$y=$y+4.5;
			}
			//SOLICITUD
			$pdf->SetFont('Arial','',9);
			$pdf->SetXY(20,$y);
			$pdf->Cell(23,4.5,'SOLICITUD:',0,'','L');
			
			$pdf->SetFont('Arial','B',9);
			$pdf->SetXY(42,$y);
			$pdf->Cell(110,4.5,$sol2,0,'','L');
			//DESTINO
			$pdf->SetFont('Arial','',9);
			$pdf->SetXY(120,$y);
			$pdf->Cell(23,4.5,'DESTINO:',0,'','L');
			
			$pdf->SetFont('Arial','B',9);
			$pdf->SetXY(140,$y);
			$pdf->Cell(110,4.5,$destino2,0,'','L');
			//-----FIN HOLOGRAMAS ITEM2------------
			if($l_arr==4)//----HOLOGRAMAS ITEM3---------------  
            {
				//MARCAS
			  $y=$y+5.5; //y=50
			  $pdf->SetFont('Arial','',9);
			  $pdf->SetXY(20,$y);
			  $pdf->Cell(23,4.5,'MARCA:',0,'','L');
			  
			  $pdf->SetFont('Arial','B',9);
			  $pdf->SetXY(42,$y);
			  $pdf->Cell(110,4.5,$n_marca3,0,'','L');
			  //FOLIOS
			  $y=$y+4.5;
			  $pdf->SetFont('Arial','',9);
			  $pdf->SetXY(20,$y);
			  $pdf->Cell(23,4.5,'FOLIOS:',0,'','L');
			  
			  $pdf->SetFont('Arial','B',10);
			  $pdf->SetXY(42,$y);
			  $pdf->Cell(110,4.5,$folios3,0,'','L');
			  //CANTIDAD
			  $pdf->SetFont('Arial','',9);
			  $pdf->SetXY(120,$y);
			  $pdf->Cell(23,4.5,'PIEZAS:',0,'','L');
			  
			  $pdf->SetFont('Arial','B',10);
			  $pdf->SetXY(140,$y);
			  $pdf->Cell(110,4.5,$total3,0,'','L');
			  $y=$y+4.5;
			  //MERMAS
			  if($mermas3>0)
			  {
			  $pdf->SetFont('Arial','',9);
			  $pdf->SetXY(20,$y);
			  $pdf->Cell(23,4.5,'MERMAS:',0,'','L');
			  
			  $pdf->SetFont('Arial','B',9);
			  $pdf->SetXY(42,$y);
			  $pdf->Cell(110,4.5,$fol_m3,0,'','L');	
			  $y=$y+4.5;
			  }
			  //SOLICITUD
			  $pdf->SetFont('Arial','',9);
			  $pdf->SetXY(20,$y);
			  $pdf->Cell(23,4.5,'SOLICITUD:',0,'','L');
			  
			  $pdf->SetFont('Arial','B',9);
			  $pdf->SetXY(42,$y);
			  $pdf->Cell(110,4.5,$sol3,0,'','L');
			  
			  $pdf->SetFont('Arial','',9);
			  $pdf->SetXY(120,$y);
			  $pdf->Cell(23,4.5,'DESTINO:',0,'','L');
			  
			  $pdf->SetFont('Arial','B',9);
			  $pdf->SetXY(140,$y);
			  $pdf->Cell(110,4.5,$destino3,0,'','L');
			}
			else
			{
				$y=$y+15;
			}
			$y=252;
			$pdf->SetFont('Arial','',9);
			$pdf->SetXY(20,$y);
			$pdf->Cell(23,5,'RECIBE:',0,'','L');
					
			$pdf->SetFont('Arial','',9);
			$pdf->SetXY(129,$y);
			$pdf->Cell(23,5,'FIRMA:',0,'','L');
		    $y=$y+4;
			$pdf->line(42,$y,120,$y);		
			$pdf->line(143,$y,185,$y);
			
			$y=$y+3;
			$pdf->SetFont('Arial','',9);
			$pdf->SetXY(184,$y);
			$pdf->Cell(23,5,'FQ-171/03',0,'','L');
			$y=$y+5;
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
			
			$nombre=str_replace('/','_',$id_recibo);
			$file = $nombre.'.pdf';	
	$pdf->Output('pdf_recibos/'.$file, 'F');
    //Redirect
	$dir_file="http://".$svr_dir."/siig/hologramas/php/recibos/pdf_recibos/".$file;
     echo json_encode(array('status' => 'OK','msj'=>$dir_file));
	

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