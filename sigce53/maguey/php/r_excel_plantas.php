<?php
/**
 * php/r_excel_plantas.php — PHP 8.3
 * Reporte Excel de plantas (descarga .xlsx vía tmp_excel/).
 *
 * Cambios vs 5.6:
 *  - PHPExcel → PhpSpreadsheet (Composer)
 *  - Consulta con prepared statements para los filtros
 *  - Sesión validada con ??/isset; header+exit en redirección
 *  - display_errors desactivado (los warnings corrompen el JSON de respuesta)
 *  - Función fecha() con match() en lugar de switch con break
 *  - Logo: PHPExcel_Worksheet_Drawing → PhpSpreadsheet\Worksheet\Drawing
 */
declare(strict_types=1);

session_set_cookie_params([
    'lifetime' => 0, 'path' => '/', 'domain' => '',
    'secure' => isset($_SERVER['HTTPS']), 'httponly' => true, 'samesite' => 'Lax',
]);
session_start();

$mod = 1;
require_once __DIR__ . '/../../common/cfg_server.php';

$d_s = (string)($_POST['id_s'] ?? '');

if (!isset($_SESSION[$d_s]) || ($_SESSION[$d_s]['logged'] ?? '') !== 'OK'
    || ($_SESSION[$d_s]['seccion_4_5'] ?? '') !== 'logged') {
    $esquema = isset($_SERVER['HTTPS']) ? 'https' : 'http';
    header("Location: {$esquema}://{$svr_dir}/acceso/login.php?mod={$mod}");
    exit;
}

// No mostrar errores en la salida (contaminan el JSON)
ini_set('display_errors', '0');
error_reporting(E_ALL);
date_default_timezone_set('America/Mexico_City');

if (PHP_SAPI === 'cli') exit('Solo desde navegador');

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../common/conexion.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

header('Content-Type: application/json; charset=utf-8');

function fecha(string $fech): string
{
    $dat = explode('-', $fech);
    if (count($dat) < 3) return $fech;
    $m = match ($dat[1]) {
        '01' => 'Ene', '02' => 'Feb', '03' => 'Mar', '04' => 'Abr',
        '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Ago',
        '09','9' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dic',
        default => $dat[1],
    };
    return "{$dat[2]}-{$m}-{$dat[0]}";
}

$search = (string)($_POST['busca'] ?? '');
$fecha1 = trim((string)($_POST['fecha1'] ?? ''));
$fecha2 = trim((string)($_POST['fecha2'] ?? ''));
$clientesel = (string)($_POST['cliente'] ?? '');
$periodo = ''; $msj_per = '';
$r_h = 5; $st_r = 6;
$file_name = 'plantas_' . rand() . '.xlsx';

