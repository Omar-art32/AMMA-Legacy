<?php
require('../../libs/fpdf/fpdf.php');
include('../../common/conexion.php');

$conexion->set_charset('utf8');

function pdf_txt($value)
{
    $value = $value === null || $value === '' ? ' ' : $value;
    $value = (string)$value;
    $isUtf8 = function_exists('mb_check_encoding')
        ? mb_check_encoding($value, 'UTF-8')
        : preg_match('//u', $value);

    if ($isUtf8) {
        if (function_exists('iconv')) {
            $converted = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $value);
            if ($converted !== false) {
                return $converted;
            }
        }
        return mb_convert_encoding($value, 'ISO-8859-1', 'UTF-8');
    }

    return $value;
}

function pdf_upper($value)
{
    $value = $value === null || $value === '' ? ' ' : (string)$value;
    $isUtf8 = function_exists('mb_check_encoding')
        ? mb_check_encoding($value, 'UTF-8')
        : preg_match('//u', $value);

    if ($isUtf8 && function_exists('mb_strtoupper')) {
        return pdf_txt(mb_strtoupper($value, 'UTF-8'));
    }

    return pdf_txt(strtoupper($value));
}

function pdf_lower_value($value)
{
    $value = $value === null || $value === '' ? ' ' : (string)$value;
    $isUtf8 = function_exists('mb_check_encoding')
        ? mb_check_encoding($value, 'UTF-8')
        : preg_match('//u', $value);

    if ($isUtf8 && function_exists('mb_strtolower')) {
        return mb_strtolower($value, 'UTF-8');
    }

    return strtolower($value);
}

function pdf_title_value($value)
{
    $value = $value === null || $value === '' ? ' ' : (string)$value;
    $isUtf8 = function_exists('mb_check_encoding')
        ? mb_check_encoding($value, 'UTF-8')
        : preg_match('//u', $value);

    if ($isUtf8 && function_exists('mb_convert_case')) {
        return mb_convert_case(pdf_lower_value($value), MB_CASE_TITLE, 'UTF-8');
    }

    return ucwords(strtolower($value));
}

function pdf_sentence_value($value)
{
    $value = pdf_lower_value($value);
    if (function_exists('mb_substr') && function_exists('mb_strtoupper')) {
        return mb_strtoupper(mb_substr($value, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($value, 1, null, 'UTF-8');
    }

    return ucfirst($value);
}

function fecha_mx_v2($value)
{
    if ($value === null || $value === '' || $value === '0000-00-00') {
        return ' ';
    }
    return date('d/m/Y', strtotime($value));
}

class PDFFormatoPueblaV2 extends FPDF
{
    var $widths;
    var $aligns;

    function __construct($orientation = 'P', $unit = 'mm', $format = 'Letter')
    {
        parent::FPDF($orientation, $unit, $format);
        $this->AddFont('Calibri', '', 'CalibriRegular.php');
        $this->AddFont('Calibri-Bold', '', 'CalibriBold.php');
        $this->AddFont('Calibri-BoldItalic', '', 'CalibriBoldItalic.php');
        $this->fontlist = array('Calibri', 'Calibri-Bold', 'Calibri-BoldItalic', 'Times', 'times', 'courier', 'helvetica', 'symbol');
        $this->SetMargins(20, 15, 20);
        $this->SetAutoPageBreak(false, 40);
    }

    function Header()
    {
        $bgNuevo = __DIR__ . '/../images/bg_amma_v.jpeg';
        $bgActual = __DIR__ . '/../../maguey/images/bg_amma_v.jpeg';
        $bg = file_exists($bgNuevo) ? $bgNuevo : $bgActual;
        if (file_exists($bg)) {
            $this->Image($bg, 0, 0, $this->w, $this->h + 9);
        }
    }

    function Footer()
    {
        $this->SetY(-36);
        $this->SetFont('Calibri-Bold', '', 7);
        $this->Cell(0, 5, pdf_txt('ESTE PREDIO O VIVERO SE ENCUENTRA DENTRO DEL TERRITORIO PROTEGIDO POR LA DENOMINACIÓN DE ORIGEN MEZCAL.'), 0, 1, 'C');
        $this->SetFont('Calibri', '', 7);
        $this->Cell(0, 5, pdf_txt('ESTE DOCUMENTO NO ES UN INSTRUMENTO LEGAL, UNICAMENTE AMPARA EL REGISTRO DE LA PLANTACIÓN DEL MAGUEY.'), 0, 1, 'C');
        //$this->Cell(0, 5, pdf_txt('Pagina ') . $this->PageNo() . '/{nb}', 0, 0, 'L');
        $this->Cell(0, 5, pdf_txt('FOR-UM-02/02.'), 0, 1, 'R');
        $this->Cell(0, 5, pdf_txt('Av. Universidad No. 312-A, Fracc. Trinidad de las Huertas, Oaxaca de Juárez, C. P. 68120, Oaxaca, Mexico'), 0, 1, 'C');
        $this->Cell(0, 5, pdf_txt('www.amma.org.mx   maguey@amma.org.mx  Teléfonos: 951 672 9399, 951 672 9474'), 0, 1, 'C');
    }

    function SetWidths($w)
    {
        $this->widths = $w;
    }

    function SetAligns($a)
    {
        $this->aligns = $a;
    }

    function Row($data, $height = 5, $fill = false)
    {
        $nb = 0;
        for ($i = 0; $i < count($data); $i++) {
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        }
        $h = $height * $nb;
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            $x = $this->GetX();
            $y = $this->GetY();
            $this->Rect($x, $y, $w, $h);
            $this->MultiCell($w, $height, $data[$i], 0, $a, $fill);
            $this->SetXY($x + $w, $y);
        }
        $this->Ln($h);
    }

    function NbLines($w, $txt)
    {
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0) {
            $w = $this->w - $this->rMargin - $this->x;
        }
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n") {
            $nb--;
        }
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
            if ($c == ' ') {
                $sep = $i;
            }
            $l += isset($cw[$c]) ? $cw[$c] : 0;
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) {
                        $i++;
                    }
                } else {
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else {
                $i++;
            }
        }
        return $nl;
    }
}

