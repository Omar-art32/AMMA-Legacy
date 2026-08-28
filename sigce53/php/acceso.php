<?php
session_start();

require_once "funciones_comunes.php";

if (is_ajax()) {
    if (isset($_POST["action"]) && !empty($_POST["action"])) {
        $action = $_POST["action"];
        switch ($action) {
            case "registrarAcceso":registrarAcceso();
                break;
            case "verificarAcceso":verificarAcceso();
                break;
        }
    }
}

function is_ajax()
{
    return isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest";
}

function registrarAcceso()
{
    $clvuser = $_POST["clvuser"] ?? '';
    $modulo = $_POST["modulo"] ?? '';
    $seccion = $_POST["seccion"] ?? '';

    try
    {

        include "../common/conexion.php";
        $conexion->autocommit(false);

        $sql = "INSERT INTO a_accesos(id_us,id_mod,num_sec,fecha)VALUES(?,?,?,NOW())";
        $ps = $conexion->prepare($sql);
        $ps->bind_param("iii", $clvuser, $modulo, $seccion);
        if (!$ps->execute()) {
            throw new Exception("Error al agregar el registro en la tabla de clientes.");
        }

        $ps->close();

        $conexion->commit();
        echo json_encode(array("status" => "correcto", "msj" => "Acceso Registrado."));
        $conexion->close();

    } catch (mysqli_sql_exception $e) {
        echo json_encode(array("status" => "error", "msj" => "Error en la base de datos: " . $e->getMessage()));
        $conexion->close();
    }

}

function verificarAcceso()
{

    $clvuser = $_POST["clvuser"] ?? '';

    try
    {

        include "../common/conexion.php";
        $conexion->autocommit(false);
        $time = time();
        $fecha = date("Y-m-d", $time);
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
		WHERE a_usuarios.id_us= ?";

        $ps = $conexion->prepare($sql);
        $ps->bind_param("i", $clvuser);
        if (!$ps->execute()) {
            throw new Exception("Error al verificarAcceso.");
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
        $ps->store_result();
        $num_rows = $ps->num_rows;
        $ps->fetch();
        $ps->close();

        if ($num_rows > 0) {

            if(
                ($dia == "Monday" || $dia == "Tuesday" || $dia == "Wednesday" || $dia == "Thursday" || $dia == "Friday") && ($horaActual >= $horaInicial_l_v && $horaActual <= $horaFinal_l_v) ||
                ($dia == "Saturday") && ( ($fines_semana == 1) && $horaActual >= $horaInicial_s && $horaActual <= $horaFinal_s) ||
                ($dia == "Sunday") && ( ($fines_semana == 1) && $horaActual >= $horaInicial_d && $horaActual <= $horaFinal_d)
            ){

                if($dia == "Monday" || $dia == "Tuesday" || $dia == "Wednesday" || $dia == "Thursday" || $dia == "Friday"){
                    $horaFinal = $horaFinal_l_v;
                } else if ($dia == "Saturday") {
                    $horaFinal = $horaFinal_s;
                } else if ($dia == "Sunday") {
                    $horaFinal = $horaFinal_d;
                }

                $horaActual = new DateTime($horaActual); //fecha inicial
                $horaFinal = new DateTime($horaFinal); //fecha de cierre

                $intervalo = $horaActual->diff($horaFinal);
                $horas = $intervalo->format('%h');
                $minutos = $intervalo->format('%i');

                echo json_encode(array("status" => "dentro", "msj" => "Dentro de horario.", "horas" => $horas, "minutos" => $minutos));

            } else if (($dia == "Saturday" || $dia == "Sunday") && ($fines_semana == 0)) {

                echo json_encode(array("status" => "fuera", "msj" => "Acceso Restringido."));

            } else {

                echo json_encode(array("status" => "fuera", "msj" => "Fuera de horario."));
            }


        } else {
            echo json_encode(array("status" => "fuera", "msj" => "Fuera de horario."));
        }

        $conexion->commit();
        $conexion->close();

    } catch (mysqli_sql_exception $e) {
        echo json_encode(array("status" => "error", "msj" => "Error en la base de datos: " . $e->getMessage()));
        $conexion->close();
    }

}