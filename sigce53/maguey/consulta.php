<?php
//include ("php/registro/conexion.php");
include("../common/conexion.php");
$conexion->set_charset("utf8");
//Archivo de conexión a la base de datos
//error_reporting(E_ALL ^ E_NOTICE);
//Variable de búsqueda
//$consultaBus=isset($_POST['state']) ? $_POST['state'] : NULL;
//$consultaBusqueda=isset($_POST['criterio']) ? $_POST['criterio'] : NULL;
$criterio=$_POST['criterio'];
$state=$_POST['state'];
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

	$tipoP=($tipo==1)?'paraje':'paraje_vivero';
	$tipoE=($tipo==1)?'existenciaplanta':'existenciaplanta_vivero';
	$tipoC=($tipo==1)?'constancias':'constancias_vivero';
	$mas  =($tipo==1)?', p.maguey_con_registro':'';
    $cconst = ($tipo == 1) ? 'p.constancia_predio' : 'p.constancia_vivero'; 
    $cconste = ($tipo == 1) ? 'p.constancia_extracciones' : 'p.constancia_extracciones';

    if($tipo==1)
        $mas .= ", p.servicio ";

	$strConsulta = "SELECT p.id_paraje,$cconst constanciadoc, $cconste constanciadocex, p.id_cliente,clientes.nombre as nombrec,Date_format(c.fecha,'%y') as anio,fecha_registro as fecha2,
    nombrep,regmaguey,LPAD(c.id_constancia,4,'0') as constancia,CONCAT('P',LPAD(p.id,4,'0')) as parajes,edad,p.paraje, comun.nombre,genespecie,existenciaplantas,e.edad,
    usufruto,tenencia,superficie,lng,lat,dis_planmetros,dis_surcometros,fecha_paraje,rcampo,p.status_predio,p.tipo $mas 
from clientes
inner join $tipoP p on clientes.no_cliente=p.id_cliente
left join $tipoC c on c.id_paraje=p.id_paraje
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

    
// OBTENER GUÍAS A COBRAR
/*$sqlNG = "
    SELECT YEAR(c.fecha) anio, MONTH(c.fecha) mes, count(c.id) VECES, 
    IF(p.maguey_con_registro='1', 'EN SITIO', 'DOCUMENTAL') mcr, 
    p.servicio 
    from paraje p 
    INNER JOIN cextracciones c ON c.id_paraje = p.id_paraje
    where p.id_cliente='$state' 
    GROUP BY YEAR(c.fecha), MONTH(c.fecha), p.maguey_con_registro, p.servicio
    ORDER BY YEAR(c.fecha), MONTH(c.fecha), p.maguey_con_registro ASC";*/
$sqlNG = "
    SELECT YEAR(c.fecha) anio, MONTH(c.fecha) mes, count(c.id) VECES, 
    IF(p.maguey_con_registro='1', 'EN SITIO', 'DOCUMENTAL') mcr, 
    trim(p.servicio) servicio, trim(oc.servicio) oc_servicio, oc.VCSUSADAS,
    IF(isnull(oc.servicio) && isnull(p.servicio),'SI',(IF(isnull(oc.servicio=p.servicio),'NO',IF(oc.servicio=p.servicio,'SI','NO'))) ) ESIGUAL
    from paraje p 
    INNER JOIN cextracciones c ON c.id_paraje = p.id_paraje
    LEFT JOIN (
		SELECT 
		YEAR(c.fecha) anio, MONTH(c.fecha) mes, hev.id_extraccion, count(hev.id_extraccion) VCSUSADAS, 
			IF(p.maguey_con_registro='1', 'EN SITIO', 'DOCUMENTAL') mcr, 
			p.servicio, p.maguey_con_registro p_mcr
		FROM paraje p
			INNER JOIN cextracciones c ON c.id_paraje = p.id_paraje
			LEFT JOIN historial_extraccion_verificadores hev ON c.id_extraccion = hev.no_guia
			WHERE p.id_cliente IN ('C0005')
		GROUP BY YEAR(c.fecha), MONTH(c.fecha), p.maguey_con_registro, p.servicio
			ORDER BY YEAR(c.fecha), MONTH(c.fecha), p.maguey_con_registro ASC
    ) AS oc ON oc.anio = YEAR(c.fecha) AND oc.mes = MONTH(c.fecha) 
    AND oc.p_mcr = p.maguey_con_registro 
    AND (IF(isnull(oc.servicio) && isnull(p.servicio),'SI',(IF(isnull(oc.servicio=p.servicio),'NO',IF(oc.servicio=p.servicio,'SI','NO'))) ) = 'SI')
    where p.id_cliente='C0005' 
    GROUP BY YEAR(c.fecha), MONTH(c.fecha), p.maguey_con_registro, p.servicio
    ORDER BY YEAR(c.fecha), MONTH(c.fecha), p.maguey_con_registro ASC;
