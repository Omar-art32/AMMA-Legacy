<?php
/**
 * php/procesa.php — PHP 8.3
 * Endpoint multiuso de la pantalla Consulta de Predio:
 *   1. POST clienteno  → llena el <select> de predios/viveros (HTML)
 *   2. GET  term       → autocomplete por No. de predio (JSON)
 *   3. GET  guia       → autocomplete por No. de guía (JSON)
 *   4. POST funcion    → guarda/actualiza atributos del paraje (JSON)
 *
 * Cambios vs 5.6:
 *  - Todas las consultas con sentencias preparadas (antes concatenación directa)
 *  - mysqli en modo excepción (default en 8.1+): los errores ya no fallan en silencio
 *  - $row_array ya no se reutiliza entre iteraciones (arrastraba claves entre filas)
 *  - Lista de conflicto validada antes del IN()
 */
declare(strict_types=1);

require_once __DIR__ . '/../../common/conexion.php';

/**
 * Regresa el fragmento " AND (col NOT IN (...)) " si el usuario tiene
 * clientes en conflicto de intereses; cadena vacía si no.
 */
function sqlConflicto(mysqli $conexion, int $idus, string $columna): string
{
    if ($idus <= 0) {
        return '';
    }
    $stmt = $conexion->prepare('SELECT getConflictoIntereses(?)');
    $stmt->bind_param('i', $idus);
    $stmt->execute();
    $stmt->bind_result($clientes_conflicto);
    $stmt->fetch();
    $stmt->close();

    if ($clientes_conflicto !== null && $clientes_conflicto !== ''
        && preg_match("/^'[A-Za-z0-9]+'(,'[A-Za-z0-9]+')*$/", $clientes_conflicto)) {
        return " AND ({$columna} NOT IN ({$clientes_conflicto})) ";
    }
    return '';
}