function cell_title_value($pdf, $title, $value, $x, $y, $w, $h, $caption)
{
    $pdf->SetXY($x, $y);
    $pdf->Rect($x, $y, $w, $h);
    $pdf->SetFont('Calibri-Bold', '', 9);
    $pdf->Cell($w, 4.5, pdf_txt($value), 0, 0, 'C');
    $pdf->SetXY($x, $y + 4.5);
    $pdf->SetFont('Calibri-Bold', '', 7);
    $pdf->Cell($w, 3, pdf_upper($caption), 0, 0, 'C');
}

function cell_value_caption($pdf, $value, $x, $y, $w, $h, $caption, $valueSize = 8, $captionSize = 6)
{
    $pdf->SetXY($x, $y);
    $pdf->Rect($x, $y, $w, $h);
    $pdf->SetFont('Calibri-Bold', '', $valueSize);
    $pdf->Cell($w, 4.5, pdf_txt($value), 0, 0, 'C');
    $pdf->SetXY($x, $y + 4.7);
    $pdf->SetFont('Calibri-Bold', '', $captionSize);
    $pdf->Cell($w, 3, pdf_upper($caption), 0, 0, 'C');
}

function fit_cell($pdf, $x, $y, $w, $h, $text, $size = 8, $style = 'Calibri-Bold', $align = 'C')
{
    $text = pdf_txt($text);
    $fontSize = $size;
    do {
        $pdf->SetFont($style, '', $fontSize);
        $fontSize -= 0.5;
    } while ($pdf->GetStringWidth($text) > ($w - 2) && $fontSize >= 5);
    $pdf->SetXY($x, $y);
    $pdf->Cell($w, $h, $text, 0, 0, $align);
}

function fit_cell_left($pdf, $x, $y, $w, $h, $text, $size = 8, $style = 'Calibri-Bold', $align = 'L')
{
    $text = pdf_txt($text);
    $fontSize = $size;
    do {
        $pdf->SetFont($style, '', $fontSize);
        $fontSize -= 0.5;
    } while ($pdf->GetStringWidth($text) > ($w - 2) && $fontSize >= 5);
    $pdf->SetXY($x, $y);
    $pdf->Cell($w, $h, $text, 0, 0, $align);
}