";
$consultaNG= $conexion->query($sqlNG);
//$filaNG = mysqli_fetch_array($consultaNG);
$guiasEx = 0; $txtEx = ""; $sumaVeces = 0; $sumaNG = 0; $sumaNGE = 0;
while($rowNG = mysqli_fetch_object($consultaNG)) {
    
    $txtEx .= '
        <tr>
            <td>'.$rowNG->mes . '/' . $rowNG->anio .'</td>
            <td>'.$rowNG->mcr . ' ' . $rowNG->servicio .'</td>
            <td>'.$rowNG->VECES.'</td>
            <td>'.$rowNG->VCSUSADAS.'</td>
        </tr>
            ';
            
    if($rowNG->servicio == "EXCLUSIVO" ) {
        $sumaNG += $rowNG->VECES;
        $sumaNGE += $rowNG->VCSUSADAS;
    }
        
    $sumaVeces += $rowNG->VECES;


}
if($txtEx != "") {
    $txtEx = '<br>
    <table style="font-size: 10px;" >
    <tbody>
        <tr style="text-align: left;
        vertical-align: top;
        border: 1px solid #000;
        border-collapse: collapse;
        padding: 0.3em; font-weight: bold;
        caption-side: bottom; font-size: 12px;" bgcolor="#ABEBC6">
            <th class="paraje" align=""><strong>AÑO</strong></th>
            <th class="paraje" align=""><strong>REGISTRO</strong></th>
            <th class="paraje" align="right"><strong>GUÍAS CREADAS</strong></th>
            <th class="paraje" align="right"><strong>GUÍAS USADAS</strong></th>
        </tr>
    </tbody>' . $txtEx . 
    '<tr style="text-align: right;
        vertical-align: top;
        border: 1px solid #000;
        border-collapse: collapse;
        padding: 0.3em; font-weight: bold;
        caption-side: bottom; font-size: 12px;" bgcolor="#ABEBC6">
        <th class="paraje" colspan="2"><strong>TOTAL GUÍAS</strong></th>
        <th class="paraje" ><strong>'.$sumaVeces.'</strong></th>
        <th></th>
    </tr>
    <tr style="text-align: right;
        vertical-align: top;
        border: 1px solid #000;
        border-collapse: collapse;
        padding: 0.3em; font-weight: bold;
        caption-side: bottom; font-size: 12px;" bgcolor="#ABEBC6">
        <th class="paraje" colspan="2"><strong>TOTAL GUÍAS DOC EX</strong></th>
        <th class="paraje" ><strong>'.$sumaNG.'</strong></th>
        <th class="paraje" ><strong>'.$sumaNGE.'</strong></th>
    </tr>
    </table>';
}

