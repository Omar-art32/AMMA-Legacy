<?php
/**
 * MÓDULO: Control de Accesos (acceso.php)
 * ACTUALIZACIÓN: Migración a PHP 8.3
 * Cambios realizados para compatibilidad con PHP 8.3:
 * 
 * 1. Casteo explícito de tipos:
 *    - Conversión forzada a (int) en las entradas $_POST ($clvuser, $modulo, $seccion)
 *      para cumplir con el tipado estricto.
 * 
 * 2. Manejo de variables y arreglos:
 *    - Implementación del operador Null Coalescing (??) para prevenir 
 *      errores/warnings por "Undefined array key" en peticiones AJAX.
 * 
 * 3. Optimización de lógica de horarios:
 *    - Agrupación explícita en la evaluación del operador condicional if/else.
 *    - Validación previa a la instanciación de DateTime() para prevenir errores 
 *      con valores nulos (null).
 * 
 * 4. Manejo de base de datos y transacciones:
 *    - Reestructuración del manejo de excepciones con try-catch-finally.
 *    - Implementación de rollback() en caso de fallos y garantía de cierre 
 *      de conexión $conexion->close() únicamente cuando existe un objeto válido.
 * 
 * 5. Respuestas AJAX:
 *    - Adición del encabezado 'Content-Type: application/json; charset=utf-8'
 *      y estandarización de las estructuras json_encode.
 */
session_start();

require_once "funciones_comunes.php";

if (is_ajax()) {
    $action = $_POST["action"] ?? '';
    switch ($action) {
        case "registrarAcceso":
            registrarAcceso();
            break;
        case "verificarAcceso":
            verificarAcceso();
            break;
    }
}

function is_ajax(): bool
{
    return isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) === "xmlhttprequest";
}

function registrarAcceso(): void
{
    header('Content-Type: application/json; charset=utf-8');

    // Casteo explícito a enteros para PHP 8.3
    $clvuser = (int)($_POST["clvuser"] ?? 0);
    $modulo  = (int)($_POST["modulo"] ?? 0);
    $seccion = (int)($_POST["seccion"] ?? 0);

    $conexion = null;

    try {
        include "../common/conexion.php";
        
        if (!$conexion) {
            throw new Exception("Error al conectar con la base de datos.");
        }

        $conexion->autocommit(false);

        $sql = "INSERT INTO a_accesos(id_us, id_mod, num_sec, fecha) VALUES (?, ?, ?, NOW())";
        $ps = $conexion->prepare($sql);
        $ps->bind_param("iii", $clvuser, $modulo, $seccion);

        if (!$ps->execute()) {
            throw new Exception("Error al insertar el registro de acceso.");
        }

        $ps->close();
        $conexion->commit();

        echo json_encode(["status" => "correcto", "msj" => "Acceso Registrado."]);

    } catch (Exception $e) {
        if ($conexion && $conexion->connect_errno === 0) {
            $conexion->rollback();
        }
        echo json_encode(["status" => "error", "msj" => "Error: " . $e->getMessage()]);
    } finally {
        if ($conexion) {
            $conexion->close();
        }
    }
}

function verificarAcceso(): void
{
    header('Content-Type: application/json; charset=utf-8');

    $clvuser = (int)($_POST["clvuser"] ?? 0);
    $conexion = null;

    try {
        include "../common/conexion.php";

        if (!$conexion) {
            throw new Exception("Error de conexión.");
        }

        $conexion->autocommit(false);
        $time = time();
        $horaActual = date("H:i:s", $time);
        $dia = date("l", $time);

        $sql = "SELECT 
                    a_usuarios.horaInicial_l_v,
                    a_usuarios.horaFinal_l_v,
                    a_usuarios.horaInicial_s,
                    a_usuarios.horaFinal_s,
                    a_usuarios.horaInicial_d,
                    a_usuarios.horaFinal_d,
                    a_usuarios.fines_semana
                FROM a_usuarios
                WHERE a_usuarios.id_us = ?";

        $ps = $conexion->prepare($sql);
        $ps->bind_param("i", $clvuser);

        if (!$ps->execute()) {
            throw new Exception("Error al verificar acceso del usuario.");
        }

        $ps->bind_result(
            $horaInicial_l_v, 
            $horaFinal_l_v, 
            $horaInicial_s, 
            $horaFinal_s,
            $horaInicial_d, 
            $horaFinal_d,  
            $fines_semana
        );

        $num_rows = 0;
        if ($ps->fetch()) {
            $num_rows = 1;
        }
        $ps->close();

        if ($num_rows > 0) {
            $fines_semana = (int)($fines_semana ?? 0);
            $esSemana = in_array($dia, ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"], true);

            $dentroDeHorario = false;
            $horaFinal = null;

            if ($esSemana && ($horaActual >= $horaInicial_l_v && $horaActual <= $horaFinal_l_v)) {
                $dentroDeHorario = true;
                $horaFinal = $horaFinal_l_v;
            } else if ($dia === "Saturday" && $fines_semana === 1 && ($horaActual >= $horaInicial_s && $horaActual <= $horaFinal_s)) {
                $dentroDeHorario = true;
                $horaFinal = $horaFinal_s;
            } else if ($dia === "Sunday" && $fines_semana === 1 && ($horaActual >= $horaInicial_d && $horaActual <= $horaFinal_d)) {
                $dentroDeHorario = true;
                $horaFinal = $horaFinal_d;
            }

            if ($dentroDeHorario && $horaFinal) {
                $dtActual = new DateTime($horaActual);
                $dtFinal  = new DateTime($horaFinal);

                $intervalo = $dtActual->diff($dtFinal);
                $horas   = $intervalo->format('%h');
                $minutos = $intervalo->format('%i');

                echo json_encode([
                    "status"  => "dentro", 
                    "msj"     => "Dentro de horario.", 
                    "horas"   => $horas, 
                    "minutos" => $minutos
                ]);
            } else if (in_array($dia, ["Saturday", "Sunday"], true) && $fines_semana === 0) {
                echo json_encode(["status" => "fuera", "msj" => "Acceso Restringido."]);
            } else {
                echo json_encode(["status" => "fuera", "msj" => "Fuera de horario."]);
            }

        } else {
            echo json_encode(["status" => "fuera", "msj" => "Usuario no encontrado o sin horario."]);
        }

        $conexion->commit();

    } catch (Exception $e) {
        if ($conexion && $conexion->connect_errno === 0) {
            $conexion->rollback();
        }
        echo json_encode(["status" => "error", "msj" => "Error en la base de datos: " . $e->getMessage()]);
    } finally {
        if ($conexion) {
            $conexion->close();
        }
    }
}