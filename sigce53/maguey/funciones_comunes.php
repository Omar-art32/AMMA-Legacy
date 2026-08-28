<?php
/**
 * funciones_comunes.php — PHP 8.3
 *
 * ESTADO: CÓDIGO MUERTO. Ningún archivo del proyecto incluye este archivo.
 *
 * Contenía una función fetch() que convertía el resultado de un
 * mysqli_stmt o mysqli_result en un arreglo asociativo. Era necesaria
 * en versiones antiguas de PHP donde mysqli_stmt no tenía get_result().
 *
 * Desde PHP 5.3+ (con mysqlnd), get_result() existe y regresa un
 * mysqli_result que se recorre con fetch_assoc() directamente.
 * Todos los archivos migrados ya usan ese patrón:
 *
 *   $stmt->execute();
 *   $result = $stmt->get_result();
 *   while ($row = $result->fetch_assoc()) { ... }
 *
 * Este archivo puede eliminarse del proyecto sin afectar nada.
 * Se conserva temporalmente con esta documentación por trazabilidad.
 */
declare(strict_types=1);

// Archivo intencionalmente vacío.
// Ver documentación arriba.