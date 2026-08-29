<?php
/**
 * MIGRADO: FPDF clásico (libs/fpdf/fpdf.php) -> setasign/fpdf (^1.9) vía Composer.
 *
 * setasign/fpdf expone la misma clase global `FPDF` (sin namespace) con
 * idéntica API, así que PDF_MC_Table no necesitó cambios en su lógica.
 *
 * Único detalle real: tu fuente personalizada 'RomanaBT-Bold' se carga desde
 * un archivo de definición clásico (RomanaBT.php). setasign/fpdf TODAVÍA
 * soporta ese formato (solo emite un aviso E_USER_DEPRECATED, no rompe nada).
 *
 * IMPORTANTE: NO se debe sobreescribir FPDF_FONTPATH globalmente, porque las
 * fuentes "core" (Arial/Helvetica, Times, Courier...) que usa el resto de tus
 * reportes (SetFont('Arial', ...)) ahora viven como .json dentro de
 * vendor/setasign/fpdf/font/, y si rediriges el fontpath global se rompe su
 * resolución (justo el error "Could not load font definition file:
 * .../helveticab.json"). Por eso la ruta a la carpeta vieja se pasa SOLO en
 * la llamada a AddFont() de RomanaBT-Bold, como 4º parámetro.
 *
 * AJUSTA la ruta de abajo si RomanaBT.php no está en libs/fpdf/font/.
 */
require_once __DIR__ . '/../../../vendor/autoload.php';

define('AMMA_CUSTOM_FONT_DIR', __DIR__ . '/../../../libs/fpdf/font/');

class PDF_MC_Table extends FPDF
{
    function Header()
	{
		//Logo
		$this->Image('../../../images/logo_amma.jpg',8,6,33);
		//Arial bold 15
		$this->SetTextColor(0,0,0);
        $fontDir = __DIR__ . '/../../../librerias/fpdf/font/json/';
                $this->AddFont('RomanaBT-Bold','B','RomanaBT-Bold.json',$fontDir);
		$this->SetFont('RomanaBT-Bold','B',18);
		//Movernos a la derecha
		$this->Cell(80);
		//Título
		$this->SetXY(90,10);
		$this->Cell(101,10,'Asociaci�n de Maguey y Mezcal Artesanal',0,0,'C');
		//Salto de línea
		$this->Ln(20);

	}
	function Footer()
	{
		$this->SetTextColor(0,0,0);
		global $fecha;
		$this->SetXY(240,-15);
		$this->SetFont('Arial','',8);
                $this->Cell(0,10,'P�gina '.$this->PageNo().' de {nb}',0,0,'C');
	}
var $col=0;
var $widths;
var $aligns;

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
	$h=7*$nb;
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
		$this->Rect($x,$y,$w,$h,'F');
		//Print the text
		$this->MultiCell($w,5,$data[$i],0,$a,1);
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
}