$idIn = isset($_GET['id']) ? trim($_GET['id']) : '';
if ($idIn === '') {
    die('No se recibio el predio.');
}

$sql = $conexion->prepare("SELECT p.id, p.id_paraje, p.id_cliente, p.paraje, p.lat, p.lng, p.tenencia,
                                  p.superficie, p.docpro, p.referencia, p.usufruto, p.fecha, p.nombrep,
                                  p.rcampo, p.tipo, p.fecharegistro,
                                  c.no_cliente, UPPER(c.nombre) AS cliente, dt.telefono, dc.correo,
                                  -- CONCAT_WS(', ', c.calle, c.noexterior, c.nointerior, c.colonia, mun_cli.nombre, edo_cli.nombre) AS domicilio,
                                  CONCAT(d.calle, ' #', d.noexterior,', ',d.colonia,', ',mun.nombre,', ',es.nombre) as domicilio,
                                  l.localidad, mun.nombre AS municipio, es.nombre AS estado,
                                  LPAD(const.id_constancia,4,'0') constancia, SUBSTRING(p.fecha_paraje, 3, 2) anio
                             FROM paraje p
                             LEFT JOIN clientes c ON p.id_cliente = c.no_cliente 
                             LEFT JOIN domicilio d on d.no_cliente=c.no_cliente 
                             LEFT JOIN localidades l ON p.id_localidad = l.id
                             LEFT JOIN municipios mun ON d.municipio = mun.id
                             -- LEFT JOIN municipios mun ON l.MunicipioID = mun.id
                             LEFT JOIN estados es ON mun.estado = es.clave 
                             LEFT JOIN constancias const on (const.id_paraje=p.id_paraje COLLATE utf8_general_ci) 
                             LEFT JOIN (
								SELECT c.no_cliente no_cliente, GROUP_CONCAT(t.numero) telefono 
								FROM clientes c
								INNER JOIN clientes_telefonos ct ON ct.cliente = c.no_cliente  
								LEFT JOIN telefonos t ON t.id = ct.telefono 
								WHERE t.tipo = 0 -- AND t.status = '2' 
								GROUP BY c.no_cliente
                             ) AS dt ON c.no_cliente = dt.no_cliente 
                            LEFT JOIN (
								SELECT ce.`id`, c.no_cliente, GROUP_CONCAT(ce.`correo`) correo
								FROM `correos_electronicos` ce
								INNER JOIN clientes_correos cc ON cc.correo=ce.id 
								INNER JOIN clientes c ON c.no_cliente = cc.cliente 
								WHERE ce.correo NOT LIKE 'registros.uac%' 
								GROUP BY c.no_cliente
								ORDER BY ce.principal DESC
                             ) AS dc ON c.no_cliente = dc.no_cliente 
                            WHERE p.id = ? OR p.id_paraje = ?
                            LIMIT 1");
if (!$sql) {
    die($conexion->error);
}
$idNum = intval($idIn);
$sql->bind_param('is', $idNum, $idIn);
$sql->execute();
$res = $sql->get_result();
$predio = $res->fetch_assoc();
$sql->close();

if (!$predio) {
    die('No se encontro el predio solicitado.');
}

$plantas = array();
$sql = $conexion->prepare("SELECT ep.regmaguey, ep.cantidadini, ep.edad, co.nombre, esp.genespecie, esp.variante
                             FROM existenciaplanta ep
                             INNER JOIN comun co ON co.id_comun = ep.id_comun
                             INNER JOIN especie esp ON co.id_especie = esp.id_especie
                            WHERE ep.id_paraje = ?
                            ORDER BY ep.id_plantas");
$sql->bind_param('s', $predio['id_paraje']);
$sql->execute();
$res = $sql->get_result();
while ($row = $res->fetch_assoc()) {
    $plantas[] = $row;
}
$sql->close();

$atributos = array();
$sql = $conexion->prepare("SELECT pa.id, pa.atributo, paa.nivel
                             FROM paraje_atributo pa
                             LEFT JOIN parajes_atributos_asignar paa
                               ON paa.atributo_id = pa.id
                              AND paa.id_paraje = ?
                              AND paa.estatus = '1'
                              AND (paa.tipo = 'PUE' OR paa.tipo IS NULL)
                            WHERE pa.estatus = '1'
                            ORDER BY pa.id");
$sql->bind_param('s', $predio['id_paraje']);
$sql->execute();
$res = $sql->get_result();
while ($row = $res->fetch_assoc()) {
    $atributos[intval($row['id'])] = $row;
}
$sql->close();

$pdf = new PDFFormatoPueblaV2('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->AddPage();

$fecha = $predio['fecha'] !== null ? $predio['fecha'] : $predio['fecharegistro'];
$idCodigo = 'P' . str_pad(preg_replace('/[^0-9]/', '', $predio['id_paraje']), 4, '0', STR_PAD_LEFT);
$correo = strpos((string)$predio['correo'], 'egistros.uac') !== false ? ' ' : $predio['correo'];

$pdf->SetXY(26, 30);
$pdf->SetFont('Calibri-Bold', '', 23);
$pdf->Cell(0, 8, pdf_txt('REGISTRO DE PLANTACIONES'), 0, 1, 'C');
$pdf->SetFont('Calibri-Bold', '', 13);
$pdf->Rect(35, 43, 10, 8, 'D');
$pdf->SetXY(45, 43);
$pdf->Cell(20, 8, pdf_txt('VIVERO'), 0, 0, 'C');
$pdf->Rect(145, 43, 10, 8, 'D');
$pdf->SetFont('Calibri-Bold', '', 17);
$pdf->SetXY(148, 43);
$pdf->Cell(5, 8, 'X', 0, 0, 'C');
$pdf->SetFont('Calibri-Bold', '', 13);
$pdf->SetXY(155, 43);
$pdf->Cell(20, 8, pdf_txt('PREDIO'), 0, 1, 'C');
$pdf->SetXY(20, 53);
$pdf->Cell(0, 3, pdf_txt('NO. DE CONTROL: ') . $predio['no_cliente'], 0, 1, 'C');

$pdf->SetTextColor(238, 55, 60);
$pdf->SetFont('Calibri-Bold', '', 14);
$pdf->Text(175, 30, pdf_txt($predio['constancia'].$idCodigo.$predio['anio']));
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Calibri-Bold', '', 8);
$pdf->Text(185, 34, 'No.:');
$pdf->Text(166, 38, pdf_txt('FECHA DE EMISIÓN:'));
$pdf->Text(178, 42, 'Vigencia:');
$pdf->SetFont('Calibri-Bold', '', 9);
$pdf->Text(194, 34, pdf_txt($idCodigo));
$pdf->Text(194, 38, pdf_txt(fecha_mx_v2($fecha)));
$pdf->Text(194, 42, 'INDEFINIDA');

$pdf->SetXY(20, 58);
$pdf->SetFont('Calibri-Bold', '', 12);
$pdf->MultiCell(176, 7, pdf_upper($predio['cliente']), 0, 'C');

$domicilio = trim(preg_replace('/,\s*,/', ',', (string)$predio['domicilio']), ', ');
$pdf->Ln(3);
$y = $pdf->GetY();
$x0 = 20;
$wLabel = 45;
$wTel = 30;
$wMailLabel = 45;
$wMail = 56;
$wLeft = 65;
$wRight = 66;

$pdf->SetFont('Calibri-Bold', '', 10);
$pdf->Rect($x0, $y, $wLabel, 7);
$pdf->Rect($x0 + $wLabel, $y, 131, 7);
fit_cell($pdf, $x0, $y, $wLabel, 7, 'DOMICILIO FISCAL:', 9);
fit_cell_left($pdf, $x0 + $wLabel, $y, 131, 7, pdf_title_value($domicilio), 7.5);

$y += 7;
$pdf->Rect($x0, $y, $wLabel, 7);
$pdf->Rect($x0 + $wLabel, $y, $wTel, 7);
$pdf->Rect($x0 + $wLabel + $wTel, $y, $wMailLabel, 7);
$pdf->Rect($x0 + $wLabel + $wTel + $wMailLabel, $y, $wMail, 7);
fit_cell($pdf, $x0, $y, $wLabel, 7, 'TELÉFONO:', 9);
fit_cell($pdf, $x0 + $wLabel, $y, $wTel, 7, $predio['telefono'], 8);
fit_cell($pdf, $x0 + $wLabel + $wTel, $y, $wMailLabel, 7, 'CORREO ELECTRÓNICO:', 8.5);
fit_cell($pdf, $x0 + $wLabel + $wTel + $wMailLabel, $y, $wMail, 7, $correo === '' ? '---' : $correo, 7.5);

$y += 7;
$pdf->Rect($x0, $y, $wLabel, 16);
fit_cell($pdf, $x0, $y, $wLabel, 16, 'UBICACIÓN', 10);
cell_title_value($pdf, '', $predio['paraje'], $x0 + $wLabel, $y, $wLeft, 8, 'Predio');
cell_title_value($pdf, '', $predio['localidad'], $x0 + $wLabel + $wLeft, $y, $wRight, 8, 'Localidad');
cell_title_value($pdf, '', $predio['municipio'], $x0 + $wLabel, $y + 8, $wLeft, 8, 'Municipio');
cell_title_value($pdf, '', $predio['estado'], $x0 + $wLabel + $wLeft, $y + 8, $wRight, 8, 'Estado');

$y += 16;
$pdf->Rect($x0, $y, $wLabel, 9);
fit_cell($pdf, $x0, $y, $wLabel, 9, 'SUPERFICIE', 10);
cell_value_caption($pdf, $predio['superficie'], $x0 + $wLabel, $y, $wTel, 9, 'Hectareas', 8, 6);
$pdf->Rect($x0 + $wLabel + $wTel, $y, $wMailLabel, 9);
fit_cell($pdf, $x0 + $wLabel + $wTel, $y + 0.8, $wMailLabel, 4, 'COORDENADAS', 9);
fit_cell($pdf, $x0 + $wLabel + $wTel, $y + 4.1, $wMailLabel, 3, 'GEOGRÁFICAS', 6);
cell_value_caption($pdf, number_format($predio['lat'], 6) , $x0 + $wLabel + $wTel + $wMailLabel, $y, 28, 9, 'Latitud', 7.5, 5.5);
cell_value_caption($pdf, number_format($predio['lng'], 6) , $x0 + $wLabel + $wTel + $wMailLabel + 28, $y, 28, 9, 'Longitud', 7.5, 5.5);

$pdf->SetXY(20, $y + 17);
$pdf->SetFont('Calibri-Bold', '', 15);
$pdf->Cell(176, 9, pdf_txt('CARACTERÍSTICAS DEL MAGUEY'), 0, 1, 'C');
$pdf->SetFillColor(85, 107, 47);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Calibri-Bold', '', 8);
$pdf->Cell(92, 5, pdf_txt('TIPO DE MAGUEY'), 1, 0, 'C', 1);
$pdf->Cell(25, 5, pdf_txt('No. DE PLANTAS'), 1, 0, 'C', 1);
$pdf->Cell(21, 5, pdf_txt('EDAD (AÑOS)'), 1, 0, 'C', 1);
$pdf->Cell(38, 5, pdf_txt('SISTEMA DE PLANTACIÓN'), 1, 1, 'C', 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Calibri-Bold', '', 8);

$maxPlantas = 3;
for ($i = 0; $i < $maxPlantas; $i++) {
    $planta = isset($plantas[$i]) ? $plantas[$i] : null;
    if(isset($plantas[$i])){
        $comun = $planta ? $planta['nombre'] : ' ';
        $especie = $planta ? pdf_sentence_value($planta['genespecie']) : ' ';
        $pdf->Cell(52, 5, pdf_upper($comun), 1, 0, 'C');
        $pdf->SetFont('Calibri-BoldItalic', '', 8);
        $pdf->Cell(40, 5, pdf_txt($especie), 1, 0, 'C');
        $pdf->SetFont('Calibri-Bold', '', 8);
        $pdf->Cell(25, 5, pdf_txt($planta ? $planta['cantidadini'] : ' '), 1, 0, 'C');
        $pdf->Cell(21, 5, pdf_txt($planta ? $planta['edad'] : ' '), 1, 0, 'C');
        $pdf->Cell(38, 5, pdf_upper($planta ? $planta['regmaguey'] : ' '), 1, 1, 'C');
    }
}

$pdf->Ln(3);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Calibri-Bold', '', 13);
$pdf->Cell(176, 8, pdf_txt('NIVEL DE CUMPLIMIENTO DE ATRIBUTOS DE SUSTENTABILIDAD'), 0, 1, 'C');
$pdf->SetFillColor(85, 107, 47);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Calibri-Bold', '', 7);
$pdf->Cell(70, 5, pdf_txt('BUENAS PRÁCTICAS EN PRODUCCIÓN DE AGAVE'), 1, 0, 'C', 1);
for ($i = 0; $i <= 4; $i++) {
    $pdf->Cell(21.2, 5, (string)$i, 1, 0, 'C', 1);
}
$pdf->Ln(5);

$filasAtributos = array(
    10 => 'Preservación de la diversidad biológica de las plantas de agave',
    11 => 'Conservación de suelo y agua',
    12 => 'Manejo tradicional u Organico',
    13 => 'Manejo integrado de plagas, enfermedades y malezas',
);
$pdf->SetWidths(array(70, 21.2, 21.2, 21.2, 21.2, 21.2));
$pdf->SetAligns(array('L', 'C', 'C', 'C', 'C', 'C'));
foreach ($filasAtributos as $idAtributo => $nombreAtributo) {
    $nivel = isset($atributos[$idAtributo]) && $atributos[$idAtributo]['nivel'] !== null ? (string)$atributos[$idAtributo]['nivel'] : '';
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Calibri-Bold', '', 7);
    $pdf->Row(array(
        pdf_txt($nombreAtributo),
        pdf_txt($nivel === '0' ? 'X' : ''),
        pdf_txt($nivel === '1' ? 'X' : ''),
        pdf_txt($nivel === '2' ? 'X' : ''),
        pdf_txt($nivel === '3' ? 'X' : ''),
        pdf_txt($nivel === '4' ? 'X' : '')
    ), 4);
}

/*$pdf->Ln(4);
$pdf->SetFont('Calibri-Bold', '', 8);
$pdf->Cell(176, 5, pdf_txt('CUMPLIMIENTO DEL REGISTRO DE PREDIO:    SI________________          NO________________'), 0, 1, 'C');*/

/*$pdf->Ln(20);
$pdf->SetFont('Calibri-Bold', '', 9);
$pdf->Cell(88, 5, pdf_txt('_______________________________________'), 0, 0, 'C');
$pdf->Cell(88, 5, pdf_txt('_______________________________________'), 0, 1, 'C');
$pdf->Cell(88, 5, pdf_txt('RESPONSABLE EN CAMPO'), 0, 0, 'C');
$pdf->Cell(88, 5, pdf_txt('TECNICO DE CAMPO'), 0, 1, 'C');
$pdf->SetFont('Calibri', '', 8);
$pdf->Cell(88, 4, pdf_txt('NOMBRE COMPLETO Y FIRMA'), 0, 0, 'C');
$pdf->Cell(88, 4, pdf_txt('NOMBRE COMPLETO Y FIRMA'), 0, 1, 'C');*/
/*$pdf->SetFont('Calibri-Bold','',10);
$pdf->SetX(135);
//$pdf->Cell( 88, 20, $pdf->Image("images/firmaa.png", $pdf->GetX(), $pdf->GetY(), 30.78), 0, 0, 'C', false );
$pdf->Ln(0);
$pdf->SetX(45);
//$pdf->Cell( 88, 20, $pdf->Image("images/firmae.jpg", $pdf->GetX(), $pdf->GetY(),40.78), 0,0, 'C', false );
$pdf->Ln(18);
$pdf->cell(88,5,utf8_decode('MSIG. URIEL TERÁN SANGERMÁN'),0,0,'C');
$pdf->Ln(0);
$pdf->cell(88,5,utf8_decode('_______________________________________'),0,0,'C');
$pdf->cell(88,5,utf8_decode('_______________________________________'),0,0,'C');
$pdf->Ln(5);
$pdf->SetFont('Calibri-Bold','',9);
$pdf->cell(88,5,utf8_decode(strtoupper('Gerente de la Unidad de Maguey')),0,0,'C');
$pdf->cell(88,5,utf8_decode(strtoupper('Presidente')),0,0,'C');*/

$conexion->close();
if (ob_get_length()) {
    ob_end_clean();
}

$pdf->Output('Formato_Puebla_V2_' . $predio['id_paraje'] . '.pdf', 'I');
?>
