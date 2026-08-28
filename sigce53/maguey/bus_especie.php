<?php
/**
 * bus_especie.php — PHP 8.3
 * Carga las opciones del <select> de especies (nombre común + científico).
 * Devuelve HTML (options), no JSON.
 *
 * Cambios vs 5.6:
 *  - set_charset("utf8") eliminado (conexion.php ya usa utf8mb4)
 *  - include → require_once con __DIR__
 *  - Salida escapada con htmlspecialchars (antes: datos de BD directo en HTML)
 *  - try/catch con error_log
 *  - Nota: esta consulta NO tiene entrada de usuario (no hay $_GET/$_POST),
 *    por lo que no había inyección SQL; se mantiene como query() directa.
 */
declare(strict_types=1);

require_once __DIR__ . '/../common/conexion.php';

//header('Content-Type: text/html; charset=utf-8');

try {
    $sql = "SELECT c.id_comun, c.nombre, e.genespecie, e.variante
            FROM comun c
            INNER JOIN especie e ON e.id_especie = c.id_especie
            WHERE c.status = 1
            ORDER BY c.nombre";
    $result = $conexion->query($sql);

    $combobit = '';
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $valor = htmlspecialchars((string)$row['id_comun'], ENT_QUOTES, 'UTF-8');
            $texto = htmlspecialchars(
                $row['nombre'] . ' - ' . $row['genespecie'] . ' ' . ($row['variante'] ?? ''),
                ENT_QUOTES, 'UTF-8'
            );
            $combobit .= "\t<option value=\"{$valor}\">{$texto}</option>\n";
        }
    } else {
        $combobit = 'No hubo resultados';
    }
    echo $combobit;
} catch (mysqli_sql_exception $e) {
    error_log('[bus_especie.php] ' . $e->getMessage());
    echo 'Error al cargar especies';
} finally {
    $conexion->close();
}