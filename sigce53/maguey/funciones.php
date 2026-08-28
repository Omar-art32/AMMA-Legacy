<?php
/**
 * funciones.php — PHP 8.3
 *
 * ESTADO: CÓDIGO MUERTO CONSERVADO COMO PUENTE.
 *
 * Este archivo se incluye desde index.php y rvivero.php, pero NINGUNO
 * de los dos llama a las funciones que contenía:
 *
 *   - getParam(): usaba get_magic_quotes_gpc() — ELIMINADA en PHP 8.0.
 *     Cualquier llamada producía Fatal Error.
 *   - sqlValue(): usaba mysql_real_escape_string() — ELIMINADA en PHP 7.0.
 *     Función de la extensión mysql_* (retirada hace 10 años).
 *
 * Ambas eran helpers de sanitización para la era pre-mysqli. Con sentencias
 * preparadas (bind_param) son innecesarias: la sanitización la hace la BD.
 *
 * El archivo se conserva vacío porque index.php y rvivero.php hacen
 * include("funciones.php") — si se borra, truenan con "file not found".
 * Se eliminará cuando esos archivos se migren y se retire el include.
 *
 * NOTA: maguey/comboeml/funciones.php es un archivo DIFERENTE con el
 * mismo nombre (contiene dameMunicipio/dameLocalidad). No confundir.
 *
 * Funciones eliminadas y su equivalente moderno:
 *
 *   getParam($param, $default)
 *     Antes: addslashes() si magic_quotes estaba desactivado
 *     Ahora: innecesario — usar ?? para defaults y bind_param para SQL
 *
 *   sqlValue($value, $type)
 *     Antes: mysql_real_escape_string() + switch por tipo
 *     Ahora: innecesario — bind_param("s", $value) o bind_param("i", $value)
 */
declare(strict_types=1);

// Archivo intencionalmente vacío.
// Ver documentación arriba.