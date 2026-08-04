<?php
//require_once('../common/cfg_server.php');
$d_s=$_GET['d_s'];

$svr_dir   = $_SERVER["HTTP_HOST"];
$protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";


session_start();
unset($_SESSION[$d_s]);
session_destroy();
header("location: ".$protocolo.$svr_dir."/sigce/acceso/login.php");	 
?>
