<?php
declare(strict_types=1);

/**
 * entrar.php — Autenticación. Migrado a PHP 8.3 + rutas centralizadas.
 *
 * Cambios de migración (ver diagnóstico previo):
 *  - mysqli en modo excepción envuelto en try/catch (antes reventaba en 500).
 *  - Prepared statements: elimina la inyección SQL.
 *  - Sin FILTER_SANITIZE_STRING (deprecado); la seguridad la dan los prepared
 *    statements y htmlspecialchars en la salida.
 *  - utf8_encode() -> mb_convert_encoding().
 *  - Accesos a $_POST con ?? ''.
 *
 * Cambios de rutas:
 *  - La ruta base '/sigce53' ya NO está hardcodeada: viene de APP_BASE_PATH
 *    (common/config.php). Un solo lugar para cambiarla.
 *
 * DEUDA PENDIENTE (fase aparte): contraseñas en MD5 (hash en cliente) y el
 * esquema de sesión con clave aleatoria viajando por la URL (?d_s=).
 */

require '../common/config.php';    // define APP_BASE_PATH y $svr_dir
require '../common/conexion.php';  // expone $conexion (mysqli), ya con charset y report

session_start();

header('Content-Type: application/json; charset=utf-8');

// Clave aleatoria de "namespace" de sesión (esquema legado conservado).
$c = '';
for ($x = 1; $x <= 5; $x++) {
    $c .= chr(64 + rand(0, 26));
}
$r_k = $c . rand(124, 12542487);