try {

    // =================================================================
    // 1) POST clienteno → opciones del <select> Predio o Vivero (HTML)
    // =================================================================
    if (isset($_POST['clienteno'])) {
        $clienteno = (string)$_POST['clienteno'];
        $criterio  = '<option value="0"> Elige un Predio o Vivero</option>';

        // PREDIOS
        $stmt = $conexion->prepare(
            'SELECT id_paraje, paraje, maguey_con_registro, servicio, tipo
             FROM paraje WHERE id_cliente = ? AND tipo = 1'
        );
        $stmt->bind_param('s', $clienteno);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($fila = $result->fetch_assoc()) {
            $tipo  = ((int)$fila['tipo'] === 1) ? 'Predio' : 'Vivero';
            $tipoP = match ((int)$fila['maguey_con_registro']) {
                2       => 'DOCUMENTAL ' . $fila['servicio'],
                1       => 'EN SITIO',
                default => '',
            };
            $criterio .= '<option value="' . htmlspecialchars($fila['id_paraje'] . '-' . $fila['tipo'], ENT_QUOTES) . '">'
                . htmlspecialchars($fila['paraje'], ENT_QUOTES)
                . '(' . htmlspecialchars($fila['id_paraje'] . '-' . $tipo . '-' . $tipoP, ENT_QUOTES) . ')</option>';
        }
        $stmt->close();

        // VIVEROS
        $stmt = $conexion->prepare(
            'SELECT id_paraje, paraje, tipo
             FROM paraje_vivero WHERE id_cliente = ? AND tipo = 2'
        );
        $stmt->bind_param('s', $clienteno);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($fila = $result->fetch_assoc()) {
            $tipo = ((int)$fila['tipo'] === 1) ? 'Predio' : 'Vivero';
            $criterio .= '<option value="' . htmlspecialchars($fila['id_paraje'] . '-' . $fila['tipo'], ENT_QUOTES) . '">'
                . htmlspecialchars($fila['paraje'], ENT_QUOTES)
                . '(' . htmlspecialchars($fila['id_paraje'] . '-' . $tipo, ENT_QUOTES) . ')</option>';
        }
        $stmt->close();

        header('Content-Type: text/html; charset=utf-8');
        echo $criterio;

    // =================================================================
    // 2) GET term → autocomplete por No. de predio (JSON)
    // =================================================================
    } elseif (isset($_GET['term'])) {
        header('Content-Type: application/json; charset=utf-8');

        $term = (string)$_GET['term'];
        $idus = (int)($_GET['idus'] ?? 0);
        $sql_conflicto = sqlConflicto($conexion, $idus, 'p.id_cliente');

        $return_arr = [];

        foreach (['paraje', 'paraje_vivero'] as $tabla) {
            $stmt = $conexion->prepare(
                "SELECT p.id_paraje, p.paraje, p.tipo, p.id_cliente, c.nombre
                 FROM {$tabla} p
                 INNER JOIN clientes c ON c.no_cliente = p.id_cliente
                 WHERE p.id_paraje LIKE CONCAT('%', ?, '%') {$sql_conflicto}"
            );
            $stmt->bind_param('s', $term);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $return_arr[] = [
                    'value'     => $row['id_paraje'],
                    'paraje'    => $row['paraje'],
                    'nocliente' => $row['id_cliente'],
                    'nombrec'   => $row['nombre'],
                    'tipo'      => $row['tipo'],
                ];
            }
            $stmt->close();
        }

        echo json_encode($return_arr);

    // =================================================================
    // 3) GET guia → autocomplete por No. de guía (JSON)
    // =================================================================
    } elseif (isset($_GET['guia'])) {
        header('Content-Type: application/json; charset=utf-8');

        $guia = (string)$_GET['guia'];
        $idus = (int)($_GET['idus'] ?? 0);
        $sql_conflicto = sqlConflicto($conexion, $idus, 'p.id_cliente');

        $return_arr = [];

        $stmt = $conexion->prepare(
            "SELECT p.id_cliente, p.id_paraje, p.paraje, ce.id_extraccion, p.tipo, c.nombre
             FROM paraje p
             INNER JOIN cextracciones ce ON ce.id_paraje = p.id_paraje
             INNER JOIN clientes c ON c.no_cliente = p.id_cliente
             WHERE ce.id_extraccion LIKE CONCAT('%', ?, '%') {$sql_conflicto}"
        );
        $stmt->bind_param('s', $guia);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $return_arr[] = [
                'value'     => $row['id_extraccion'],
                'id_paraje' => $row['id_paraje'],
                'paraje'    => $row['paraje'],
                'nocliente' => $row['id_cliente'],
                'nombrec'   => $row['nombre'],
                'tipo'      => $row['tipo'],
            ];
        }
        $stmt->close();

        echo json_encode($return_arr);

    // =================================================================
    // 4) POST funcion → guardar/actualizar atributos del paraje (JSON)
    // =================================================================
    } elseif (isset($_POST['funcion'])) {
        header('Content-Type: application/json; charset=utf-8');

        $respuesta = new stdClass();
        $idUs      = (int)($_POST['idUs'] ?? 0);
        $nopredio  = (string)($_POST['nopredio'] ?? '');
        $msjQ      = '';

        $inicial = 0;
        $limite  = 9;
        if ($_POST['funcion'] === 'guardarAtributosI') {
            $valID   = (int)($_POST['valID'] ?? 0);
            $inicial = $valID;
            $limite  = $valID + 1;
        }

        $sel = $conexion->prepare(
            'SELECT valor, observaciones FROM parajes_atributos WHERE id_paraje = ? AND atributo = ?'
        );
        $upd = $conexion->prepare(
            'UPDATE parajes_atributos SET fecha = NOW(), idUs = ?, valor = ?, observaciones = ?
             WHERE id_paraje = ? AND atributo = ?'
        );
        $ins = $conexion->prepare(
            'INSERT INTO parajes_atributos (fecha, id_paraje, status, atributo, valor, observaciones, idUs)
             VALUES (NOW(), ?, "1", ?, ?, ?, ?)'
        );

        for ($i = $inicial; $i < $limite; $i++) {
            $indicador = (string)($_POST['indicador' . $i] ?? '');
            $obs       = (string)($_POST['txt' . $i] ?? '');
            if ($indicador === '') {
                continue;
            }

            try {
                $sel->bind_param('si', $nopredio, $i);
                $sel->execute();
                $res  = $sel->get_result();
                $fila = $res->fetch_object();

                if ($fila !== null) {
                    if ($fila->valor !== $indicador || $fila->observaciones !== $obs) {
                        $upd->bind_param('isssi', $idUs, $indicador, $obs, $nopredio, $i);
                        $upd->execute();
                    }
                } else {
                    $ins->bind_param('sissi', $nopredio, $i, $indicador, $obs, $idUs);
                    $ins->execute();
                }
            } catch (mysqli_sql_exception $e) {
                error_log('[procesa.php atributo ' . $i . '] ' . $e->getMessage());
                $msjQ .= ' Error al guardar atributo ' . $i . '.';
            }
        }
        $sel->close();
        $upd->close();
        $ins->close();

        if ($msjQ === '') {
            $respuesta->status = '1';
            $respuesta->text   = 'Registro Guardado';
        } else {
            $respuesta->status = '0';
            $respuesta->text   = 'Error al Guardar o Actualizar Parámetros:' . $msjQ;
        }

        echo json_encode($respuesta);
    }

} catch (mysqli_sql_exception $e) {
    error_log('[procesa.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error interno al procesar la consulta']);
} finally {
    $conexion->close();
}