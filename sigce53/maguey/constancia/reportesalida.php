<?php
//header('Content-Type: text/html; charset=UTF-8');
//include_once("Polyline.php");
//require('fpdf/fpdf.php');
require('../../librerias/fpdf/fpdf.php');
include("../../common/conexion.php");
$conexion->set_charset("utf8");
header('Content-Type: text/html; charset=UTF-8');
class PDF extends FPDF
{
var $widths;
var $aligns;

// function SetWidths($w){
// 	$this->widths=$w;
// }

function __construct($orientation='P',$unit='mm',$format='A4') {                  
		parent::FPDF($orientation,$unit,$format);

		//Initialization
		////////Protection part/////////////////////////////////////////////////////////////
		$this->encrypted=false;
		$this->last_rc4_key='';
		$this->padding="\x28\xBF\x4E\x5E\x4E\x75\x8A\x41\x64\x00\x4E\x56\xFF\xFA\x01\x08".
		"\x2E\x2E\x00\xB6\xD0\x68\x3E\x80\x2F\x0C\xA9\xFE\x64\x53\x69\x7A";
		////////////////////////////////////////////////////////////////////////////////////
		$this->B=0;
		$this->I=0;
		$this->U=0;
		$this->HREF='';

		$this->tableborder=0;
		$this->tdbegin=false;
		$this->tdwidth=0;
		$this->tdheight=0;
		$this->tdalign="L";
		$this->tdbgcolor=false;

		$this->oldx=0;
		$this->oldy=0;
		$this->AddFont('Calibri','','CalibriRegular.php');
		$this->AddFont('Calibri-Bold','','CalibriBold.php');
		$this->AddFont('Calibri-BoldItalic','','CalibriBoldItalic.php');
		$this->AddFont('Calibri-Italic','','CalibriItalic.php');
		$this->AddFont('Calibri-Light','','CalibriLight.php');
		$this->AddFont('Calibri-LightItalic','','CalibriLightItalic.php');
		$this->fontlist=array("Calibri","Times","times","courier","helvetica","symbol");
		$this->issetfont=false;
		$this->issetcolor=false;
		$this->SetAutoPageBreak( 1 , 30);
		$this->SetMargins(25,15,15);
		$this->SetAutoPageBreak(false,40);
	}

function SetAligns($a){
	$this->aligns=$a;
}

