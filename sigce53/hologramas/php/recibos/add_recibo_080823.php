<?php
require 'mc_table.php';
require_once '../../../common/cfg_server.php';
class PDF extends FPDF
{
    public $col = 0;
}
include '../../../common/conexion.php';
$conexion->autocommit(false);
//

$grales = json_decode($_POST['grales'], true);

$usr = $grales['usuario'];
$anio_r = $grales['anio_recibo'];

$sqlNoRecibo = "select if(max(id_recibo) is null,0,max(id_recibo))  from h_salidas where anio_rcbo=$anio_r";

$result = $conexion->query($sqlNoRecibo);
$row = $result->fetch_row();
$recibo = $row[0] + 1;

$id_recibo = 'AR' . str_pad($recibo, 4, '0', STR_PAD_LEFT) . '/' . $anio_r;
$cliente = $grales['cte'];
$empresa = utf8_decode($grales['empresa']);
//$empresa="PRODUCTORES DE MIELES Y JARABES DE MAGUEY DE ZARAGOZA DE SOLIS MUNICIPIO DE VILLA DE GUADALUPE S.L.P. S.C. DE R.L.";
//$nombre_marca=$grales['nombre_marc'];
$nombre_marca = get_marca($cliente, $grales['marca']);
$sol = utf8_decode(strtoupper($grales['solicitud']));
$fecha_e = utf8_decode($grales['fecha_entrega']);
$destino = utf8_decode($grales['destino']);
$obs_ent = utf8_decode($grales['obs_entrega']);
$fecha = date("Y-m-d H:i:s");
//obtenemos los arreglos de los datos en detalle
$arr_data = json_decode($_POST['arr_data'], true);
$arr_det = $arr_data['data'];
$n_r = count($arr_det);
//obtenemos estado y tipo del primer registro
$edo_gen = $arr_det[0]['edo'];
$tipo_gen = get_tipo($arr_det[0]['tipo']);

$nota1 = utf8_decode("El  cliente de AMMA se obliga a utilizar los folios de hologramas entregados para los lotes descritos en la solicitud de ingreso, en caso de utilizar dichos hologramas en lotes distintos a los mencionados en el presente acuse, los lotes deberán ser certificados o en proceso de certificación, por lo que deberá notificar al inspector en turno dicho uso para que constate el cumplimiento con la NOM Mezcal vigente y quede manifestado en dictamen. En caso contrario se procederá a cancelar los hologramas y no se realizará la emisión de los certificados correspondientes.");
$nota2 = utf8_decode("De conformidad con el numeral 7. Comercialización de la NOM-070-SCFI-2016,  el Sello de Certificación (holograma) debe colocarse abarcando parte de la etiqueta que se encuentra en la superficie principal de exhibición y parte del envase.");

