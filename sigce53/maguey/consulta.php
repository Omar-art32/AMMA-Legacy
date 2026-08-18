<?php
/**
 * consulta.php — PHP 8.3
 * Recibe el POST de buscar.php y regresa el detalle HTML del predio/vivero.
 *
 * Cambios vs 5.6:
 *  - Validación real de entrada (reemplaza el "filtro anti-XSS" de str_replace,
 *    que no protegía el SQL y corrompía los datos legítimos)
 *  - Nombres de tabla resueltos por whitelist a partir de $tipo (1|2)
 *  - Todas las consultas con sentencias preparadas
 *  - Salida escapada con htmlspecialchars (helper e())
 *  - ELIMINADO: bloque "$sqlNG / $txtEx" (guías a cobrar). Era código muerto:
 *    el div que lo mostraba estaba comentado y además tenía el cliente
 *    'C0005' hardcodeado. Si se quiere revivir, hay que parametrizar
 *    el cliente y descomentar el div en el HTML.
 *  - La consulta de "guías sin usar" se ejecuta UNA vez (antes corría dentro
 *    del loop de especies, repetida con el mismo resultado)
 *  - Variables inicializadas ($mensaje, $contGuiasDisp...) — en 8.x su uso
 *    sin definir genera warnings
 */
declare(strict_types=1);

require_once __DIR__ . '/../common/conexion.php';

header('Content-Type: text/html; charset=utf-8');

/** Escape corto para salida HTML */
function e(mixed $v): string
{
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}

// ---------------------------------------------------------------------
// Validación de entrada
//   criterio llega como "ID_PARAJE-TIPO" (lo arma procesa.php)
//   state es el No. de Control del cliente (p.ej. C0005)
// ---------------------------------------------------------------------
$criterio = (string)($_POST['criterio'] ?? '');
$state    = (string)($_POST['state'] ?? '');

if (!preg_match('/^([A-Za-z0-9]+)-([12])$/', $criterio, $m)
    || !preg_match('/^[A-Za-z0-9]+$/', $state)) {
    echo '<center><p>NO HAY NINGUN PARAJE SELECCIONADO</p></center>';
    exit;
}

$id_paraje = $m[1];
$tipo      = (int)$m[2];

// Whitelist de tablas según tipo (nunca desde la entrada del usuario)
$tipoP = ($tipo === 1) ? 'paraje'           : 'paraje_vivero';
$tipoE = ($tipo === 1) ? 'existenciaplanta' : 'existenciaplanta_vivero';
$tipoC = ($tipo === 1) ? 'constancias'      : 'constancias_vivero';

$mas    = ($tipo === 1) ? ', p.maguey_con_registro, p.servicio ' : '';
$cconst = ($tipo === 1) ? 'p.constancia_predio' : 'p.constancia_vivero';

$mensaje = '';

