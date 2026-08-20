<?php
/**
 * reporteexcel.php — PHP 8.3
 * Genera el reporte Excel de predios (descarga .xlsx).
 *
 * Cambios vs versión original:
 *  - PHPExcel (abandonada 2015, incompatible con PHP 8: sintaxis $str{n}
 *    eliminada) → PhpOffice\PhpSpreadsheet vía Composer.
 *  - Consulta N+1 eliminada: el nombre del cliente ahora viene por JOIN
 *    en la consulta principal (antes: una query por cada fila del reporte).
 *  - Autorización reforzada: el original solo comparaba ?aleat= contra una
 *    lista de IDs — cualquier visitante con la URL descargaba el reporte.
 *    Ahora se exige además sesión activa (seccion_4_4 = logged). La lista
 *    de IDs autorizados se conserva como regla de negocio.
 *  - freezePaneByColumnAndRow() no existe en PhpSpreadsheet → freezePane('A3')
 *  - Autoajuste de columnas extendido a Y y Z (el original se quedaba en X)
 *
 * Requiere: composer require phpoffice/phpspreadsheet  (ver instrucciones)
 */
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../common/conexion.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

if (PHP_SAPI === 'cli') {
    exit('Este archivo solo se puede ver desde un navegador web');
}

// ---------------------------------------------------------------------
// Autorización: sesión activa + ID en la lista de usuarios permitidos
// ---------------------------------------------------------------------
$sesionValida = false;
foreach ($_SESSION as $dato) {
    if (is_array($dato) && ($dato['seccion_4_4'] ?? '') === 'logged') {
        $sesionValida = true;
        break;
    }
}

$idus = (int)($_GET['aleat'] ?? 0);
$usuariosAutorizados = [1, 21, 23, 34, 48, 117, 148];

if (!$sesionValida || !in_array($idus, $usuariosAutorizados, true)) {
    http_response_code(403);
    exit('No autorizado.');
}

date_default_timezone_set('America/Mexico_City');

// ---------------------------------------------------------------------
// Consulta principal (nombre de cliente por JOIN; antes: query por fila)
// ---------------------------------------------------------------------
$consulta = "SELECT
    paraje.id_paraje,
    paraje.id_cliente,
    cl.nombre AS clientenombre,
    paraje.nombrep,
    existenciaplanta.regmaguey AS regmaguey,
    LPAD(constancias.id_constancia,4,'0') as constancia,
    CONCAT('P',LPAD(SUBSTR(paraje.id_paraje, 2, LENGTH(paraje.id_paraje)),4,'0')) as parajes,
    DATE_FORMAT(constancias.fecha,'%y') as anio,
    constancias.id_constancia as numeroconstancia,
    municipios.nombre as nombrem,
    estados.nombre as nombree,
    localidades.localidad,
    paraje.paraje,
    comun.nombre, genespecie, existenciaplantas,
    existenciaplanta.edad, usufruto, tenencia, superficie, lng, lat,
    dis_planmetros, dis_surcometros, fecha_paraje, rcampo, cantidadini,
    paraje.numa, au.login
FROM estados
INNER JOIN municipios  ON municipios.estado = estados.clave
INNER JOIN localidades ON localidades.MunicipioID = municipios.id
INNER JOIN paraje      ON localidades.id = paraje.id_localidad
INNER JOIN clientes cl ON cl.no_cliente = paraje.id_cliente
INNER JOIN constancias ON constancias.id_paraje = paraje.id_paraje
INNER JOIN existenciaplanta ON paraje.id_paraje = existenciaplanta.id_paraje
INNER JOIN comun   ON comun.id_comun   = existenciaplanta.id_comun
INNER JOIN especie ON comun.id_especie = especie.id_especie
INNER JOIN a_usuarios au ON paraje.id_us = au.id_us
ORDER BY paraje.id";