	function Row($data){
	//Calculate the height of the row calcular la altura de la fila
	$nb=0;
		for($i=0;$i<count($data);$i++)
		$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
		$h=5*$nb;
		//Issue a page break first if needed Emita primero un salto de página si es necesario
		$this->CheckPageBreak($h);

			//Dibuja las celdas de la fila
			for($i=0;$i<count($data);$i++)
			{
			$w=$this->widths[$i];
			$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';

			// Guarde la posición actual
			$x=$this->GetX();
			$y=$this->GetY();
			//$x=tiene la posicion de eje x.
			//$y= tiene la posicion de eje y.
			//Dibuja el borde
			$this->Rect($x,$y,$w,$h);

			$this->MultiCell($w,5,$data[$i],0,$a,'true');

			// Coloque la posición a la derecha de la celda
			$this->SetXY($x+$w,$y);
			}//termina el for($i=0;$i<count($data);$i++)

	//va para la siguiente linea
	$this->Ln($h);
	}// termina function Row($data)

function CheckPageBreak($h)
{
	//If the height h would cause an overflow, add a new page immediately Si la altura h causaría un desbordamiento, agregue una nueva página inmediatamente
	if($this->GetY()+$h>$this->PageBreakTrigger)
		$this->AddPage($this->CurOrientation);
}//function CheckPageBreak($h)

function NbLines($w,$txt)
{
	//Calcula el número de líneas que tomará una MultiCell de ancho w
	$cw=&$this->CurrentFont['cw'];
	if($w==0)
		$w=$this->w-$this->rMargin-$this->x;
	$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	$s=str_replace("\r",'',$txt);
	$nb=strlen($s);
	if($nb>0 and $s[$nb-1]=="\n")
		$nb--;
	$sep=-1;
	$i=0;
	$j=0;
	$l=0;
	$nl=1;
	while($i<$nb)
	{
		$c=$s[$i];
		if($c=="\n")
		{
			$i++;
			$sep=-1;
			$j=$i;
			$l=0;
			$nl++;
			continue;
		}
		if($c==' ')
			$sep=$i;
		$l+=$cw[$c];
		if($l>$wmax)
		{
			if($sep==-1)
			{
				if($i==$j)
					$i++;
			}
			else
				$i=$sep+1;
			$sep=-1;
			$j=$i;
			$l=0;
			$nl++;
		}
		else
			$i++;
	}
	return $nl;
}//

function Header()
{
	$img_file = '../../images/bg_amma_v.jpeg';
    $this->Image($img_file, 0, 0, $this->w, $this->h + 9);
}

function Footer()
{

	$this->SetFont('Calibri-BoldItalic','',9);
		$this->AliasNbPages();
		$this->SetY(-30);
    $this->Cell(0,5,utf8_decode('Página').$this->PageNo().'/{nb}',0,5,'L');
    $this->SetY(-21);
    if(isset($_GET['tipo']) && $_GET['tipo'] == "V" ) 
    	$this->Cell(0,5,Utf8_decode('FOR-UM-05/01.'),0,5,'R');
    else
       $this->Cell(0,5,Utf8_decode('FOR-UM-03/01.'),0,5,'R');



	$this->SetY(-25);
	$this->Cell(47,5, utf8_decode('--> Llenar solo vendedor' ),0,5,'C');
	$this->Cell(50,6, utf8_decode('--> Llenar solo comprador'),0,5,'C');
	$this->SetLineWidth(0.1);
	$this->SetDrawColor(33,33,33);
	$this->SetFillColor(23,97,66);
	//$this->SetY(-20);
	$this->Rect(20, 254, 5, 5, 'FD');
	$this->SetDrawColor(33,33,33);
	$this->SetFillColor(70,105,176);
	$this->Rect(20, 260, 5, 5, 'FD');
	//$this->Cell(15, 6, '', 1 , 1);
	

	//
	$this->SetFont('Calibri-Bold','',7);
	$this->Ln(-1);
	$this->Cell(0,5,utf8_decode('Av. Universidad N° 312-A, Fracc. Trinidad de las Huertas, Oaxaca de Juárez, C. P. 68120, Oaxaca, México'),0,5,'C');
	$this->Ln(-1);
	$this->Cell(0,5,utf8_decode('www.amma.org.mx   maguey@amma.org.mx  Teléfonos: 951 672 9399, 951 672 9474'),0,5,'C');
}

}//class PDF extends FPDF
	$paraje= $_GET['id'];

	//$tipo = (isset($_GET['tipo']) && $_GET['tipo'] == "V" ) ? "V" : "P";
	if(isset($_GET['tipo']) && $_GET['tipo'] == "V" ) {
		$tabla = "paraje_vivero";
		$tipo = "V";
		$ep = "existenciaplanta_vivero";
		//$cadcampo = " CONCAT('V',LPAD(p.id,4,'0')) ";
	} else {
		$tabla = "paraje";
		$tipo = "P";
		$ep = "existenciaplanta";
		//$cadcampo = " CONCAT('P',LPAD(p.id,4,'0')) ";
	}


	$random_Number      = rand(0, 9999999999);
    $nuevoNombre        = "Extraccion_".$paraje."_".$random_Number.".pdf";
    $destino =          "pdfConstanciaExtraccion/" . $nuevoNombre;


    $strUpdate="UPDATE $tabla SET constancia_extracciones = '$nuevoNombre' WHERE id_paraje='$paraje'";
	$parajesUpdate= $conexion->query($strUpdate);




