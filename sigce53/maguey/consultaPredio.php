<?php  
//include ("php/registro/conexion.php"); 
include("../common/conexion.php");
$conexion->set_charset("utf8");
//Archivo de conexión a la base de datos
//error_reporting(E_ALL ^ E_NOTICE);
//Variable de búsqueda
//$consultaBus=isset($_POST['state']) ? $_POST['state'] : NULL;
//$consultaBusqueda=isset($_POST['criterio']) ? $_POST['criterio'] : NULL;
$predios = $_POST['predios'];
$noasociado = $_POST['noasociado'];
//Filtro anti-XSS
$caracteres_malos = array("<", ">", "\"", "'", "/", "<", ">", "'", "/");
$caracteres_buenos = array("& lt;", "& gt;", "& quot;", "& #x27;", "& #x2F;", "& #060;", "& #062;", "& #039;", "& #047;");
$predios = str_replace($caracteres_malos, $caracteres_buenos, $predios);
//$state = str_replace($caracteres_malos, $caracteres_buenos, $state);

//Variable vacía (para evitar los E_NOTICE)
$mensaje = "";
$atrr = array(); 
//Comprueba si $consultaBusqueda está seteado
if (isset($predios)) {
    $exp = explode("-", $predios);
    $id_paraje = $exp[0];
    $tipo = $exp[1];

    $tipoP=($tipo==1)?'paraje':'paraje_vivero';
    $tipoE=($tipo==1)?'existenciaplanta':'existenciaplanta_vivero';
    $tipoC=($tipo==1)?'constancias':'constancias_vivero';
    $mas  =($tipo==1)?', p.maguey_con_registro':'';

    $strConsulta = "SELECT p.id_paraje,p.id_cliente,clientes.nombre as nombrec,Date_format(c.fecha,'%y') as anio,fecha_registro as fecha2,nombrep,regmaguey,LPAD(c.id_constancia,4,'0') as constancia,LPAD(p.id_paraje,4,'0') as parajes,edad,p.paraje, comun.nombre,genespecie,existenciaplantas,e.edad,usufruto,tenencia,superficie,lng,lat,dis_planmetros,dis_surcometros,fecha_paraje,rcampo,p.status_predio,p.tipo $mas
    from clientes 
    inner join $tipoP p on clientes.no_cliente=p.id_cliente 
    inner join $tipoC c on c.id_paraje=p.id_paraje
    inner join $tipoE e on p.id_paraje=e.id_paraje
    Inner Join comun ON comun.id_comun= e.id_comun 
    Inner Join especie ON comun.id_especie = especie.id_especie
    where p.id_cliente='$noasociado' and  p.id_paraje='$id_paraje';";
    $parajes= $conexion->query($strConsulta);
    $fila = mysqli_fetch_array($parajes);



    $strConsultaa = "SELECT municipios.nombre as nombrem,estados.nombre as nombree,localidades.localidad
    from estados 
    inner join municipios on municipios.estado=estados.clave
    inner join localidades on localidades.MunicipioID=municipios.id 
    inner join $tipoP p on localidades.id=p.id_localidad 
    where p.id_cliente='$noasociado' and  p.id_paraje='$id_paraje';";

    $parajess= $conexion->query($strConsultaa);
    $filaa = mysqli_fetch_array($parajess);


    $Consulta = "SELECT p.id_paraje,p.id_cliente,nombrep,regmaguey,c.id_constancia as numeroconstancia,municipios.nombre as nombrem,estados.nombre as nombree,localidades.localidad,p.paraje, comun.nombre,genespecie,existenciaplantas,e.edad,usufruto,tenencia,superficie,lng,lat,dis_planmetros,dis_surcometros,fecha_paraje,rcampo,p.status_predio,p.tipo
    from estados 
    inner join municipios on municipios.estado=estados.clave
    inner join localidades on localidades.MunicipioID=municipios.id 
    inner join $tipoP p on localidades.id=p.id_localidad 
    inner join $tipoC c on c.id_paraje=p.id_paraje
    inner join $tipoE e on p.id_paraje=e.id_paraje
    Inner Join comun ON comun.id_comun= e.id_comun 
    Inner Join especie ON comun.id_especie = especie.id_especie
    where p.id_cliente='$noasociado' and  p.id_paraje='$id_paraje';";

    $consultita= $conexion->query($Consulta);
    $numfilas = mysqli_num_rows($consultita);



    if ($numfilas===0) 
        $mensaje = "<center><p>NO HAY NINGUN PARAJE SELECCIONADO</p></center>";
    else {
        $mensaje .= '<a class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                        Mostrar Detalle del Predio
                      </a>';
        $mensaje .= '<div class="collapse" id="collapseExample">
                  <div class="card card-body">';
        $mensaje .= '<fieldset><legend align="">DATOS DEL CLIENTE</legend></fieldset>';
        $mensaje .= 'NO.CLIENTE:   <strong>'.$noasociado.'</strong></br>';
        $mensaje .= 'NOMBRE DEL CLIENTE:   <strong>'.$fila['nombrec'].'</strong></br>';
        $mensaje .= 'NOMBRE DEL PRODUCTOR:   <strong>'.$fila['nombrep'].'</strong></br>';
        $mensaje .= 'NOMBRE DEL REPRESENTANTE EN CAMPO:   <strong>'.$fila['rcampo'].'</strong></br></br>';
        //datos del predio
        $mensaje .= '<fieldset><legend align="">DATOS DEL PREDIO</legend></fieldset>';
        $mensaje .= 'NO.CONSTANCIA:   <strong>'.strtoupper($fila['constancia']).$fila['parajes'].$fila['anio'].'</strong></br>';
        $mensaje .= 'NO.PARAJE:   <strong>'.$fila['parajes'].'</strong></br>';
        $mensaje .= 'NOMBRE DEL PARAJE:   <strong>'.$fila['paraje'].'</strong></br>';
        $mensaje .= 'ESTADO:   <strong>'.$filaa['nombree'].'</strong></br>';
        $mensaje .= 'MUNICIPIO:   <strong>'.$filaa['nombrem'].'</strong></br>';
        $mensaje .= 'LOCALIDAD:   <strong>'.$filaa['localidad'].'</strong></br>';
        //aqui va localidad, municipio y estado
        $mensaje .= 'USUFRUTO DE LA TIERRA:   <strong>'.$fila['usufruto'].'</strong></br>';
        $mensaje .= 'TENENCIA DE LA TIERRA:   <strong>'.$fila['tenencia'].'</strong></br>';
        $mensaje .= 'SUPERFICIE:   <strong>'.$fila['superficie'].'</strong></br>';
        $mensaje .= 'LONGITUD:   <strong>'.$fila['lng'].'</strong></br>';
        $mensaje .= 'LATITUD:   <strong>'.$fila['lat'].'</strong></br>';
        if($tipo==1){
            switch ($fila['maguey_con_registro']) {
                case 1:
                    $tMaguey='NORMAL';
                    break;
                case 2:
                    $tMaguey='EXTEMPORÁNEO';
                    break;

                    default:
                            $tMaguey='';
                break;

            }
            $mensaje .= 'MAGUEY CON REGISTRO:   <strong>'.$tMaguey.'</strong></br>';
        }
        $mensaje .= 'FECHA DE REGISTRO:   <strong>'.$fila['fecha_paraje'].'</strong></br></br></br>';

        $status_predio=$fila['status_predio'];
        $tipoParaje=$fila['tipo'];
        $checked1=($status_predio==1)?'checked':'';
        $checked2=($status_predio==0)?'checked':'';

        /*$mensaje .= 'ESTATUS PREDIO:
        <strong>
        <input type="radio" id="predio_activo" name="status_predio" value="1" onclick="actualizarPredio(this.value,'.$fila["id_paraje"].','.$tipoParaje.')" '.$checked1.'>
        <label for="predio_activo">Mostrar</label>
        <input type="radio" id="predio_inactivo" name="status_predio" value="0" onclick="actualizarPredio(this.value,'.$fila["id_paraje"].','.$tipoParaje.')" '.$checked2.'>
        <label for="predio_inactivo">Ocultar</label>
        </strong>
        </br></br></br>';*/
        
        $mensaje .= '
        <fieldset>
            <legend align="">DATOS DEL MAGUEY</legend>
        </fieldset>
        <div class="">
            <table class="table table-bordered table-success"> 
            <tbody>
                <tr>
                    <td class="paraje" align=""><strong>ESPECIE (NOMBRE COMÚN)</strong></td>
                    <td class="especie" align=""><strong>ESPECIE (NOMBRE CIENTIFICO)</strong></td>
                    <td class="situacion" align=""><strong>SITUACIÓN DE MANEJO</strong></td>
                    <td class="existencia" align=""><strong>EXISTENCIA DE PLANTAS</strong></td>
                    <td class="edad" align=""><strong>EDAD (AÑOS)</strong></td>
                    <td class="distanciap" align=""><strong>DISTANCIA ENTRE PLANTAS (METROS)</strong></td>
                    <td class="distancias" align=""><strong>DISTANCIA ENTRE SURCOS (METROS)</strong></td>
                </tr>
        ';
        while($row = mysqli_fetch_array($consultita)) {
            $mensaje .= "
                <tr>
                    <td class=\"nombre\">".$row['nombre']."</td>
                    <td class=\"genespecie\">".$row['genespecie']."</td>
                    <td class=\"regmaguey\">".$row['regmaguey']."</td>
                    <td class=\"existenciaplantas\" align=\"center\">".$row['existenciaplantas']."</td>
                    <td class=\"edad\" align=\"center\">".$row['edad']."</td>
                    <td class=\"dis_planmetros\" align=\"center\">".$row['dis_planmetros']."</td>
                    <td class=\"edad\" align=\"center\">".$row['dis_surcometros']."</td>
                </tr>";
        }
        $mensaje .= "
            </tbody>
            </table>
          </div>
         </div>
        </div>
        ";
        
        
    }
    
    $result = $conexion->query("
        SELECT *
        FROM parajes_atributos 
        WHERE id_paraje = '$id_paraje'");
    $numrows = mysqli_num_rows($result);
    $i = 0;
    while($row = mysqli_fetch_array($result)) {
        /*if($row["medalla_mipe"] != "") {
            $atrr[$i]["val"] = $row["diagnostico_medalla_mipe"]
        } else {*/
            
        //}
        $atrr[$row["atributo"]]["valor"] = $row["valor"];
        $atrr[$row["atributo"]]["observaciones"] = $row["observaciones"];
    }
}
$respuesta = array();
$respuesta["atributos"] = $atrr;
$respuesta["mensaje"] = $mensaje;
echo json_encode($respuesta);
?>
		