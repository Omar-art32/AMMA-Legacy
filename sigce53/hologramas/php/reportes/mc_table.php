<?php
// Definir la ruta física de la carpeta 'font' de FPDF

// 1. Definir la ruta exacta de la carpeta font
define('FPDF_FONTPATH', $_SERVER['DOCUMENT_ROOT'] . '/sigce53/libs/fpdf/font/');

// 2. Requerir el archivo fpdf.php usando ruta absoluta
require_once($_SERVER['DOCUMENT_ROOT'] . '/sigce53/libs/fpdf/fpdf.php');

class PDF_MC_Table extends FPDF
{
	function __construct($orientation='P', $unit='mm', $size='A4')
    {
        // Compatibilidad de constructor para FPDF en PHP 8.3
        if (method_exists('FPDF', '__construct')) {
            parent::__construct($orientation, $unit, $size);
        } else {
            $this->FPDF($orientation, $unit, $size);
        }
    }

	function Header()
    {
        // Asegurar la ruta de las fuentes para la tipografía original
        if (!defined('FPDF_FONTPATH')) {
            define('FPDF_FONTPATH', $_SERVER['DOCUMENT_ROOT'] . '/sigce53/libs/fpdf/font/');
        }

        // Cargar imagen con validación de existencia
        $logoPath = $_SERVER['DOCUMENT_ROOT'] . '/sigce53/images/logo_amma.jpg';
        if (file_exists($logoPath) && is_readable($logoPath)) {
            $this->Image($logoPath, 8, 6, 33);
        }

        // fuente tipográfica original de la plantilla (RomanaBT-Bold)
        $this->SetTextColor(0, 0, 0);
        $this->AddFont('RomanaBT-Bold', 'B', 'RomanaBT.php');
        $this->SetFont('RomanaBT-Bold', 'B', 18);
        
        $this->Cell(80);
        
        //  Codificación compatible con PHP 8.3 (remplaza utf8_decode)
        $texto_titulo = mb_convert_encoding('Asociación de Maguey y Mezcal Artesanal', 'ISO-8859-1', 'UTF-8');
        
        $this->SetXY(90, 10);
        $this->Cell(101, 10, $texto_titulo, 0, 0, 'C');
        $this->Ln(20);
    }

	function Footer()
	{
		$this->SetTextColor(0,0,0);
		global $fecha;
		$this->SetXY(240,-15);
		$this->SetFont('Arial','',8);
		
		// Reemplazo de utf8_decode por mb_convert_encoding
		$texto_pagina = mb_convert_encoding('Página '.$this->PageNo().' de {nb}', 'ISO-8859-1', 'UTF-8');
		
		$this->Cell(0,10,$texto_pagina,0,0,'C');
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
?>