try {
    $resultado = $conexion->query($consulta);

    if ($resultado->num_rows === 0) {
        exit('No hay resultados para mostrar');
    }

    // -----------------------------------------------------------------
    // Libro y propiedades
    // -----------------------------------------------------------------
    $spreadsheet = new Spreadsheet();
    $spreadsheet->getProperties()
        ->setCreator('NJGC')
        ->setLastModifiedBy('NJGC')
        ->setTitle('REPORTE DE PREDIOS AMMA')
        ->setSubject('REPORTE DE PREDIOS AMMA')
        ->setDescription('REPORTE DE PREDIOS AMMA')
        ->setKeywords('REPORTE DE PREDIOS AMMA')
        ->setCategory('REPORTE DE PREDIOS AMMA');

    $hoja = $spreadsheet->getActiveSheet();
    $hoja->setTitle('PREDIOS');

    $tituloReporte = 'REPORTE DE PREDIOS DE MAGUEY';
    $titulosColumnas = [
        'NO_PARAJE','NO_CLIENTE','NOMBRE DEL CLIENTE','NOMBRE DE PRODUCTOR',
        'SITUACIÓN DE MANEJO','NO.CONSTANCIA','LOCALIDAD','MUNICIPIO','ESTADO',
        'NOMBRE DEL PARAJE','NOMBRE COMÚN (ESPECIE)','NOMBRE CIENTIFICO (ESPECIE)',
        'CANTIDAD INICIAL','CANTIDAD DE EXISTENCIA PLANTAS','EDAD','USUFRUTO',
        'TENENCIA','SUPERFICIE','LONGITUD','LATITUD',
        'DISTANCIA ENTRE PLANTAS (METROS)','DISTANCIA ENTRE SURCOS (METROS)',
        'FECHA DE REGISTRO','REPRESENTANTE EN CAMPO','ORIGEN','REGISTRÓ',
    ];

    $hoja->mergeCells('A1:Z1');
    $hoja->setCellValue('A1', $tituloReporte);

    // Encabezados de columnas (A2..Z2)
    foreach ($titulosColumnas as $n => $titulo) {
        $col = chr(ord('A') + $n);            // A..Z (26 columnas)
        $hoja->setCellValue($col . '2', $titulo);
    }

    // -----------------------------------------------------------------
    // Filas de datos
    // -----------------------------------------------------------------
    $i = 3;
    while ($registro = $resultado->fetch_array()) {
        $origen = ((int)$registro['numa'] > 0) ? 'EXTERNO' : 'AMMA';

        // Explicit string: conserva ceros a la izquierda (P0001, C0005)
        $hoja->setCellValueExplicit('A' . $i, $registro['parajes'], DataType::TYPE_STRING);
        $hoja->setCellValueExplicit('B' . $i, $registro['id_cliente'], DataType::TYPE_STRING);
        $hoja->setCellValue('C' . $i, $registro['clientenombre']);
        $hoja->setCellValue('D' . $i, $registro['nombrep']);
        $hoja->setCellValue('E' . $i, $registro['regmaguey']);
        $hoja->setCellValueExplicit(
            'F' . $i,
            strtoupper((string)$registro['constancia']) . $registro['parajes'] . $registro['anio'],
            DataType::TYPE_STRING
        );
        $hoja->setCellValue('G' . $i, $registro['localidad']);
        $hoja->setCellValue('H' . $i, $registro['nombrem']);
        $hoja->setCellValue('I' . $i, $registro['nombree']);
        $hoja->setCellValue('J' . $i, $registro['paraje']);
        $hoja->setCellValue('K' . $i, $registro['nombre']);
        $hoja->setCellValue('L' . $i, $registro['genespecie']);
        $hoja->setCellValue('M' . $i, $registro['cantidadini']);
        $hoja->setCellValue('N' . $i, $registro['existenciaplantas']);
        $hoja->setCellValue('O' . $i, $registro['edad']);
        $hoja->setCellValue('P' . $i, $registro['usufruto']);
        $hoja->setCellValue('Q' . $i, $registro['tenencia']);
        $hoja->setCellValue('R' . $i, $registro['superficie']);
        $hoja->setCellValue('S' . $i, $registro['lng']);
        $hoja->setCellValue('T' . $i, $registro['lat']);
        $hoja->setCellValue('U' . $i, $registro['dis_planmetros']);
        $hoja->setCellValue('V' . $i, $registro['dis_surcometros']);
        $hoja->setCellValue('W' . $i, $registro['fecha_paraje']);
        $hoja->setCellValue('X' . $i, $registro['rcampo']);
        $hoja->setCellValue('Y' . $i, $origen);
        $hoja->setCellValue('Z' . $i, $registro['login']);
        $i++;
    }
    $ultimaFila = $i - 1;

    // -----------------------------------------------------------------
    // Estilos (equivalentes a los originales, claves de PhpSpreadsheet:
    // type→fillType, allborders→allBorders, startcolor→startColor,
    // wrap→wrapText)
    // -----------------------------------------------------------------
    $estiloTituloReporte = [
        'font' => [
            'name' => 'Verdana', 'bold' => true, 'italic' => false,
            'strikethrough' => false, 'size' => 16,
            'color' => ['rgb' => 'FFFFFF'],
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'color'    => ['argb' => '1C7F33'],
        ],
        'borders' => [
            'allBorders' => ['borderStyle' => Border::BORDER_NONE],
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical'   => Alignment::VERTICAL_CENTER,
            'textRotation' => 0,
            'wrapText'   => true,
        ],
    ];

    $estiloTituloColumnas = [
        'font' => [
            'name' => 'Arial', 'bold' => true, 'size' => 11,
            'color' => ['rgb' => 'FFFFFF'],
        ],
        'fill' => [
            'fillType'   => Fill::FILL_GRADIENT_LINEAR,
            'rotation'   => 90,
            'startColor' => ['rgb' => '4AE66F'],
            'endColor'   => ['argb' => 'FF431A5D'],
        ],
        'borders' => [
            'top' => [
                'borderStyle' => Border::BORDER_MEDIUM,
                'color' => ['rgb' => '53DA73'],
            ],
            'bottom' => [
                'borderStyle' => Border::BORDER_MEDIUM,
                'color' => ['rgb' => '29D551'],
            ],
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical'   => Alignment::VERTICAL_CENTER,
            'wrapText'   => true,
        ],
    ];

    $estiloInformacion = [
        'font' => [
            'name' => 'Arial', 'size' => 9,
            'color' => ['rgb' => '000000'],
        ],
        'borders' => [
            'left' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => 'B2E5CB'],   // el original traía '#B2E5CB'; el # era inválido
            ],
        ],
    ];

    $hoja->getStyle('A1:Z1')->applyFromArray($estiloTituloReporte);
    $hoja->getStyle('A2:Z2')->applyFromArray($estiloTituloColumnas);
    if ($ultimaFila >= 3) {
        $hoja->getStyle('A3:Z' . $ultimaFila)->applyFromArray($estiloInformacion);
    }

    // Autoajuste A..Z (el original se detenía en X y dejaba Y, Z sin ajustar)
    foreach (range('A', 'Z') as $col) {
        $hoja->getColumnDimension($col)->setAutoSize(true);
    }

    // Inmovilizar encabezados (equivale a freezePaneByColumnAndRow(0,3))
    $hoja->freezePane('A3');

    $spreadsheet->setActiveSheetIndex(0);

    // -----------------------------------------------------------------
    // Descarga
    // -----------------------------------------------------------------
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Reportedepredios.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
    exit;

} catch (mysqli_sql_exception $e) {
    error_log('[reporteexcel.php] ' . $e->getMessage());
    http_response_code(500);
    exit('Error al generar el reporte.');
} finally {
    $conexion->close();
}