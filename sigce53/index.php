<?php
/**
 * index.php — Página de inicio (home tras login). Migrado a PHP 8.3.
 *
 * Cambios respecto al original (mínimos; el archivo ya funcionaba en 8.3):
 *  - Dominio de cookie sin puerto: HTTP_HOST puede venir como "host:puerto",
 *    y un dominio de cookie con puerto es inválido y algunos navegadores lo
 *    rechazan (la sesión no persiste). Se limpia el puerto.
 *  - Redirect con protocolo dinámico: antes usaba "http://" fijo, lo que en
 *    HTTPS degradaba a HTTP. Ahora usa $protocolo_actual (de config.php).
 *  - Cookie de sesión con httponly + SameSite=Lax (endurecimiento estándar).
 */

require_once('common/cfg_server.php');   // define $svr_dir y $protocolo_actual

/**
 * Configuración de la cookie de sesión.
 *
 * Se elimina el puerto de HTTP_HOST porque los navegadores no aceptan
 * dominios de cookie con formato "host:puerto" (ej. localhost:8080),
 * lo que puede impedir que la sesión se conserve correctamente.
 *
 * Además, se configuran los atributos de seguridad:
 * - secure: envía la cookie únicamente mediante HTTPS cuando aplica.
 * - httponly: evita el acceso a la cookie desde JavaScript.
 * - samesite=Lax: reduce el riesgo de ataques CSRF manteniendo la
 *   compatibilidad con la navegación normal del sistema.
 */

$cookie_domain = preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST'] ?? '');

session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => $cookie_domain,
    'secure'   => ($protocolo_actual === 'https:'),
    'httponly' => true,
    'samesite' => 'Lax',
]);

session_start();
$mod = 1;
$d_s = $_GET['d_s'] ?? '';

if (isset($_SESSION[$d_s]) && $_SESSION[$d_s]['logged'] === 'OK') {
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<link rel="icon" type="image/ico" href="favicon.ico" />
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SIGCE</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.css?3" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="css/metisMenu.min.css" rel="stylesheet">

    <!-- Timeline CSS -->
    <link href="css/timeline.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
	<div id="pageLoading"></div>
	<header>
		<!-- Navigation -->
        <nav class="navbar navbar-default navbar-fixed-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php?d_s=<?php echo htmlspecialchars($d_s, ENT_QUOTES); ?>"><i class="fa fa-lg fa-home" aria-hidden="true"></i>SIGCE</a>
				<div class="menu-toggler sidebar-toggler">
									<span class="sr-only">Toggle navigation</span>
									<span class="icon-bar"></span>
									<span class="icon-bar"></span>
									<span class="icon-bar"></span>
				</div>
	             <ul class="nav navbar-top-links navbar-right">
	                <li class="dropdown">
	                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
	                        <i class="fa fa-envelope fa-fw"></i>  <i class="fa fa-caret-down"></i>
	                    </a>
	                    <ul class="dropdown-menu dropdown-messages">
	                        <li>
	                            <a href="#">
	                                <div>
	                                    <strong>Admin</strong>
	                                    <span class="pull-right text-muted"><em>--</em></span>
	                                </div>
	                                <div>--</div>
	                            </a>
	                        </li>
	                        <li class="divider"></li>
	                        <li>
	                            <a class="text-center" href="#">
	                                <strong>Ver todos</strong>
	                                <i class="fa fa-angle-right"></i>
	                            </a>
	                        </li>
	                    </ul>
	                </li>
	                <li class="dropdown">
	                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
	                        <i class="fa fa-bell fa-fw"></i>  <i class="fa fa-caret-down"></i>
	                    </a>
	                    <ul class="dropdown-menu dropdown-alerts">
	                        <li>
	                            <a href="#">
	                                <div>
	                                    <i class="fa fa-comment fa-fw"></i> Sin alertas
	                                    <span class="pull-right text-muted small">--</span>
	                                </div>
	                            </a>
	                        </li>
	                        <li class="divider"></li>
	                        <li>
	                            <a class="text-center" href="#">
	                                <strong>Ver todas la alertas </strong>
	                                <i class="fa fa-angle-right"></i>
	                            </a>
	                        </li>
	                    </ul>
	                </li>
	                <li class="dropdown">
	                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
	                        <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
	                    </a>
	                    <ul class="dropdown-menu dropdown-user">
	                        <li><a href="#"><i class="fa fa-gear fa-fw"></i> Configuraciones</a></li>
	                        <li class="divider"></li>
	                        <li><a href="acceso/cerrar.php?d_s=<?php echo htmlspecialchars($d_s, ENT_QUOTES); ?>"><i class="fa fa-sign-out fa-fw"></i> Salir</a></li>
	                    </ul>
	                </li>
	            </ul>
			</div>
            </div>
        </nav>
	</header>
    <div id="wrapper">
        <!-- Navigation -->
        <nav role="navigation" style="margin-bottom: 0; margin-top: -1px;">
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse" id="sidebar-area">
                    <ul class="nav" id="sidebar">
                       <?php echo $_SESSION[$d_s]['links'] ?? ''; ?>
                    </ul>
                </div>
            </div>
        </nav>

        <div id="page-wrapper">
            <div class="row">
              <div class="col-lg-12">
                    <div class="col-lg-11"><img class="img-responsive" src="acceso/images/sigce.png" style="max-width:350px; margin-top:20px; margin-left:auto !important; margin-right:auto !important;"/>
                        <hr/>
                        <div style="max-width:800px; margin-right:auto !important; margin-left:auto !important;">
                            <div class="col-lg-12">
                                <h1 class="page-header" style="text-align:center; margin-top:30px;">Bienvenido: </h1>
                                <p id="p_empresa" style="font-size:21px; text-align:center; max-width:800px; margin-right:auto !important; margin-left:auto !important; color:#084E03; text-transform: uppercase;"> <?php echo htmlspecialchars($_SESSION[$d_s]['s_username'] ?? '', ENT_QUOTES); ?> </p>
                            </div>
                        </div>
                        <div class="col-lg-12" style="margin-top:20px;">
                            <p style="font-size:18px; text-align:justify; max-width:800px; margin-right:auto !important; margin-left:auto !important;">Mensaje y descarga de manuales
                            <br><br>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.cookie.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="js/sb-admin-2.js"></script>

</body>
</html>
<?php
} else {
    header('Location: ' . $protocolo_actual . '//' . $svr_dir . '/acceso/login.php');
    exit;
}