$strConsulta = "SELECT Date_format(cextracciones.fecha,'%y') as anio,nombrep,paraje,p.tenencia,p.id_paraje,p.id_cliente,regmaguey,
LPAD(cextracciones.id_extraccion,5,'0') as constanciae,
CONCAT('P',LPAD(p.id,4,'0')) as parajes,cextracciones.fecha as fecha1,YEAR(date_add(cextracciones.fecha, INTERVAL 2 YEAR)) as fecha2
from  $tabla p
inner join cextracciones on (cextracciones.id_paraje=p.id_paraje COLLATE utf8_general_ci)
inner join $ep ep on p.id_paraje=ep.id_paraje
where p.id_paraje='$paraje'";

	$consulta="SELECT SUBSTR(id_paraje,2,length(id_paraje)) id FROM $tabla WHERE id_paraje = '$paraje' ";
    $consultaid = $conexion->query($consulta);
    if($consultaid==false) throw new Exception("Error al obtener id paraje");
    if ($consultaid->num_rows > 0){
        while ($row = $consultaid->fetch_array(MYSQLI_ASSOC)) 
			$idtp = $tipo.str_pad($row['id'], 4, "0", STR_PAD_LEFT);
    }
	
	//Cambiar locales a español México
	$parajes= $conexion->query($strConsulta);
	//$parajes = mysql_query($strConsulta);
	$fila = mysqli_fetch_array($parajes);


	//aqui empieza del paraje

	$Consulta = "SELECT localidades.localidad,municipios.nombre as nombrem,estados.nombre as nombree,p.paraje,p.referencia,p.lat,p.lng,p.superficie
		from estados
		inner join municipios on municipios.estado=estados.clave
		inner join localidades on localidades.MunicipioID=municipios.id
		inner join $tabla p on p.id_localidad=localidades.id
		where p.id_paraje='$paraje'";

		//$nombrecc = $fila['nombrec'];
	$ubicaciones= $conexion->query($Consulta);
	//$parajes = mysql_query($strConsulta);
	$dato = mysqli_fetch_array($ubicaciones);



	$fila['parajes'] = $idtp;

	//aqui termina paraje
	// AQUI INGRESAMOS EL FORPARA REPETIR LAS HOJAS
	

	$pdf=new PDF('P','mm','Letter');
	$pdf->Open();

	$numini = ($tipo == "P") ? 2: 3;
	$strConsulta = "SELECT CONCAT('G',LPAD(cextracciones.id,5,'0')) as id, LPAD(cextracciones.id,5,'0') cid,  
	SUBSTR(id_extraccion,$numini,length(id_extraccion)) idsolo
	FROM cextracciones WHERE id_paraje = '$paraje' ORDER BY id ASC";
	$extracciones= $conexion->query($strConsulta);

	// $d = $fila['fecha2'];
	// $fechaaa = strftime("%Y",strtotime($d));
	// $fecha2 = ucfirst($fechaaa);
	// $fecha2 = $d;

	//fecha nueva
	$fechaD = date('Y');
    $nuevafecha = strtotime ( '+1 year' , strtotime ( $fechaD ) ) ;
    $fecha2 = date ( 'Y' , $nuevafecha );
 
	while ($extraccion = mysqli_fetch_array($extracciones)) {

	$pdf->AddPage();
	$pdf->SetMargins(15,5,15,5);
	$pdf->SetAutoPageBreak(true,20);
	// aqui empieza
	setlocale(LC_ALL,"es_ES@euro","Es_ES","esp");
// $d = $fila['fecha1'];
// $fecha = strftime("%d-%b-%Y", strtotime($d));
// $fecha1 = ucfirst(strtolower($fecha));
//fecha1
	if($tipo == "V") {
		$fechaC = $fila['fecha1'];
		date_add($fechaC, date_interval_create_from_date_string("6 months"));
		$fechaN = date_format($fechaC,"Y-m-d");

		$fechaC = $fila['fecha1'];
		$fechaN = strtotime('+6 months', strtotime($fechaC));
		$fechaN = date('d-m-Y', $fechaN);
		$fechaN = strtotime('+1 year', strtotime($fechaN));
		$fechaN = date('d-m-Y', $fechaN);
	}

//$fechaa = strftime("%Y", strtotime($d));

	//termina fecha

	$pdf->Ln(30);
	$pdf->SetXY(20,28);

	$tipoext = ($tipo == "P") ? "G": "GP";
	$idextrac = $tipoext.str_pad($extraccion['idsolo'], 5, "0", STR_PAD_LEFT);
	$extraccion['id'] = $idextrac;
	$extraccion['cid'] =$idextrac;

	$pdf->SetFont('Calibri-Bold','',25);
	if($tipo == "P") 
		$pdf->Cell(0,8, utf8_decode('GUÍA DE MAGUEY'),0,5, 'C');
	else
		$pdf->Cell(0,8, utf8_decode('GUÍA DE PLÁNTULAS'),0,5, 'C');
	$pdf->SetTextColor(238,55,60);
	$pdf->SetFont('Calibri','',14);
	$pdf->Text(175,30,strtoupper($extraccion['id']).'/'.strtoupper($fila['parajes']),0,5,'C');
	//$pdf->Text(179,30,strtoupper($extraccion['id']),0,5,'C');
	$pdf->SetTextColor(33,33,33);
	$pdf->SetFont('Calibri-Bold','',10);
	$pdf->Text(175,35,utf8_decode('VIGENCIA:'),0,5,'C');
	$pdf->SetFont('Calibri-Bold','',10);
	if($tipo == "P") 
		$pdf->Text(191,35,'INDEFINIDA',0,5,'C');
	else
		$pdf->Text(191,35,$fechaN,0,5,'C');

	$pdf->Ln(3);
	$pdf->SetFont('Calibri-Bold','',11);
	$pdf->SetTextColor(33,33,33);
	$pdf->Cell(0,6,strtoupper('DATOS DEL VENDEDOR'), 0,5, 'C');
	$pdf->SetTextColor(33,33,33);
	$pdf->Ln(6);


	$pdf->SetFont('Calibri-Bold','',10);
	if($tipo == "P") 
		$pdf->Cell(28,7,'No. DE PREDIO: ',0,0);
	else
		$pdf->Cell(28,7,'No. DE VIVERO: ',0,0);
	$pdf->SetFont('Calibri-Bold','',9);
	$pdf->Cell(25,7,strtoupper($fila['parajes']),0,0,'C');
	$pdf->SetFont('Calibri-Bold','',10);
	$pdf->Cell(26,7,utf8_decode('No. DE EXTRACCIÓN: '),0,0);
	$pdf->SetFont('Calibri-Bold','',9);
	$pdf->Cell(23,7,strtoupper($extraccion['cid']),0,0,'C');
	$pdf->SetFont('Calibri-Bold','',10);
	$pdf->Cell(50,7,'     No. DE CONTROL: ',0,0);
	$pdf->SetFont('Calibri-Bold','',9);
	$pdf->Cell(22,7,strtoupper($fila['id_cliente']),0,0,'C');
	///
	$pdf->Ln(0);
	$pdf->SetFont('Calibri','',10);
	$pdf->Cell(28,7,'',0,0);
	$pdf->SetFont('Calibri','',9);
	$pdf->Cell(25,7,'______________',0,0,'C');
	$pdf->SetFont('Calibri','',10);
	$pdf->Cell(26,7,' ',0,0);
	$pdf->SetFont('Calibri','',9);
	$pdf->Cell(32,7,'____________',0,0,'C');
	$pdf->SetFont('Calibri','',10);
	$pdf->Cell(40,7,'',0,0);
	$pdf->SetFont('Calibri','',9);
	$pdf->Cell(20,7,'______________',0,0,'C');
		///
	$pdf->Ln(7);
	$pdf->SetFont('Calibri-Bold','',10);
	$pdf->Cell(22,7,utf8_decode('NOMBRE: '),0,0);
	$pdf->SetFont('Calibri-Bold','',9);
	
	$pdf->Cell(150,7,strtoupper(utf8_decode($fila['nombrep'])),0,5,'C');
	$pdf->Ln(-7);
	$pdf->SetFont('Calibri','',10);
	$pdf->Cell(22,7,utf8_decode(''),0,0);
	$pdf->SetFont('Calibri','',9);
	$pdf->Cell(153,7,'___________________________________________________________________________________________',0,5,'C');


	
	///
	$pdf->Ln(2);
	$pdf->SetFont('Calibri-Bold','',10);
	if($tipo == "P") 
		$pdf->Cell(44,7,utf8_decode('NOMBRE DE PREDIO: '),0,0);
	else
		$pdf->Cell(44,7,utf8_decode('NOMBRE DE VIVERO: '),0,0);
	$pdf->SetFont('Calibri-Bold','',9);
	$pdf->Cell(100,7,strtoupper(utf8_decode($fila['paraje'])),0,5,'C');
	$pdf->Ln(-7);
	$pdf->SetFont('Calibri','',10);
	$pdf->Cell(44,7,utf8_decode(''),0,0);
	$pdf->SetFont('Calibri','',9);
	$pdf->Cell(131,7,'_______________________________________________________________________________',0,5,'C');
	$pdf->Ln(1);
	$pdf->SetFont('Calibri-Bold','',10);
	if($tipo == "P")
		$pdf->Cell(33,7,utf8_decode('FECHA DE CORTE: '),0,0);
	else
		$pdf->Cell(33,7,utf8_decode('FECHA DE SALIDA: '),0,0);
	$pdf->SetFont('Calibri','',9);
	$pdf->Cell(141,7,'_______________________',0,5);

	// la consulta para datos del predio
	$Consulta = "SELECT localidades.localidad,municipios.nombre as nombrem,estados.nombre as nombree,p.paraje,p.referencia,p.lat,p.lng,p.superficie
		from estados
		inner join municipios on municipios.estado=estados.clave
		inner join localidades on localidades.MunicipioID=municipios.id
		inner join $tabla p on p.id_localidad=localidades.id
		where p.id_paraje='$paraje'";

	$ubicaciones= $conexion->query($Consulta);
	$dato = mysqli_fetch_array($ubicaciones);
// Aqui empieza la tabla de atributos de la tierra

	$pdf->Ln(3);
	/*$pdf->SetFont('Calibri-Bold','',10);
	$pdf->SetDrawColor(33,33,33);// color a las lineas de la tabla
	$pdf->Cell(0,5, utf8_decode('ATRIBUTOS DE LA TIERRA'),0,5);
	$pdf->Ln(2);
	$pdf->SetFont('Calibri-Bold','',7);
	$pdf->Cell(67,8,utf8_decode('MANEJO SUSTENTABLE DE MAGUEY SILVESTRE'),1,0);
	$pdf->SetFont('Calibri','',7);
	$pdf->Cell(9,8,'---',1,0,'C');
	$pdf->SetFont('Calibri-Bold','',7);
	$pdf->MultiCell(90,4,utf8_decode('PRESERVACIÓN DE POLINIZADORES Y VARIABILIDAD GENÉTICA DEL MAGUEY EN CULTIVOS'),1);
	$pdf->Ln(-8);
	$pdf->SetX(181);
	$pdf->SetFont('Calibri-Bold','',7);
	$pdf->Cell(9,8,'---',1,0,'C');
	$pdf->Ln(8);
	$pdf->SetFont('Calibri-Bold','',7);
	$pdf->Cell(67,8,utf8_decode('MANEJO SUSTENTABLE DE CULTIVOS DE LADERAS'),1,0);
	$pdf->SetFont('Calibri-Bold','',7);
	$pdf->Cell(9,8,'---',1,0,'C');
	$pdf->SetFont('Calibri-Bold','',7);
	$pdf->Cell(90,8,utf8_decode('MANEJO ORGÁNICO DEL CULTIVO DE MAGUEY'),1,0);
	$pdf->SetFont('Calibri-Bold','',7);
	$pdf->Cell(9,8,'---',1,0,'C');
	$pdf->Ln(7);

	$pdf->Ln(2);*/
	$pdf->SetFont('Calibri-Bold','',10);
	$pdf->SetTextColor(33,33,33);
	if($tipo == "P")
		$pdf->Cell(0,5, utf8_decode('ESPECIFICACIONES DEL MAGUEY'), 0,5,'C');
	else
		$pdf->Cell(0,5, utf8_decode('ESPECIFICACIONES DE LA PLÁNTULA'), 0,5,'C');
	$pdf->Ln(2);
	$pdf->SetTextColor(33,33,33);
	$pdf->SetDrawColor(33,33,33);// color a las lineas de la tabla
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Calibri-Bold','',7.5);
	$pdf->SetLineWidth(0.1);
	$pdf->Cell(53,9,utf8_decode('MAGUEY (NOMBRE COMÚN)'),1,0,'C',1);
	$pdf->Cell(41,9,utf8_decode('AGAVE (NOMBRE CIENTÍFICO)'),1,0,'C',1);
	$pdf->Cell(35,9,utf8_decode('SISTEMA DE PLANTACIÓN'),1,0,'C',1);
	if($tipo == "P") {
		$pdf->Cell(23,9,utf8_decode('EDAD (AÑOS)'),1,0,'C',1);
		$pdf->Cell(24,9,utf8_decode('No. DE PIÑAS'),1,0,'C',1);
	} else
		$pdf->Cell(40,9,utf8_decode('PLÁNTULAS'),1,0,'C',1);

	$pdf->Ln(9);

	$condpla = "";
	if($tipo == "P") 
		$condpla = " AND ep.edad > 4 ";

	$strConsulta = "SELECT p.id_paraje, ep.regmaguey, ep.cantidadini, ep.edad, comun.nombre,especie.genespecie,especie.variante
	FROM $ep ep
	Inner Join comun ON comun.id_comun= ep.id_comun
	Inner Join especie ON comun.id_especie = especie.id_especie
	Inner Join $tabla p ON p.id_paraje=ep.id_paraje
	WHERE  p.id_paraje='$paraje'  $condpla";
	// AND ep.existenciaplantas > 0
	$historial= $conexion->query($strConsulta);
	$numfilas = mysqli_num_rows($historial);
   				$pdf->SetFont('Arial','',9);
				$pdf->SetFillColor(255,255,255);
   			    $pdf->SetTextColor(33,33,33);
				$pdf->SetDrawColor(33,33,33);// color a las lineas de la tabla


	$pdf->SetLineWidth(0);
	$pdf->SetDrawColor(33,33,33);


	/*for ($i=0; $i<$numfilas; $i++)
		{*/
    while($resultado = mysqli_fetch_array($historial)) {
		$pdf->SetLineWidth(0.1);
		$pdf->SetFont('Calibri-Bold','',7.5);
		$pdf->Cell(53,5,utf8_decode($resultado['nombre']),1,0,'C');
		$pdf->SetFont('Calibri-BoldItalic','',8);
		$pdf->Cell(41,5,utf8_decode(ucfirst(strtolower($resultado['genespecie']))),1,0,'C');
		$pdf->SetFont('Calibri-Bold','','7.5');
		$pdf->Cell(35,5,utf8_decode(strtoupper($resultado['regmaguey'])),1,0,'C');
		if($tipo == "P") {
			$pdf->Cell(23,5,utf8_decode(strtoupper($resultado['edad'])),1,0,'C');
			$pdf->SetFont('calibri','');
			$pdf->Cell(24,5,'',1,0,'C');
		} else
			$pdf->Cell(40,5,'',1,0,'C');
		$pdf->Ln();

	}


	//}
	$pdf->Ln(10);
					//RECTANGULO EMPIEZA VERDE
	$pdf->SetLineWidth(2.0);
	$pdf->SetDrawColor(23,97,66);
	$pdf->SetFillColor(255,255,255);
	//margen izquierdo, margen superior,margen derecho,margen inferior
	$pdf->Rect(10, 48, 195, 62 + ($numfilas - 1) * 5); /*es el cuarto numero*/
	$saltoFirma = 16;
	//RECTANGULO TERMINADO
	if($numfilas<=5){
		//RECTANGULO EMPIEZA AZUL
		$pdf->SetDrawColor(70,105,176);
		$pdf->SetFillColor(255,255,255);
		$pdf->Rect(10, 126 + ($numfilas - 1) * 5, 195, 62 + ($numfilas - 1) * 5);
	} else {
		$pdf->AddPage();

		$pdf->SetDrawColor(70,105,176);
		$pdf->SetFillColor(255,255,255);
		$pdf->Rect(10, 40, 195, 62 + ($numfilas - 1) * 5); /*es el cuarto numero*/
		$pdf->Ln(25);
		$saltoFirma = 20;
	}

	$pdf->SetFont('Calibri-Bold','',11);
	$pdf->SetTextColor(33,33,33);
	//$pdf->Ln(11);
	$pdf->Cell(0,6,strtoupper('DATOS DEL COMPRADOR'), 0,5, 'C');
	$pdf->SetTextColor(33,33,33);
	$pdf->Ln(8);
	if($tipo == "P") {
		$pdf->SetFont('Calibri-Bold','',10);
		$pdf->Cell(57,7,utf8_decode('N° DE CERTIFICADO DE PRODUCTOR: '),0,0);
		$pdf->SetFont('Calibri','',9);
		$pdf->Cell(119,7,'______________________________',0,5);
	}
	$pdf->Ln(3);
	$pdf->SetFont('Calibri-Bold','',10);
	if($tipo == "P") 
		$pdf->Cell(57,7,utf8_decode('FECHA DE INGRESO A FÁBRICA: '),0,0);
	else
		$pdf->Cell(57,7,utf8_decode('FECHA DE RECEPCIÓN: '),0,0);
	$pdf->SetFont('Calibri','',9);
	$pdf->Cell(119,7,'______________________________',0,5);
	$pdf->Ln(1);
	$pdf->SetFont('Calibri-Bold','',10);
	$pdf->Cell(43,7,'No. DE CONTROL: ',0,0);
	$pdf->SetFont('Calibri','',9);
	$pdf->Cell(25,7,'_____________',0,0,'C');
	$pdf->SetFont('Calibri-Bold','',10);
	$pdf->Cell(18,7,'NOMBRE: ',0,0);
	$pdf->SetFont('Calibri','',9);
	$pdf->Cell(91,7,'___________________________________________________',0,0,'C');
	$pdf->Ln(5);
	$pdf->SetFont('Calibri-Bold','',10);
	$pdf->Cell(45,7,utf8_decode('DOMICILIO DE ENTREGA: '),0,0);
	$pdf->SetFont('Calibri','',9);
	$pdf->Cell(131,7,'__________________________________________________________________________',0,5,'C');


	// la consulta para datos del predio
	$Consulta = "SELECT localidades.localidad,municipios.nombre as nombrem,estados.nombre as nombree,
	p.paraje,p.referencia,p.lat,p.lng,p.superficie
		from estados
		inner join municipios on municipios.estado=estados.clave
		inner join localidades on localidades.MunicipioID=municipios.id
		inner join $tabla p on p.id_localidad=localidades.id
		where p.id_paraje='$paraje'";

		//$nombrecc = $fila['nombrec'];
		$ubicaciones= $conexion->query($Consulta);
	$dato = mysqli_fetch_array($ubicaciones);

	$pdf->Ln(3);
	$pdf->SetFont('Calibri-Bold','',10);
	$pdf->SetTextColor(33,33,33);
	if($tipo == "P")
		$pdf->Cell(0,5, utf8_decode('ESPECIFICACIONES DEL MAGUEY'), 0,5,'C');
	else
		$pdf->Cell(0,5, utf8_decode('ESPECIFICACIONES DE LA PLÁNTULA'), 0,5,'C');
	
	$pdf->Ln(2);
	$pdf->SetTextColor(33,33,33);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetDrawColor(33,33,33);// color a las lineas de la tabla
	$pdf->SetFont('Calibri-Bold','',7.5);
	$pdf->SetLineWidth(0.1);
	$pdf->Cell(53,9,utf8_decode('MAGUEY (NOMBRE COMÚN)'),1,0,'C',1);
	$pdf->Cell(41,9,utf8_decode('AGAVE (NOMBRE CIENTÍFICO)'),1,0,'C',1);
	$pdf->Cell(35,9,utf8_decode('SISTEMA DE PLANTACIÓN'),1,0,'C',1);
	if($tipo == "P") {
		$pdf->Cell(23,9,utf8_decode('kg. DE MAGUEY'),1,0,'C',1);
		$pdf->Cell(24,9,utf8_decode('% ART'),1,0,'C',1);
	} else 
		$pdf->Cell(40,9,utf8_decode('PLÁNTULAS'),1,0,'C',1);
	


	$pdf->Ln(9);
	$condpla = "";
	if($tipo == "P") 
		$condpla = " AND ep.edad > 4 ";
	$strConsulta = "SELECT p.id_paraje, ep.regmaguey, ep.cantidadini, ep.edad, comun.nombre,especie.genespecie,especie.variante
	FROM $ep ep
	Inner Join comun ON comun.id_comun= ep.id_comun
	Inner Join especie ON comun.id_especie = especie.id_especie
	Inner Join $tabla p ON p.id_paraje=ep.id_paraje
	WHERE  p.id_paraje='$paraje' $condpla";
	// AND ep.existenciaplantas > 0
	$historial= $conexion->query($strConsulta);
	$numfilas = mysqli_num_rows($historial);
	
				$pdf->SetFont('Arial','',9);
				$pdf->SetFillColor(255,255,255);
   			    $pdf->SetTextColor(33,33,33);
				$pdf->SetDrawColor(33,33,33);// color a las lineas de la tabla

	for ($i=0; $i<$numfilas; $i++)
		{

			while($resultado = mysqli_fetch_array($historial))
			{


				$pdf->SetLineWidth(0.1);
				$pdf->SetFont('Calibri-Bold','',7.5);
				$pdf->Cell(53,5,utf8_decode($resultado['nombre']),1,0,'C');
				 $pdf->SetFont('Calibri-BoldItalic','',8);
				$pdf->Cell(41,5,utf8_decode(ucfirst(strtolower($resultado['genespecie']))),1,0,'C');
				$pdf->SetFont('Calibri-Bold','',7.5);
				 $pdf->Cell(35,5,utf8_decode(strtoupper($resultado['regmaguey'])),1,0,'C');
				if($tipo == "P") {
					$pdf->Cell(23,5,'',1,0,'C');
					$pdf->Cell(24,5,'',1,0,'C');
				} else
					$pdf->Cell(40,5,'',1,0,'C');
				 $pdf->Ln();


			}

		}


					$pdf->Ln(25);

			$pdf->SetFont('Calibri-Bold','',11);
			 $pdf->SetTextColor(33,33,33);


			$pdf->cell(88,5,utf8_decode('___________________________________'),0,0,'C');
			$pdf->cell(88,5,utf8_decode('___________________________________'),0,0,'C');
			$pdf->Ln(5);
			$pdf->SetFont('Calibri-Bold','',10);
			$pdf->cell(88,5,utf8_decode('FIRMA DEL VENDEDOR'),0,0,'C');
			$pdf->cell(88,5,utf8_decode('FIRMA DEL COMPRADOR'),0,0,'C');


			}
   ob_end_clean();
   //$pdf->Output("Extraccion".$paraje.".pdf",'D');

   $conexion->close();

   $pdf->Output($destino,'F');
   $pdf->Output($nuevoNombre,'D');
?>