try {
    $username  = trim($_POST['user']     ?? '');
    $contra    = trim($_POST['pswd']     ?? '');   // llega como MD5 desde el cliente
    $svr_host  = trim($_POST['tipoCon']  ?? '');   // document.domain del navegador
    $protocolo = trim($_POST['protocol'] ?? '');

    if ($username === '') {
        echo json_encode(['status' => 'error', 'msj' => 'Datos incorrectos Sin Usuario']);
        exit;
    }
    if ($contra === '') {
        echo json_encode(['status' => 'error', 'msj' => 'Datos incorrectos PSW NULL']);
        exit;
    }

    $time       = time();
    $horaActual = date('H:i:s', $time);
    $dia        = date('l', $time);

    // ---- Módulos/secciones del usuario (prepared statement) ----
    $sql_in = "SELECT a_usuarios.id_us, a_usuarios.nombre, crm_deptos.clave, crm_personal.id_cargo,
                      a_modulos.n_mod, a_modulos.icono, a_secciones.nombre nom_sec,
                      a_us_mod.id_mod, a_us_mod.id_sec, a_us_mod.nivel, a_secciones.url
               FROM a_usuarios
               INNER JOIN a_us_mod   ON a_us_mod.id_us = a_usuarios.id_us
               INNER JOIN a_modulos  ON a_modulos.id_mod = a_us_mod.id_mod
               INNER JOIN a_secciones ON a_secciones.id_mod = a_us_mod.id_mod
                                     AND a_secciones.num_sec = a_us_mod.id_sec
               INNER JOIN crm_personal ON crm_personal.id_personal = a_usuarios.id_personal
               INNER JOIN crm_cargos   ON crm_cargos.id_cargo = crm_personal.id_cargo
               INNER JOIN crm_deptos   ON crm_deptos.id_depto = crm_cargos.id_depto
               WHERE a_usuarios.login = ? AND a_usuarios.password = ? AND a_usuarios.status = 1
               ORDER BY a_us_mod.id_mod, a_us_mod.id_sec ASC";

    $stmt = $conexion->prepare($sql_in);
    $stmt->bind_param('ss', $username, $contra);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'msj' => 'Error de usuario y contraseña']);
        exit;
    }

    // ---- Datos de la cuenta (horarios, IP, fines de semana) ----
    $stmt_acc = $conexion->prepare(
        "SELECT * FROM a_usuarios WHERE login = ? AND password = ? AND status = 1"
    );
    $stmt_acc->bind_param('ss', $username, $contra);
    $stmt_acc->execute();
    $result_acc = $stmt_acc->get_result();

    if ($result_acc->num_rows === 0) {
        echo json_encode(['status' => 'error', 'msj' => 'Error de usuario y contraseña']);
        exit;
    }

    $datosPersonal   = $result_acc->fetch_assoc();
    $horaInicial_l_v = $datosPersonal['horaInicial_l_v'];
    $horaFinal_l_v   = $datosPersonal['horaFinal_l_v'];
    $horaInicial_s   = $datosPersonal['horaInicial_s'];
    $horaFinal_s     = $datosPersonal['horaFinal_s'];
    $horaInicial_d   = $datosPersonal['horaInicial_d'];
    $horaFinal_d     = $datosPersonal['horaFinal_d'];
    $fines_semana    = $datosPersonal['fines_semana'];

    // ---- Validación de horario (lógica de negocio original, intacta) ----
    $enHorario =
        (in_array($dia, ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'], true)
            && $horaActual >= $horaInicial_l_v && $horaActual <= $horaFinal_l_v)
        || ($dia === 'Saturday' && $fines_semana == 1
            && $horaActual >= $horaInicial_s && $horaActual <= $horaFinal_s)
        || ($dia === 'Sunday' && $fines_semana == 1
            && $horaActual >= $horaInicial_d && $horaActual <= $horaFinal_d);

    if (!$enHorario) {
        if (($dia === 'Saturday' || $dia === 'Sunday') && $fines_semana == 0) {
            echo json_encode(['status' => 'fuera', 'msj' => 'Acceso Restringido.']);
        } else {
            echo json_encode(['status' => 'fuera', 'msj' => 'Fuera de horario.']);
        }
        exit;
    }

    // ---- Validación de IP ----
    if ((int) $datosPersonal['verificar_ip'] === 1 && $datosPersonal['ip'] !== get_real_ip()) {
        $stmt_ip = $conexion->prepare("INSERT INTO bitacora_siig (ip, usuario) VALUES (?, ?)");
        $ip = get_real_ip();
        $stmt_ip->bind_param('ss', $ip, $username);
        $stmt_ip->execute();
        echo json_encode(['status' => 'ip', 'msj' => 'Acceso Restringido, error de IP ' . get_real_ip()]);
        exit;
    }

    // ---- Construcción de la sesión y menú de enlaces ----
    // Base de URL construida con APP_BASE_PATH (antes '/sigce53' hardcodeado).
    $base_url = $protocolo . '//' . $svr_host . APP_BASE_PATH . '/';

    $new_mod = $old_mod = $enlaces = $first_url = '';

    while ($row = $result->fetch_assoc()) {
        $new_mod = $row['id_mod'];
        $_SESSION[$r_k]['s_username'] = $row['nombre'];
        $_SESSION[$r_k]['logged']     = 'OK';
        $_SESSION[$r_k]['protocolo']  = $protocolo;
        $_SESSION[$r_k]['direccion']  = $svr_host;
        $_SESSION[$r_k]['id_us']      = $row['id_us'];
        $_SESSION[$r_k]['dpto']       = $row['clave'];
        $_SESSION[$r_k]['cargo']      = $row['id_cargo'];

        $num_sec   = 'seccion_' . $row['id_mod'] . '_' . $row['id_sec'];
        $sec_level = 'sec_lvl_' . $row['id_mod'] . '_' . $row['id_sec'];
        $_SESSION[$r_k][$num_sec]   = 'logged';
        $_SESSION[$r_k][$sec_level] = $row['nivel'];

        $url = $row['url'];
        if ($first_url === '') {
            $first_url = $url;
        }

        $hrefSeccion = htmlspecialchars($base_url . $url . '?d_s=' . $r_k, ENT_QUOTES);
        $enlaceSeccion = '<a href="' . $hrefSeccion . '">-- ' . htmlspecialchars($row['nom_sec'], ENT_QUOTES) . '</a>';

        if ($new_mod != $old_mod) {
            $cabecera = '<a href="#" class="dropdown-collapse"><i class="fa ' . htmlspecialchars($row['icono'], ENT_QUOTES)
                . ' fa-fw"></i><span class="side-menu-title"> ' . htmlspecialchars($row['n_mod'], ENT_QUOTES)
                . '</span><span class="fa arrow"></span></a><ul class="nav nav-second-level"><li>' . $enlaceSeccion . '</li> ';
            $enlaces .= ($enlaces === '') ? '<li>' . $cabecera : '</ul></li><li>' . $cabecera;
        } else {
            $enlaces .= ' <li>' . $enlaceSeccion . '</li> ';
        }
        $old_mod = $new_mod;
    }
    $enlaces .= '</ul></li>';
    $_SESSION[$r_k]['links'] = $enlaces;

    $url_ini = $protocolo . '//' . $svr_host . APP_BASE_PATH . '/index.php?d_s=' . $r_k;

    echo json_encode([
        'status' => 'OK',
        'msj'    => $url_ini,
        'links'  => mb_convert_encoding($enlaces, 'UTF-8', 'ISO-8859-1'),
        'num_r'  => $result->num_rows,
    ]);

} catch (mysqli_sql_exception $e) {
    error_log('entrar.php DB error: ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'msj' => 'Error de usuario y contraseña']);
}

function get_real_ip(): string
{
    foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED',
              'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED'] as $clave) {
        if (!empty($_SERVER[$clave])) {
            return $_SERVER[$clave];
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? '';
}