try
{
    $pdf = new PDF_MC_Table();
    $pdf->AliasNbPages();
    $pdf->SetDisplayMode(100, 'continuous');
    $pdf->AddPage('P', 'Letter');
    $pdf->Image('../../../images/bg_hologramas_oc.jpg','0','0','216','280','JPG');
    $pdf->SetTextColor(0, 0, 0);

    $pdf->SetAutoPageBreak(true, 5);
    //---------------RECIBO 1--------------------------------------------
    //INSERTAR LOGO
    $y = 8;
    //$pdf->Image('../../../images/logo.png', 12, $y, 45, 19);
    //$pdf->Image('../../images/logo2.jpg',168,18,32,17);
    $pdf->SetFont('Calibri-Bold', '', 16);
    $y = $y + 2; //18
    $pdf->SetXY(67, $y);
    //$pdf->Cell(90, 5, utf8_decode('Asociación de Maguey y Mezcal Artesanal'), '0', '', 'C');
    //CABECERA
    $pdf->SetFont('Calibri-Bold', '', 9);
    $y = $y + 7; //28
    $pdf->SetXY(67, $y);
    //$pdf->Cell(90, 5, utf8_decode('Asociación de Maguey y Mezcal Artesanal A. C.'), 0, '', 'C');
    //TITULO AREA
    $pdf->SetFont('Calibri-Bold', '', 16);
    $y = $y + 10; //36
    $pdf->SetXY(106, $y);
    $pdf->Cell(100, 5, utf8_decode('O R G A N I S M O  D E  C E R T I F I C A C I Ó N'), 0, '', 'C');

    //TITULO DOCUMENTO
    $pdf->SetFont('Calibri-Bold', '', 12);
    $y = $y + 6; //46
    $pdf->SetXY(130, $y);
    $pdf->Cell(65, 5, 'ACUSE DE RECIBO DE HOLOGRAMAS', 0, '', 'C');
    //datos recibo
    $y = $y + 6; //40
    $pdf->SetFont('Calibri-Bold', '', 10);
    $pdf->SetXY(150, $y);
    $pdf->Cell(6, 5, 'No.', 0, '', 'C');

    $pdf->SetFont('Calibri-Bold', '', 10);
    $pdf->SetXY(157, $y);
    $pdf->Cell(20, 5, $id_recibo, 0, 0, 'R');

    $y = $y + 6; //46
    $pdf->SetFont('Calibri-Bold', '', 10);
    $pdf->SetXY(148, $y);
    $pdf->Cell(30, 5, fecha($fecha_e), 0, 0, 'R');

    //CUERPO RECIBO
    $y = $y + 8; //54
    $pdf->SetFont('Calibri', '', 9);
    $pdf->SetXY(20, $y);
    $pdf->Cell(23, 5, 'EMPRESA:', 0, '', 'L');
    $pdf->SetFont('Calibri-Bold', '', 9);
    $pdf->SetXY(42, $y);
    $num_l = $pdf->NbLines(160, $empresa);
    $next_esp = 6 * $num_l;
    $pdf->MultiCell(160, 5, $empresa, 0, 'L', 0);

    $y = $y + $next_esp; //66
    $pdf->SetFont('Calibri', '', 9);
    $pdf->SetXY(20, $y);
    $pdf->Cell(23, 5, 'MARCA:', 0, '', 'L');
    $pdf->SetFont('Calibri-Bold', '', 9);
    $pdf->SetXY(42, $y);
    $pdf->Cell(110, 5, $nombre_marca, 0, '', 'L');

    $y = $y + 6; //82
    $pdf->SetFont('Calibri', '', 9);
    $pdf->SetXY(20, $y);
    //$pdf->Cell(23, 5, 'SOLICITUD:', 0, '', 'L');
    $pdf->SetFont('Calibri-Bold', '', 9);
    $pdf->SetXY(42, $y);
    //$pdf->Cell(110, 5, $sol, 0, '', 'L');
    //DESTINO
    /*$pdf->SetFont('Calibri','',9);
    $pdf->SetXY(90,$y);
    $pdf->Cell(23,5,'DESTINO:',0,'','L');
    $pdf->SetFont('Calibri','B',9);
    $pdf->SetXY(110,$y);
    $pdf->Cell(110,5,$destino,0,'','L');*/

    if ($edo_gen != '') {
        $y = $y + 6; //82
        $pdf->SetFont('Calibri', '', 9);
        $pdf->SetXY(20, $y);
        $pdf->Cell(23, 5, 'ESTADO:', 0, '', 'L');
        $pdf->SetFont('Calibri-Bold', '', 9);
        $pdf->SetXY(42, $y);
        $pdf->Cell(110, 5, $edo_gen, 0, '', 'L');
        //DESTINO
        $pdf->SetFont('Calibri', '', 9);
        $pdf->SetXY(90, $y);
        $pdf->Cell(23, 5, 'TIPO:', 0, '', 'L');
        $pdf->SetFont('Calibri-Bold', '', 9);
        $pdf->SetXY(110, $y);
        $pdf->Cell(110, 5, $tipo_gen, 0, '', 'L');
    }
    $next_esp = 6;
    for ($x = 0; $x < $n_r; $x++) {
        $id_exs = $arr_det[$x]['id_exs'];
        $borrar_fila = $arr_det[$x]['borrar'];
        $serie = $arr_det[$x]['serie'];
        $marca = $arr_det[$x]['marca'];
        $edo = $arr_det[$x]['edo'];
        $tipo = $arr_det[$x]['tipo'];
        $fi = $arr_det[$x]['fol_ini'];
        $ff = $arr_det[$x]['fol_fin'];
        $serie = $arr_det[$x]['serie'];
        $subtotal = $arr_det[$x]['cant'];
        $total = $arr_det[$x]['cant_real'];
        //mermas
        $mermas = $arr_det[$x]['tot_mermas'];
        $motivo_merma = utf8_decode($arr_det[$x]['motivo_merma']);
        $fol_m1 = $arr_det[$x]['mermas_folio'];
        $fol_m1_num = $arr_det[$x]['mermas_nums'];
        $clientesel = ($arr_det[$x]['cliente_crm'] != '')? $arr_det[$x]['cliente_crm']: $cliente;

        $folios = $clientesel . $marca . str_pad($fi, 7, '0', STR_PAD_LEFT) . $serie . " - " . $clientesel . $marca . str_pad($ff, 7, '0', STR_PAD_LEFT) . $serie;

        $y = $y + $next_esp;

        $pdf->SetFont('Calibri', '', 9);
        $pdf->SetXY(20, $y);
        $pdf->Cell(23, 7, 'FOLIOS:', 0, '', 'L');
        $pdf->SetFont('Calibri-Bold', '', 10);
        $pdf->SetXY(42, $y);
        $pdf->Cell(110, 7, $folios, 0, '', 'L');

        $pdf->SetFont('Calibri', '', 9);
        $pdf->SetXY(142, $y);
        $pdf->Cell(23, 5, 'PIEZAS:', 0, '', 'L');
        $pdf->SetFont('Calibri-Bold', '', 10);
        $pdf->SetXY(162, $y);
        $pdf->Cell(110, 5, $total, 0, '', 'L');

        $next_esp = 6; //si entra al if de mermas next_esp aumenta de acuerdo al numero de filas
        if ($mermas != 0) {
            $y = $y + 7; //54
            $pdf->SetFont('Calibri', '', 9);
            $pdf->SetXY(20, $y);
            $pdf->Cell(23, 5, 'MERMAS:', 0, '', 'L');
            $pdf->SetFont('Calibri-Bold', '', 9);
            $pdf->SetXY(42, $y);
            $num_l = $pdf->NbLines(160, $fol_m1);
            $next_esp = 6 * $num_l;
            $pdf->MultiCell(160, 5, $fol_m1, 0, 'L', 0);
        }

        $sql_ins = "INSERT INTO h_salidas (id_recibo, anio_rcbo, no_cliente, marca, serie,edo,tipo, solicitud, destino, fecha_entr, fi1, ff1, m1, fol_m1, fol_m1_num, motivo, obs_ent, se1, f_cap, linea, usr, cliente_crm) VALUES
					('{$recibo}', '{$anio_r}', '{$cliente}', '{$marca}', '{$serie}','{$edo}','{$tipo}', '{$sol}', '{$destino}', '{$fecha_e}', '{$fi}', '{$ff}','{$mermas}', '{$fol_m1}', '{$fol_m1_num}', '{$motivo_merma}', '{$obs_ent}', '{$total}', '{$fecha}', '0',  '{$usr}', '".$arr_det[$x]['cliente_crm']."');";
        if ($borrar_fila == 1) {
            $sql_existencias = "delete from h_existencias where id_existencias=$id_exs";
        } else {
            $sql_existencias = "update h_existencias set fol_ini=if($ff<fol_fin,$ff+1,0), fol_fin=if($ff<fol_fin,fol_fin,0), existencias=existencias-$subtotal where id_existencias=$id_exs";
        }
        $result = $conexion->query($sql_ins);
        if ($result != true) {
            throw new Exception("Error al registrar la salida." . $sql_ins);
        }

        $res_up = $conexion->query($sql_existencias);
        if ($res_up != true) {
            throw new Exception("Error al actualizar las existencias." . $sql_existencias);
        }

        $conexion->commit();

    }
    $y = $y + $next_esp;
    $conexion->close();
    //DATOS RECIBE
    if ($y < 60) {
        $y = 58; //54
        $pdf->SetFont('Calibri-Bold', '', 8);
        $pdf->SetXY(20, $y);
        $pdf->Cell(23, 5, 'NOTA:', 0, '', 'L');
        $pdf->SetFont('Calibri', '', 7);
        $pdf->SetXY(32, $y);
        $num_l = $pdf->NbLines(160, $nota);
        $next_esp = 2 * $num_l;
        $pdf->MultiCell(160, 3, $nota, 0, 'J', 0);

        $y += 24; //106
        $pdf->SetFont('Calibri', '', 9);
        $pdf->SetXY(20, $y);
        $pdf->Cell(23, 5, 'RECIBE:', 0, '', 'L');
        $y = $y + 4; //110
        $pdf->line(42, $y, 120, $y);

        $y += 4; //106  -- 100
        $pdf->SetFont('Calibri', '', 9);
        $pdf->SetXY(20, $y);
        $pdf->Cell(23, 5, 'FECHA:', 0, '', 'L');
        $pdf->SetFont('Calibri', '', 9);
        $pdf->SetXY(78, $y);
        $pdf->Cell(23, 5, 'HORA:', 0, '', 'L');
        $pdf->SetFont('Calibri', '', 9);
        $pdf->SetXY(129, $y);
        $pdf->Cell(23, 5, 'FIRMA:', 0, '', 'L');

        $y = $y + 4; //110
        $pdf->line(42, $y, 70, $y);
        $pdf->line(92, $y, 120, $y);
        $pdf->line(143, $y, 185, $y);

        $y = $y + 2; //119
        //$pdf->Image('../../../images/footer.jpg', 15, $y, 185, 19);

        /*$y=$y+9;//119
        $pdf->SetFont('Calibri','',9);
        $pdf->SetXY(179,$y);
        $pdf->Cell(23,5,'FQ-171/03',0,'','L');
        $y=$y+4;//123
        $pdf->line(20,$y,200,$y);
        $y=$y+1;//124
        $pdf->SetFont('Calibri','',8);
        $pdf->SetXY(60,$y);
        $dir="Cofre de Perote No. 325 Col. Volcanes, Oaxaca, Oaxaca, C.P. 68020";
        $pdf->Cell(90,5,$dir,0,'','L');
        $y=$y+5;//129
        $pdf->SetFont('Calibri','',11);
        $pdf->SetXY(20,$y);
        $dir2="www.crm.org.mx    info@crm.org.mx";
        $pdf->Cell(90,5,$dir2,0,'','L');

        $pdf->SetFont('Calibri','',9);
        $pdf->SetXY(120,$y);
        $dir3=utf8_decode("Télefonos: 01(951) 517 45 79   y   01(951) 206 18 57");
        $pdf->Cell(90,5,$dir3,0,'','L');*/

        $pdf->SetFont('Calibri', '', 10);
        $pdf->SetXY(5, 135); //esta es fija
        $linea = '.........................................................................................................';
        $pdf->Cell(180, 5, $linea . $linea, 0, '', 'L');

        //---------------RECIBO 2--------------------------------------------

        $y = 145;

        ///--------------------------------
        //$pdf->Image('../../../images/logo.png', 12, $y, 45, 19);
        //$pdf->Image('../../images/logo2.jpg',168,18,32,17);
        $pdf->SetFont('Calibri-Bold', '', 16);
        $y = $y + 2; //18
        $pdf->SetXY(67, $y);
        $pdf->Cell(90, 5, utf8_decode('Asociación de Maguey y Mezcal Artesanal'), '0', '', 'C');
        //CABECERA
        $pdf->SetFont('Calibri-Bold', '', 9);
        $y = $y + 7; //28
        $pdf->SetXY(67, $y);
        $pdf->Cell(90, 5, utf8_decode('Asociación de Maguey y Mezcal Artesanal'), 0, '', 'C');
        //TITULO AREA
        $pdf->SetFont('Calibri-Bold', '', 12);
        $y = $y + 7; //36
        $pdf->SetXY(62, $y);
        $pdf->Cell(100, 5, 'O R G A N I S M O  D E  C E R T I F I C A C I O N', 0, '', 'C');
        //-------------------------------

        //TITULO DOCUMENTO
        $pdf->SetFont('Calibri-Bold', '', 10);
        $y = $y + 7; //46
        $pdf->SetXY(62, $y);
        $pdf->Cell(90, 5, 'ACUSE DE RECIBO DE HOLOGRAMAS', 0, '', 'C');
        //datos recibo
        $y = $y - 6; //40
        $pdf->SetFont('Calibri-Bold', '', 10);
        $pdf->SetXY(171, $y);
        $pdf->Cell(6, 5, 'No.', 0, '', 'C');

        $pdf->SetFont('Calibri-Bold', '', 10);
        $pdf->SetXY(178, $y);
        $pdf->Cell(20, 5, $id_recibo, 0, 0, 'R');

        $y = $y + 6; //46
        $pdf->SetFont('Calibri-Bold', '', 10);
        $pdf->SetXY(148, $y);
        $pdf->Cell(50, 5, fecha($fecha_e), 0, 0, 'R');

        //CUERPO RECIBO
        //CUERPO RECIBO
        $y = $y + 8; //54
        $pdf->SetFont('Calibri', '', 9);
        $pdf->SetXY(20, $y);
        $pdf->Cell(23, 5, 'EMPRESA:', 0, '', 'L');
        $pdf->SetFont('Calibri-Bold', '', 9);
        $pdf->SetXY(42, $y);
        $num_l = $pdf->NbLines(160, $empresa);
        $next_esp = 6 * $num_l;
        $pdf->MultiCell(160, 5, $empresa, 0, 'L', 0);

        $y = $y + $next_esp; //66
        $pdf->SetFont('Calibri', '', 9);
        $pdf->SetXY(20, $y);
        $pdf->Cell(23, 5, 'MARCA:', 0, '', 'L');
        $pdf->SetFont('Calibri-Bold', '', 9);
        $pdf->SetXY(42, $y);
        $pdf->Cell(110, 5, $nombre_marca, 0, '', 'L');

        $y = $y + 6; //82
        $pdf->SetFont('Calibri', '', 9);
        $pdf->SetXY(20, $y);
        //$pdf->Cell(23, 5, 'SOLICITUD:', 0, '', 'L');
        $pdf->SetFont('Calibri-Bold', '', 9);
        $pdf->SetXY(42, $y);
        //$pdf->Cell(110, 5, $sol, 0, '', 'L');
        //DESTINO
        /*$pdf->SetFont('Calibri','',9);
        $pdf->SetXY(90,$y);
        $pdf->Cell(23,5,'DESTINO:',0,'','L');
        $pdf->SetFont('Calibri','B',9);
        $pdf->SetXY(110,$y);
        $pdf->Cell(110,5,$destino,0,'','L');*/

        if ($edo_gen != '') {
            $y = $y + 6; //82
            $pdf->SetFont('Calibri', '', 9);
            $pdf->SetXY(20, $y);
            $pdf->Cell(23, 5, 'ESTADO:', 0, '', 'L');
            $pdf->SetFont('Calibri-Bold', '', 9);
            $pdf->SetXY(42, $y);
            $pdf->Cell(110, 5, $edo_gen, 0, '', 'L');
            //DESTINO
            $pdf->SetFont('Calibri', '', 9);
            $pdf->SetXY(90, $y);
            $pdf->Cell(23, 5, 'TIPO:', 0, '', 'L');
            $pdf->SetFont('Calibri-Bold', '', 9);
            $pdf->SetXY(110, $y);
            $pdf->Cell(110, 5, $tipo_gen, 0, '', 'L');
        }
        //INICIA CICLO PARA SEGUNDO RECIBO
        $next_esp = 6;
        for ($x = 0; $x < $n_r; $x++) {
            $id_exs = $arr_det[$x]['id_exs'];
            $borrar_fila = $arr_det[$x]['borrar'];
            $serie = $arr_det[$x]['serie'];
            $marca = $arr_det[$x]['marca'];
            $edo = $arr_det[$x]['edo'];
            $tipo = $arr_det[$x]['tipo'];
            $fi = $arr_det[$x]['fol_ini'];
            $ff = $arr_det[$x]['fol_fin'];
            $serie = $arr_det[$x]['serie'];
            $subtotal = $arr_det[$x]['cant'];
            $total = $arr_det[$x]['cant_real'];
            $mermas = $arr_det[$x]['tot_mermas'];
            $motivo_merma = utf8_decode($arr_det[$x]['motivo_merma']);
            $fol_m1 = $arr_det[$x]['mermas_folio'];
            $fol_m1_num = $arr_det[$x]['mermas_nums'];
            $clientesel = ($arr_det[$x]['cliente_crm'] != '')? $arr_det[$x]['cliente_crm']: $cliente;

            $folios = $clientesel . $marca . str_pad($fi, 7, '0', STR_PAD_LEFT) . $serie . " - " . $clientesel . $marca . str_pad($ff, 7, '0', STR_PAD_LEFT) . $serie;
            $y = $y + $next_esp;

            $pdf->SetFont('Calibri', '', 9);
            $pdf->SetXY(20, $y);
            $pdf->Cell(23, 7, 'FOLIOS:', 0, '', 'L');
            $pdf->SetFont('Calibri-Bold', '', 10);
            $pdf->SetXY(42, $y);
            $pdf->Cell(110, 7, $folios, 0, '', 'L');

            $pdf->SetFont('Calibri', '', 9);
            $pdf->SetXY(142, $y);
            $pdf->Cell(23, 5, 'PIEZAS:', 0, '', 'L');
            $pdf->SetFont('Calibri-Bold', '', 10);
            $pdf->SetXY(162, $y);
            $pdf->Cell(110, 5, $total, 0, '', 'L');

            //si entra al if de mermas next_esp aumenta de acuerdo al numero de filas
            if ($mermas != 0) {
                $y = $y + 7; //54
                $pdf->SetFont('Calibri', '', 9);
                $pdf->SetXY(20, $y);
                $pdf->Cell(23, 5, 'MERMAS:', 0, '', 'L');
                $pdf->SetFont('Calibri-Bold', '', 9);
                $pdf->SetXY(42, $y);
                $num_l = $pdf->NbLines(160, $fol_m1);
                $next_esp = 6 * $num_l;
                $pdf->MultiCell(160, 5, $fol_m1, 0, 'L', 0);
            }

        }
        //DATOS RECIBE
    } //fin if y<100 para imprimir 2 recibos

    $y = 195; //54
    $pdf->SetFont('Calibri-Bold', '', 9);
    $pdf->SetXY(20, $y);
    $pdf->Cell(23, 5, 'NOTA 1:', 0, '', 'L');
    $pdf->SetFont('Calibri', '', 8);
    $pdf->SetXY(35, $y);
    $num_l = $pdf->NbLines(160, $nota1);
    $next_esp = 2 * $num_l;
    $pdf->MultiCell(160, 3, $nota1, 0, 'J', 0);

    $y += $next_esp+10;
    $pdf->SetFont('Calibri-Bold', '', 9);
    $pdf->SetXY(20, $y);
    $pdf->Cell(23, 5, 'NOTA 2:', 0, '', 'L');
    $pdf->SetFont('Calibri-Bold', '', 8);
    $pdf->SetXY(35, $y);
    $pdf->MultiCell(160, 3, $nota2, 0, 'J', 0);

    $y += 15; //106
    $pdf->SetFont('Calibri', '', 8);
    $pdf->SetXY(20, $y);
    $pdf->Cell(23, 5, 'RECIBE:', 0, '', 'L');
    $y = $y + 4; //110
    $pdf->line(42, $y, 120, $y);

    $y += 6; //106  -- 100
    $pdf->SetFont('Calibri', '', 8);
    $pdf->SetXY(20, $y);
    $pdf->Cell(23, 5, 'FECHA:', 0, '', 'L');
    $pdf->SetFont('Calibri', '', 8);
    $pdf->SetXY(78, $y);
    $pdf->Cell(23, 5, 'HORA:', 0, '', 'L');
    $pdf->SetFont('Calibri', '', 9);
    $pdf->SetXY(129, $y);
    $pdf->Cell(23, 5, 'FIRMA:', 0, '', 'L');
    $y = $y + 4; //110
    $pdf->line(42, $y, 70, $y);
    $pdf->line(92, $y, 120, $y);
    $pdf->line(143, $y, 185, $y);

    $y = $y + 10; //119
    //$pdf->Image('../../../images/footer.png', 15, $y, 185, 19);

    /*$y=$y+9;//119
    $pdf->SetFont('Calibri','',9);
    $pdf->SetXY(179,$y);
    $pdf->Cell(23,5,'FQ-171/03',0,'','L');
    $y=$y+4;//123
    $pdf->line(20,$y,200,$y);
    $y=$y+1;//124
    $pdf->SetFont('Calibri','',8);
    $pdf->SetXY(60,$y);
    $dir="Cofre de Perote No. 325 Col. Volcanes, Oaxaca, Oaxaca, C.P. 68020";
    $pdf->Cell(90,5,$dir,0,'','L');
    $y=$y+5;//129
    $pdf->SetFont('Calibri','',11);
    $pdf->SetXY(20,$y);
    $dir2="www.crm.org.mx    info@crm.org.mx";
    $pdf->Cell(90,5,$dir2,0,'','L');

    $pdf->SetFont('Calibri','',9);
    $pdf->SetXY(120,$y);
    $dir3=utf8_decode("Télefonos: 01(951) 517 45 79   y   01(951) 206 18 57");
    $pdf->Cell(90,5,$dir3,0,'','L');*/

    $nombre = str_replace('/', '_', $id_recibo);
    $file = $nombre . '.pdf';
    $pdf->Output('pdf_recibos/' . $file, 'F');

    $dir_file = "http://" . $svr_dir . "/sigce/hologramas/php/recibos/pdf_recibos/" . $file;
    //Redirect
    //echo json_encode(array('status' => 'correcto','msj'=>$dir_file,'sql'=>utf8_encode($sql_ins)));
    echo json_encode(array('status' => 'OK', 'msj' => $dir_file, 'sql' => 'nothing to do'));
    //Redirect
    //echo json_encode(array('status' => 'correcto','msj'=>$file));

} catch (Exception $e) {
    $conexion->rollback();
    echo json_encode(array("status" => "error", "msj" => $e->getMessage()));
    $conexion->close();
}
function get_tipo($num_t)
{
    switch ($num_t) {
        case 1:
            {
                return "MEZCAL";
            }
        case 2:
            {
                return "MEZCAL ARTESANAL";
            }
        case 3:
            {
                return "MEZCAL ANCESTRAL";
            }
        default:
            {
                return "N/A";
            }
    }

}
function fecha($fech)
{
    $dat = array();
    $dat = explode('-', $fech);
    $m = '';
    switch ($dat[1]) {
        case '01':
            {
                $m = "Enero";
                break;
            }
        case '02':
            {
                $m = "Febrero";
                break;
            }
        case '03':
            {
                $m = "Marzo";
                break;
            }
        case '04':
            {
                $m = "Abril";
                break;
            }
        case '05':
            {
                $m = "Mayo";
                break;
            }
        case '06':
            {
                $m = "Junio";
                break;
            }

        case '07':
            {
                $m = "Julio";
                break;
            }
        case '08':
            {
                $m = "Agosto";
                break;
            }
        case '9':
            {
                $m = "Septiembre";
                break;
            }
        case '10':
            {
                $m = "Octubre";
                break;
            }
        case '11':
            {
                $m = "Noviembre";
                break;
            }
        case '12':
            {
                $m = "Diciembre";
                break;
            }

    }
    $nfech = $dat[2] . " de " . $m . " de " . $dat[0];
    return $nfech;
}

function get_marca($cliente, $marca)
{
    include "../../../common/conexion.php";

    $sql = $conexion->prepare("SELECT marca FROM marcas WHERE no_cliente='$cliente' AND cve_marca = '$marca' AND status=1");

    if (!$sql) {
        throw new Exception("Error al obtener el numero de certificado. $conexion->error");
    }

    if (!$sql->execute()) {
        throw new Exception("Error al obtener el numero de certificado. $conexion->error");
    }

    $sql->store_result();
    $sql->bind_result($nombre_marca);
    $sql->fetch();
    $sql->close();

    return $nombre_marca;

}
