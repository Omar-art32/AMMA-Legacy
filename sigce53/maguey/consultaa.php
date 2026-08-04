<?php
include ("php/registro/conexion.php");
$conexion->set_charset("utf8");
$criterio=$_POST['mpredio'];
$state=$_POST['mnoclienteah'];
//Filtro anti-XSS
$caracteres_malos = array("<", ">", "\"", "'", "/", "<", ">", "'", "/");
$caracteres_buenos = array("& lt;", "& gt;", "& quot;", "& #x27;", "& #x2F;", "& #060;", "& #062;", "& #039;", "& #047;");
$criterio = str_replace($caracteres_malos, $caracteres_buenos, $criterio);
$state = str_replace($caracteres_malos, $caracteres_buenos, $state);

//Variable vacía (para evitar los E_NOTICE)
$mensaje = "";

//Comprueba si $consultaBusqueda está seteado
if (isset($criterio)) {

	$exp = explode("-", $criterio);
	$id_paraje = $exp[0];
	$tipo = $exp[1];

	$tipoP = 'paraje';
	$tipoE = 'existenciaplanta';
	$tipoC = 'constancias';

	$strConsulta = "SELECT p.id_paraje,p.constancia_predio,p.id_cliente,clientes.nombre as nombrec,Date_format(c.fecha,'%y') as anio,fecha_registro as fecha2,nombrep,regmaguey,LPAD(c.id_constancia,4,'0') as constancia,
	p.id_paraje as parajes,edad,p.paraje, comun.nombre,genespecie,existenciaplantas,e.edad,usufruto,tenencia,superficie,lng,lat,dis_planmetros,dis_surcometros,fecha_paraje,rcampo,p.status_predio,
	p.tipo, p.maguey_con_registro
	from siig.clientes
	inner join $tipoP p on clientes.no_cliente=p.id_cliente
	inner join $tipoC c on c.id_paraje=p.id_paraje
	inner join $tipoE e on p.id_paraje=e.id_paraje
	Inner Join comun ON comun.id_comun= e.id_comun
	Inner Join especie ON comun.id_especie = especie.id_especie
	where p.id_cliente='$state' and  p.id_paraje='$id_paraje';";
	$parajes= $conexion->query($strConsulta);
	$fila = mysqli_fetch_array($parajes);



	$strConsultaa = "SELECT municipios.nombre as nombrem,estados.nombre as nombree,localidades.localidad
	from estados
	inner join municipios on municipios.estado=estados.clave
	inner join localidades on localidades.MunicipioID=municipios.id
	inner join $tipoP p on localidades.id=p.id_localidad
	where p.id_cliente='$state' and  p.id_paraje='$id_paraje';";

	$parajess= $conexion->query($strConsultaa);
	$filaa = mysqli_fetch_array($parajess);

	$Consulta = "SELECT 
    p.id_paraje, p.id_cliente, p.nombrep, p.usufruto, p.tenencia, p.fecha_paraje,
    p.superficie, p.status_predio,p.tipo,p.lng,p.lat, p.paraje, p.rcampo,
    e.existenciaplantas,e.edad,e.regmaguey, e.id_plantas, e.cantidadini, e.dis_planmetros, e.dis_surcometros,
    municipios.nombre as nombrem,
    estados.nombre as nombree,
    localidades.localidad,
    comun.nombre, especie.genespecie
    from estados
    inner join municipios on municipios.estado=estados.clave
    inner join localidades on localidades.MunicipioID=municipios.id
    inner join $tipoP p on localidades.id=p.id_localidad
    inner join $tipoE e on p.id_paraje=e.id_paraje
    Inner Join comun ON comun.id_comun= e.id_comun
    Inner Join especie ON comun.id_especie = especie.id_especie
    where p.id_cliente='$state' and  p.id_paraje='$id_paraje';";

	$consultita= $conexion->query($Consulta);
	$numfilas = mysqli_num_rows($consultita);



	if ($numfilas===0) {
		$mensaje = "<center><p>NO HAY NINGUN PARAJE SELECCIONADO</p></center>";
	} else {
		
		$txtp = '
			<style>
                table, tr, td, th {
                    border: 1px solid black;
                    border-collapse:collapse;
                }
                tr.header {
                    cursor:pointer;
                }
                .header .sign:after {
                  content:"+";
                  display:inline-block;
                }
                .header.expand .sign:after {
                  content:"-";
                }
			</style>
		'.
		'<input type="hidden" class="form-control" id="mpredioa" name="mpredioa" value="'.$fila['id_paraje'].'">'.
		'<div class="col-lg-1">&nbsp;</div>
			<div class="col-lg-10">
				<h4>Información del Predio</h4>
				<table class="table caption-top">
				  <thead>
					<tr>
					  <th "><b>NOMBRE DEL PRODUCTOR:</b></th>
					  <th >'.$fila['nombrep'].'</th>
					</tr>
					<tr>
					  <th><b>NOMBRE DEL REPRESENTANTE EN CAMPO:</b></th>
					  <th>'.$fila['rcampo'].'</th>
					</tr>
					<tr>
					  <th><b>No. DE PREDIO:</b></th>
					  <th>'.$fila['id_paraje'].'</th>
					</tr>
					<tr>
					  <th><b>NOMBRE DEL PARAJE:</b></th>
					  <th>'.$fila['paraje'].'</th>
					</tr>
					<tr>
					  <th><b>ESTADO:</b></th>
					  <th>'.$filaa['nombree'].'</th>
					</tr>
					<tr>
					  <th><b>MUNICIPIO:</b></th>
					  <th>'.$filaa['nombrem'].'</th>
					</tr>
					<tr>
					  <th><b>LOCALIDAD:</b></th>
					  <th>'.$filaa['localidad'].'</th>
					</tr>
					<tr>
					  <th><b>USUFRUTO:</b></th>
					  <th>'.$fila['usufruto'].'</th>
					</tr>
					<tr>
					  <th><b>TENENCIA:</b></th>
					  <th>'.$fila['tenencia'].'</th>
					</tr>
					<tr>
					  <th><b>SUPERFICIE:</b></th>
					  <th>'.$fila['superficie'].'</th>
					</tr>
					<tr>
					  <th><b>LONGITUD:</b></th>
					  <th>'.$fila['lng'].'</th>
					</tr>
					<tr>
					  <th><b>LATITUD:</b></th>
					  <th>'.$fila['lat'].'</th>
					</tr>				
				  </tbody>
				</table>
				
				<div class="card-header">
					<h4>Plantas</h4>
				</div>
				<table class="table table-bordered table-success">
					<thead>
					<tr style="font-size: 10px;" bgcolor="#52BE80">
									<th class="paraje" align=""><strong>ESPECIE (NOMBRE COMÚN)</strong></th>
									<th class="especie" align=""><strong>ESPECIE (NOMBRE CIENTIFICO)</strong></th>
									<th class="situacion" align=""><strong>SITUACIÓN DE MANEJO</strong></th>
									<th class="situacion" align=""><strong>CANTIDAD INICIAL</strong></th>
									<th class="existencia" align=""><strong>EXISTENCIA DE PLANTAS</strong></th>
									<th class="edad" align=""><strong>EDAD (AÑOS)</strong></th>
									<th class="distanciap" align=""><strong>DISTANCIA ENTRE PLANTAS (METROS)</strong></th>
									<th class="distancias" align="" ><strong>DISTANCIA ENTRE SURCOS (METROS)</strong></th>
					</tr>
					</thead>
					<tbody>
				';
		while($row = mysqli_fetch_array($consultita)) {
			$txtp .= 
				"<tr style='font-size: 10px;'>
					<td class=\"nombre\"><b>".$row['nombre']."</b></td>
					<td class=\"genespecie\"><b>".$row['genespecie']."</b></td>
					<td class=\"regmaguey\"><b>".$row['regmaguey']."</b></td>
					<td class=\"cantini\" align=\"center\"><b>".$row['cantidadini']."</b></td>
					<td class=\"existenciaplantas\" align=\"center\"><b>".$row['existenciaplantas']."</b></td>
					<td class=\"edad\" align=\"center\"><b>".$row['edad']."</b></td>
					<td class=\"dis_planmetros\" align=\"center\"><b>".$row['dis_planmetros']."</b></td>
					<td class=\"edad\" align=\"center\"><b>".$row['dis_surcometros']."</b></td>
				</tr>";
     
		}
		
		$sqlGP = 
		"SELECT c.id_extraccion guia
		FROM paraje p 
		INNER JOIN cextracciones c ON p.id_paraje = c.id_paraje 
		WHERE c.id_extraccion NOT IN (SELECT no_guia FROM historial_extraccion_verificadores) 
		AND p.id_paraje IN(".$id_paraje.")";
		$guias = $conexion->query($sqlGP);
		$txtg = "";
		while($filag = mysqli_fetch_array($guias)) {
			$txtg .= ($txtg != "")? ",": "";
			$txtg .= $filag["guia"];
		}
		
		$txtp .= ' 
			</tbody>
		</table>
		
		<div class="card-header">
			<h4>Guías disponibles</h4>
		</div>		
		<ul class="list-group list-group-flush">
			<li class="list-group-item">
				<b>'.$txtg.'</b>
			</li>
		</ul>
		</div>
		<div class="col-lg-1">&nbsp;</div>';
		
		
		echo $txtp;
            

            $constancia = ($fila["constancia_predio"]!="")?' <a href=constancia/pdfConstanciaPredio/'.$fila["constancia_predio"].' target=\"_blank\" >'.strtoupper($fila['constancia']).$fila['parajes'].$fila['anio'].'</a>':strtoupper($fila['constancia']).$fila['parajes'].$fila['anio'];


		$status_predio=$fila['status_predio'];
		$tipoParaje=$fila['tipo'];
		$checked1=($status_predio==1)?'checked':'';
		$checked2=($status_predio==0)?'checked':'';

		
	}
}
	echo $mensaje;
?>
		</tbody>
	</table>
</div>


<script>
    $(document).ready(function() {
        $('.header').click(function(){
            $(this).toggleClass('expand').nextUntil('tr.header').slideToggle(100);
        });
    });

</script>
