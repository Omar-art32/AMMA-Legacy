<?php
declare(strict_types=1);

include_once("Polyline.php");
require_once __DIR__ . '/../../vendor/autoload.php';
//include('../php/registro/conexion.php');
//include('../php/registro/conexion_remota.php');
include("../../common/conexion.php");
$conexion->set_charset("utf8");
//$conexion_remota->set_charset("utf8");
header("Content-Type: text/html; charset=iso-8859-1 ");




/**
 *  1) compat_utf8_decode() esta OBSOLETA desde PHP 8.2 (se elimina en PHP 9).
 *     compat_utf8_decode() reproduce EXACTAMENTE el mismo resultado
 *     (conversion UTF-8 -> ISO-8859-1) usando mb_convert_encoding(),
 *     sin generar el aviso "Deprecated".
 *
 *  2) compat_strftime() esta OBSOLETA desde PHP 8.1 (se elimina en PHP 9).
 *     compat_strftime() reproduce el mismo formato de fecha en espanol
 *     que usaba este reporte ("%d-%b-%Y" y "%Y"), sin depender del
 *     locale del sistema operativo (setlocale ya no es necesario, pero
 *     se deja la llamada original para no modificar la logica existente).
 *
 *  Ambas funciones se definen solo si no existen, para poder incluir
 *  este archivo mas de una vez sin provocar errores de redeclaracion.
 */
if (!function_exists('compat_utf8_decode')) {
	function compat_utf8_decode(?string $string): string
	{
		// Mismo comportamiento que el nativo compat_utf8_decode(): UTF-8 -> ISO-8859-1
		return mb_convert_encoding((string) $string, 'ISO-8859-1', 'UTF-8');
	}
}

if (!function_exists('compat_strftime')) {
	function compat_strftime(string $format, $timestamp = null): string
	{
		static $meses = array('ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic');
		$timestamp = ($timestamp === null) ? time() : (int) $timestamp;
		$dt = new DateTime('@' . $timestamp);
		$dt->setTimezone(new DateTimeZone(date_default_timezone_get()));
		switch ($format) {
			case '%d-%b-%Y':
				// Igual que el formato usado por strftime en este reporte
				return sprintf('%02d-%s-%d', (int) $dt->format('d'), $meses[(int) $dt->format('n') - 1], (int) $dt->format('Y'));
			case '%Y':
				return $dt->format('Y');
			default:
				return $dt->format('Y-m-d');
		}
	}
}

#[AllowDynamicProperties] // Necesario en PHP 8.2+ porque esta clase asigna propiedades dinamicas que FPDF no declara
class PDF extends FPDF
{
var $widths;
var $aligns;