try {
    $cond = []; $tipos = ''; $params = [];
    if ($search !== '' && $search !== 'undefined') {
        $cond[] = "(p.id_paraje LIKE CONCAT('%',?,'%') OR p.paraje LIKE CONCAT('%',?,'%')
            OR p.lat LIKE CONCAT('%',?,'%') OR p.lng LIKE CONCAT('%',?,'%')
            OR p.id_cliente LIKE CONCAT('%',?,'%') OR p.nombrep LIKE CONCAT('%',?,'%')
            OR p.rcampo LIKE CONCAT('%',?,'%') OR c.nombre LIKE CONCAT('%',?,'%')
            OR ep.regmaguey LIKE CONCAT('%',?,'%'))";
        $tipos .= str_repeat('s', 9);
        for ($i = 0; $i < 9; $i++) $params[] = $search;
    }
    if ($fecha1 !== '' && $fecha2 !== '') {
        $cond[] = 'DATE(p.fecharegistro) BETWEEN ? AND ?';
        $tipos .= 'ss'; array_push($params, $fecha1, $fecha2);
        $periodo = fecha($fecha1) . '  a  ' . fecha($fecha2); $msj_per = 'Periodo:';
    } elseif ($fecha1 !== '') {
        $cond[] = 'DATE(p.fecharegistro) = ?';
        $tipos .= 's'; $params[] = $fecha1;
        $periodo = fecha($fecha1); $msj_per = 'Fecha:';
    }
    if ($clientesel !== '' && $clientesel !== '0') {
        $cond[] = 'p.id_cliente = ?'; $tipos .= 's'; $params[] = $clientesel;
    }
    $where = $cond ? 'WHERE ' . implode(' AND ', $cond) : '';

    $consulta = "SELECT ep.*, DATE(p.fecharegistro) fecharegistro, c.nombre comun, cli.nombre nombrecli,
        l.localidad local, m.nombre municipio, e.nombre mestado, p.paraje, p.id_cliente, p.superficie
        FROM existenciaplanta ep
        INNER JOIN paraje p ON ep.id_paraje = p.id_paraje
        INNER JOIN comun c ON ep.id_comun = c.id_comun
        INNER JOIN clientes cli ON p.id_cliente = cli.no_cliente
        INNER JOIN localidades l ON p.id_localidad = l.id
        INNER JOIN municipios m ON l.MunicipioID = m.id
        INNER JOIN estados e ON m.estado = e.clave
        {$where} ORDER BY p.fecharegistro ASC";

    $stmt = $conexion->prepare($consulta);
    if ($tipos !== '') $stmt->bind_param($tipos, ...$params);
    $stmt->execute();
    $res = $stmt->get_result();

    $spreadsheet = new Spreadsheet();
    $spreadsheet->getProperties()->setCreator('NJGC')->setTitle('REPORTE DE PLANTAS');

    $styleArray = [
        'font' => ['bold' => true],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        'fill' => [
            'fillType' => Fill::FILL_GRADIENT_LINEAR, 'rotation' => 90,
            'startColor' => ['argb' => 'FFA0A0A0'], 'endColor' => ['argb' => 'FFFFFFFF'],
        ],
    ];
    $styleArray2 = [
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9DB2B3']]],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '23719E']],
    ];
    $styleArray3 = [
        'font' => ['bold' => false],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '6A8696']]],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '2E966D']],
    ];

    // Header del libro
    $spreadsheet->getActiveSheet()->mergeCells('C1:Q1');
    $spreadsheet->getActiveSheet()->setCellValue('C1', 'ASOCIACIÓN DE MAGUEY Y MEZCAL ARTESANAL');
    $spreadsheet->getActiveSheet()->getStyle('C1')->getFont()->setSize(18)->setBold(true);
    $spreadsheet->getActiveSheet()->getStyle('C1:Q1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getActiveSheet()->getRowDimension(1)->setRowHeight(50);
    $spreadsheet->getActiveSheet()->getRowDimension(2)->setRowHeight(30);
    $spreadsheet->getActiveSheet()->mergeCells('C2:Q2');
    $spreadsheet->getActiveSheet()->setCellValue('C2', 'REPORTE DE PLANTAS');
    $spreadsheet->getActiveSheet()->getStyle('C2')->getFont()->setSize(14)->setBold(true);
    $spreadsheet->getActiveSheet()->getStyle('C2:Q2')->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

    // Logo
    $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);
    $spreadsheet->getActiveSheet()->mergeCells('A1:B2');
    $logo = new Drawing();
    $logo->setName('logo')->setDescription('Logo AMMA');
    $logo->setPath(__DIR__ . '/../../images/logo_amma.jpg');
    $logo->setHeight(80)->setCoordinates('A1')->setOffsetX(10);
    $logo->setWorksheet($spreadsheet->getActiveSheet());

    $hoja = $spreadsheet->getActiveSheet();
    $hoja->getStyle('A'.$r_h.':O'.$r_h)->applyFromArray($styleArray2);

    $arrTitsCampos = [
        'FECHA' => 'fecharegistro', 'PREDIO' => 'id_paraje', 'NOMBRE PREDIO' => 'paraje',
        'NO. CONTROL' => 'id_cliente', 'NOMBRE' => 'nombrecli',
        'COMÚN' => 'comun', 'EDAD(AÑOS)' => 'edad',
        'EXISTENCIA INICIAL' => 'cantidadini', 'EXISTENCIA ACTUAL' => 'existenciaplantas',
        'REGISTRO DE MAGUEY' => 'regmaguey', 'HECTÁREAS' => 'superficie',
        'PARAJE' => 'paraje', 'LOCALIDAD' => 'local', 'MUNICIPIO' => 'municipio', 'ESTADO' => 'mestado',
    ];
    $letra = 'A';
    foreach ($arrTitsCampos as $titulo => $_) { $hoja->setCellValue($letra . $r_h, $titulo); $letra++; }

    $n = $st_r;
    while ($row = $res->fetch_assoc()) {
        $letra = 'A';
        foreach ($arrTitsCampos as $_ => $col) {
            $hoja->setCellValue($letra . $n, $row[$col] ?? '');
            $letra++;
        }
        $n++;
    }
    $letra = 'A';
    foreach ($arrTitsCampos as $_ => $__) { $hoja->getColumnDimension($letra)->setAutoSize(true); $letra++; }
    $hoja->setTitle('PLANTAS');

    // Guardar en tmp_excel y responder JSON con URL
    $esquema = isset($_SERVER['HTTPS']) ? 'https' : 'http';
    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save(__DIR__ . '/../tmp_excel/' . $file_name);
    $dir_file = $esquema . '://' . $svr_dir . '/maguey/tmp_excel/' . $file_name;
    echo json_encode(['status' => 'OK', 'msj' => $dir_file]);
    exit;

} catch (\Throwable $e) {
    error_log('[r_excel_plantas] ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'msj' => 'Error al generar el reporte']);
} finally {
    $conexion->close();
}