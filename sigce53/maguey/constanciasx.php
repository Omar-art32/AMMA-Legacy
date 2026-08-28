<?php
/**
 * constanciasx.php — PHP 8.3
 * Pantalla de constancias (versión alternativa con 3 pestañas).
 * Las tablas se llenan vía AJAX. Usa DataTables local (media/js/).
 *
 * Cambios vs 5.6: mismos que constancias.php (sesión, XSS, URLs, exit).
 */
declare(strict_types=1);

session_set_cookie_params([
    'lifetime' => 0, 'path' => '/', 'domain' => '',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true, 'samesite' => 'Lax',
]);
session_start();

$mod = 1;
require_once __DIR__ . '/../common/cfg_server.php';

$d_s = $_GET['d_s'] ?? '';

if ($d_s !== ''
    && isset($_SESSION[$d_s]['seccion_4_3'])
    && $_SESSION[$d_s]['seccion_4_3'] === 'logged')
{
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="images/icon.ico">
    <title>SIIG CRM V2.0</title>
    <!--CSS-->
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/metisMenu.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.css" rel="stylesheet">
    <link href="css/estilos_control.css" rel="stylesheet">
    <link href="media/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="media/font-awesome/css/font-awesome.css" rel="stylesheet">
    <!--Javascript-->
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.cookie.js" type="text/javascript"></script>
    <script src="js/sb-admin-2.js" type="text/javascript"></script>      
    <script src="media/js/bootstrap.js"></script>
    <script src="media/js/jquery.dataTables.min.js"></script>
    <script src="media/js/dataTables.bootstrap.min.js"></script>
    <script src="js/acciones_constancias.js?24042019"></script>
    <script src="media/js/lenguajepredio.js"></script>
    <script src="media/js/lenguajeextencion.js"></script>
    <script src="media/js/lenguajevivero.js"></script>
    <script src="plugin/loadingoverlay.min.js"></script>
    <link href="../librerias/sweet-alert2/sweetalert2.css" rel="stylesheet">
    <script src="../librerias/sweet-alert2/sweetalert2.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui.min.js"></script>
    <link href="smoothness/jquery-ui.css" rel="stylesheet" type="text/css">


    <script type="text/javascript">
      var id_s=<?php echo json_encode($d_s); ?>;
      var id_depto=<?php echo json_encode($_SESSION[$d_s]["dpto"] ?? ""); ?>;
      var usr_cargo=<?php echo json_encode($_SESSION[$d_s]["cargo"] ?? ""); ?>;
      var user=<?php echo json_encode($_SESSION[$d_s]["s_username"] ?? ""); ?>;
      var clvuser=<?php echo json_encode($_SESSION[$d_s]["id_us"] ?? ""); ?>;
      
      var moduloAcceso=4;
      var seccionAcceso=3;
    </script> 


    <style>
      .ui-autocomplete {
      z-index:9999999999;
      }
    </style>


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
          <a class="navbar-brand" href="../index.php?d_s=<?php echo urlencode($d_s); ?>"><i class="fa fa-lg fa-home" aria-hidden="true"></i> SIIG CRM V2.0</a>
          <div class="menu-toggler sidebar-toggler">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </div>
          <ul class="nav navbar-top-links navbar-right">
            <li class="dropdown">
              <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-envelope fa-fw"></i>  <i class="fa fa-caret-down"></i></a>
              <ul class="dropdown-menu dropdown-messages">
                <li>
                  <a href="#"><div><strong>Admin</strong><span class="pull-right text-muted"><em>--</em></span></div><div>--</div></a>
                </li>
                <li class="divider"></li>                        
                <li>
                  <a class="text-center" href="#"><strong>Ver todos</strong><i class="fa fa-angle-right"></i></a>
                </li>
              </ul>
              <!-- /.dropdown-messages -->
            </li>

            <!-- /.dropdown -->
            <li class="dropdown">
              <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-bell fa-fw"></i>  <i class="fa fa-caret-down"></i></a>
              <ul class="dropdown-menu dropdown-alerts">
                <li>
                  <a href="#"><div><i class="fa fa-comment fa-fw"></i> Sin alertas <span class="pull-right text-muted small">--</span></div></a>
                </li>
                <li class="divider"></li>
                <li>
                  <a class="text-center" href="#"><strong>Ver todas la alertas </strong><i class="fa fa-angle-right"></i></a>
                </li>
              </ul>
              <!-- /.dropdown-alerts -->
            </li>
            <!-- /.dropdown -->
            <li class="dropdown">
              <a class="dropdown-toggle" data-toggle="dropdown" href="#"> <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i></a>
              <ul class="dropdown-menu dropdown-user">
                <li><a href="#"><i class="fa fa-gear fa-fw"></i> Configuraciones</a></li>
                <li class="divider"></li>
                <li><a href="../acceso/cerrar.php?d_s=<?php echo urlencode($d_s); ?>"><i class="fa fa-sign-out fa-fw"></i> Salir</a></li>
              </ul>
              <!-- /.dropdown-user -->
            </li>
            <!-- /.dropdown -->
          </ul>
          <!-- /.navbar-top-links -->
        </div>
        <!-- /.navbar-header -->
      </nav>
    </header>
    <div id="wrapper">

      <!-- Navigation -->
      <nav role="navigation" style="margin-bottom: 0; margin-top: -1px;">
        <div class="navbar-default sidebar" role="navigation">
          <div class="sidebar-nav navbar-collapse" id="sidebar-area">
            <ul class="nav" id="sidebar"> <?php echo $_SESSION[$d_s]['links'] ?? ''; ?> </ul>
          </div>
        <!-- /.sidebar-collapse -->
        </div>
        <!-- /.navbar-static-side -->
      </nav>
        <div id="page-wrapper">
            <div class="row panel-pincipal">
              <div class="col-lg-12" align="center">
                <h3 class="page-header text-white">CONSTANCIAS</h3>                    
              </div>
            </div>
            <div class="row">
              <div class="col-lg-12" style="margin-top:20px;">                   
                <div class="panel  panel-che" style="max-width:1100px; margin-left: auto !important; margin-right:auto !important; margin-bottom:75px;">
                  <div class="panel-heading"></div>
                  <div class="panel-body">
                    <ul class="nav nav-tabs" role="tablist">
                      <li role="user-data" class="active"><a href="#tab1success" aria-controls="changePassword" role="tab" data-toggle="tab"><span class="fa fa-list-alt"></span> Constancia de Predios</a></li>
                      <li role="user-data" class=""><a href="#tab2success" aria-controls="changePassword" role="tab" data-toggle="tab"><span class="fa fa-list-alt"></span> Constancia de Extracción</a></li>
                      <li role="user-data" class=""><a href="#tab3success" aria-controls="changePassword" role="tab" data-toggle="tab"><span class="fa fa-list-alt"></span> Constancia de Viveros</a></li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="tab1success">
                            <div style="margin-left:auto !important; margin-right:auto !important;">
                                <div class="panel  panel-default" style="margin-bottom:0 !important; margin-top:5px !important;">
                                    <div class="panel-heading" style="text-align:center; font-size:16px; font-weight:bold;">Constancia de Registro de Predio
                                    </div>
                                    <div class="panel-body">
                                        <table id="example_pr" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th># Paraje</th>
                                                    <th># Cliente</th>
                                                    <th>Nombre</th>
                                                    <th>Constancias</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th># Paraje</th>
                                                    <th># Cliente</th>
                                                    <th>Nombre</th>
                                                    <th>Constancias</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div> 
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="tab2success">
                            <div style="margin-left:auto !important; margin-right:auto !important;">
                                <div class="panel  panel-default" style="margin-bottom:0 !important; margin-top:5px !important;">
                                    <div class="panel-heading" style="text-align:center; font-size:16px; font-weight:bold;">Constancia de Extracción 
                                      
                                    </div>
                                    <div class="panel-body">

                                      <a href="" title="Constancias" class="btn btn-primary pull-right"  data-toggle="modal" data-target="#exampleModalCenter2"><span class="glyphicon glyphicon-plus"></span>Predios sin guias</a>

                                      <br>
                                      <br>

                                        <table id="example_ex" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th># Paraje</th>
                                                    <th># Guía</th>
                                                    <th># Cliente</th>
                                                    <th>Nombre</th>
                                                    <th>Constancias</th>
                                                    <th>Opciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th># Paraje</th>
                                                    <th># Guía</th>
                                                    <th># Cliente</th>
                                                    <th>Nombre</th>
                                                    <th>Constancias</th>
                                                    <th>Opciones</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                        <div class="modal fade" id="exampleModalCenter" role="dialog">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                  <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    <h4 class="modal-title">Ingreso de Constancias</h4>
                                                  </div>
                                                  <form class="form-horizontal" id="formmodal" action="" method="POST" name="formmodal" enctype="multipart/form-data" >
                                                    <div class="modal-body ">
                                                      <input type="hidden" name='usr' id='usr' value='<?php echo htmlspecialchars($_SESSION[$d_s]["s_username"] ?? "", ENT_QUOTES); ?>'>
                                                      <div class=" row">
                                                        <div class="col-md-2" style="margin-left: 10px;">
                                                           <label for="formmodal">No. Paraje</label>
                                                            <input type="text" readonly class="form-control" id="noparaje" name="noparaje">
                                                        </div>
                                                        <div class="col-md-2">
                                                           <label for="formmodal">No. Cliente</label>
                                                          <input type="text" readonly class="form-control" id="nocliente" name="nocliente">
                                                        </div>
                                                        <div class="col-md-2" style="margin-left: 210px;">
                                                          <label for="formmodal"  >Cantidad</label>
                                                          <input type="number" class="form-control" align="right" id="cant" name="cant" required="true">
                                                        </div>
                                                      </div>
                                                      <label for="formmodal">Nombre</label>
                                                      <input type="text"  readonly class="form-control" id="nomcliente" name="nomcliente" aria-describedby="emailHelp"> 
                                                    </div>
                                                  </form>
                                                  <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove" value=""></span> Cancelar</button>
                                                    <button type="submit" class="btn btn-success" onClick="historial()" ><span class="glyphicon glyphicon-ok"></span> Guardar</button>
                                                  </div>
                                                </div>
                                            </div>
                                        </div>


                                            <div class="modal fade" id="exampleModalCenter2" role="dialog">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                  <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    <h4 class="modal-title">Ingreso de Constancias</h4>
                                                  </div>
                                                  <form class="form-horizontal" id="formmodal" action="" method="POST" name="formmodal" enctype="multipart/form-data" >
                                                    <div class="modal-body ">
                                                      <input type="hidden" name='usr2' id='usr2' value='<?php echo htmlspecialchars($_SESSION[$d_s]["s_username"] ?? "", ENT_QUOTES); ?>'>
                                                      <div class=" row">
                                                        <div class="col-md-2" style="margin-left: 10px;">
                                                           <label for="formmodal">No. Paraje</label>
                                                            <input type="text" class="form-control" id="noparaje2" name="noparaje2">
                                                        </div>
                                                        <div class="col-md-2" style="margin-left: 210px;">
                                                          <label for="formmodal"  >Cantidad</label>
                                                          <input type="number" class="form-control" align="right" id="cant2" name="cant2" required="true">
                                                        </div>
                                                      </div>
                                                    </div>
                                                  </form>
                                                  <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove" value=""></span> Cancelar</button>
                                                    <button type="button" class="btn btn-success" onClick="historial2()" ><span class="glyphicon glyphicon-ok"></span> Guardar</button>
                                                  </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div> 
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="tab3success">
                            <div style="margin-left:auto !important; margin-right:auto !important;">
                                <div class="panel  panel-default" style="margin-bottom:0 !important; margin-top:5px !important;">
                                    <div class="panel-heading" style="text-align:center; font-size:16px; font-weight:bold;">Constancia de Registro de Vivero
                                    </div>
                                    <div class="panel-body">
                                        <table id="example_vi" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th># Paraje</th>
                                                    <th># Cliente</th>
                                                    <th>Nombre</th>
                                                    <th>Constancias</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th># Paraje</th>
                                                    <th># Cliente</th>
                                                    <th>Nombre</th>
                                                    <th>Constancias</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div> 
                            </div>
                        </div>                    
                    </div>                             
                  </div>
                </div>
              </div>              
            </div>
        </div>
    </div>

     <?php include __DIR__ . "/../includes/acceso.php"; ?>
</body>
</html>
<?php 
  }
  else
  {
    header("location: http://".$svr_dir."/siigv2/acceso/login.php");  
  }
?>