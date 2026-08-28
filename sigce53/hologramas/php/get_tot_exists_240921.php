<?php
include(__DIR__ . '/../../common/conexion.php');

//obtenemos los parametros de la busqueda
$client=mb_convert_encoding ($_POST['cliente'], 'ISO-8859-1');
$client=substr($client,0,5);
$respuesta="";
$new_ind="";
$old_ind="";
$total=0;
$i=0;

$m="";
$e="";
$aux=1;
$x=0;



$tipo_mez="";

$sql_bus="SELECT marcas.cve_marca,marcas.marca,h_existencias.serie,h_existencias.
fol_ini,h_existencias.fol_fin,if(h_existencias.existencias is null,0,h_existencias.existencias) as existencias,
h_existencias.edo,h_existencias.tipo
FROM marcas LEFT JOIN h_existencias on h_existencias.no_cliente=marcas.no_cliente and marcas.cve_marca=h_existencias.marca
where marcas.no_cliente='$client'  AND marcas.cve_marca != '' 
order by marcas.cve_marca, h_existencias.edo, h_existencias.fol_ini asc  ";

$result=$conexion->query($sql_bus);
// Ahora comprobaremos que todo ha ido correctamente
if($result==false)
{
  echo json_encode(['status' => 'error','msj'=> 'Disculpe ha ocurrido un error, intente mas tarde']);
}
else
{
  $tot=$result->num_rows;
  if($tot>0)
  {
	  $respuesta.="<section id='plans'><div class='container'><div class='row'>";
	  while($row=$result->fetch_row())
	{
	  $cve=trim($row[0]);
	  $marca=mb_convert_encoding(trim($row[1]), 'UTF-8', 'ISO-8859-1');
	  $serie=trim($row[2]);
	  $f_ini=trim($row[3]);
	  $f_fin=trim($row[4]);
	  $existencia=trim($row[5]);
	  $edo=mb_convert_encoding(trim($row[6]), 'UTF-8', 'ISO-8859-1');
	  $new_ind=$cve.$edo;

	  switch($row[7])
	  {
		case 0:
            $tipo_mez="";
            break;
			case 1:
                $tipo_mez="MEZCAL";
                break;
			case 2:
                $tipo_mez="ARTESANAL";
                break;
			case 3:
                $tipo_mez="ANCESTRAL";
                break;
	   }

	  if($existencia>0)
	  {
		$folios=$client.$cve."<font color='#000099'><b>".str_pad($f_ini, 7,'0',STR_PAD_LEFT)."</b></font>".$serie." &nbsp;-&nbsp; ".$client.$cve."<font color='#000099'><b>".str_pad($f_fin, 7,'0',STR_PAD_LEFT)."</b></font>".$serie;
	  }
	  else
	  {
		 $folios="<font color='#AF0707'><b>Sin existencias</b></font>";
	  }

	  if($m !== $cve){

	  	if($x === 0)
		{
			 $color ="success";
			 $x=1;
		}

		else
		{
			 $color ="warning";
			 $x=0;
		}

	  	if($aux !== 1)$respuesta .="</ul></div></div></div>";

		$respuesta.="<div class='col-md-3 text-center'></div>";

	  	$respuesta .="<div class='col-md-7 text-center'><div class='panel panel-{$color} panel-pricing'>";

	  	$respuesta .="  <div class='panel-heading'>
                            <a data-toggle='collapse' href='#collapseExample{$i}' aria-expanded='false' aria-controls='collapseExample'>
                            <h4><i class='fa fa-dot-circle-o'></i>&nbsp;&nbsp;{$cve} - {$marca}</h4>
                            </a>
                        </div>";

        $respuesta .="<div class='collapse in' id='collapseExample{$i}'>";

        $i++;
	  	$m=$cve;
	  	$e="";

	  }


	  if($e !== $edo){

	  	$respuesta.="   <div class='panel-body text-center'>
                            <p><strong>{$edo}</strong></p>
                        </div>";

        $respuesta.="<ul class='list-group text-center'>";

	  	$e=$edo;

	  }


		$respuesta.=" <li class='list-group-item'><i class='fa fa-check'></i> {$folios} &nbsp;&nbsp;&nbsp;<b>[{$existencia}] &nbsp;&nbsp;&nbsp;[{$tipo_mez}]</b></li>";

		$aux++;

	}

	 $respuesta.="</ul></div></div></div></div></div></section>";

	echo json_encode(['status' => 'OK','msj'=> $respuesta]);
  }
  else
  {
	echo json_encode(['status' => 'error','msj'=> 'No se tienen registros de hologramas de esta MARCA']);
  }
}



?>