$Consulta = "SELECT p.id,
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
            echo '
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
            ';
        if($tipo == 1) {
            /*$constancia = ($fila["constanciadoc"]!="")?' <a href=constancia/pdfConstanciaPredio/'.$fila["constanciadoc"].' target=\"_blank\" >'.strtoupper($fila['constancia']).$fila['parajes'].$fila['anio'].'</a>':strtoupper($fila['constancia']).$fila['parajes'].$fila['anio'];
            $constanciaex = ($fila["constanciadocex"]!="")?' <a href=constancia/pdfConstanciaExtraccion/'.$fila["constanciadocex"].' target=\"_blank\" >'.$fila['parajes'].'</a>':strtoupper($fila['parajes']);*/
            //https://portal.amma.org.mx/sigce/maguey/constancia/pdfConstanciaExtraccion/Extraccion_P1_1065509935.pdf
            $constancia = ($fila["constanciadoc"]!="")?' <a href=constancia/pdfConstanciaPredio/'.$fila["constanciadoc"].' target=\"_blank\" >CONSTANCIA DE PREDIO</a>':'CONSTANCIA DE PREDIO';
            $constanciaex = ($fila["constanciadocex"]!="")?' <a href=constancia/pdfConstanciaExtraccion/'.$fila["constanciadocex"].' target=\"_blank\" >CONSTANCIA DE EXTRACCIÓN</a>':'CONSTANCIA DE EXTRACCIÓN';
        } else {
            /*$constancia = ($fila["constanciadoc"]!="")?' <a href=constancia/pdfConstanciaVivero/'.$fila["constanciadoc"].' target=\"_blank\" >'.strtoupper($fila['constancia']).$fila['parajes'].$fila['anio'].'</a>':strtoupper($fila['constancia']).$fila['parajes'].$fila['anio'];
            $constanciaex = ($fila["constanciadocex"]!="")?' <a href=constancia/pdfConstanciaExtraccion/'.$fila["constanciadocex"].' target=\"_blank\" >'.$fila['parajes'].'</a>':strtoupper($fila['parajes']);*/
            // https://portal.amma.org.mx/sigce/maguey/constancia/pdfConstanciaExtraccion/Extraccion_V1_671898232.pdf
            $constancia = ($fila["constanciadoc"]!="")?' <a href=constancia/pdfConstanciaVivero/'.$fila["constanciadoc"].' target=\"_blank\" >CONSTANCIA DE VIVERO</a>':'CONSTANCIA DE VIVERO';
            $constanciaex = ($fila["constanciadocex"]!="")?' <a href=constancia/pdfConstanciaExtraccion/'.$fila["constanciadocex"].' target=\"_blank\" >CONSTANCIA DE EXTRACCIÓN</a>':'CONSTANCIA DE EXTRACCIÓN';
        }

        /*
        <div class="form-group">
                        <span class="col-md-12">DETALLES DE GUÍAS GENERADAS: </span>
                        <div class="col-md-9" >'.$txtEx.'</div>
                        
                    </div>
        */

            echo '
            <div class="form-group row">
                <div class="form-group col-md-6">
                    <fieldset><legend align="">DATOS</legend></fieldset>
                    <div class="form-group">
                        <span class="col-md-5">NO. DE CONTROL: </span>
                        <label class="col-md-7 control-label">&nbsp;'.$state.'</label>
                    </div>
                    <div class="form-group">
                        <span class="col-md-5">NOMBRE: </span>
                        <label class="col-md-7 control-label">&nbsp;'.$fila['nombrec'].'</label>
                    </div>
                    <div class="form-group">
                        <span class="col-md-5">NOMBRE DEL PRODUCTOR: </span>
                        <label for="" class="col-md-7 control-label">&nbsp;'.$fila['nombrep'].'</label>
                    </div>
                    <div class="form-group">
                        <span class="col-md-5">NOMBRE DEL REPRESENTANTE EN CAMPO: </span>
                        <label for="" class="col-md-7 control-label">&nbsp;'.$fila['rcampo'].'</label>
                    </div>
                    
                </div>
                <div class="form-group col-md-6">
                    <fieldset><legend align="">DATOS DEL PREDIO</legend></fieldset>
                    <div class="form-group">
                        <span class="col-md-5">NO.CONSTANCIA: </span>';
                //echo '<label for="" class="col-md-7 control-label">&nbsp;'.strtoupper($fila['constancia']).$fila['parajes'].$fila['anio'].'</label>';
                echo '<label for="" class="col-md-7 control-label">&nbsp;'.$constancia.'</label>';
                echo '
                    </div>
                    <div class="form-group">
                        <span class="col-md-5">No. DE PREDIO: </span>
                        <label for="" class="col-md-7 control-label">&nbsp;'.$fila['id_paraje'].'</label>
                    </div>
                    <div class="form-group">
                        <span class="col-md-5">NOMBRE DEL PARAJE: </span>
                        <label for="" class="col-md-7 control-label">&nbsp;'.$fila['paraje'].'</label>
                    </div>
                    <div class="form-group">
                        <span class="col-md-5">ESTADO: </span>
                        <label for="" class="col-md-7 control-label">&nbsp;'.$filaa['nombree'].'</label>
                    </div>
                    <div class="form-group">
                        <span class="col-md-5">MUNICIPIO: </span>
                        <label for="" class="col-md-7 control-label">&nbsp;'.$filaa['nombrem'].'</label>
                    </div>
                    <div class="form-group">
                        <span class="col-md-5">LOCALIDAD: </span>
                        <label for="" class="col-md-7 control-label">&nbsp;'.$filaa['localidad'].'</label>
                    </div>
                    <div class="form-group">
                        <span class="col-md-5">USUFRUTO DE LA TIERRA: </span>
                        <label for="" class="col-md-7 control-label">&nbsp;'.$fila['usufruto'].'</label>
                    </div>
                    <div class="form-group">
                        <span class="col-md-5">TENENCIA DE LA TIERRA: </span>
                        <label for="" class="col-md-7 control-label">&nbsp;'.$fila['tenencia'].'</label>
                    </div>
                    <div class="form-group">
                        <span class="col-md-5">SUPERFICIE: </span>
                        <label for="" class="col-md-7 control-label">&nbsp;'.$fila['superficie'].'</label>
                    </div>
                    <div class="form-group">
                        <span class="col-md-5">LONGITUD: </span>
                        <label for="" class="col-md-7 control-label">&nbsp;'.$fila['lng'].'</label>
                    </div>
                    <div class="form-group">
                        <span class="col-md-5">LATITUD: </span>
                        <label for="" class="col-md-7 control-label">&nbsp;'.$fila['lat'].'</label>
                    </div>
                    
                    ';
            if($tipo==1){
                switch ($fila['maguey_con_registro']) {
                    case 1:
                        $tMaguey='EN SITIO';
                        break;
                    case 2:
                        $tMaguey='DOCUMENTAL ' . $fila['servicio'];
                        break;

                        default:
                                $tMaguey='';
                    break;
                }
                //echo 'MAGUEY CON REGISTRO:   <strong>'.$tMaguey.'</strong></br>
                echo '
                    <div class="form-group">
                        <span class="col-md-5">MAGUEY CON REGISTRO:: </span>
                        <label for="" class="col-md-7 control-label">&nbsp; '.$tMaguey.'</label>
                    </div>';
            }

            echo '
                    <div class="form-group">
                        <span class="col-md-5">FECHA DE REGISTRO:: </span>
                        <label for="" class="col-md-7 control-label">&nbsp;'.$fila['fecha_paraje'].'</label>
                    </div>

                    <div class="form-group">
                        <span class="col-md-5">CONSTANCIA DE EXTRACCIÓN: </span>
                        <label for="" class="col-md-7 control-label">&nbsp;'.$constanciaex.'</label>
                    </div>
                </div>
            </div>
            ';

            

		$status_predio=$fila['status_predio'];
		$tipoParaje=$fila['tipo'];
		$checked1=($status_predio==1)?'checked':'';
		$checked2=($status_predio==0)?'checked':'';


		echo 'ESTATUS PREDIO:
		<strong>
		<input type="radio" id="predio_activo" name="status_predio" value="1" onclick="actualizarPredio(this.value,'.$fila["id_paraje"].','.$tipoParaje.')" '.$checked1.'>
        <label for="predio_activo">Mostrar</label>
        <input type="radio" id="predio_inactivo" name="status_predio" value="0" onclick="actualizarPredio(this.value,'.$fila["id_paraje"].','.$tipoParaje.')" '.$checked2.'>
        <label for="predio_inactivo">Ocultar</label>
		</strong>
		</br></br></br>
	    <fieldset>
	    	<legend align="">DATOS DEL MAGUEY</legend>
	    </fieldset>
		<div class="">
			<table class="table table-bordered table-success">
		<tbody>
			<tr style="font-size: 14px;" bgcolor="#52BE80">
                <th class="paraje" align=""><strong>ESPECIE (NOMBRE COMÚN)</strong></th>
                <th class="especie" align=""><strong>ESPECIE (NOMBRE CIENTIFICO)</strong></th>
                <th class="situacion" align=""><strong>SITUACIÓN DE MANEJO</strong></th>
                <th class="situacion" align=""><strong>CANTIDAD INICIAL</strong></th>
                <th class="existencia" align=""><strong>EXISTENCIA DE PLANTAS</strong></th>
                <th class="edad" align=""><strong>EDAD (AÑOS)</strong></th>
                <th class="distanciap" align=""><strong>DISTANCIA ENTRE PLANTAS (METROS)</strong></th>
                <th class="distancias" align="" ><strong>DISTANCIA ENTRE SURCOS (METROS)</strong></th>
                <th class="distancias" align="" colspan="2" ><strong>DETALLE</strong></th>
			</tr>';
	

		while($row = mysqli_fetch_array($consultita)) {
            $sqlC = "
            SELECT hev.*, v.*, ep.*, c.id_extraccion,c.fecha fechac, GROUP_CONCAT(pe.tapada) tapadas, SUM(pe.lts_producidos) sum_lts, 
            DATE(hev.fecha_realizo) fecha_realizo
            FROM $tipoE ep 
            left JOIN historial_extraccion_verificadores hev ON hev.id_plantas = ep.id_plantas 
            LEFT JOIN cextracciones c ON hev.no_guia = c.id_extraccion
            LEFT JOIN verificadores v  ON hev.id_verificador = v.id_us
            LEFT JOIN rv_produccion_entrada pe ON hev.no_guia = pe.no_guia 
            where ep.id_plantas IN
            (".$row['id_plantas'].")
            GROUP BY hev.id_extraccion
            ORDER by hev.no_guia ";
            $ep= $conexion->query($sqlC);
            $numfilasep = mysqli_num_rows($ep);
            $class = ($numfilasep > 0 && $tipo == 1) ? "class='header' ": "";
            $txt = ($numfilasep > 0 && $tipo == 1) ? "<strong>+</strong> ": "";

                echo "
                    <tr $class style='font-size: 14px;'>
                        <td class=\"nombre\"><b>".$row['nombre']."</b></td>
                        <td class=\"genespecie\"><b>".$row['genespecie']."</b></td>
                        <td class=\"regmaguey\"><b>".$row['regmaguey']."</b></td>
                        <td class=\"cantini\" align=\"center\"><b>".$row['cantidadini']."</b></td>
                        <td class=\"existenciaplantas\" align=\"center\"><b>".$row['existenciaplantas']."</b></td>
                        <td class=\"edad\" align=\"center\"><b>".$row['edad']."</b></td>
                        <td class=\"dis_planmetros\" align=\"center\"><b>".$row['dis_planmetros']."</b></td>
                        <td class=\"edad\" align=\"center\"><b>".$row['dis_surcometros']."</b></td>
                        <td class=\"\" align=\"center\" colspan='2'><b>$txt</b></td>
                    </tr>";

            if($tipo == 1) {
                if($numfilasep > 0) {
                    echo "
                        <tr style='width: 25%;
                        text-align: left;
                        vertical-align: top;
                        border: 1px solid #000;
                        border-collapse: collapse;
                        padding: 0.3em; font-weight: bold;
                        caption-side: bottom; font-size: 12px;; display: none;' bgcolor='#ABEBC6'>
                            <td># GUÍA</td>
                            <td>FECHA DE CREACIÓN</td>
                            <td>CLIENTE ENVÍA</td>
                            <td>CLIENTE RECIBE</td>
                            <td>CANTIDAD</td>
                            <td >INSPECTOR</td>
                            <td >FECHA DE USO</td>
                            <td >TAPADAS</td>
                            <td >LITROS PRODUCIDOS</td>

                        </tr>
                    ";
                }
                $GuiasDisp = 0;
                while($filaep = mysqli_fetch_array($ep)) {
                    $tapadas = ""; $lts_producidos = ""; $fecha_realizo = "";
                    if($filaep["tapadas"] != "") {
                        $tapadas = $filaep["tapadas"];
                        $lts_producidos = $filaep["sum_lts"];
                        $fecha_realizo = $filaep["fecha_realizo"];
                    } else {
                        $sqlt = $conexion->prepare("SELECT CONCAT(pe.tapada) tapada, SUM(pe.lts_producidos) lts_producidos, 
                        DATE(hev.fecha_realizo) fecha_realizo
                        FROM rv_produccion_entrada pe 
                            INNER JOIN rv_produccion_ensamble pen ON pe.id_produccion_entrada = pen.id_produccion_entrada
                            LEFT JOIN historial_extraccion_verificadores hev ON pe.no_guia = hev.no_guia  
                            WHERE pen.no_guia = '".$filaep["no_guia"]."' 
                            GROUP BY hev.no_guia ");
                        if ($sqlt) { /*si la conexion esta preparada*/
                            $sqlt->execute(); /* ejecutar la consulta */
                            $resultSetT = $sqlt->get_result();
                            $resultT = $resultSetT->fetch_all(MYSQLI_ASSOC);
                            foreach($resultT as $rowt) {
                                $tapadas = $rowt["tapada"];
                                $lts_producidos = $rowt["lts_producidos"];
                                $fecha_realizo = $filaep["fecha_realizo"];
                            }
                        }
                    }

                    echo "
                        <tr style='width: 25%;
                        text-align: left;
                        vertical-align: top;
                        border: 1px solid #000;
                        border-collapse: collapse;
                        padding: 0.3em;
                        caption-side: bottom; font-size: 12px; display: none;'>";
                    if($filaep["no_guia"] == "") 
                        $GuiasDisp++;
                    else {
                        echo "
                            <td>".$filaep["id_extraccion"]."</td>
                            <td>".$filaep["fechac"]."</td>
                            <td >".$filaep["no_cliente_envia"]."</td>
                            <td>".$filaep["no_cliente_recibe"]."</td>
                            <td>".$filaep["extraccion"]."</td>
                            <td>".$filaep["nombre"]."</td>
                            <td>".$fecha_realizo."</td>
                            <td>".$tapadas."</td>
                            <td>".$lts_producidos."</td>
                        ";
                    } 
                    echo "</tr>";
                } // end while
                
                
                $contGuiasDisp = "";
                // GUÍAS SIN USAR
                $sqlG = $conexion->prepare("
                SELECT c.id_extraccion, c.fecha fechac, hev.id_plantas 
                FROM cextracciones c
                    LEFT JOIN historial_extraccion_verificadores hev ON c.id_extraccion = hev.no_guia 
                    WHERE c.id_paraje = '".$id_paraje."' 
                     ");
                if ($sqlG) { /*si la conexion esta preparada*/
                    $sqlG->execute(); /* ejecutar la consulta */
                    $resultSetG = $sqlG->get_result();
                    $resultG = $resultSetG->fetch_all(MYSQLI_ASSOC);
                    foreach($resultG as $rowg) {
                        if($rowg["id_plantas"] == "") {
                            $contGuiasDisp .= "<tr>
                                <td>".$rowg["id_extraccion"]."</td>
                                <td>".$rowg["fechac"]."</td>
                                <td>D I S P O N I B L E</td>
                                </tr>
                            ";
                        }
                    }
                }
            }
		}

        echo "</table>";
        if($contGuiasDisp != "") {
            $contGuiasDisp = "
            <fieldset>
            <legend>GUÍAS SIN USAR</legend>
            </fieldset>
            <table class='table table-bordered table-success'>
                <tbody>
                <tr style='width: 25%;
                text-align: left;
                vertical-align: top;
                border: 1px solid #000;
                border-collapse: collapse;
                padding: 0.3em; font-weight: bold;
                caption-side: bottom; font-size: 12px;' bgcolor='#ABEBC6'>
                    <td width=20%># GUÍA</td>
                    <td width=20%>FECHA DE CREACIÓN</td>
                    <td width=60%>ESTADO</td>
                </tr>
                </tbody>                                
            " . $contGuiasDisp . "</table>";;
        }
        
        echo $contGuiasDisp;   
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
