<?php
require('../../../libs/fpdf/fpdf.php');
class PDF_MC_Table extends FPDF
{

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
	
function Header()
	{
		/*
		 //Logo
		$this->Image('../../images/logo.jpg',8,6,33);
		//Arial bold 15
		
        $this->AddFont('RomanaBT-Bold','B','RomanaBT.php');	
		$this->SetFont('RomanaBT-Bold','B',18);
		//Movernos a la derecha
		$this->Cell(80);
		//Título
		$this->SetXY(90,10);
		$this->Cell(101,10,utf8_decode('Consejo Regulador del Mezcal'),0,0,'C');
		//Salto de línea
		$this->Ln(20);*/

				
	}
	function Footer()
	{
		/*global $fecha;
		$this->SetXY(160,-15);
		$this->SetFont('Arial','',8);		
		$this->Cell(0,10,utf8_decode('Página '.$this->PageNo().' de {nb}'),0,0,'C');*/
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
