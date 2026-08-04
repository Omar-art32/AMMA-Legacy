<?php
session_start();

//require_once("enviar_mail.php");

require_once "../edicionAsociados/php/funciones_comunes.php";
require_once("../edicionAsociados/php/exportarClientes.php");

if (is_ajax()) {
	if (isset($_POST["action"]) && !empty($_POST["action"])) {
		$action = $_POST["action"];
		switch($action) {
			case "bitacora_consultas": bitacora_consultas(); break;

		}
	}
}

function is_ajax() {
	return isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest";
}

function bitacora_consultas()
{

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
    
    function obtenerdominio($dominio)
    {
      $dominio = trim($dominio);
      $dominio = str_replace(array("http://", "www."), '', $dominio);
      $dominio = explode("/", $dominio);
      $dominio = $dominio[0];
      return $dominio;
    }
    function obtenerpagina($dominio)
    {
      $dominio = explode("/", $dominio);
      return end($dominio);
    }
    // FIN FUNCIONES

    $ipadress   = get_real_ip();

	$usuario=$_POST["usuario"];
    $origen=$_POST["origen"];
    $tipo=$_POST["tipo"];
    $no_cliente=null;
    $documento=null;
    $solicitud=null;
    if ($_POST["no_cliente"]!=0) {
        $no_cliente = $_POST["no_cliente"];
    }
    if ($_POST["solicitud"]!=0) {
        $solicitud = $_POST["solicitud"];
    }
    if ($_POST["documento"]!=0) {
        $documento = hexdec($_POST["documento"])^1337;
    }
    include("../common/conexion.php");
	$sql = "INSERT INTO respaldo_amma.bitacora_consultas(usuario, origen, tipo,no_cliente,documento,solicitud,ip) VALUES (?,?,?,?,?,?,?)";
	$ps = $conexion->prepare($sql);
	$ps->bind_param("iiisiis", $usuario,$origen,$tipo,$no_cliente,$documento,$solicitud,$ipadress);
	if (!$ps->execute()) throw new Exception("Error al generar reporte");
	$ps->close();
	echo json_encode(array("status" => "correcto"));
}
