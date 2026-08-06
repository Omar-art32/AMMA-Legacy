<?php
// Configura los datos de tu cuenta
$x = 1;
$c = "";
$r_k = rand(124, 12542487);
for ($x = 1; $x <= 5; $x++) {
    $c .= chr(64 + rand(0, 26));
}
$r_k = $c . $r_k;
//session_name($r_k);
session_start();
include '../common/conexion.php';
//require_once('../common/cfg_server.php');
$conexion->set_charset("utf8");
if ($_POST['user']) {
    //Comprobacion del envio del nombre de usuario y password
    $username = limpiarPropiedades($_POST['user']);
    $password = limpiarPropiedades($_POST['pswd']);
    $svr_dir = limpiarPropiedades($_POST['tipoCon']);
    $protocolo = limpiarPropiedades($_POST['protocol']);
    //$contra = md5(trim($password));
    $contra = utf8_decode($password);
    //$mod=$_POST['mod'];
    $id_us = "";
    if ($password == null) {
        echo json_encode(array('status' => 'error', 'msj' => 'Datos incorrectos PSW NULL'));
    } //if password==null
    else {
        $time = time();
        $fecha = date("Y-m-d", $time);
        $horaActual = date("H:i:s", $time);

        $new_mod = "";
        $old_mod = "";
        $enlaces = "";
        $first_url = "";
        //$result = $conexion->query("SELECT nombre, password FROM usuarios WHERE login = '$username'");
        $sql_in = "SELECT a_usuarios.id_us, a_usuarios.nombre,crm_deptos.clave,crm_personal.id_cargo, a_modulos.n_mod, a_modulos.icono, a_secciones.nombre nom_sec,a_us_mod.id_mod, a_us_mod.id_sec, a_us_mod.nivel, a_secciones.url FROM a_usuarios INNER JOIN a_us_mod on a_us_mod.id_us=a_usuarios.id_us INNER JOIN a_modulos ON a_modulos.id_mod=a_us_mod.id_mod INNER JOIN a_secciones on a_secciones.id_mod=a_us_mod.id_mod and a_secciones.num_sec=a_us_mod.id_sec INNER JOIN crm_personal on crm_personal.id_personal=a_usuarios.id_personal INNER JOIN crm_cargos ON crm_cargos.id_cargo=crm_personal.id_cargo INNER JOIN crm_deptos on crm_deptos.id_depto=crm_cargos.id_depto WHERE a_usuarios.login= '$username' and a_usuarios.password='$contra' AND a_usuarios.status=1 ORDER BY a_us_mod.id_mod, a_us_mod.id_sec asc";
        $result = $conexion->query($sql_in);
        if ($result != false) {
            $num_rows = $result->num_rows;
            if ($num_rows > 0) {

                $sql_acc = "SELECT * FROM a_usuarios WHERE a_usuarios.login= '$username' and a_usuarios.password='$contra' AND a_usuarios.status=1 AND (a_usuarios.horaInicial_l_v <= '$horaActual' and a_usuarios.horaFinal_l_v>='$horaActual')";
                $result_acc = $conexion->query($sql_acc);

                if ($result_acc != false) {
                    $num_rows_acc = $result_acc->num_rows;
                    if ($num_rows_acc > 0) {

                        $datosPersonal = mysqli_fetch_array($result_acc);
                        $fines_semana = $datosPersonal["fines_semana"];
                        $clvuser = $datosPersonal["id_us"];
                        $time = time();
                        $dia = date("l", $time);

                        if (($dia == "Saturday" || $dia == "Sunday") && ($fines_semana == 0)) {

                            echo json_encode(array("status" => "fuera", "msj" => "Acceso Restringido."));

                        } else if ( ($clvuser == 29 || $clvuser == 34 || $clvuser == 26 || $clvuser == 25 || $clvuser == 33) && ($dia == "Sunday") && ($fines_semana == 1)) {

                            echo json_encode(array("status" => "fuera", "msj" => "Acceso Restringido."));
            
                        } else if ( ($clvuser == 29 || $clvuser == 34 || $clvuser == 26 || $clvuser == 25 || $clvuser == 33) && ($dia == "Saturday") && ($fines_semana == 1) && ($horaActual <= "07:00:00" || $horaActual >= "22:00:00") ) {
            
                            echo json_encode(array("status" => "fuera", "msj" => "Fuera de horario."));
            
                        } else if ($datosPersonal["verificar_ip"]==1 && $datosPersonal["ip"] !=  get_real_ip()){
							$sql_ip = "INSERT INTO bitacora_siig(ip,usuario)Values('".get_real_ip()."','".$username."')";
							//echo $sql_ip;
							$conexion->query($sql_ip);
							echo json_encode(array("status" => "ip", "msj" => "Acceso Restringido, error de IP ".get_real_ip()));
						} else {
							//$ipadress = get_real_ip();
							//echo $datosPersonal["ip"];

                            while ($row = mysqli_fetch_array($result)) {
                                $new_mod = $row['id_mod'];
                                $_SESSION[$r_k]['s_username'] = $row['nombre'];
                                $id_us = $row['id_us'];
                                $_SESSION[$r_k]['logged'] = 'OK';
                                $_SESSION[$r_k]['protocolo'] = $protocolo;
                                $_SESSION[$r_k]['direccion'] = $svr_dir;
                                $_SESSION[$r_k]['id_us'] = $row['id_us'];
                                $_SESSION[$r_k]['dpto'] = $row['clave'];
                                $_SESSION[$r_k]['cargo'] = $row['id_cargo'];
                                $num_sec = 'seccion_' . $row['id_mod'] . '_' . $row['id_sec'];
                                $sec_level = 'sec_lvl_' . $row['id_mod'] . '_' . $row['id_sec'];
                                $_SESSION[$r_k][$num_sec] = 'logged';
                                $_SESSION[$r_k][$sec_level] = $row['nivel'];
                                $url = $row['url'];
                                if ($first_url == "") {
                                    $first_url = $url;
                                }

                                if ($new_mod != $old_mod) {
                                    if ($enlaces == "") {
                                        $enlaces .= '<li>
											  <a href="#" class="dropdown-collapse"><i class="fa ' . $row['icono'] . ' fa-fw"></i><span class="side-menu-title"> ' . $row['n_mod'] . '</span><span class="fa arrow"></span></a>

											  <ul class="nav nav-second-level">
												  <li>
													  <a href="' . $protocolo . '//' . $svr_dir . '/sigce/' . $url . '?d_s=' . $r_k . '">-- ' . $row['nom_sec'] . '</a>
												  </li> ';
                                    } else {
                                        $enlaces .= '</ul></li><li>
											  <a href="#" class="dropdown-collapse"><i class="fa ' . $row['icono'] . ' fa-fw"></i><span class="side-menu-title"> ' . $row['n_mod'] . '</span><span class="fa arrow"></span></a>
											  <ul class="nav nav-second-level">
												  <li>
													  <a href="' . $protocolo . '//' . $svr_dir . '/sigce/' . $url . '?d_s=' . $r_k . '">-- ' . $row['nom_sec'] . '</a>
												  </li> ';
                                    }
                                } else {
                                    $enlaces .= ' <li>
											<a href="' . $protocolo . '//' . $svr_dir . '/sigce/' . $url . '?d_s=' . $r_k . '">-- ' . $row['nom_sec'] . '</a>
										</li> ';
                                }
                                $old_mod = $new_mod;
                            }
                            $enlaces .= '</ul></li>';
                            $_SESSION[$r_k]['links'] = $enlaces;
                            $url_ini = $protocolo . '//' . $svr_dir . '/sigce/index.php?d_s=' . $r_k;
                            //$sql_acc="insert into a_accesos (id_us,fecha) values($id_us,NOW())";
                            //$res_acc = $conexion->query($sql_acc);

                            echo json_encode(array('status' => 'OK', 'msj' => $url_ini, 'links' => utf8_encode($enlaces), 'num_r' => $num_rows));

                        }

                    } //fuera de horario

                    else {
                        echo json_encode(array('status' => 'error', 'msj' => 'Acceso fuera de horario'));
                    }

                } //fuera de horario

                else {
                    echo json_encode(array('status' => 'error', 'msj' => 'Acceso fuera de horario'));
                }

            } else {
                echo json_encode(array('status' => 'error', 'msj' => 'Error de usuario y contraseña'));
            }

        } else {
            echo json_encode(array('status' => 'error', 'msj' => 'Error de usuario y contraseña'));
        }
    } //else password null
} //if post user
else {
    echo json_encode(array('status' => 'error', 'msj' => 'Datos incorrectos Sin Usuario'));
}

function limpiarPropiedades($var)
{
    $limpia = filter_var($var, FILTER_SANITIZE_STRING);
    return $limpia;
}

function get_real_ip()
{
  if (isset($_SERVER["HTTP_CLIENT_IP"])) {
    return $_SERVER["HTTP_CLIENT_IP"];
  } elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
    return $_SERVER["HTTP_X_FORWARDED_FOR"];
  } elseif (isset($_SERVER["HTTP_X_FORWARDED"])) {
    return $_SERVER["HTTP_X_FORWARDED"];
  } elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])) {
    return $_SERVER["HTTP_FORWARDED_FOR"];
  } elseif (isset($_SERVER["HTTP_FORWARDED"])) {
    return $_SERVER["HTTP_FORWARDED"];
  } else {
    return $_SERVER["REMOTE_ADDR"];
  }
}

?>