try {

    // -----------------------------------------------------------------
    // 1) Encabezado del paraje (constancias, cliente, registro)
    // -----------------------------------------------------------------
    $stmt = $conexion->prepare(
        "SELECT p.id_paraje, {$cconst} constanciadoc, p.constancia_extracciones constanciadocex,
                p.id_cliente, clientes.nombre AS nombrec,
                DATE_FORMAT(c.fecha,'%y') AS anio, fecha_registro AS fecha2,
                nombrep, regmaguey, LPAD(c.id_constancia,4,'0') AS constancia,
                CONCAT('P', LPAD(p.id,4,'0')) AS parajes,
                edad, p.paraje, comun.nombre, genespecie, existenciaplantas, e.edad,
                usufruto, tenencia, superficie, lng, lat,
                dis_planmetros, dis_surcometros, fecha_paraje, rcampo,
                p.status_predio, p.tipo {$mas}
         FROM clientes
         INNER JOIN {$tipoP} p ON clientes.no_cliente = p.id_cliente
         LEFT  JOIN {$tipoC} c ON c.id_paraje = p.id_paraje
         INNER JOIN {$tipoE} e ON p.id_paraje = e.id_paraje
         INNER JOIN comun   ON comun.id_comun   = e.id_comun
         INNER JOIN especie ON comun.id_especie = especie.id_especie
         WHERE p.id_cliente = ? AND p.id_paraje = ?"
    );
    $stmt->bind_param('ss', $state, $id_paraje);
    $stmt->execute();
    $fila = $stmt->get_result()->fetch_array();
    $stmt->close();

    // -----------------------------------------------------------------
    // 2) Ubicación (estado, municipio, localidad)
    // -----------------------------------------------------------------
    $stmt = $conexion->prepare(
        "SELECT municipios.nombre AS nombrem, estados.nombre AS nombree, localidades.localidad
         FROM estados
         INNER JOIN municipios  ON municipios.estado = estados.clave
         INNER JOIN localidades ON localidades.MunicipioID = municipios.id
         INNER JOIN {$tipoP} p  ON localidades.id = p.id_localidad
         WHERE p.id_cliente = ? AND p.id_paraje = ?"
    );
    $stmt->bind_param('ss', $state, $id_paraje);
    $stmt->execute();
    $filaa = $stmt->get_result()->fetch_array() ?: ['nombrem' => '', 'nombree' => '', 'localidad' => ''];
    $stmt->close();

    // -----------------------------------------------------------------
    // 3) Detalle principal (especies / existencias)
    // -----------------------------------------------------------------
    $stmt = $conexion->prepare(
        "SELECT p.id, p.id_paraje, p.id_cliente, p.nombrep, p.usufruto, p.tenencia, p.fecha_paraje,
                p.superficie, p.status_predio, p.tipo, p.lng, p.lat, p.paraje, p.rcampo,
                e.existenciaplantas, e.edad, e.regmaguey, e.id_plantas, e.cantidadini,
                e.dis_planmetros, e.dis_surcometros,
                municipios.nombre AS nombrem, estados.nombre AS nombree, localidades.localidad,
                comun.nombre, especie.genespecie
         FROM estados
         INNER JOIN municipios  ON municipios.estado = estados.clave
         INNER JOIN localidades ON localidades.MunicipioID = municipios.id
         INNER JOIN {$tipoP} p  ON localidades.id = p.id_localidad
         INNER JOIN {$tipoE} e  ON p.id_paraje = e.id_paraje
         INNER JOIN comun   ON comun.id_comun   = e.id_comun
         INNER JOIN especie ON comun.id_especie = especie.id_especie
         WHERE p.id_cliente = ? AND p.id_paraje = ?"
    );
    $stmt->bind_param('ss', $state, $id_paraje);
    $stmt->execute();
    $consultita = $stmt->get_result();
    $numfilas   = $consultita->num_rows;

    if ($numfilas === 0 || $fila === null) {
        $mensaje = '<center><p>NO HAY NINGUN PARAJE SELECCIONADO</p></center>';
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

        // Enlaces a constancias (nombres de archivo vienen de la BD)
        if ($tipo === 1) {
            $constancia = ($fila['constanciadoc'] != '')
                ? ' <a href="constancia/pdfConstanciaPredio/' . e($fila['constanciadoc']) . '" target="_blank">CONSTANCIA DE PREDIO</a>'
                : 'CONSTANCIA DE PREDIO';
        } else {
            $constancia = ($fila['constanciadoc'] != '')
                ? ' <a href="constancia/pdfConstanciaVivero/' . e($fila['constanciadoc']) . '" target="_blank">CONSTANCIA DE VIVERO</a>'
                : 'CONSTANCIA DE VIVERO';
        }
        $constanciaex = ($fila['constanciadocex'] != '')
            ? ' <a href="constancia/pdfConstanciaExtraccion/' . e($fila['constanciadocex']) . '" target="_blank">CONSTANCIA DE EXTRACCIÓN</a>'
            : 'CONSTANCIA DE EXTRACCIÓN';

        echo '
        <div class="form-group row">
            <div class="form-group col-md-6">
                <fieldset><legend align="">DATOS</legend></fieldset>
                <div class="form-group">
                    <span class="col-md-5">NO. DE CONTROL: </span>
                    <label class="col-md-7 control-label">&nbsp;' . e($state) . '</label>
                </div>
                <div class="form-group">
                    <span class="col-md-5">NOMBRE: </span>
                    <label class="col-md-7 control-label">&nbsp;' . e($fila['nombrec']) . '</label>
                </div>
                <div class="form-group">
                    <span class="col-md-5">NOMBRE DEL PRODUCTOR: </span>
                    <label for="" class="col-md-7 control-label">&nbsp;' . e($fila['nombrep']) . '</label>
                </div>
                <div class="form-group">
                    <span class="col-md-5">NOMBRE DEL REPRESENTANTE EN CAMPO: </span>
                    <label for="" class="col-md-7 control-label">&nbsp;' . e($fila['rcampo']) . '</label>
                </div>
            </div>
            <div class="form-group col-md-6">
                <fieldset><legend align="">DATOS DEL PREDIO</legend></fieldset>
                <div class="form-group">
                    <span class="col-md-5">NO.CONSTANCIA: </span>
                    <label for="" class="col-md-7 control-label">&nbsp;' . $constancia . '</label>
                </div>
                <div class="form-group">
                    <span class="col-md-5">No. DE PREDIO: </span>
                    <label for="" class="col-md-7 control-label">&nbsp;' . e($fila['id_paraje']) . '</label>
                </div>
                <div class="form-group">
                    <span class="col-md-5">NOMBRE DEL PARAJE: </span>
                    <label for="" class="col-md-7 control-label">&nbsp;' . e($fila['paraje']) . '</label>
                </div>
                <div class="form-group">
                    <span class="col-md-5">ESTADO: </span>
                    <label for="" class="col-md-7 control-label">&nbsp;' . e($filaa['nombree']) . '</label>
                </div>
                <div class="form-group">
                    <span class="col-md-5">MUNICIPIO: </span>
                    <label for="" class="col-md-7 control-label">&nbsp;' . e($filaa['nombrem']) . '</label>
                </div>
                <div class="form-group">
                    <span class="col-md-5">LOCALIDAD: </span>
                    <label for="" class="col-md-7 control-label">&nbsp;' . e($filaa['localidad']) . '</label>
                </div>
                <div class="form-group">
                    <span class="col-md-5">USUFRUTO DE LA TIERRA: </span>
                    <label for="" class="col-md-7 control-label">&nbsp;' . e($fila['usufruto']) . '</label>
                </div>
                <div class="form-group">
                    <span class="col-md-5">TENENCIA DE LA TIERRA: </span>
                    <label for="" class="col-md-7 control-label">&nbsp;' . e($fila['tenencia']) . '</label>
                </div>
                <div class="form-group">
                    <span class="col-md-5">SUPERFICIE: </span>
                    <label for="" class="col-md-7 control-label">&nbsp;' . e($fila['superficie']) . '</label>
                </div>
                <div class="form-group">
                    <span class="col-md-5">LONGITUD: </span>
                    <label for="" class="col-md-7 control-label">&nbsp;' . e($fila['lng']) . '</label>
                </div>
                <div class="form-group">
                    <span class="col-md-5">LATITUD: </span>
                    <label for="" class="col-md-7 control-label">&nbsp;' . e($fila['lat']) . '</label>
                </div>
                ';

        if ($tipo === 1) {
            $tMaguey = match ((int)$fila['maguey_con_registro']) {
                1       => 'EN SITIO',
                2       => 'DOCUMENTAL ' . $fila['servicio'],
                default => '',
            };
            echo '
                <div class="form-group">
                    <span class="col-md-5">MAGUEY CON REGISTRO:: </span>
                    <label for="" class="col-md-7 control-label">&nbsp; ' . e($tMaguey) . '</label>
                </div>';
        }

        echo '
                <div class="form-group">
                    <span class="col-md-5">FECHA DE REGISTRO:: </span>
                    <label for="" class="col-md-7 control-label">&nbsp;' . e($fila['fecha_paraje']) . '</label>
                </div>

                <div class="form-group">
                    <span class="col-md-5">CONSTANCIA DE EXTRACCIÓN: </span>
                    <label for="" class="col-md-7 control-label">&nbsp;' . $constanciaex . '</label>
                </div>
            </div>
        </div>
        ';

        $status_predio = (int)$fila['status_predio'];
        $tipoParaje    = (int)$fila['tipo'];
        $checked1 = ($status_predio === 1) ? 'checked' : '';
        $checked2 = ($status_predio === 0) ? 'checked' : '';
        $idParajeJs = e($fila['id_paraje']);

        // NOTA: id_paraje va entre comillas ('P1') — en el original iba sin
        // comillas, lo que generaba un ReferenceError en JS y el radio
        // Mostrar/Ocultar nunca llegaba a disparar el AJAX.
        echo 'ESTATUS PREDIO:
        <strong>
        <input type="radio" id="predio_activo" name="status_predio" value="1" onclick="actualizarPredio(this.value,\'' . $idParajeJs . '\',' . $tipoParaje . ')" ' . $checked1 . '>
        <label for="predio_activo">Mostrar</label>
        <input type="radio" id="predio_inactivo" name="status_predio" value="0" onclick="actualizarPredio(this.value,\'' . $idParajeJs . '\',' . $tipoParaje . ')" ' . $checked2 . '>
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

        // Statement reutilizable para el fallback de tapadas/litros
        $stmtTapadas = $conexion->prepare(
            'SELECT CONCAT(pe.tapada) tapada, SUM(pe.lts_producidos) lts_producidos,
                    DATE(hev.fecha_realizo) fecha_realizo
             FROM rv_produccion_entrada pe
                 INNER JOIN rv_produccion_ensamble pen ON pe.id_produccion_entrada = pen.id_produccion_entrada
                 LEFT  JOIN historial_extraccion_verificadores hev ON pe.no_guia = hev.no_guia
             WHERE pen.no_guia = ?
             GROUP BY hev.no_guia'
        );

        while ($row = $consultita->fetch_array()) {

            // e.id_plantas es una lista "1,2,3" almacenada en la BD.
            // No se puede bindear un IN() variable: se sanitiza a dígitos y comas.
            $idPlantasList = preg_replace('/[^0-9,]/', '', (string)$row['id_plantas']);

            $numfilasep = 0;
            $ep         = null;

            if ($idPlantasList !== '' && trim($idPlantasList, ',') !== '') {
                $sqlC = "SELECT hev.*, v.*, ep.*, c.id_extraccion, c.fecha fechac,
                                GROUP_CONCAT(pe.tapada) tapadas, SUM(pe.lts_producidos) sum_lts,
                                DATE(hev.fecha_realizo) fecha_realizo
                         FROM {$tipoE} ep
                         LEFT JOIN historial_extraccion_verificadores hev ON hev.id_plantas = ep.id_plantas
                         LEFT JOIN cextracciones c ON hev.no_guia = c.id_extraccion
                         LEFT JOIN verificadores v ON hev.id_verificador = v.id_us
                         LEFT JOIN rv_produccion_entrada pe ON hev.no_guia = pe.no_guia
                         WHERE ep.id_plantas IN ({$idPlantasList})
                         GROUP BY hev.id_extraccion
                         ORDER BY hev.no_guia";
                $ep         = $conexion->query($sqlC);
                $numfilasep = $ep->num_rows;
            }

            $class = ($numfilasep > 0 && $tipo === 1) ? "class='header' " : '';
            $txt   = ($numfilasep > 0 && $tipo === 1) ? '<strong>+</strong> ' : '';

            echo "
                <tr {$class} style='font-size: 14px;'>
                    <td class=\"nombre\"><b>" . e($row['nombre']) . "</b></td>
                    <td class=\"genespecie\"><b>" . e($row['genespecie']) . "</b></td>
                    <td class=\"regmaguey\"><b>" . e($row['regmaguey']) . "</b></td>
                    <td class=\"cantini\" align=\"center\"><b>" . e($row['cantidadini']) . "</b></td>
                    <td class=\"existenciaplantas\" align=\"center\"><b>" . e($row['existenciaplantas']) . "</b></td>
                    <td class=\"edad\" align=\"center\"><b>" . e($row['edad']) . "</b></td>
                    <td class=\"dis_planmetros\" align=\"center\"><b>" . e($row['dis_planmetros']) . "</b></td>
                    <td class=\"edad\" align=\"center\"><b>" . e($row['dis_surcometros']) . "</b></td>
                    <td class=\"\" align=\"center\" colspan='2'><b>{$txt}</b></td>
                </tr>";

            if ($tipo === 1 && $ep !== null) {
                if ($numfilasep > 0) {
                    echo "
                        <tr style='width: 25%;
                        text-align: left;
                        vertical-align: top;
                        border: 1px solid #000;
                        border-collapse: collapse;
                        padding: 0.3em; font-weight: bold;
                        caption-side: bottom; font-size: 12px; display: none;' bgcolor='#ABEBC6'>
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

                while ($filaep = $ep->fetch_array()) {
                    $tapadas = '';
                    $lts_producidos = '';
                    $fecha_realizo  = '';

                    if (($filaep['tapadas'] ?? '') != '') {
                        $tapadas        = $filaep['tapadas'];
                        $lts_producidos = $filaep['sum_lts'];
                        $fecha_realizo  = $filaep['fecha_realizo'];
                    } elseif (($filaep['no_guia'] ?? '') != '') {
                        // Fallback: guías ensambladas
                        $noGuia = (string)$filaep['no_guia'];
                        $stmtTapadas->bind_param('s', $noGuia);
                        $stmtTapadas->execute();
                        $resultT = $stmtTapadas->get_result()->fetch_all(MYSQLI_ASSOC);
                        foreach ($resultT as $rowt) {
                            $tapadas        = $rowt['tapada'];
                            $lts_producidos = $rowt['lts_producidos'];
                            $fecha_realizo  = $filaep['fecha_realizo'];
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
                    if (($filaep['no_guia'] ?? '') != '') {
                        echo "
                            <td>" . e($filaep['id_extraccion']) . "</td>
                            <td>" . e($filaep['fechac']) . "</td>
                            <td >" . e($filaep['no_cliente_envia']) . "</td>
                            <td>" . e($filaep['no_cliente_recibe']) . "</td>
                            <td>" . e($filaep['extraccion']) . "</td>
                            <td>" . e($filaep['nombre']) . "</td>
                            <td>" . e($fecha_realizo) . "</td>
                            <td>" . e($tapadas) . "</td>
                            <td>" . e($lts_producidos) . "</td>
                        ";
                    }
                    echo '</tr>';
                }
            }
        }

        $stmtTapadas->close();
        echo '</table>';

        // -------------------------------------------------------------
        // Guías sin usar — UNA sola consulta (antes: repetida por especie)
        // -------------------------------------------------------------
        $contGuiasDisp = '';
        if ($tipo === 1) {
            $stmtG = $conexion->prepare(
                'SELECT c.id_extraccion, c.fecha fechac, hev.id_plantas
                 FROM cextracciones c
                     LEFT JOIN historial_extraccion_verificadores hev ON c.id_extraccion = hev.no_guia
                 WHERE c.id_paraje = ?'
            );
            $stmtG->bind_param('s', $id_paraje);
            $stmtG->execute();
            $resultG = $stmtG->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmtG->close();

            foreach ($resultG as $rowg) {
                if (($rowg['id_plantas'] ?? '') == '') {
                    $contGuiasDisp .= '<tr>
                        <td>' . e($rowg['id_extraccion']) . '</td>
                        <td>' . e($rowg['fechac']) . '</td>
                        <td>D I S P O N I B L E</td>
                        </tr>
                    ';
                }
            }
        }

        if ($contGuiasDisp !== '') {
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
            " . $contGuiasDisp . '</table>';
        }

        echo $contGuiasDisp;
    }

    echo $mensaje;

} catch (mysqli_sql_exception $e) {
    error_log('[consulta.php] ' . $e->getMessage());
    // La salida es HTML en streaming: si la excepción ocurre a media tabla,
    // los headers ya se enviaron y http_response_code() generaría un warning.
    if (!headers_sent()) {
        http_response_code(500);
    }
    echo '<center><p>Ocurrió un error al consultar el predio. Intente de nuevo.</p></center>';
} finally {
    $conexion->close();
}
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