	function __construct($orientation='P',$unit='mm',$format='A4') {                  
		parent::__construct($orientation,$unit,$format);

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
		$fontDir = __DIR__ . '/../../vendor/setasign/fpdf/makefont/';
		$this->AddFont('Calibri', '', 'CalibriRegular.json', $fontDir);
		$this->AddFont('Calibri-Bold', '', 'calibri-bold.json', $fontDir);
		$this->AddFont('Calibri-BoldItalic', '', 'calibri-bold-italic.json', $fontDir);
		$this->AddFont('Calibri-Italic', '', 'calibri-italic.json', $fontDir);
		$this->AddFont('Calibri-Light', '', 'calibri-light.json', $fontDir);
		$this->AddFont('Calibri-LightItalic', '', 'calibri-light-italic.json', $fontDir);
		$this->fontlist=array("Calibri","Times","times","courier","helvetica","symbol");
		$this->issetfont=false;
		$this->issetcolor=false;
		$this->SetAutoPageBreak( 1 , 30);
		$this->SetMargins(25,15,15);
		$this->SetAutoPageBreak(false,40);
	}


function SetWidths($w)
{
	//Set the array of column widths
	$this->widths=$w;
}

function SetAligns($a)
{
	//Set the array of column alignments
	$this->aligns=$a;
}


function Row($data)
{
//Calculate the height of the row
$nb=0;
for($i=0;$i<count($data);$i++)
$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
$h=5*$nb;
//Issue a page break first if needed
$this->CheckPageBreak($h);
//Draw the cells of the row
for($i=0;$i<count($data);$i++)
{
$w=$this->widths[$i];
$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
//Save the current position
$x=$this->GetX();
$y=$this->GetY();
//Draw the border

$this->Rect($x,$y,$w,$h);
$this->MultiCell($w,5,$data[$i],0,$a,'true');
//Put the position to the right of the cell
$this->SetXY($x+$w,$y);
}
//Go to the next line
$this->Ln($h);
}

function CheckPageBreak($h)
{
	//If the height h would cause an overflow, add a new page immediately
	if($this->GetY()+$h>$this->PageBreakTrigger)
		$this->AddPage($this->CurOrientation);
}
// CREAMOS ESTA FUNCION
function VariasLineas($cadena, $cantidad)
{
$this->Cell(176,0,'','B');
while (!(strlen($cadena)==''))
{
    $subcadena = substr($cadena, 0, $cantidad);
    $this->Cell(176,5,$subcadena,'LR',0,'L');
    $cadena= substr($cadena,$cantidad);
	$this->Ln();

}
$this->Cell(176,0,'','T');
}
//TERMINAMOS LA FUNCION

function NbLines($w,$txt)
{
	//Computes the number of lines a MultiCell of width w will take
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
}

function Header() {
	$img_file = '../../images/bg_amma_v.jpeg';
    $this->Image($img_file, 0, 0, $this->w, $this->h + 9);
}




function Footer()
{
	//include('../php/registro/conexion.php');
	include("../../common/conexion.php");
	$paraje= $_GET['id'];
	$strConsulta="select tipo from paraje_vivero where id_paraje='$paraje'";
	$parajes= $conexion->query($strConsulta);
	$fila = mysqli_fetch_array($parajes);

	//$this->SetFont('Helvetica','BI',7);
	$this->SetFont('Calibri','',7);
	if($fila['tipo']=='1'){
		$this->AliasNbPages();
   		$this->SetY(-36);
    
		//$this->SetY(-7);
		$this->Ln(-2);
		$this->SetFont('Calibri-Bold','',7);
		$this->Cell(0,5, compat_utf8_decode('ESTE PREDIO Ó VIVERO SE ENCUENTRA DENTRO DEL TERRITORIO PROTEGIDO POR LA DENOMINACIÓN DE ORIGEN MEZCAL, PUBLICADO EN EL DIARIO OFICIAL DE LA FEDERACIÓN '),0,5,'C');
		   $this->Ln(-2);
		$this->Cell(0,5, compat_utf8_decode('EL 28 DE NOVIEMBRE DE 1994, ASÍ COMO SUS MODIFICACIONES SUBSECUENTES.'),0,5,'C');
		//$pdf->MultiCell(185,8,compat_utf8_decode(strtoupper($filaClientes['clienten'])),0, 'C');
		//$this->SetY(-12);
		$this->Ln(2);
		$this->SetFont('Calibri','',7);
       $this->Cell(0,5, compat_utf8_decode('ESTE DOCUMENTO NO ES UN INSTRUMENTO LEGAL, ÚNICAMENTE AMPARA EL REGISTRO DE LA PLANTACIÓN DEL MAGUEY DENTRO'),0,5,'C');
	   $this->Ln(-2);
		$this->Cell(0,5, compat_utf8_decode('DEL PREDIO Ó VIVERO PARA GARANTIZAR LA TRAZABILIDAD DE LA MATERIA PRIMA UTILIZADA EN LA PRODUCCIÓN DE MEZCAL.'),0,5,'C');
		$this->Ln(1);
		$this->Cell(0,5,compat_utf8_decode('Página').$this->PageNo().'/{nb}',0,5,'L');
    //$this->SetY(-21);
		$this->Ln(-4);
		$this->Cell(0,5,Utf8_decode('FOR-UM-02/02.'),0,5,'R');
		//
		$this->Ln(-1);
		$this->Cell(0,5,compat_utf8_decode('Av. Universidad N° 312-A, Fracc. Trinidad de las Huertas, Oaxaca de Juárez, C. P. 68120, Oaxaca, México'),0,5,'C');
		$this->Ln(-1);
		$this->Cell(0,5,compat_utf8_decode('www.amma.org.mx   maguey@amma.org.mx  Teléfonos: 951 672 9399, 951 672 9474'),0,5,'C');
	} else {
		/*$this->Cell(0,5, compat_utf8_decode('FM-02/00.'),0,5,'R');
		$this->Cell(0,5, compat_utf8_decode(''),0,5,'C');
		$this->Cell(0,5, compat_utf8_decode('ESTE DOCUMENTO NO ES UN INSTRUMENTO LEGAL, ÚNICAMENTE AMPARA EL REGISTRO DE LA PLANTACIÓN DEL MAGUEY DENTRO DEL VIVERO.'),0,5);*/
		$this->AliasNbPages();
   		$this->SetY(-36);
    
		//$this->SetY(-7);
		$this->Ln(-2);
		$this->SetFont('Calibri-Bold','',7);
		$this->Cell(0,5, compat_utf8_decode('ESTE PREDIO Ó VIVERO SE ENCUENTRA DENTRO DEL TERRITORIO PROTEGIDO POR LA DENOMINACIÓN DE ORIGEN MEZCAL, PUBLICADO EN EL DIARIO OFICIAL DE LA FEDERACIÓN '),0,5,'C');
		   $this->Ln(-2);
		$this->Cell(0,5, compat_utf8_decode('EL 28 DE NOVIEMBRE DE 1994, ASÍ COMO SUS MODIFICACIONES SUBSECUENTES.'),0,5,'C');
		//$pdf->MultiCell(185,8,compat_utf8_decode(strtoupper($filaClientes['clienten'])),0, 'C');
		//$this->SetY(-12);
		$this->Ln(2);
		$this->SetFont('Calibri','',7);
       $this->Cell(0,5, compat_utf8_decode('ESTE DOCUMENTO NO ES UN INSTRUMENTO LEGAL, ÚNICAMENTE AMPARA EL REGISTRO DE LA PLANTACIÓN DEL MAGUEY DENTRO'),0,5,'C');
	   $this->Ln(-2);
		$this->Cell(0,5, compat_utf8_decode('DEL PREDIO Ó VIVERO PARA GARANTIZAR LA TRAZABILIDAD DE LA MATERIA PRIMA UTILIZADA EN LA PRODUCCIÓN DE MEZCAL.'),0,5,'C');
		$this->Ln(1);
		$this->Cell(0,5,compat_utf8_decode('Página').$this->PageNo().'/{nb}',0,5,'L');
    //$this->SetY(-21);
		$this->Ln(-4);
		$this->Cell(0,5,Utf8_decode('FOR-UM-02/02.'),0,5,'R');
		//
		$this->Ln(-1);
		$this->Cell(0,5,compat_utf8_decode('Av. Universidad N° 312-A, Fracc. Trinidad de las Huertas, Oaxaca de Juárez, C. P. 68120, Oaxaca, México'),0,5,'C');
		$this->Ln(-1);
		$this->Cell(0,5,compat_utf8_decode('www.amma.org.mx   maguey@amma.org.mx  Teléfonos: 951 672 9399, 951 672 9474'),0,5,'C');
	}
}

}

	$paraje = $_GET['id'];

	$random_Number      = rand(0, 9999999999);
    $nuevoNombre        = "RegistroVivero_".$paraje."_".$random_Number.".pdf";
    $destino =          "pdfConstanciaVivero" . DIRECTORY_SEPARATOR . $nuevoNombre;

	if($paraje != "P1"){
		$strUpdate="UPDATE paraje_vivero SET constancia_vivero = '$nuevoNombre' WHERE id_paraje='$paraje'";
		$parajesUpdate= $conexion->query($strUpdate);
	}

	$consulta="SELECT SUBSTR(id_paraje,2,length(id_paraje)) id FROM paraje_vivero WHERE id_paraje = '$paraje' ";
    $consultaid = $conexion->query($consulta);
    if($consultaid==false) throw new Exception("Error al obtener id paraje");
    if ($consultaid->num_rows > 0){
        while ($row = $consultaid->fetch_array(MYSQLI_ASSOC))
			$idtp = 'P'.str_pad($row['id'], 4, "0", STR_PAD_LEFT);
    }

	$strConsulta="SELECT Date_format(cv.fecha,'%y') as anio,nombrep,pv.id_paraje,pv.id_cliente,regmaguey,LPAD(cv.id_constancia,4,'0') as constancia,pv.tipo,
	CONCAT('V',LPAD(pv.id,4,'0')) as parajes,
	cv.fecha as fecha1,date_add(cv.fecha, INTERVAL 2 YEAR) as fecha2
	   from  paraje_vivero pv 
	   inner join constancias_vivero cv on (cv.id_paraje=pv.id_paraje COLLATE utf8_general_ci)
	   inner join existenciaplanta_vivero epv on pv.id_paraje=epv.id_paraje 
	   where pv.id_paraje='$paraje'";




	$parajes= $conexion->query($strConsulta);
	$fila = mysqli_fetch_array($parajes);



	$cliente=$fila['id_cliente'];
	$strCliente = "SELECT character_length(CONCAT(domicilio.calle, ' #', domicilio.noexterior,', ',domicilio.colonia,', ',municipios.nombre,', ',estados.nombre)) as contador,CONCAT(domicilio.calle, ' #', domicilio.noexterior,', ',domicilio.colonia,', ',municipios.nombre,', ',estados.nombre) as domicilio,
	clientes.no_cliente,UPPER(clientes.nombre) as clienten,domicilio.calle,domicilio.noexterior,domicilio.nointerior,domicilio.colonia,domicilio.telefono, ce.correo
				  from clientes
				  inner join  domicilio on domicilio.no_cliente=clientes.no_cliente
				  inner join municipios ON domicilio.municipio = municipios.id
				  inner join estados ON municipios.estado = estados.clave
                  INNER JOIN clientes_correos cc ON clientes.no_cliente = cc.cliente 
                  INNER JOIN correos_electronicos ce ON cc.correo = ce.id AND ce.principal = '1'
				  where clientes.no_cliente='$cliente' and domicilio.estatus=1 
				  ORDER BY domicilio.idDomicilio  LIMIT 1";

	$clientes= $conexion->query($strCliente);
	$filaClientes = mysqli_fetch_array($clientes);


	if($fila['tipo']=='1'){

	$pdf=new PDF('P','mm','Letter');
	$pdf->Open();
	$pdf->AddPage();
	$pdf->SetMargins(20,20,20);
	// aqui empieza
	setlocale(LC_ALL,"es_ES@euro","Es_ES","esp");
	$d = $fila['fecha1'];
	$fecha = compat_strftime("%d-%b-%Y", strtotime($d));
	$fecha1 = ucfirst(strtolower($fecha));
	$fecha1 = str_replace(".", "", $fecha1);
	//fecha1
	// $d = $fila['fecha2'];
	// $fechaa = compat_strftime("%Y", strtotime($d));
	// $fecha2 = ucfirst($fechaa);
	//fecha nueva
	$fechaD = date('Y');
    $nuevafecha = strtotime ( '+1 year' , strtotime ( $fechaD ) ) ;
    $fecha2 = date ( 'Y' , $nuevafecha );
	$fila['parajes'] = $idtp;
	//termina fecha
	$pdf->Ln(4);
	$pdf->SetXY(26,26);
	$pdf->SetFont('Calibri-Bold','',23);
	$pdf->Cell(0,8, compat_utf8_decode('REGISTRO DE MAGUEY'),0,5, 'C');
	$pdf->SetFont('Calibri-Bold','',13);
	$pdf->Ln(14);
	$pdf->Cell(0,3, compat_utf8_decode(strtoupper('N° DE CONTROL: ')).$filaClientes['no_cliente'],0,5, 'C');
	$pdf->SetTextColor(238,55,60);
	$pdf->SetFont('Calibri-Bold','',14);
	$pdf->Text(175,28,strtoupper($fila['constancia']).$fila['parajes'].$fila['anio'],0,5,'C');
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('Calibri-Bold','',8);
	$pdf->Text(170,34,strtoupper('No. de Predio: '),0,5,'C');
	$pdf->Text(165,38,compat_utf8_decode('FECHA DE EMISIÓN: '),0,5,'C');
	$pdf->Text(178,42,strtoupper('Vigencia:'),0,5,'C');
	$pdf->SetFont('Calibri-Bold','',9);
	$pdf->Text(194,34,$fila['parajes'],0,5,'C');
	$pdf->Text(194,38,$fecha1,0,5,'C');
	$pdf->Text(194,42,'INDEFINIDA',0,5,'C');

		$pdf->Ln(3);

	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('Calibri-Bold','',12);
	$largoTel = strlen($filaClientes['telefono']);

	$pos = strpos($filaClientes['correo'], "egistros.uac");
	$filaClientes['correo'] = ($pos>0) ? ' ': $filaClientes['correo'];
    $largoCorreo = strlen($filaClientes['correo']);
    
	$pdf->MultiCell(185,8,compat_utf8_decode($filaClientes['clienten']),0, 'C');

	//$filaClientes['contador'] += 94;
	//$filaClientes['domicilio'] .= $filaClientes['domicilio'];
	// DOMICILIO
	$vecesSalto = 1;
	if($filaClientes['contador'] > 94){
		$vecesSalto = $filaClientes['contador'] / 90;
		$vecesSalto = ceil($vecesSalto);
	}
	$tamSalto = $vecesSalto * 5;
	$pdf->Ln(6);
	$pdf->SetFont('Calibri-Bold','',10);
	$pdf->Cell(45,$tamSalto,'DOMICILIO FISCAL:',1, 0,'C');
	$pdf->SetX(65);
	$pdf->SetFont('Calibri-Bold','',9);
	$pdf->MultiCell(131,5,ucwords(strtolower(compat_utf8_decode($filaClientes['domicilio']))),1);
		
	//$largoTel += ($largoTel*2);
	//$largoCorreo += $largoCorreo;
	//$filaClientes['telefono'] .= $filaClientes['telefono'].$filaClientes['telefono'];
	//$filaClientes['correo'] .= $filaClientes['correo'];
	//$filaClientes['telefono'] = substr($filaClientes['telefono'], 0,20);
	// TELÉFONO Y CORREO
	$vecesSalto = 1;
	if($largoTel > 17){ //TELÉFONO
		$vecesSalto = $largoTel / 17;
		$vecesSalto = ceil($vecesSalto);
	}
	$tamSalto1 = $vecesSalto * 5;
	//CORREO
	//$vecesSalto = 1;
	if($largoCorreo > 37){ 
		$vecesSalto = $largoCorreo / 37;
		$vecesSalto = ceil($vecesSalto);
	}
	$tamSalto2 = $vecesSalto * 5;
	if($tamSalto1 > $tamSalto2)
		$tamSalto = $tamSalto1;
	elseif($tamSalto2 > $tamSalto1)
		$tamSalto = $tamSalto2;
		
	$pdf->SetFont('Calibri-Bold','',10);
	$pdf->Cell(45,$tamSalto,compat_utf8_decode('TELÉFONO: '),1,0,'C');
	$pdf->SetX(65);
	$pdf->SetFont('Calibri-Bold','',9);
	if($largoTel < 18 && $largoCorreo > 37)
		$pdf->Cell(30,$tamSalto,$filaClientes['telefono'],1,0,'C');
	else {
		$pdf->MultiCell(30,5,$filaClientes['telefono'],1,'C');
		$pdf->Ln(-$tamSalto);
	}
	$pdf->SetX(95);
	$pdf->SetFont('Calibri-Bold','',10);
	$pdf->Cell(45,$tamSalto,compat_utf8_decode('CORREO ELECTRÓNICO: '),1,0,'C');
	$pdf->SetX(140);
	$pdf->SetFont('Calibri-Bold','',9);
	if($filaClientes['correo']=="")
		$pdf->Cell(56,$tamSalto,compat_utf8_decode('---'),1,0,'C');
	else {
		if($largoTel > 17 && $largoCorreo < 38)
			$pdf->Cell(56,$tamSalto,$filaClientes['correo']."H1",1,0,'C');
		else 
			$pdf->MultiCell(56,5,compat_utf8_decode($filaClientes['correo'])."H!",1,'C');
	}
	
    

	// la consulta para datos del paraje
	$Consulta = "SELECT localidades.localidad,municipios.nombre as nombrem,estados.nombre as nombree,paraje.paraje,paraje.referencia,paraje.lat,paraje.lng,paraje.superficie
		from estados
		inner join municipios on municipios.estado=estados.clave
		inner join localidades on localidades.MunicipioID=municipios.id
		inner join paraje on paraje.id_localidad=localidades.id
		where paraje.id_paraje='$paraje'";


	$ubicaciones= $conexion->query($Consulta);
	$dato = mysqli_fetch_array($ubicaciones);
	/*if($fila['nombrep']==$filaClientes['clienten'] or $fila['nombrep']==''){ MANITO ANALI*/
	//if(true){

		//$pdf->Ln(20);

		$pdf->SetFont('Calibri-Bold','',10);
		$pdf->Cell(45,16, compat_utf8_decode('UBICACIÓN DEL PREDIO'), 1,0,'C');
		$pdf->SetFont('Calibri-Bold','',10);
		$pdf->Cell(65,8,ucwords(strtolower('')),1,0,'C');
		$pdf->Cell(66,8,ucwords(strtolower('')),1,0,'C');

		$pdf->Ln(0);
		$pdf->SetX(65);
		$pdf->SetFont('Calibri-Bold','',9);
		$pdf->Cell(65,5,compat_utf8_decode($dato['paraje']),0,0,'C');
		$pdf->Cell(66,5,ucwords(strtolower(compat_utf8_decode($dato['localidad']))),0,0,'C');

		$pdf->Ln(0);
		$pdf->SetX(65);
		$pdf->SetFont('Calibri-Bold','',7);
		$pdf->Cell(65,12,strtoupper('Predio'),0,0,'C');
		$pdf->Cell(66,12,strtoupper ('localidad'),0,0,'C');
		//Termina paraje y localidad
		$pdf->Ln(8);
		$pdf->SetX(65);

		// aqui empieza el municipio y el estado
		// estado y municipio
		$pdf->Ln(0);
		$pdf->SetX(65);
		$pdf->SetFont('calibri','',10);
		$pdf->Cell(65,8,ucwords(strtolower('')),1,0,'C');
		$pdf->Cell(66,8,ucwords(strtolower('')),1,0,'C');


		$pdf->Ln(0);
		$pdf->SetX(65);
		$pdf->SetFont('Calibri-Bold','',9);
		$pdf->Cell(65,5,ucwords(strtolower(compat_utf8_decode($dato['nombrem']))),0,0,'C');
		$pdf->Cell(66,5,ucwords(strtolower(compat_utf8_decode($dato['nombree']))),0,0,'C');

		$pdf->Ln(0);
		$pdf->SetX(65);
		$pdf->SetFont('Calibri-Bold','',7);
		$pdf->Cell(65,12,'MUNICIPIO',0,0,'C');
		$pdf->Cell(66,12,'ESTADO',0,0,'C');
		//termina estado y municipio

		// no tiene referencia
		$pdf->SetX(20);
			$pdf->Ln(8);
	$pdf->SetFont('Calibri-Bold','',10);
	$pdf->Cell(45,9, compat_utf8_decode('SUPERFICIE'), 1,0, 'C');
	 $pdf->SetFont('Calibri-Bold','',9);
	 $pdf->Cell(30,9,$dato['superficie'],1,0,'C');
	 $pdf->Ln(0);
	 $pdf->SetX(64);
	 $pdf->SetFont('Calibri-Bold','',7);
	 $pdf->Cell(31,14,'HECTÁREAS',0,0,'C');
	 $pdf->Cell(45,9,'',1,0,'C');
	$pdf->SetFont('Calibri-Bold','',10);
	 $pdf->Ln(1);
	 $pdf->SetX(102);
	 $pdf->Cell(31,4.5,compat_utf8_decode('COORDENADAS'), 0,0,'C');
	 $pdf->Ln(4);
	 $pdf->SetX(101);
	 $pdf->Cell(31,4.5,compat_utf8_decode('GEOGRÁFICAS'), 0,0,'C');
	 $pdf->Ln(-5);
	 $pdf->SetX(140);
	 $pdf->SetFont('Calibri-Bold','',9);
	 $pdf->Cell(56,9,''.'    '.'',1,0,'C');
	 $pdf->SetX(140);
	 $pdf->Cell(56,7,$dato['lat'].'        '.$dato['lng'],0,0,'C');
	 $pdf->Ln(0);
	 $pdf->SetX(129);
	 $pdf->SetFont('Calibri-Bold','',7);
	 $pdf->Cell(56,13,'LATITUD',0,0,'C');
	 $pdf->SetX(152);
	 $pdf->SetFont('Calibri-Bold','',7);
	 $pdf->Cell(53,13,'LONGITUD',0,0,'C');

	 // ARREGLO ATRIBUTOS
         $arrtits = array(
            /*0=>["txt1"=>"Manejo Integrado de Plagas y Enfermedades", "txt2"=>"Manejo Integrado de Plagas y Enfermedades", "ids"=>"0"],
            1=>["txt1"=>"Preservación de la Diversidad Biológica de Magueyes", "txt2"=>"", "ids"=>"1,2"],
            2=>["txt1"=>"Conservación de Suelo y Agua", "txt2"=>"", "ids"=>"3,4,5"],
            3=>["txt1"=>"Manejo Orgánico", "txt2"=>"", "ids"=>"6,7"],
            4=>["txt1"=>"Aprovechamiento Controlado de Magueyes Silvestres ", "txt2"=>"", "ids"=>"8"]*/
            1=>["txt1"=>"Preservación de la Diversidad Biológica de Magueyes", "txt2"=>"", "ids"=>"1,2"],
            2=>["txt1"=>"Conservación de Suelo y Agua", "txt2"=>"", "ids"=>"3,4,5"],
            3=>["txt1"=>"Manejo Orgánico", "txt2"=>"", "ids"=>"6,7"],
            4=>["txt1"=>"Aprovechamiento Controlado de Magueyes Silvestres ", "txt2"=>"", "ids"=>"8"],
            0=>["txt1"=>"Manejo Integrado de Plagas y Enfermedades", "txt2"=>"Manejo Integrado de Plagas y Enfermedades", "ids"=>"0"]
            /*0=>["txt1"=>"Manejo Integrado de Plagas y Enfermedades", "txt2"=>"Manejo Integrado de Plagas y Enfermedades"],
            1=>["txt1"=>"Especies Presentes en el predio", "txt2"=>"Especies Presentes en el predio"],
            2=>["txt1"=>"Magueyes en Floración", "txt2"=>"Magueyes en Floración"],
            3=>["txt1"=>"Curvas a Nivel", "txt2"=>"Curvas a Nivel"],
            4=>["txt1"=>"No remosión del suelo", "txt2"=>"No remosión del suelo"],
            5=>["txt1"=>"Suelo con Cobertura", "txt2"=>"Suelo con Cobertura"],
            6=>["txt1"=>"Seguridad y Protocolos", "txt2"=>"Seguridad y Protocolos"],
            7=>["txt1"=>"Manejo Orgánico", "txt2"=>"Manejo Orgánico"],
            8=>["txt1"=>"Extracción de Magueyes Maduros", "txt2"=>"Extracción de Magueyes Maduros"]*/ );

        // Aqui empieza la tabla de atributos de la tierra
	 $pdf->SetX(20);
	$pdf->Ln(13);
	$pdf->SetFont('Calibri-Bold','',15);
	$pdf->SetTextColor(0,0,0);
	

	 // Aqui termina
	$pdf->SetX(20);
	$pdf->Ln(7);
	$pdf->SetFont('Calibri-Bold','',15);
	$pdf->SetTextColor(0,0,0);
	$pdf->Cell(0,12, compat_utf8_decode('CARACTERÍSTICAS DEL MAGUEY'), 0,5, 'C');
        $pdf->Ln(3);
	$pdf->SetFillColor(85,107,47);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFont('Calibri-Bold','',8);
	$pdf->Cell(92,5,compat_utf8_decode('TIPO DE MAGUEY'),1,0,'C',1);
	$pdf->Cell(25,5,'No. DE PLANTAS',1,0,'C',1);
	$pdf->Cell(21,5,compat_utf8_decode('EDAD (AÑOS)'),1,0,'C',1);
	$pdf->Cell(38,5,compat_utf8_decode('SISTEMA DE PLANTACIÓN'),1,0,'C',1);
	$pdf->Ln(5);

	$strConsulta = "SELECT paraje.id_paraje, epv.regmaguey, epv.cantidadini, epv.edad, comun.nombre,especie.genespecie,especie.variante
	FROM existenciaplanta_vivero epv
	Inner Join comun ON comun.id_comun= epv.id_comun
	Inner Join especie ON comun.id_especie = especie.id_especie
	Inner Join paraje ON paraje.id_paraje=epv.id_paraje 
	WHERE  paraje.id_paraje='$paraje'  ";
	// and epv.existenciaplantas != 0
	$historial= $conexion->query($strConsulta);
	$numfilas = mysqli_num_rows($historial);
        $pdf->SetFont('Arial','B',9);
        $pdf->SetFillColor(255,255,255);
        $pdf->SetTextColor(0,0,0);
	//$pdf->Cell(52,5,$numfilas,1,0,'C');
        //$pdf->Ln();
        //$numfilasi = 12;
        $n = 0;
        //for ($n=0; $n<16; $n++) {
            for ($i=0; $i<$numfilas; $i++) {


                while($resultado = mysqli_fetch_array($historial)) {
                    if($numfilas > 10 && $n == 10) {
                        $pdf->AddPage();
                        $pdf->SetX(20);
                        //$pdf->Ln(5);
                        $pdf->SetFont('Calibri-Bold','',15);
                        $pdf->SetTextColor(0,0,0);
                        $pdf->Cell(0,12, compat_utf8_decode('CARACTERÍSTICAS DEL MAGUEY'), 0,5, 'C');
                        $pdf->Ln(3);
                        $pdf->SetFillColor(85,107,47);
                        $pdf->SetTextColor(255,255,255);
                        $pdf->SetFont('Calibri-Bold','',8);
                        $pdf->Cell(92,5,compat_utf8_decode('TIPO DE MAGUEY'),1,0,'C',1);
                        $pdf->Cell(25,5,'No. DE PLANTAS',1,0,'C',1);
                        $pdf->Cell(21,5,compat_utf8_decode('EDAD (AÑOS)'),1,0,'C',1);
                        $pdf->Cell(38,5,compat_utf8_decode('SISTEMA DE PLANTACIÓN'),1,0,'C',1);
                        $pdf->Ln(5);
                        $pdf->SetFont('Arial','B',9);
                        $pdf->SetFillColor(255,255,255);
                        $pdf->SetTextColor(0,0,0);
                    }
                    $pdf->SetFont('Calibri-Bold','',8);
                    $pdf->Cell(52,5,compat_utf8_decode(strtoupper($resultado['nombre'])),1,0,'C');
                    $pdf->SetFont('Calibri-BoldItalic','');
                    $pdf->Cell(40,5, compat_utf8_decode(ucfirst(strtolower($resultado['genespecie']))),1,0,'C');
                    $pdf->SetFont('Calibri-Bold','');
                    $pdf->Cell(25,5,$resultado['cantidadini'],1,0,'C');
                    $pdf->Cell(21,5,$resultado['edad'],1,0,'C');
                    $pdf->SetFont('Calibri-Bold','');
                    $pdf->Cell(38,5,strtoupper($resultado['regmaguey']),1,0,'C');
                    $pdf->Ln();
                    $n++;
                }
            }

        {
            $pdf->Ln(15);
            $pdf->SetFont('Calibri-Bold','',10);
            $pdf->SetX(143);
            //$pdf->Cell( 88, 20, $pdf->Image("images/firmau.png", $pdf->GetX(), $pdf->GetY(), 20.58), 0, 0, 'C', false );
			//$pdf->Cell( 88, 20, $pdf->Image("images/FIRMA_LUCIO.jpg", $pdf->GetX(), $pdf->GetY()+10, 20.58), 0, 0, 'C', false );
            $pdf->Ln(0);
            $pdf->SetX(45);
            //$pdf->Cell( 88, 20, $pdf->Image("images/TELLO.jpg", $pdf->GetX(), $pdf->GetY(),40.78), 0,0, 'C', false );
            $pdf->Ln(18);
            $pdf->cell(88,5,compat_utf8_decode('BIOL. BENJAMIN PICHE VENEGAS'),0,0,'C');
            //$pdf->cell(88,5,'',0,0,'C');
			$nocliente = @$filaClientes['no_cliente'];
			/*$puestooc = "GERENTE DEL ORGANISMO DE CERTIFICACIÓN";
			$nombreoc = "L.I.N.M. LILI ARIADNA CARRASCO MERINO";*/
			$puestooc = "DIRECTOR GENERAL";
			$nombreoc = "MSIG. URIEL TERÁN SANGERMÁN";
			
			$pdf->cell(88,5,compat_utf8_decode($nombreoc),0,0,'C');
            $pdf->Ln(0);
            $pdf->cell(88,5,compat_utf8_decode('_______________________________________'),0,0,'C');
            $pdf->cell(88,5,compat_utf8_decode('_______________________________________'),0,0,'C');
            $pdf->Ln(5);
            $pdf->SetFont('Calibri-Bold','',9);
            $pdf->cell(88,5,compat_utf8_decode(strtoupper('Coordinador de la Unidad de Maguey')),0,0,'C');
            $pdf->cell(88,5,compat_utf8_decode(strtoupper($puestooc)),0,0,'C');
        }


        $pdf->Ln(40);

        $Consulta = "SELECT * FROM paraje WHERE paraje.id_paraje='$paraje'";
        $historial= $conexion->query($Consulta);



			while($resultado = mysqli_fetch_array($historial))


    {
   		$id = $resultado['id_paraje'];
        $coordenada1 = $resultado['lat'];
        $coordenada2 = $resultado['lng'];

    }
	/*Poligono*/
		$Consulta = "SELECT AsBinary(poligono), lat, lng FROM paraje_vivero
		where paraje.id_paraje='$paraje' AND (lat != '' and lng != '') ";
		$parajes= $conexion->query($Consulta);

			if ($parajes) {

		if($row = $parajes->fetch_row()) {

			$clat = $row[1];
			$clng = $row[2];

			
	/*aqui para imprimir estados con Dom*/
	$strConsulta = "SELECT pv.*, estados.ubica as enombreee,estados.nombre 
	from estados 
	inner join municipios on municipios.estado=estados.clave 
	inner join localidades on localidades.MunicipioID=municipios.id 
	inner join paraje_vivero pv on pv.id_localidad=localidades.id where  pv.id_paraje='$paraje'";
	$parajes= $conexion->query($strConsulta);
	//$parajes = mysql_query($strConsulta);
	$fila = mysqli_fetch_array($parajes);
	//Aqui termina

		

			$pdf->AddPage();
			$pdf->SetFont('Calibri-Bold','',20);
			$pdf->Ln(7);
			$pdf->Cell(0, 4, compat_utf8_decode('PREDIO GEORREFERENCIADO'), 0,5, 'C');
			//$pdf->Cell(0, 4, compat_utf8_decode('________________________________'), 0,5, 'C');
			$pdf->Cell(140,120);

		  	//$pdf->Image('estadosDOM/oaxaca.png', 90, 35, 40, 30, "PNG");
		    $pdf->Image($fila['enombreee'], 90, 50, 40, 30, "PNG");
			//$pdf->Image($urlGoogle, 62, 90, 100, 60, "PNG");  // deshabilitado 130622
			//$pdf->Image($urlGooglec, 72, 165, 80, 60, "PNG");	// deshabilitado 130622
			// IMÁGENES DE ARCHIVO
			$img01 = 'imgconstancia/'.$paraje.'_01.jpg';
			$img02 = 'imgconstancia/'.$paraje.'_02.jpg';
			if (file_exists($img01)) 
    			$pdf->Image($img01, 62, 90, 100, 60);  // 140622
    		if (file_exists($img02)) 
				$pdf->Image($img02, 72, 165, 80, 60);	// 140622
			

			//$pdf->Image($urlGoogleg, 15, 135, 185, 120, "PNG");

		}
			}
	}

	//AQUI EMPIEZA EL DE VIVEROS
	else {
			$pdf=new PDF('P','mm','Letter');
	$pdf->Open();
	$pdf->AddPage();
	$pdf->SetMargins(20,20,20);
	// aqui empieza
	setlocale(LC_ALL,"es_ES@euro","Es_ES","esp");
	$d = $fila['fecha1'];
	$fecha = compat_strftime("%d-%b-%Y", strtotime($d));
	$fecha1 = ucfirst(strtolower($fecha));
	//fecha1
	$d = $fila['fecha2'];
	$fechaa = compat_strftime("%Y", strtotime($d));
	$fecha2 = ucfirst($fechaa);
	//termina fecha
	$pdf->Ln(30);
	$pdf->SetXY(26,30);
	$pdf->SetFont('Calibri-Bold','',23);
	$pdf->Cell(0,8, compat_utf8_decode('REGISTRO DE PLANTACIONES'),0,5, 'C');
	$pdf->SetFont('Calibri-Bold','',13);
	$pdf->Rect(35, 43, 10, 8, 'D');
	$pdf->SetFont('Calibri-Bold','',17);
	$pdf->SetXY(38,43);
	$pdf->Cell(5,8, 'X',0,5, 'C');
	$pdf->SetFont('Calibri-Bold','',13);
	$pdf->SetXY(45,43);
	$pdf->Cell(20,8, compat_utf8_decode('VIVERO'),0,5, 'C');
	$pdf->Rect(145, 43, 10, 8, 'D');
	$pdf->SetXY(155,43);
	$pdf->Cell(20,8, compat_utf8_decode('PREDIO'),0,5, 'C');
	$pdf->Ln(5);
	$pdf->Cell(0,3, compat_utf8_decode(strtoupper('No DE CONTROL: ')).$filaClientes['no_cliente'],0,5, 'C');
	$pdf->SetTextColor(238,55,60);
	$pdf->SetFont('Calibri-Bold','',14);
	$pdf->Text(185,28,strtoupper($fila['constancia']).$fila['parajes'].$fila['anio'],0,5,'C');
	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('Calibri-Bold','',8);
	$pdf->Text(185,34,strtoupper('No.: '),0,5,'C');
	$pdf->Text(167,38,compat_utf8_decode('FECHA DE EMISIÓN: '),0,5,'C');
	$pdf->Text(178,42,strtoupper('Vigencia:'),0,5,'C');
	$pdf->SetFont('Calibri-Bold','',9);
	$pdf->Text(194,34,$fila['parajes'],0,5,'C');
	$pdf->Text(194,38,$fecha1,0,5,'C');
	$pdf->Text(194,42,'INDEFINIDA',0,5,'C');

		$pdf->Ln(3);

	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('Calibri-Bold','',15);
	$pdf->MultiCell(0,8,compat_utf8_decode($filaClientes['clienten']),0, 'C');


		if($filaClientes['contador']<=82){
			$pdf->Ln(3);
	$pdf->SetX(65);
	$pdf->SetFont('Calibri-Bold','',9);
	$pdf->MultiCell(131,7,ucwords(strtolower(compat_utf8_decode($filaClientes['domicilio']))),1);
	$pdf->Ln(-7);
	$pdf->SetFont('Calibri-Bold','',10);
	$pdf->MultiCell(45,7,'DOMICILIO FISCAL:',1,'C');

	}else {
		$pdf->Ln(3);
	$pdf->SetX(65);
	$pdf->SetFont('Calibri-Bold','',9);
	$pdf->MultiCell(131,5,ucwords(strtolower(compat_utf8_decode($filaClientes['domicilio']))),1);
	$pdf->Ln(-10);
	$pdf->SetFont('Calibri-Bold','',10);
	$pdf->MultiCell(45,10,'DOMICILIO FISCAL:',1,'C');
	}

	$pdf->Ln(0);
	$pdf->SetFont('Calibri-Bold','',10);
	$pdf->Cell(45,7,compat_utf8_decode('TELÉFONO: '),1,0,'C');
	$pdf->SetFont('Calibri-Bold','',9);
	$pdf->Cell(30,7,$filaClientes['telefono'],1,0,'C');
	$pdf->SetFont('Calibri-Bold','',10);
	$pdf->Cell(45,7,compat_utf8_decode('CORREO ELECTRÓNICO: '),1,0,'C');
	if($filaClientes['correo']==""){
	$pdf->SetFont('Calibri-Bold','',9);
	$pdf->Cell(56,7,compat_utf8_decode('---'),1,0,'C');
	}else{
		$pdf->SetFont('Calibri-Bold','',9);
	$pdf->Cell(56,7,compat_utf8_decode($filaClientes['correo']),1,0,'C');
	}

	// la consulta para datos del paraje
	$Consulta = "SELECT localidades.localidad,municipios.nombre as nombrem,estados.nombre as 		nombree,pv.paraje,pv.referencia,pv.lat,pv.lng,pv.superficie
		from estados
		inner join municipios on municipios.estado=estados.clave
		inner join localidades on localidades.MunicipioID=municipios.id
		inner join paraje_vivero pv on pv.id_localidad=localidades.id
		where pv.id_paraje='$paraje'";


	$ubicaciones= $conexion->query($Consulta);
	$dato = mysqli_fetch_array($ubicaciones);
	if($fila['nombrep']==$filaClientes['clienten'] or $fila['nombrep']==''){

		$pdf->Ln(7);

		$pdf->SetFont('Calibri-Bold','',10);
		$pdf->Cell(45,16, compat_utf8_decode('UBICACIÓN DEL PREDIO'), 1,0,'C');
		$pdf->SetFont('Calibri-Bold','',10);
		$pdf->Cell(65,8,ucwords(strtolower('')),1,0,'C');
		$pdf->Cell(66,8,ucwords(strtolower('')),1,0,'C');

		$pdf->Ln(0);
		$pdf->SetX(65);
		$pdf->SetFont('Calibri-Bold','',9);
		$pdf->Cell(65,5,compat_utf8_decode($dato['paraje']),0,0,'C');
		$pdf->Cell(66,5,ucwords(strtolower(compat_utf8_decode($dato['localidad']))),0,0,'C');

		$pdf->Ln(0);
		$pdf->SetX(65);
		$pdf->SetFont('Calibri-Bold','',7);
		$pdf->Cell(65,12,strtoupper('Predio'),0,0,'C');
		$pdf->Cell(66,12,strtoupper ('localidad'),0,0,'C');
		//Termina paraje y localidad
		$pdf->Ln(8);
		$pdf->SetX(65);

		// aqui empieza el municipio y el estado
		// estado y municipio
		$pdf->Ln(0);
		$pdf->SetX(65);
		$pdf->SetFont('calibri','',10);
		$pdf->Cell(65,8,ucwords(strtolower('')),1,0,'C');
		$pdf->Cell(66,8,ucwords(strtolower('')),1,0,'C');


		$pdf->Ln(0);
		$pdf->SetX(65);
		$pdf->SetFont('Calibri-Bold','',9);
		$pdf->Cell(65,5,ucwords(strtolower(compat_utf8_decode($dato['nombrem']))),0,0,'C');
		$pdf->Cell(66,5,ucwords(strtolower(compat_utf8_decode($dato['nombree']))),0,0,'C');

		$pdf->Ln(0);
		$pdf->SetX(65);
		$pdf->SetFont('Calibri-Bold','',7);
		$pdf->Cell(65,12,'MUNICIPIO',0,0,'C');
		$pdf->Cell(66,12,'ESTADO',0,0,'C');
		//termina estado y municipio


	   // condicion si  hay productor de maguey
	}else{

		$pdf->Ln(7);

		$pdf->SetX(75);
		$pdf->SetFont('Calibri-Bold','',9);
		$pdf->Cell(65,12,compat_utf8_decode('QUIEN MANIFIESTA SER PROPIETARIO DEL MAGUEY DESCRITO A CONTINUACIÓN, Y QUE SE ENCUENTRA EN EL'),0,0,'C');
		$pdf->Ln(5);
		$pdf->SetX(70);
		$pdf->Cell(66,12,compat_utf8_decode('VIVERO CUYOS DERECHOS DE EXPLOTACIÓN LE PERTENECE AL PRODUCTOR:'),0,0,'C');
		$pdf->SetFont('Calibri-Bold','',15);
		$pdf->Ln(7);
		$pdf->cell(176,12,compat_utf8_decode(strtoupper($fila['nombrep'])),0,0,'C');
		//ubicación del paraje
		$pdf->Ln(12);
		$pdf->SetFont('Calibri-Bold','',10);
		$pdf->Cell(45,16, compat_utf8_decode('UBICACIÓN DEL VIVERO'), 1,0,'C');
		//paraje y localidad
		$pdf->SetFont('calibri','',10);
		$pdf->Cell(65,8,ucwords(strtolower('')),1,0,'C');
		$pdf->Cell(66,8,ucwords(strtolower('')),1,0,'C');

		$pdf->Ln(0);
		$pdf->SetX(65);
		$pdf->SetFont('Calibri-Bold','',9);
		$pdf->Cell(65,5,$dato['paraje'],0,0,'C');
		$pdf->Cell(66,5,ucwords(strtolower(compat_utf8_decode($dato['localidad']))),0,0,'C');

		$pdf->Ln(0);
		$pdf->SetX(65);
		$pdf->SetFont('Calibri-Bold','',7);
		$pdf->Cell(65,12,'VIVERO',0,0,'C');
		$pdf->Cell(66,12,'LOCALIDAD',0,0,'C');
		//Termina paraje y localidad
		// estado y municipio
		$pdf->Ln(8);
		$pdf->SetX(65);
		$pdf->SetFont('calibri','',10);
		$pdf->Cell(65,8,ucwords(strtolower('')),1,0,'C');
		$pdf->Cell(66,8,ucwords(strtolower('')),1,0,'C');

		$pdf->Ln(0);
		$pdf->SetX(65);
		$pdf->SetFont('Calibri-Bold','',9);
		$pdf->Cell(65,5,ucwords(strtolower(compat_utf8_decode($dato['nombrem']))),0,0,'C');
		$pdf->Cell(66,5,ucwords(strtolower(compat_utf8_decode($dato['nombree']))),0,0,'C');

		$pdf->Ln(0);
		$pdf->SetX(65);
		$pdf->SetFont('Calibri-Bold','',7);
		$pdf->Cell(65,12,'MUNICIPIO',0,0,'C');
		$pdf->Cell(66,12,'ESTADO',0,0,'C');
		//termina estado y municipio

	}
	// no tiene referencia
	 $pdf->SetX(20);
	 $pdf->Ln(8);
	 $pdf->Cell(45,9,'',1,0,'C');
	 $pdf->SetFont('Calibri-Bold','',10);
	 $pdf->Ln(1);
	 $pdf->SetX(26);
	 $pdf->Cell(31,4.5,compat_utf8_decode('COORDENADAS'), 0,0,'C');
	 $pdf->Ln(4);
	 $pdf->SetX(25);
	 $pdf->Cell(31,4.5,compat_utf8_decode('GEOGRÁFICAS'), 0,0,'C');
	 $pdf->Ln(-5);
	 $pdf->SetX(65);
	 $pdf->SetFont('Calibri-Bold','',9);
	 $pdf->Cell(131,9,''.'    '.'',1,0,'C');
	 $pdf->SetX(100);
	 $pdf->Cell(56,7,$dato['lat'].'                                   '.$dato['lng'],0,0,'C');
	 $pdf->Ln(0);
	 $pdf->SetX(77);
	 $pdf->SetFont('Calibri-Bold','',7);
	 $pdf->Cell(56,13,'LATITUD',0,0,'C');
	 $pdf->SetX(125);
	 $pdf->SetFont('Calibri-Bold','',7);
	 $pdf->Cell(53,13,'LONGITUD',0,0,'C');


// Aqui empieza la tabla de atributos de la tierra
	 $pdf->SetX(20);
	$pdf->Ln(7);
	$pdf->Ln(5);
	$pdf->SetFont('Calibri-Bold','',15);
	$pdf->SetTextColor(0,0,0);
	$pdf->Cell(0,12, compat_utf8_decode('CARACTERÍSTICAS DEL MAGUEY'), 0,5, 'C');
	$pdf->SetFillColor(85,107,47);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetFont('Calibri-Bold','',8);
	$pdf->Cell(60,5,compat_utf8_decode('TIPO DE MAGUEY'),1,0,'C',1);
	$pdf->Cell(25,5,'No. DE PLANTAS',1,0,'C',1);
	$pdf->Cell(29,5,compat_utf8_decode('FECHA DE SIEMBRA'),1,0,'C',1);
	$pdf->Cell(30,5,compat_utf8_decode('ORIGEN'),1,0,'C',1);
	$pdf->Cell(38,5,compat_utf8_decode('SISTEMA DE PLANTACIÓN'),1,0,'C',1);




	$pdf->Ln(5);
	$consultandovive = "SELECT genespecie,fecha_siembra,foto1,foto2 
	from paraje_vivero pv 
	inner join existenciaplanta_vivero epv on (epv.id_paraje=pv.id_paraje COLLATE utf8_general_ci)
	inner join comun on comun.id_comun=epv.id_comun 
	inner join especie on especie.id_especie=comun.id_especie WHERE  pv.id_paraje='$paraje'";
	$historialito= $conexion->query($consultandovive);
	$result = mysqli_fetch_array($historialito);

	$strConsulta = "SELECT pv.id_paraje,origen, epv.regmaguey, epv.cantidadini,fecha_siembra,epv.edad, comun.nombre,especie.genespecie,especie.variante
	FROM existenciaplanta_vivero epv
	Inner Join comun ON comun.id_comun= epv.id_comun
	Inner Join especie ON comun.id_especie = especie.id_especie
	Inner Join paraje_vivero pv ON (pv.id_paraje=epv.id_paraje)
	WHERE  pv.id_paraje='$paraje'";
	$historial= $conexion->query($strConsulta);
	$numfilas = mysqli_num_rows($historial);
				$pdf->SetFont('Arial','B',9);
				$pdf->SetFillColor(255,255,255);
   			    $pdf->SetTextColor(0,0,0);

		setlocale(LC_ALL,"es_ES@euro","Es_ES","esp");
		$ds = $result['fecha_siembra'];
		$fechas = compat_strftime("%d-%b-%Y", strtotime($ds));
		//$fechasi = ucfirst(strtolower($fechas));
		$cientifico =compat_utf8_decode(ucfirst(strtolower($result['genespecie'])));
		$cien=$cientifico;

	for ($i=0; $i<$numfilas; $i++)
		{

			while($resultado = mysqli_fetch_array($historial))
			{
				$pdf->SetFont('Calibri-BoldItalic','',8);
				$pdf->Cell(60,5,compat_utf8_decode(strtoupper($resultado['nombre']))." (".compat_utf8_decode(ucfirst(strtolower($resultado['genespecie']))).")",1,0,'C');
				 $pdf->SetFont('Calibri-BoldItalic','');
				//$pdf->Cell(40,5, compat_utf8_decode(ucfirst(strtolower($resultado['genespecie']))).$cientifico,1,0,'C');
				$pdf->SetFont('Calibri-Bold','');
				 $pdf->Cell(25,5,$resultado['cantidadini'],1,0,'C');
				 //$pdf->Cell(29,5,$resultado['fecha_siembra'],1,0,'C');
				 $pdf->Cell(29,5,'---',1,0,'C');
				 $pdf->Cell(30,5,strtoupper($resultado['origen']),1,0,'C');
					$pdf->SetFont('Calibri-Bold','');
				$pdf->Cell(38,5,strtoupper($resultado['regmaguey']),1,0,'C');

				 $pdf->Ln();
			}

					}{

					$pdf->Ln(8);

			$pdf->SetFont('Calibri-Bold','',10);
			$pdf->SetX(135);
			//$pdf->Cell( 88, 20, $pdf->Image("images/firmaa.png", $pdf->GetX(), $pdf->GetY(), 30.78), 0, 0, 'C', false );
			$pdf->Ln(0);
			$pdf->SetX(45);
			//$pdf->Cell( 88, 20, $pdf->Image("images/firmae.jpg", $pdf->GetX(), $pdf->GetY(),40.78), 0,0, 'C', false );
			$pdf->Ln(18);
            $pdf->cell(88,5,compat_utf8_decode('BIOL. BENJAMIN PICHE VENEGAS'),0,0,'C');
            //$pdf->cell(88,5,'',0,0,'C');
            //$pdf->cell(88,5,compat_utf8_decode('DR. EN C. HIPÓCRATES NOLASCO CANCINO'),0,0,'C');
            //$pdf->cell(88,5,compat_utf8_decode('Q. B. ABELINO COHETERO VILLEGAS'),0,0,'C');
			$nocliente = @$filaClientes['no_cliente'];
			/*$puestooc = "GERENTE DEL ORGANISMO DE CERTIFICACIÓN";
			$nombreoc = "L.I.N.M. LILI ARIADNA CARRASCO MERINO";*/

			$puestooc = "DIRECTOR GENERAL";
			$nombreoc = "MSIG. URIEL TERÁN SANGERMÁN";
			
			$pdf->cell(88,5,compat_utf8_decode($nombreoc),0,0,'C');
            $pdf->Ln(0);
            $pdf->cell(88,5,compat_utf8_decode('_______________________________________'),0,0,'C');
            $pdf->cell(88,5,compat_utf8_decode('_______________________________________'),0,0,'C');
            $pdf->Ln(5);
            $pdf->SetFont('Calibri-Bold','',9);
            $pdf->cell(88,5,compat_utf8_decode(strtoupper('Coordinador de la Unidad de Maguey')),0,0,'C');
            $pdf->cell(88,5,compat_utf8_decode(strtoupper($puestooc)),0,0,'C');
			}


			$pdf->Ln(40);


		$Consulta = "SELECT * FROM paraje WHERE paraje.id_paraje='$paraje'";
		$historial= $conexion->query($Consulta);



			while($resultado = mysqli_fetch_array($historial))


    {
   		$id = $resultado['id_paraje'];
        $coordenada1 = $resultado['lat'];
        $coordenada2 = $resultado['lng'];

    }


	/*aqui para imprimir estados con Dom*/
	$strConsulta = "SELECT pv.*,pv.foto1,pv.foto2, estados.ubica as enombreee,estados.nombre 
	from estados 
	inner join municipios on municipios.estado=estados.clave 
	inner join localidades on localidades.MunicipioID=municipios.id 
	inner join paraje pv on pv.id_localidad=localidades.id 
	where  pv.id_paraje='$paraje'";
	$parajes= $conexion->query($strConsulta);
	//$parajes = mysql_query($strConsulta);
	$fila = mysqli_fetch_array($parajes);
	//Aqui termina

		/*$urlGoogle ="http://maps.googleapis.com/maps/api/staticmap?key=AIzaSyCD3xqb8eMEVsAd4m9QnD7s1wOE9_bnALY&center=$coordenada1,$coordenada2&zoom=8&scale=false&size=600x300&maptype=hybrid&format=png&visual_refresh=true&markers=size:mid%7Ccolor:0xff0000%7Clabel:*%7C$coordenada1,$coordenada2";
			$urlGooglec ="http://maps.googleapis.com/maps/api/staticmap?key=AIzaSyCD3xqb8eMEVsAd4m9QnD7s1wOE9_bnALY&center=$coordenada1,$coordenada2&zoom=6&scale=false&size=600x300&maptype=hybrid&format=png&visual_refresh=true&markers=size:mid%7Ccolor:0xff0000%7Clabel:*%7C$coordenada1,$coordenada2";*/



			$pdf->AddPage();
			$pdf->SetFont('Calibri-Bold','',20);
			$pdf->Ln(10);
			$pdf->Cell(0, 4, compat_utf8_decode('PLANTACIONES'), 0,5, 'C');
			//$pdf->Cell(0, 4, compat_utf8_decode('________________________________'), 0,5, 'C');
			$pdf->Cell(140,120);

		  	//$pdf->Image('estadosDOM/oaxaca.png', 90, 35, 40, 30, "PNG");

		   //$pdf->Image($urlGoogle, 110, 50, 90, 70, "PNG");
			//$pdf->Image($urlGooglec, 15,50, 90, 70, "PNG");

			$img01 = 'imgconstancia/'.$paraje.'_01.jpg';
			$img02 = 'imgconstancia/'.$paraje.'_02.jpg';
			if (file_exists($img01)) 
    			$pdf->Image($img01, 110, 50, 90, 70);  // 140622
    		if (file_exists($img02)) 
				$pdf->Image($img02, 15,50, 90, 70);	// 140622
			
			$pdf->Image("../".$result['foto2'], 15, 125, 90, 90);
			$pdf->Image("../".$result['foto1'], 110, 125, 90, 90);

		}

   if (ob_get_level() > 0) {
		ob_end_clean();
	}


    $conexion->close();
   //$conexion_remota->close();


   $pdf->Output($destino,'F');
   $pdf->Output($nuevoNombre,'D');



?>