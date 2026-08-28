<?php
declare(strict_types=1);


include_once("Polyline.php");
require_once __DIR__ . '/../../vendor/autoload.php';
include('../php/registro/conexion.php');
include('../php/registro/conexion_remota.php');
$conexion->set_charset("utf8");
$conexion_remota->set_charset("utf8");
header("Content-Type: text/html; charset=iso-8859-1 ");

/**
 * ============================================================================
 *  CAPA DE COMPATIBILIDAD PHP 8.3 (no altera la logica ni el diseno del PDF)
 * ============================================================================
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
 * ============================================================================
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
class PDF extends FPDF {

    var $widths;
    var $aligns;

    function SetWidths($w) {
        //Set the array of column widths
        $this->widths = $w;
    }

    function SetAligns($a) {
        //Set the array of column alignments
        $this->aligns = $a;
    }

    function Row($data) {
//Calculate the height of the row 
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 5 * $nb;
//Issue a page break first if needed 
        $this->CheckPageBreak($h);
//Draw the cells of the row 
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
//Save the current position 
            $x = $this->GetX();
            $y = $this->GetY();
//Draw the border 

            $this->Rect($x, $y, $w, $h);
            $this->MultiCell($w, 5, $data[$i], 0, $a, 'true');
//Put the position to the right of the cell 
            $this->SetXY($x + $w, $y);
        }
//Go to the next line 
        $this->Ln($h);
    }

    function CheckPageBreak($h) {
        //If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

// CREAMOS ESTA FUNCION 
    function VariasLineas($cadena, $cantidad) {
        $this->Cell(176, 0, '', 'B');
        while (!(strlen($cadena) == '')) {
            $subcadena = substr($cadena, 0, $cantidad);
            $this->Cell(176, 5, $subcadena, 'LR', 0, 'L');
            $cadena = substr($cadena, $cantidad);
            $this->Ln();
        }
        $this->Cell(176, 0, '', 'T');
    }

//TERMINAMOS LA FUNCION 

    function NbLines($w, $txt) {
        //Computes the number of lines a MultiCell of width w will take
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }

    function Header() {
        
    }

    function Footer() {
        include('../php/registro/conexion.php');
        $paraje = $_GET['id'];
        $strConsulta = "select tipo from paraje where paraje.id_paraje='$paraje'";
        $parajes = $conexion->query($strConsulta);
        $fila = mysqli_fetch_array($parajes) ?? ['tipo' => null];

        $this->SetFont('Helvetica', 'BI', 7);
        if ($fila['tipo'] == '1') {
            $this->AliasNbPages();
            $this->SetY(-21);
            $this->Cell(0, 5, compat_utf8_decode('Página') . $this->PageNo() . '/{nb}', 0, 5, 'L');
            $this->SetY(-21);
            $this->Cell(0, 5, Utf8_decode('FM-02/00.'), 0, 5, 'R');

            $this->SetY(-17);
            $this->Cell(0, 5, compat_utf8_decode('ESTE DOCUMENTO NO ES UN INSTRUMENTO LEGAL, ÚNICAMENTE AMPARA EL REGISTRO DE LA PLANTACIÓN DEL MAGUEY DENTRO'), 0, 5, 'C');
            $this->Cell(0, 5, compat_utf8_decode('DEL PREDIO PARA GARANTIZAR LA TRAZABILIDAD DE LA MATERIA PRIMA UTILIZADA EN LA PRODUCCIÓN DE MEZCAL.'), 0, 5, 'C');
        } else {
            $this->Cell(0, 5, compat_utf8_decode('FM-02/00.'), 0, 5, 'R');
            $this->Cell(0, 5, compat_utf8_decode(''), 0, 5, 'C');
            $this->Cell(0, 5, compat_utf8_decode('ESTE DOCUMENTO NO ES UN INSTRUMENTO LEGAL, ÚNICAMENTE AMPARA EL REGISTRO DE LA PLANTACIÓN DEL MAGUEY DENTRO DEL VIVERO.'), 0, 5);
        }
    }

}


$strP = "SELECT * FROM crmreg.paraje_vivero 
		where constancia_vivero != '' 
        AND id_paraje IN (
        1003,1166,1170,1171,1172,1355,1488,1524,1532,1737,1767,1,2159,2178,2179,2180,2181,2271,2273,233,266,2712,2872,28,2,3098,3,
        422,4379,4392,4436,4437,4443,4448,4458,4462,4476,4477,4484,4496,4508,4522,4525,4540,478,47,494,495,496,4,5456,5457,5458,5459,
        5509,5510,5528,5568,5569,5571,5572,5576,5577,5578,5579,5580,5602,5605,5820,5825,5842,5843,5844,5845,5846,
        5847,5848,5849,5850,5851,5852,5853,5854,5855,5856,5861,5862,5863,5864,5879,5880,5892,5905,5906,5920,5921,5924,5926,5927,5930,
        5931,5934,5936,5937,5943,5944,5978,5979,5980,5981,5985,5986,5997,5998,6000,6001,6004,6005,6006,6007,6008,6009,6010,6011,6012,
        6014,6015,6016,6017,6018,6019,6020,6021,6022,6031,6032,6033,6049,6050,6052,6053,6054,6055,6056,6057,6069,6073,6076,6081,6082,
        6083,6089,6090,6091,6114,6115,6136,6137,6164,6165,6166,6167,6168,6169,6170,6175,6177,6179,6180,6181,6182,6183,6206,6207,6209,
        6210,6211,6212,6213,6214,6215,6216,6217,6218,6219,6220,6221,6222,6223,6250,6251,6252,6253,6254,6255,6256,6257,6258,6259,6260,
        6262,6263,6264,6265,6266,6280,6281,6299,6300,6301,6302,6303,6304,633,634,6369,6370,6371,6372,6373,6374,6376,6377,6378,6379,6380,
        6381,6384,6386,6387,6388,6389,6390,6392,6393,6394,6402,6403,6404,6405,6406,6407,6408,6409,6410,6411,6412,6413,6414,6415,6416,6417,
        6419,6420,6421,6483,6486,6487,6488,6489,6490,6491,6492,6508,6509,6510,6511,6519,6520,6521,6522,6551,6561,6564,6593,813,814,81,82
        );";
$resulP = $conexion->query($strP);

while($filaP = mysqli_fetch_object($resulP)) {
	
	$paraje = $_GET['id'];

	$random_Number = rand(0, 9999999999);
	$nuevoNombre = "RegistroVivero_" . $paraje . "_" . $random_Number . ".pdf";
	$destino = "pdfConstanciaVivero/" . $nuevoNombre;


	$strUpdate = "UPDATE paraje_vivero SET constancia_vivero = '$nuevoNombre' WHERE id_paraje='$paraje'";
	$parajesUpdate = $conexion->query($strUpdate);

	$strConsulta = "SELECT Date_format(constancias_vivero.fecha,'%y') as anio,nombrep,paraje_vivero.id_paraje,paraje_vivero.id_cliente,regmaguey,LPAD(constancias_vivero.id_constancia,4,'0') as constancia,paraje_vivero.tipo,LPAD(paraje_vivero.id_paraje,4,'0') as parajes,constancias_vivero.fecha as fecha1,date_add(constancias_vivero.fecha, INTERVAL 1 YEAR) as fecha2 from paraje_vivero inner join constancias_vivero on constancias_vivero.id_paraje=paraje_vivero.id_paraje inner join existenciaplanta_vivero on paraje_vivero.id_paraje=existenciaplanta_vivero.id_paraje where paraje_vivero.id_paraje='$paraje'";


	$parajes = $conexion->query($strConsulta);
	$fila = mysqli_fetch_array($parajes) ?? [
		'anio'=>'','nombrep'=>'','id_paraje'=>null,'id_cliente'=>null,'regmaguey'=>'',
		'constancia'=>'','tipo'=>'','parajes'=>'','fecha1'=>null,'fecha2'=>null,
	];


	$cliente = $fila['id_cliente'];
	$strCliente = "SELECT character_length(CONCAT(domicilio.calle, ' #', domicilio.noexterior,', ',domicilio.colonia,', ',municipios.nombre,', ',estados.nombre)) as contador,CONCAT(domicilio.calle, ' #', domicilio.noexterior,', ',domicilio.colonia,', ',municipios.nombre,', ',estados.nombre) as domicilio,clientes.no_cliente,clientes.nombre as clienten,domicilio.calle,domicilio.noexterior,domicilio.nointerior,domicilio.colonia,domicilio.telefono,domicilio.correo
					  from clientes
					  inner join  domicilio on domicilio.no_cliente=clientes.no_cliente
					  inner join municipios ON domicilio.municipio = municipios.id 
					  inner join estados ON municipios.estado = estados.clave 
					  where clientes.no_cliente='$cliente' and domicilio.estatus=1 ORDER BY domicilio.idDomicilio  LIMIT 1";

	$clientes = $conexion_remota->query($strCliente);
	$filaClientes = mysqli_fetch_array($clientes) ?? [
		'contador' => 0, 'domicilio' => '', 'no_cliente' => $cliente,
		'clienten' => '', 'calle' => '', 'noexterior' => '', 'nointerior' => '',
		'colonia' => '', 'telefono' => '', 'correo' => '',
	];



	if ($fila['tipo'] == '1') {

	    $pdf = new PDF('P', 'mm', 'Letter');
	    $pdf->Open();
	    $pdf->AddPage();
	    $pdf->SetMargins(20, 20, 20);
	    // aqui empieza
	    setlocale(LC_ALL, "es_ES@euro", "Es_ES", "esp");
	    $d = $fila['fecha1'];
	    $fecha = compat_strftime("%d-%b-%Y", strtotime($d));
	    $fecha1 = ucfirst(strtolower($fecha));
	    //fecha1
	    // $d = $fila['fecha2'];
	    // $fechaa = compat_strftime("%Y", strtotime($d));
	    // $fecha2 = ucfirst($fechaa);
	    //fecha nueva
	    $fechaD = date('Y');
	    $nuevafecha = strtotime('+1 year', strtotime($fechaD));
	    $fecha2 = date('Y', $nuevafecha);
	    //termina fecha
	    $pdf->Ln(30);
	    $pdf->SetXY(26, 20);
	    $pdf->SetFont('Helvetica', 'B', 23);
	    //TITULO DE REGISTRI MAGUEY
	    $pdf->Cell(0, 8, compat_utf8_decode('REGISTRO DE MAGUEY'), 0, 5, 'C');
	    $pdf->SetFont('Helvetica', 'B', 13);
	    $pdf->Ln(10);
	    //IMPRIME EL ASOCIOADO: NO_ASOCIADO
	    $pdf->Cell(0, 3, compat_utf8_decode(strtoupper('ASOCIADO ')) . $filaClientes['no_cliente'], 0, 5, 'C');
	    $pdf->SetTextColor(238, 55, 60);
	    $pdf->SetFont('Helvetica', 'B', 14);
	    $pdf->Text(185, 28, strtoupper($fila['constancia']) . $fila['parajes'] . $fila['anio'], 0, 5, 'C');
	    $pdf->SetTextColor(0, 0, 0);
	    $pdf->SetFont('Helvetica', 'B', 8);
	    $pdf->Text(170, 34, strtoupper('No. de Predio: '), 0, 5, 'C');
	    //IMPRIME LA FECHA DE EMISION Y LA OTRA LINEA IMPRIME LA VIGENCIA
	    $pdf->Text(165, 38, compat_utf8_decode('FECHA DE EMISIÓN: '), 0, 5, 'C');
	    $pdf->Text(178, 42, strtoupper('Vigencia:'), 0, 5, 'C');
	    $pdf->SetFont('Helvetica', 'B', 9);
	    $pdf->Text(194, 34, $fila['parajes'], 0, 5, 'C');
	    $pdf->Text(194, 38, $fecha1, 0, 5, 'C');
	    $pdf->Text(194, 42, $fecha2, 0, 5, 'C');

	    $pdf->Ln(3);

	    $pdf->SetTextColor(0, 0, 0);
	    $pdf->SetFont('Helvetica', 'B', 15);
	    $pdf->MultiCell(185, 8, compat_utf8_decode(strtoupper($filaClientes['clienten'])), 0, 'C');


	    if ($filaClientes['contador'] <= 82) {
	        $pdf->Ln(3);
	        $pdf->SetX(65);
	        $pdf->SetFont('Helvetica', 'B', 9);
	        $pdf->MultiCell(131, 7, ucwords(strtolower(compat_utf8_decode($filaClientes['domicilio']))), 1);
	        $pdf->Ln(-7);
	        $pdf->SetFont('Helvetica', 'B', 10);
	        $pdf->MultiCell(45, 7, 'DOMICILIO FISCAL:', 1, 'C');
	    } else {
	        $pdf->Ln(3);
	        $pdf->SetX(65);
	        $pdf->SetFont('Helvetica', 'B', 9);
	        $pdf->MultiCell(131, 5, ucwords(strtolower(compat_utf8_decode($filaClientes['domicilio']))), 1);
	        $pdf->Ln(-10);
	        $pdf->SetFont('Helvetica', 'B', 10);
	        $pdf->MultiCell(45, 10, 'DOMICILIO FISCAL:', 1, 'C');
	    }

	    $pdf->Ln(0);
	    $pdf->SetFont('Helvetica', 'B', 10);
	    $pdf->Cell(22, 7, compat_utf8_decode('TELÉFONO: '), 1, 0, 'C');
	    $pdf->SetFont('Helvetica', 'B', 9);
	    $pdf->Cell(53, 7, $filaClientes['telefono'], 1, 0, 'C');
	    $pdf->SetFont('Helvetica', 'B', 10);
	    $pdf->Cell(45, 7, compat_utf8_decode('CORREO ELECTRÓNICO: '), 1, 0, 'C');
	    if ($filaClientes['correo'] == "") {
	        $pdf->SetFont('Helvetica', 'B', 9);
	        $pdf->Cell(56, 7, compat_utf8_decode('---'), 1, 0, 'C');
	    } else {
	        $pdf->SetFont('Helvetica', 'B', 9);
	        $pdf->Cell(56, 7, compat_utf8_decode($filaClientes['correo']), 1, 0, 'C');
	    }

	    // la consulta para datos del paraje
	    $Consulta = "SELECT localidades.localidad,municipios.nombre as nombrem,estados.nombre as nombree,paraje_vivero.paraje,paraje_vivero.referencia,paraje_vivero.lat,paraje_vivero.lng,paraje_vivero.superficie 
			from estados 
			inner join municipios on municipios.estado=estados.clave 
			inner join localidades on localidades.MunicipioID=municipios.id 
			inner join paraje_vivero on paraje_vivero.id_localidad=localidades.id
			where paraje_vivero.id_paraje='$paraje'";


	    $ubicaciones = $conexion->query($Consulta);
	    $dato = mysqli_fetch_array($ubicaciones) ?? [
		'localidad'=>'','nombrem'=>'','nombree'=>'','paraje'=>'','referencia'=>'',
		'lat'=>null,'lng'=>null,'superficie'=>'',
	    ];
	    if ($fila['nombrep'] == $filaClientes['clienten'] or $fila['nombrep'] == '') {

	        $pdf->Ln(7);

	        $pdf->SetFont('Helvetica', 'B', 10);
	        //imprime la ubicacion del predio
	        $pdf->Cell(45, 16, compat_utf8_decode('UBICACIÓN DEL PREDIO'), 1, 0, 'C');
	        $pdf->SetFont('Helvetica', 'B', 10);
	        $pdf->Cell(65, 8, ucwords(strtolower('')), 1, 0, 'C');
	        $pdf->Cell(66, 8, ucwords(strtolower('')), 1, 0, 'C');

	        $pdf->Ln(0);
	        $pdf->SetX(65);
	        $pdf->SetFont('Helvetica', 'B', 9);
	        $pdf->Cell(65, 5, compat_utf8_decode($dato['paraje']), 0, 0, 'C');
	        $pdf->Cell(66, 5, ucwords(strtolower(compat_utf8_decode($dato['localidad']))), 0, 0, 'C');

	        $pdf->Ln(0);
	        $pdf->SetX(65);
	        $pdf->SetFont('Helvetica', 'B', 7);
	        $pdf->Cell(65, 12, strtoupper('Predio'), 0, 0, 'C');
	        $pdf->Cell(66, 12, strtoupper('localidad'), 0, 0, 'C');
	        //Termina paraje y localidad
	        $pdf->Ln(8);
	        $pdf->SetX(65);

	        // aqui empieza el municipio y el estado
	        // estado y municipio
	        $pdf->Ln(0);
	        $pdf->SetX(65);
	        $pdf->SetFont('Helvetica', '', 10);
	        $pdf->Cell(65, 8, ucwords(strtolower('')), 1, 0, 'C');
	        $pdf->Cell(66, 8, ucwords(strtolower('')), 1, 0, 'C');


	        $pdf->Ln(0);
	        $pdf->SetX(65);
	        $pdf->SetFont('Helvetica', 'B', 9);
	        $pdf->Cell(65, 5, ucwords(strtolower(compat_utf8_decode($dato['nombrem']))), 0, 0, 'C');
	        $pdf->Cell(66, 5, ucwords(strtolower(compat_utf8_decode($dato['nombree']))), 0, 0, 'C');

	        $pdf->Ln(0);
	        $pdf->SetX(65);
	        $pdf->SetFont('Helvetica', 'B', 7);
	        $pdf->Cell(65, 12, 'MUNICIPIO', 0, 0, 'C');
	        $pdf->Cell(66, 12, 'ESTADO', 0, 0, 'C');
	        //termina estado y municipio
	        // condicion si  hay productor de maguey 
	    } else {

	        $pdf->Ln(7);

	        $pdf->SetX(75);
	        $pdf->SetFont('Helvetica', 'B', 9);
	        $pdf->Cell(65, 12, compat_utf8_decode('QUIEN MANIFIESTA SER PROPIETARIO DEL MAGUEY DESCRITO A CONTINUACIÓN, Y QUE SE ENCUENTRA EN EL'), 0, 0, 'C');
	        $pdf->Ln(5);
	        $pdf->SetX(70);
	        $pdf->Cell(66, 12, compat_utf8_decode('PREDIO CUYOS DERECHOS DE EXPLOTACIÓN LE PERTENECE AL PRODUCTOR:'), 0, 0, 'C');
	        $pdf->SetFont('Helvetica', 'B', 15);
	        $pdf->Ln(7);
	        $pdf->cell(176, 12, compat_utf8_decode(strtoupper($fila['nombrep'])), 0, 0, 'C');
	        //ubicación del paraje
	        $pdf->Ln(12);
	        $pdf->SetFont('Helvetica', 'B', 10);
	        $pdf->Cell(45, 16, compat_utf8_decode('UBICACIÓN DEL PREDIO'), 1, 0, 'C');
	        //paraje y localidad
	        $pdf->SetFont('Helvetica', '', 10);
	        $pdf->Cell(65, 8, ucwords(strtolower('')), 1, 0, 'C');
	        $pdf->Cell(66, 8, ucwords(strtolower('')), 1, 0, 'C');

	        $pdf->Ln(0);
	        $pdf->SetX(65);
	        $pdf->SetFont('Helvetica', 'B', 9);
	        //devuelve el dato de para y localidad
	        $pdf->Cell(65, 5, $dato['paraje'], 0, 0, 'C');
	        $pdf->Cell(66, 5, ucwords(strtolower(compat_utf8_decode($dato['localidad']))), 0, 0, 'C');

	        $pdf->Ln(0);
	        $pdf->SetX(65);
	        $pdf->SetFont('Helvetica', 'B', 7);
	        $pdf->Cell(65, 12, 'PREDIO', 0, 0, 'C');
	        $pdf->Cell(66, 12, 'LOCALIDAD', 0, 0, 'C');
	        //Termina paraje y localidad
	        // estado y municipio
	        $pdf->Ln(8);
	        $pdf->SetX(65);
	        $pdf->SetFont('Helvetica', '', 10);
	        $pdf->Cell(65, 8, ucwords(strtolower('')), 1, 0, 'C');
	        $pdf->Cell(66, 8, ucwords(strtolower('')), 1, 0, 'C');

	        $pdf->Ln(0);
	        $pdf->SetX(65);
	        $pdf->SetFont('Helvetica', 'B', 9);
	        //$datos devuelve el nombre de la especia, y el nombre del municipio
	        $pdf->Cell(65, 5, ucwords(strtolower(compat_utf8_decode($dato['nombrem']))), 0, 0, 'C');
	        $pdf->Cell(66, 5, ucwords(strtolower(compat_utf8_decode($dato['nombree']))), 0, 0, 'C');

	        $pdf->Ln(0);
	        $pdf->SetX(65);
	        $pdf->SetFont('Helvetica', 'B', 7);
	        $pdf->Cell(65, 12, 'MUNICIPIO', 0, 0, 'C');
	        $pdf->Cell(66, 12, 'ESTADO', 0, 0, 'C');
	        //termina estado y municipio
	    }

	    // no tiene referencia
	    $pdf->SetX(20);
	    $pdf->Ln(8);
	    $pdf->SetFont('Helvetica', 'B', 10);
	    $pdf->Cell(45, 9, compat_utf8_decode('SUPERFICIE'), 1, 0, 'C');
	    $pdf->SetFont('Helvetica', 'B', 9);
	    //$datos devuelve la superficie que esta alamcenado en la base de datos
	    $pdf->Cell(30, 9, $dato['superficie'], 1, 0, 'C');
	    $pdf->Ln(0);
	    $pdf->SetX(64);
	    $pdf->SetFont('Helvetica', 'B', 7);
	    $pdf->Cell(31, 14, 'HECTAREAS', 0, 0, 'C');
	    $pdf->Cell(45, 9, '', 1, 0, 'C');
	    $pdf->SetFont('Helvetica', 'B', 10);
	    $pdf->Ln(1);
	    $pdf->SetX(102);
	    $pdf->Cell(31, 4.5, compat_utf8_decode('COORDENADAS'), 0, 0, 'C');
	    $pdf->Ln(4);
	    $pdf->SetX(101);
	    $pdf->Cell(31, 4.5, compat_utf8_decode('GEOGRÁFICAS'), 0, 0, 'C');
	    $pdf->Ln(-5);
	    $pdf->SetX(140);
	    $pdf->SetFont('Helvetica', 'B', 9);
	    $pdf->Cell(56, 9, '' . '    ' . '', 1, 0, 'C');
	    $pdf->SetX(140);
	    //$datos devuelve la latitud y la longitud
	    $pdf->Cell(56, 7, $dato['lat'] . '        ' . $dato['lng'], 0, 0, 'C');
	    $pdf->Ln(0);
	    $pdf->SetX(129);
	    $pdf->SetFont('Helvetica', 'B', 7);
	    $pdf->Cell(56, 13, 'LATITUD', 0, 0, 'C');
	    $pdf->SetX(152);
	    $pdf->SetFont('Helvetica', 'B', 7);
	    $pdf->Cell(53, 13, 'LONGITUD', 0, 0, 'C');


	// Aqui empieza la tabla de atributos de la tierra
	    $pdf->SetX(20);
	    $pdf->Ln(10);
	    $pdf->SetFont('Helvetica', 'B', 15);
	    $pdf->SetTextColor(0, 0, 0);
	    $pdf->Cell(0, 12, strtoupper('Atributos de la Tierra'), 0, 5, 'C');
	    $pdf->Ln(0);
	    $pdf->SetFont('Helvetica', 'B', 8);
	    $pdf->Cell(140, 5, strtoupper('Manejo sustentable de maguey silvestre'), 1, 0, 'C');
	    $pdf->SetFont('Helvetica', 'B', 8);
	    $pdf->Cell(36, 5, '---', 1, 0, 'C');
	    $pdf->Ln(5);
	    $pdf->SetFont('Helvetica', 'B', 8);
	    $pdf->Cell(140, 5, compat_utf8_decode(strtoupper('Manejo sustentable de cultivos')), 1, 0, 'C');
	    $pdf->SetFont('Helvetica', 'B', 8);
	    $pdf->Cell(36, 5, '---', 1, 0, 'C');
	    $pdf->Ln(5);
	    $pdf->SetFont('Helvetica', 'B', 8);
	    $pdf->Cell(140, 5, compat_utf8_decode('PRESERVACIÓN DE POLINIZADORES Y VARIABILIDAD GENÉTICA DEL MAGUEY EN CULTIVOS'), 1, 0, 'C');
	    $pdf->SetFont('Helvetica', 'B', 8);
	    $pdf->Cell(36, 5, '---', 1, 0, 'C');
	    $pdf->Ln(5);
	    $pdf->SetFont('Helvetica', 'B', 8);
	    $pdf->Cell(140, 5, compat_utf8_decode('MANEJO ORGÁNICO DEL CULTIVO DE MAGUEY'), 1, 0, 'C');
	    $pdf->SetFont('Helvetica', 'B', 8);
	    $pdf->Cell(36, 5, '---', 1, 0, 'C');

	    // Aqui termina 
	    $pdf->SetX(20);
	    $pdf->Ln(5);
	    $pdf->SetFont('Helvetica', 'B', 15);
	    $pdf->SetTextColor(0, 0, 0);
	    $pdf->Cell(0, 12, compat_utf8_decode('CARACTERÍSTICAS DEL MAGUEY'), 0, 5, 'C');
	    $pdf->SetFillColor(85, 107, 47);
	    $pdf->SetTextColor(255, 255, 255);
	    $pdf->SetFont('Helvetica', 'B', 8);
	    $pdf->Cell(92, 5, compat_utf8_decode('TIPO DE MAGUEY'), 1, 0, 'C', 1);
	    $pdf->Cell(25, 5, 'No. DE PLANTAS', 1, 0, 'C', 1);
	    $pdf->Cell(21, 5, compat_utf8_decode('EDAD (AÑOS)'), 1, 0, 'C', 1);
	    $pdf->Cell(38, 5, compat_utf8_decode('SISTEMA DE PLANTACIÓN'), 1, 0, 'C', 1);



	    $pdf->Ln(5);

	    $strConsulta = "SELECT paraje_vivero.id_paraje, existenciaplanta_vivero.regmaguey, existenciaplanta_vivero.cantidadini, existenciaplanta_vivero.edad, comun.nombre,especie.genespecie,especie.variante
		FROM existenciaplanta_vivero
		Inner Join comun ON comun.id_comun= existenciaplanta_vivero.id_comun 
		Inner Join especie ON comun.id_especie = especie.id_especie
		Inner Join paraje_vivero ON paraje_vivero.id_paraje=existenciaplanta_vivero.id_paraje
		WHERE  paraje_vivero.id_paraje='$paraje'";

	    $historial = $conexion->query($strConsulta);
	    $numfilas = mysqli_num_rows($historial);
	    $pdf->SetFont('Arial', 'B', 9);
	    $pdf->SetFillColor(255, 255, 255);
	    $pdf->SetTextColor(0, 0, 0);

	    for ($i = 0; $i < $numfilas; $i++) {

	        while ($resultado = mysqli_fetch_array($historial)) {
	            $pdf->SetFont('Helvetica', 'B', 8);
	            //imprime el resultado de la consulta que es el nombre el gen especia, la cantidad inicial, la edad y el regmaguey
	            $pdf->Cell(52, 5, compat_utf8_decode(strtoupper($resultado['nombre'])), 1, 0, 'C');
	            $pdf->SetFont('Helvetica', 'BI');
	            $pdf->Cell(40, 5, compat_utf8_decode(ucfirst(strtolower($resultado['genespecie']))), 1, 0, 'C');
	            $pdf->SetFont('Helvetica', 'B');
	            $pdf->Cell(25, 5, $resultado['cantidadini'], 1, 0, 'C');
	            $pdf->Cell(21, 5, $resultado['edad'], 1, 0, 'C');
	            $pdf->SetFont('Helvetica', 'B');
	            $pdf->Cell(38, 5, strtoupper($resultado['regmaguey']), 1, 0, 'C');
	            $pdf->Ln();
	        }
	    } {

	        $pdf->Ln(1);

	        $pdf->SetFont('Helvetica', 'B', 10);
	        $pdf->SetX(135);
	        $pdf->Cell(88, 20, $pdf->Image("images/firmaa.png", $pdf->GetX(), $pdf->GetY(), 30.78), 0, 0, 'C', false);
	        $pdf->Ln(0);
	        $pdf->SetX(45);
	        $pdf->Cell(88, 20, $pdf->Image("images/firmae.jpg", $pdf->GetX(), $pdf->GetY(), 40.78), 0, 0, 'C', false);
	        $pdf->Ln(18);
	        $pdf->cell(88, 5, compat_utf8_decode('M. EN C. EFRAÍN PAREDES HERNÁNDEZ'), 0, 0, 'C');
	        //$pdf->cell(88,5,compat_utf8_decode('DR. EN C. HIPÓCRATES NOLASCO CANCINO'),0,0,'C');
	        $pdf->cell(88, 5, compat_utf8_decode('Q. B. ABELINO COHETERO VILLEGAS'), 0, 0, 'C');
	        $pdf->Ln(0);
	        //se imprime las lineas de las firmas.
	        $pdf->cell(88, 5, compat_utf8_decode('_______________________________________'), 0, 0, 'C');
	        $pdf->cell(88, 5, compat_utf8_decode('_______________________________________'), 0, 0, 'C');
	        $pdf->Ln(5);
	        $pdf->SetFont('Helvetica', 'B', 9);
	        $pdf->cell(88, 5, compat_utf8_decode(strtoupper('Cordinador de la Unidad de Maguey')), 0, 0, 'C');
	        $pdf->cell(88, 5, compat_utf8_decode(strtoupper('Presidente')), 0, 0, 'C');
	    }


	    $pdf->Ln(40);

	    $Consulta = "SELECT * FROM paraje_vivero WHERE paraje_vivero.id_paraje='$paraje'";
	    $historial = $conexion->query($Consulta);



	    while ($resultado = mysqli_fetch_array($historial)) {
	        $id = $resultado['id_paraje'];
	        $coordenada1 = $resultado['lat'];
	        $coordenada2 = $resultado['lng'];
	    }
	    /* Poligono */
	    $Consulta = "SELECT AsBinary(poligono), lat, lng FROM paraje_vivero
			where paraje_vivero.id_paraje='$paraje'";
	    $parajes = $conexion->query($Consulta);

	    if ($parajes) {

	        if ($row = $parajes->fetch_row()) {

	            $clat = $row[1];
	            $clng = $row[2];

	            $geo = unpack("Corder/Ltype/Lnum", $row[0]);


	            if ($geo["type"] == 3) {
	                $num = $geo["num"];
	                $offset = 9;

	                $puntos = array();

	                for ($i = 0; $i < $num; $i++) {
	                    $h = unpack("@" . $offset . "/Lnumpts", $row[0]);
	                    $numpts = $h["numpts"];

	                    $offset += 4;

	                    $nump = $numpts * 2;

	                    $pts = unpack("@" . $offset . "/d" . $nump, $row[0]);

	                    $lat = 0;
	                    $lon = 0;
	                    $esLongitud = true;
	                    foreach ($pts as $value) {
	                        $esLongitud ? $lon = $value : $lat = $value;

	                        if (!$esLongitud) {
	                            array_push($puntos, array($lat, $lon));
	                        }

	                        $esLongitud = !$esLongitud;
	                    }

	                    $offset += ($nump * 8);
	                }
	            }

	            $puntosCodificados = Polyline::Encode($puntos);


	            /* aqui para imprimir estados con Dom */
	            $strConsulta = "SELECT paraje_vivero.*, estados.ubica as enombreee,estados.nombre from estados inner join municipios on municipios.estado=estados.clave inner join localidades on localidades.MunicipioID=municipios.id inner join paraje_vivero on paraje_vivero.id_localidad=localidades.id where  paraje_vivero.id_paraje='$paraje'";
	            $parajes = $conexion->query($strConsulta);
	            //$parajes = mysql_query($strConsulta);
	            $fila = mysqli_fetch_array($parajes) ?? ['id'=>null,'id_paraje'=>null,'id_localidad'=>null,'id_cliente'=>null,'paraje'=>'','lat'=>null,'lng'=>null,'poligono'=>'','tenencia'=>'','superficie'=>'','docpro'=>'','referencia'=>'','usufruto'=>'','fecha'=>null,'nombrep'=>'','fecha_paraje'=>null,'rcampo'=>'','status'=>null,'foto1'=>'','foto2'=>'','tipo'=>'','constancia_vivero'=>'','status_predio'=>null,'constancia_extracciones'=>null,'enombreee'=>'','nombre'=>''];
	            //Aqui termina

	            $urlGoogle = "http://maps.googleapis.com/maps/api/staticmap?center=$coordenada1,$coordenada2&zoom=8&scale=false&size=600x300&maptype=hybrid&format=png&visual_refresh=true&markers=size:mid%7Ccolor:0xff0000%7Clabel:*%7C$coordenada1,$coordenada2";

	            if ($dato['superficie'] > 600) {

	                if ($dato['superficie'] > 3000) {
	                    $urlGoogleg = "http://maps.googleapis.com/maps/api/staticmap?key=AIzaSyDUEYxYysL5-sWW_D3qKs4nm7h3iLzJ03U&zoom=10" .
	                            "&size=640x450&maptype=hybrid&sensor=false&path=color:red|weight:1|fillcolor:red|enc:" . $puntosCodificados;
	                } else {
	                    $urlGoogleg = "http://maps.googleapis.com/maps/api/staticmap?key=AIzaSyDUEYxYysL5-sWW_D3qKs4nm7h3iLzJ03U&zoom=13" .
	                            "&size=640x450&maptype=hybrid&sensor=false&path=color:red|weight:1|fillcolor:red|enc:" . $puntosCodificados;
	                }
	            } else {
	                if ($dato['superficie'] <= 2.6) {

	                    $urlGoogleg = "http://maps.googleapis.com/maps/api/staticmap?key=AIzaSyDUEYxYysL5-sWW_D3qKs4nm7h3iLzJ03U&zoom=17" .
	                            "&size=640x450&maptype=hybrid&sensor=false&path=color:red|weight:1|fillcolor:red|enc:" . $puntosCodificados;
	                } else {
	                    $urlGoogleg = "http://maps.googleapis.com/maps/api/staticmap?key=AIzaSyDUEYxYysL5-sWW_D3qKs4nm7h3iLzJ03U&zoom=15" . //zoom=12
	                            "&size=640x450&maptype=hybrid&sensor=false&path=color:red|weight:1|fillcolor:red|enc:" . $puntosCodificados;
	                }
	            }



	            $urlGooglec = "http://maps.googleapis.com/maps/api/staticmap?center=$coordenada1,$coordenada2&zoom=11&scale=false&size=600x300&maptype=hybrid&format=png&visual_refresh=true&markers=size:mid%7Ccolor:0xff0000%7Clabel:*%7C$coordenada1,$coordenada2";



	            $pdf->AddPage();
	            $pdf->SetFont('Helvetica', 'B', 20);
	            $pdf->Cell(0, 4, compat_utf8_decode('PREDIO GEORREFERENCIADO'), 0, 5, 'C');
	            $pdf->Cell(0, 4, compat_utf8_decode('________________________________'), 0, 5, 'C');
	            $pdf->Cell(140, 120);

	            //$pdf->Image('estadosDOM/oaxaca.png', 90, 35, 40, 30, "PNG");
	            $pdf->Image($fila['enombreee'], 90, 35, 40, 30, "PNG");
	            $pdf->Image($urlGoogle, 15, 70, 100, 60, "PNG");
	            $pdf->Image($urlGooglec, 120, 70, 80, 60, "PNG");
	            $pdf->Image($urlGoogleg, 15, 135, 185, 120, "PNG");
	        }
	    }
	}

	//AQUI EMPIEZA EL DE VIVEROS
	else {
	    $pdf = new PDF('P', 'mm', 'Letter');
	    $pdf->Open();
	    $pdf->AddPage();
	    $pdf->SetMargins(20, 20, 20);
	    // aqui empieza
	    setlocale(LC_ALL, "es_ES@euro", "Es_ES", "esp");
	    $d = $fila['fecha1'];
	    $fecha = compat_strftime("%d-%b-%Y", strtotime($d));
	    $fecha1 = ucfirst(strtolower($fecha));
	    $fecha1 = str_replace(".", "", $fecha1);
	    //fecha1
	    // $d = $fila['fecha2'];
	    // $fechaa = compat_strftime("%Y", strtotime($d));
	    // $fecha2 = ucfirst($fechaa);

	    $fechaD = date('Y');
	    $nuevafecha = strtotime('+1 year', strtotime($fechaD));
	    $fecha2 = date('Y', $nuevafecha);
	    //termina fecha
	    $pdf->Ln(30);
	    $pdf->SetXY(26, 20);
	    $pdf->SetFont('Helvetica', 'B', 23);
	    $pdf->Cell(0, 8, compat_utf8_decode('REGISTRO DE VIVERO'), 0, 5, 'C');
	    $pdf->Ln(10);
	    $pdf->SetFont('Helvetica', 'B', 13);
	    //$pdf->Cell(0,3, compat_utf8_decode(strtoupper('ASOCIADO ')).$filaClientes['no_cliente'],0,5, 'C');
	    $pdf->Cell(0, 3, compat_utf8_decode(strtoupper('No DE REGISTRO CRM ')) . $filaClientes['no_cliente'], 0, 5, 'C');
	    $pdf->SetTextColor(238, 55, 60);
	    $pdf->SetFont('Helvetica', 'B', 14);
	    $pdf->Text(185, 28, strtoupper($fila['constancia']) . $fila['parajes'] . $fila['anio'], 0, 5, 'C');
	    $pdf->SetTextColor(0, 0, 0);
	    $pdf->SetFont('Helvetica', 'B', 8);
	    $pdf->Text(170, 34, strtoupper('No. de Vivero: '), 0, 5, 'C');
	    $pdf->Text(165, 38, compat_utf8_decode('FECHA DE EMISIÓN: '), 0, 5, 'C');
	    $pdf->Text(178, 42, strtoupper('Vigencia:'), 0, 5, 'C');
	    $pdf->SetFont('Helvetica', 'B', 9);
	    $pdf->Text(194, 34, $fila['parajes'], 0, 5, 'C');
	    $pdf->Text(194, 38, $fecha1, 0, 5, 'C');
	    $pdf->Text(194, 42, $fecha2, 0, 5, 'C');

	    $pdf->Ln(3);

	    $pdf->SetTextColor(0, 0, 0);
	    $pdf->SetFont('Helvetica', 'B', 15);
	    $pdf->MultiCell(185, 8, compat_utf8_decode(strtoupper($filaClientes['clienten'])), 0, 'C');


	    if ($filaClientes['contador'] <= 82) {
	        $pdf->Ln(3);
	        $pdf->SetX(65);
	        $pdf->SetFont('Helvetica', 'B', 9);
	        $pdf->MultiCell(131, 7, ucwords(strtolower(compat_utf8_decode($filaClientes['domicilio']))), 1);
	        $pdf->Ln(-7);
	        $pdf->SetFont('Helvetica', 'B', 10);
	        $pdf->MultiCell(45, 7, 'DOMICILIO FISCAL:', 1, 'C');
	    } else {
	        $pdf->Ln(3);
	        $pdf->SetX(65);
	        $pdf->SetFont('Helvetica', 'B', 9);
	        $pdf->MultiCell(131, 5, ucwords(strtolower(compat_utf8_decode($filaClientes['domicilio']))), 1);
	        $pdf->Ln(-10);
	        $pdf->SetFont('Helvetica', 'B', 10);
	        $pdf->MultiCell(45, 10, 'DOMICILIO FISCAL:', 1, 'C');
	    }

	    $pdf->Ln(0);
	    $pdf->SetFont('Helvetica', 'B', 10);
	    $pdf->Cell(22, 7, compat_utf8_decode('TELÉFONO: '), 1, 0, 'C');
	    $pdf->SetFont('Helvetica', 'B', 9);
	    $pdf->Cell(53, 7, $filaClientes['telefono'], 1, 0, 'C');
	    $pdf->SetFont('Helvetica', 'B', 10);
	    $pdf->Cell(45, 7, compat_utf8_decode('CORREO ELECTRÓNICO: '), 1, 0, 'C');
	    if ($filaClientes['correo'] == "") {
	        $pdf->SetFont('Helvetica', 'B', 9);
	        $pdf->Cell(56, 7, compat_utf8_decode('---'), 1, 0, 'C');
	    } else {
	        $pdf->SetFont('Helvetica', 'B', 9);
	        $pdf->Cell(56, 7, compat_utf8_decode($filaClientes['correo']), 1, 0, 'C');
	    }

	    // la consulta para datos del paraje
	    $Consulta = "SELECT localidades.localidad,municipios.nombre as nombrem,estados.nombre as 		nombree,paraje_vivero.paraje,paraje_vivero.referencia,paraje_vivero.lat,paraje_vivero.lng,paraje_vivero.superficie 
			from estados 
			inner join municipios on municipios.estado=estados.clave 
			inner join localidades on localidades.MunicipioID=municipios.id 
			inner join paraje_vivero on paraje_vivero.id_localidad=localidades.id
			where paraje_vivero.id_paraje='$paraje'";


	    $ubicaciones = $conexion->query($Consulta);
	    $dato = mysqli_fetch_array($ubicaciones) ?? [
		'localidad'=>'','nombrem'=>'','nombree'=>'','paraje'=>'','referencia'=>'',
		'lat'=>null,'lng'=>null,'superficie'=>'',
	    ];
	    if ($fila['nombrep'] == $filaClientes['clienten'] or $fila['nombrep'] == '') {

	        $pdf->Ln(7);

	        $pdf->SetFont('Helvetica', 'B', 10);
	        $pdf->Cell(45, 16, compat_utf8_decode('UBICACIÓN DEL PREDIO'), 1, 0, 'C');
	        $pdf->SetFont('Helvetica', 'B', 10);
	        $pdf->Cell(65, 8, ucwords(strtolower('')), 1, 0, 'C');
	        $pdf->Cell(66, 8, ucwords(strtolower('')), 1, 0, 'C');

	        $pdf->Ln(0);
	        $pdf->SetX(65);
	        $pdf->SetFont('Helvetica', 'B', 9);
	        $pdf->Cell(65, 5, compat_utf8_decode($dato['paraje']), 0, 0, 'C');
	        $pdf->Cell(66, 5, ucwords(strtolower(compat_utf8_decode($dato['localidad']))), 0, 0, 'C');

	        $pdf->Ln(0);
	        $pdf->SetX(65);
	        $pdf->SetFont('Helvetica', 'B', 7);
	        $pdf->Cell(65, 12, strtoupper('Predio'), 0, 0, 'C');
	        $pdf->Cell(66, 12, strtoupper('localidad'), 0, 0, 'C');
	        //Termina paraje y localidad
	        $pdf->Ln(8);
	        $pdf->SetX(65);

	        // aqui empieza el municipio y el estado
	        // estado y municipio
	        $pdf->Ln(0);
	        $pdf->SetX(65);
	        $pdf->SetFont('Helvetica', '', 10);
	        $pdf->Cell(65, 8, ucwords(strtolower('')), 1, 0, 'C');
	        $pdf->Cell(66, 8, ucwords(strtolower('')), 1, 0, 'C');


	        $pdf->Ln(0);
	        $pdf->SetX(65);
	        $pdf->SetFont('Helvetica', 'B', 9);
	        $pdf->Cell(65, 5, ucwords(strtolower(compat_utf8_decode($dato['nombrem']))), 0, 0, 'C');
	        $pdf->Cell(66, 5, ucwords(strtolower(compat_utf8_decode($dato['nombree']))), 0, 0, 'C');

	        $pdf->Ln(0);
	        $pdf->SetX(65);
	        $pdf->SetFont('Helvetica', 'B', 7);
	        $pdf->Cell(65, 12, 'MUNICIPIO', 0, 0, 'C');
	        $pdf->Cell(66, 12, 'ESTADO', 0, 0, 'C');
	        //termina estado y municipio
	        // condicion si  hay productor de maguey 
	    } else {

	        $pdf->Ln(7);

	        $pdf->SetX(75);
	        $pdf->SetFont('Helvetica', 'B', 9);
	        $pdf->Cell(65, 12, compat_utf8_decode('QUIEN MANIFIESTA SER PROPIETARIO DEL MAGUEY DESCRITO A CONTINUACIÓN, Y QUE SE ENCUENTRA EN EL'), 0, 0, 'C');
	        $pdf->Ln(5);
	        $pdf->SetX(70);
	        $pdf->Cell(66, 12, compat_utf8_decode('VIVERO CUYOS DERECHOS DE EXPLOTACIÓN LE PERTENECE AL PRODUCTOR:'), 0, 0, 'C');
	        $pdf->SetFont('Helvetica', 'B', 15);
	        $pdf->Ln(7);
	        $pdf->cell(176, 12, compat_utf8_decode(strtoupper($fila['nombrep'])), 0, 0, 'C');
	        //ubicación del paraje
	        $pdf->Ln(12);
	        $pdf->SetFont('Helvetica', 'B', 10);
	        $pdf->Cell(45, 16, compat_utf8_decode('UBICACIÓN DEL VIVERO'), 1, 0, 'C');
	        //paraje y localidad
	        $pdf->SetFont('Helvetica', '', 10);
	        $pdf->Cell(65, 8, ucwords(strtolower('')), 1, 0, 'C');
	        $pdf->Cell(66, 8, ucwords(strtolower('')), 1, 0, 'C');

	        $pdf->Ln(0);
	        $pdf->SetX(65);
	        $pdf->SetFont('Helvetica', 'B', 9);
	        $pdf->Cell(65, 5, $dato['paraje'], 0, 0, 'C');
	        $pdf->Cell(66, 5, ucwords(strtolower(compat_utf8_decode($dato['localidad']))), 0, 0, 'C');

	        $pdf->Ln(0);
	        $pdf->SetX(65);
	        $pdf->SetFont('Helvetica', 'B', 7);
	        $pdf->Cell(65, 12, 'VIVERO', 0, 0, 'C');
	        $pdf->Cell(66, 12, 'LOCALIDAD', 0, 0, 'C');
	        //Termina paraje y localidad
	        // estado y municipio
	        $pdf->Ln(8);
	        $pdf->SetX(65);
	        $pdf->SetFont('Helvetica', '', 10);
	        $pdf->Cell(65, 8, ucwords(strtolower('')), 1, 0, 'C');
	        $pdf->Cell(66, 8, ucwords(strtolower('')), 1, 0, 'C');

	        $pdf->Ln(0);
	        $pdf->SetX(65);
	        $pdf->SetFont('Helvetica', 'B', 9);
	        $pdf->Cell(65, 5, ucwords(strtolower(compat_utf8_decode($dato['nombrem']))), 0, 0, 'C');
	        $pdf->Cell(66, 5, ucwords(strtolower(compat_utf8_decode($dato['nombree']))), 0, 0, 'C');

	        $pdf->Ln(0);
	        $pdf->SetX(65);
	        $pdf->SetFont('Helvetica', 'B', 7);
	        $pdf->Cell(65, 12, 'MUNICIPIO', 0, 0, 'C');
	        $pdf->Cell(66, 12, 'ESTADO', 0, 0, 'C');
	        //termina estado y municipio
	    }
	    // no tiene referencia
	    $pdf->SetX(20);
	    $pdf->Ln(8);
	    $pdf->Cell(45, 9, '', 1, 0, 'C');
	    $pdf->SetFont('Helvetica', 'B', 10);
	    $pdf->Ln(1);
	    $pdf->SetX(26);
	    $pdf->Cell(31, 4.5, compat_utf8_decode('COORDENADAS'), 0, 0, 'C');
	    $pdf->Ln(4);
	    $pdf->SetX(25);
	    $pdf->Cell(31, 4.5, compat_utf8_decode('GEOGRÁFICAS'), 0, 0, 'C');
	    $pdf->Ln(-5);
	    $pdf->SetX(65);
	    $pdf->SetFont('Helvetica', 'B', 9);
	    $pdf->Cell(131, 9, '' . '    ' . '', 1, 0, 'C');
	    $pdf->SetX(100);
	    $pdf->Cell(56, 7, $dato['lat'] . '                                   ' . $dato['lng'], 0, 0, 'C');
	    $pdf->Ln(0);
	    $pdf->SetX(77);
	    $pdf->SetFont('Helvetica', 'B', 7);
	    $pdf->Cell(56, 13, 'LATITUD', 0, 0, 'C');
	    $pdf->SetX(125);
	    $pdf->SetFont('Helvetica', 'B', 7);
	    $pdf->Cell(53, 13, 'LONGITUD', 0, 0, 'C');


	// Aqui empieza la tabla de atributos de la tierra
	    $pdf->SetX(20);
	    $pdf->Ln(7);
	    $pdf->Ln(5);
	    $pdf->SetFont('Helvetica', 'B', 15);
	    $pdf->SetTextColor(0, 0, 0);
	    $pdf->Cell(0, 12, compat_utf8_decode('CARACTERÍSTICAS DEL MAGUEY'), 0, 5, 'C');
	    $pdf->SetFillColor(85, 107, 47);
	    $pdf->SetTextColor(255, 255, 255);
	    $pdf->SetFont('Helvetica', 'B', 8);
	    $pdf->Cell(60, 5, compat_utf8_decode('TIPO DE MAGUEY'), 1, 0, 'C', 1);
	    $pdf->Cell(25, 5, 'No. DE PLANTAS', 1, 0, 'C', 1);
	    $pdf->Cell(29, 5, compat_utf8_decode('FECHA DE SIEMBRA'), 1, 0, 'C', 1);
	    $pdf->Cell(30, 5, compat_utf8_decode('ORIGEN'), 1, 0, 'C', 1);
	    $pdf->Cell(38, 5, compat_utf8_decode('SISTEMA DE PLANTACIÓN'), 1, 0, 'C', 1);




	    $pdf->Ln(5);
	    $consultandovive = "SELECT genespecie,fecha_siembra,foto1,foto2 from paraje_vivero inner join existenciaplanta_vivero on existenciaplanta_vivero.id_paraje=paraje_vivero.id_paraje inner join comun on comun.id_comun=existenciaplanta_vivero.id_comun inner join especie on especie.id_especie=comun.id_especie WHERE  paraje_vivero.id_paraje='$paraje'";
	    $historialito = $conexion->query($consultandovive);
	    $result = mysqli_fetch_array($historialito) ?? [
		'genespecie'=>'','fecha_siembra'=>null,'foto1'=>'','foto2'=>'',
	    ];

	    $strConsulta = "SELECT paraje_vivero.id_paraje,origen, existenciaplanta_vivero.regmaguey, existenciaplanta_vivero.cantidadini,fecha_siembra,existenciaplanta_vivero.edad, comun.nombre,especie.genespecie,especie.variante
		FROM existenciaplanta_vivero
		Inner Join comun ON comun.id_comun= existenciaplanta_vivero.id_comun 
		Inner Join especie ON comun.id_especie = especie.id_especie
		Inner Join paraje_vivero ON paraje_vivero.id_paraje=existenciaplanta_vivero.id_paraje
		WHERE  paraje_vivero.id_paraje='$paraje'";
	    $historial = $conexion->query($strConsulta);
	    $numfilas = mysqli_num_rows($historial);
	    $pdf->SetFont('Arial', 'B', 9);
	    $pdf->SetFillColor(255, 255, 255);
	    $pdf->SetTextColor(0, 0, 0);

	    setlocale(LC_ALL, "es_ES@euro", "Es_ES", "esp");
	    $ds = $result['fecha_siembra'];
	    $fechas = compat_strftime("%d-%b-%Y", strtotime($ds));
	    $fechasi = ucfirst(strtolower($fechas));
	    $cientifico = compat_utf8_decode(ucfirst(strtolower($result['genespecie'])));
	    $cien = $cientifico;

	    for ($i = 0; $i < $numfilas; $i++) {

	        while ($resultado = mysqli_fetch_array($historial)) {
	            $pdf->SetFont('Helvetica', 'BI', 8);
	            $pdf->Cell(60, 5, compat_utf8_decode(strtoupper($resultado['nombre'])) . " (" . compat_utf8_decode(ucfirst(strtolower($resultado['genespecie']))) . ")", 1, 0, 'C');
	            $pdf->SetFont('Helvetica', 'BI');
	            //$pdf->Cell(40,5, compat_utf8_decode(ucfirst(strtolower($resultado['genespecie']))).$cientifico,1,0,'C');
	            $pdf->SetFont('Helvetica', 'B');
	            $pdf->Cell(25, 5, $resultado['cantidadini'], 1, 0, 'C');
	            $fechas = compat_strftime("%d-%b-%Y", strtotime($resultado['fecha_siembra']));
	            $fechasi = ucfirst(strtolower($fechas));
	            $pdf->Cell(29, 5, $fechasi, 1, 0, 'C');
	            $pdf->Cell(30, 5, strtoupper($resultado['origen']), 1, 0, 'C');
	            $pdf->SetFont('Helvetica', 'B');
	            $pdf->Cell(38, 5, strtoupper($resultado['regmaguey']), 1, 0, 'C');

	            $pdf->Ln();
	        }
	    } {

	        $pdf->Ln(8);

	        $pdf->SetFont('Helvetica', 'B', 10);
	        $pdf->SetX(135);
	        $pdf->Cell(88, 20, $pdf->Image("images/firmaa.png", $pdf->GetX(), $pdf->GetY(), 30.78), 0, 0, 'C', false);
	        $pdf->Ln(0);
	        $pdf->SetX(45);
	        $pdf->Cell(88, 20, $pdf->Image("images/firmae.jpg", $pdf->GetX(), $pdf->GetY(), 40.78), 0, 0, 'C', false);
	        $pdf->Ln(18);
	        $pdf->cell(88, 5, compat_utf8_decode('M. EN C. EFRAÍN PAREDES HERNÁNDEZ'), 0, 0, 'C');
	        //$pdf->cell(88,5,compat_utf8_decode('DR. EN C. HIPÓCRATES NOLASCO CANCINO'),0,0,'C');
	        $pdf->cell(88, 5, compat_utf8_decode('Q. B. ABELINO COHETERO VILLEGAS'), 0, 0, 'C');
	        $pdf->Ln(0);
	        $pdf->cell(88, 5, compat_utf8_decode('_______________________________________'), 0, 0, 'C');
	        $pdf->cell(88, 5, compat_utf8_decode('_______________________________________'), 0, 0, 'C');
	        $pdf->Ln(5);
	        $pdf->SetFont('Helvetica', 'B', 9);
	        $pdf->cell(88, 5, compat_utf8_decode(strtoupper('Cordinador de la Unidad de Maguey')), 0, 0, 'C');
	        $pdf->cell(88, 5, compat_utf8_decode(strtoupper('Presidente')), 0, 0, 'C');
	    }


	    $pdf->Ln(40);


	    $Consulta = "SELECT * FROM paraje_vivero WHERE paraje_vivero.id_paraje='$paraje'";
	    $historial = $conexion->query($Consulta);



	    while ($resultado = mysqli_fetch_array($historial)) {
	        $id = $resultado['id_paraje'];
	        $coordenada1 = $resultado['lat'];
	        $coordenada2 = $resultado['lng'];
	    }


	    /* aqui para imprimir estados con Dom */
	    $strConsulta = "SELECT paraje_vivero.*,paraje_vivero.foto1,paraje_vivero.foto2, estados.ubica as enombreee,estados.nombre from estados inner join municipios on municipios.estado=estados.clave inner join localidades on localidades.MunicipioID=municipios.id inner join paraje_vivero on paraje_vivero.id_localidad=localidades.id where  paraje_vivero.id_paraje='$paraje'";
	    $parajes = $conexion->query($strConsulta);
	    //$parajes = mysql_query($strConsulta);
	    $fila = mysqli_fetch_array($parajes) ?? ['id'=>null,'id_paraje'=>null,'id_localidad'=>null,'id_cliente'=>null,'paraje'=>'','lat'=>null,'lng'=>null,'poligono'=>'','tenencia'=>'','superficie'=>'','docpro'=>'','referencia'=>'','usufruto'=>'','fecha'=>null,'nombrep'=>'','fecha_paraje'=>null,'rcampo'=>'','status'=>null,'foto1'=>'','foto2'=>'','tipo'=>'','constancia_vivero'=>'','status_predio'=>null,'constancia_extracciones'=>null,'enombreee'=>'','nombre'=>''];
	    //Aqui termina


	    $urlGoogle = "http://maps.googleapis.com/maps/api/staticmap?key=AIzaSyCD3xqb8eMEVsAd4m9QnD7s1wOE9_bnALY&center=$coordenada1,$coordenada2&zoom=8&scale=false&size=600x300&maptype=hybrid&format=png&visual_refresh=true&markers=size:mid%7Ccolor:0xff0000%7Clabel:*%7C$coordenada1,$coordenada2";
	    $urlGooglec = "http://maps.googleapis.com/maps/api/staticmap?key=AIzaSyCD3xqb8eMEVsAd4m9QnD7s1wOE9_bnALY&center=$coordenada1,$coordenada2&zoom=6&scale=false&size=600x300&maptype=hybrid&format=png&visual_refresh=true&markers=size:mid%7Ccolor:0xff0000%7Clabel:*%7C$coordenada1,$coordenada2";



	    $pdf->AddPage();
	    $pdf->SetFont('Helvetica', 'B', 20);
	    $pdf->Cell(0, 4, compat_utf8_decode('VIVERO REGISTRADO'), 0, 5, 'C');
	    $pdf->Cell(0, 4, compat_utf8_decode('________________________________'), 0, 5, 'C');
	    $pdf->Cell(140, 120);

	    //$pdf->Image('estadosDOM/oaxaca.png', 90, 35, 40, 30, "PNG");
	    //$pdf->Image("../".$fila['foto1'], 15, 135, 185, 120);
	    $pdf->Image($urlGoogle, 110, 50, 90, 70, "PNG");
	    //$pdf->Image($urlGoogle, 15,80, 100, 60, "PNG");
	    $pdf->Image($urlGooglec, 15, 50, 90, 70, "PNG");
	    //$pdf->Image($urlGoogleg, 15, 135, 185, 120, "PNG");
	    $pdf->Image("../" . $result['foto2'], 15, 125, 90, 90);
	    $pdf->Image("../" . $result['foto1'], 110, 125, 90, 90);
	}

	ob_end_clean();
	//$pdf->Output("RegistroVivero".$paraje.".pdf",'D');

	$conexion->close();
	$conexion_remota->close();



	$pdf->Output($destino, 'F');
}

//$pdf->Output($nuevoNombre, 'D');
?>
