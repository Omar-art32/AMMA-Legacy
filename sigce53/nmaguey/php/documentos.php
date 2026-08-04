<?php

//session_set_cookie_params($lifetime, $path, $domain, $secure, $httponly);
session_start();
$mod=1;
$svr_dir=$_SERVER['HTTP_HOST'];
include("../../common/conexion.php");
$conexion->set_charset("utf8");

$id = $_GET["f"];
$sql = $conexion->prepare("SELECT nombre,ruta
                              FROM parajes_atributos_fotos
                              WHERE id  = ?;");
if (!$sql) throw new Exception("Ocurrio un error (ERROR:01) $conexion->error");
$sql->bind_param("s",$id);
if (!$sql->execute()) throw new Exception("Ocurrio un error (ERROR:02) $conexion->error");
$sql->store_result();
$sql->bind_result($nombreoriginal,$ruta);
$sql->fetch();
$sql->close();
$nombre = $_SERVER['DOCUMENT_ROOT']."/sigce/nmaguey/images/fotosAtributos/" . $ruta;
$extension = get_extension($nombre);

if ($extension == 'jpg' || $extension == 'jpeg') {
  header("Content-type: image/jpeg");
  readfile($nombre);
} else if ($extension == 'png') {
  header("Content-type: image/png");
  readfile($nombre);
} else if ($extension == 'pdf' || $extension == 'PDF') {
  $fp = fopen($nombre, 'rb');

  header("Content-Type: application/pdf");
  header("Content-Length: " . filesize($nombre));
  header("Content-Disposition: inline; filename=\"" . $original . "\"");
  fpassthru($fp);
}else if ($extension == 'svg') {
  header("Content-type: image/svg+xml");
  readfile($nombre);
}else if ($extension == 'mp4') {
  header('Content-type: video/mp4');
  header("Accept-Ranges: bytes");
  readfile($nombre);
}

  

function get_extension($str) {
  $partes = pathinfo($str);
  return $partes['extension'];
}
?>
