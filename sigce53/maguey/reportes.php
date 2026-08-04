<?php
session_start();
session_set_cookie_params(0, "/", $_SERVER["HTTP_HOST"], 0);
$mod=1;
require_once('../common/cfg_server.php');
$d_s=$_GET['d_s'];
if(isset($_SESSION[$d_s]) && $_SESSION[$d_s]["seccion_4_5"] == "logged")
{
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
   <!-- <meta http-equiv="X-UA-Compatible" content="IE=edge">-->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>REPORTES</title>
    <link rel="icon" type="image/ico" href="../favicon.ico" />
    <!-- Bootstrap Core CSS -->
    <link href="../css/bootstrap.css" rel="stylesheet">
    <!-- MetisMenu CSS -->
    <link href="../css/metisMenu.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../css/sb-admin-2.css" rel="stylesheet">
    <link href="../css/smoothness/jquery-ui.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" media="screen" href="../css/ui.jqgrid-bootstrap2.css" />
    <link href="../css/custom-style.css" rel="stylesheet">
    <link href="../css/bootstrap-toggle.css" rel="stylesheet">
    <link href="../css/hologramas/reportes.css" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="../css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <script type="text/javascript">
	  var user="<?php echo $_SESSION[$d_s]['s_username']; ?>";
	  var clvuser="<?php echo $_SESSION[$d_s]['id_us'];?>";
    var cargo = "<?php echo $_SESSION[$d_s]['cargo'];?>";

	  var moduloAcceso=1;
	  var seccionAcceso=4;
	</script>


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
                <a class="navbar-brand" href="../index.php?d_s=<?php echo $d_s?>"><i class="fa fa-lg fa-home" aria-hidden="true"></i> SIGCE</a>
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
	                                    <span class="pull-right text-muted">
	                                        <em>--</em>
	                                    </span>
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
	                    <!-- /.dropdown-messages -->
	                </li>

	                <!-- /.dropdown -->
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
	                    <!-- /.dropdown-alerts -->
	                </li>
	                <!-- /.dropdown -->
	                <li class="dropdown">
	                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
	                        <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
	                    </a>
	                    <ul class="dropdown-menu dropdown-user">
	                        <li><a href="#"><i class="fa fa-gear fa-fw"></i> Configuraciones</a>
	                        </li>
	                        <li class="divider"></li>
	                        <li><a href="../acceso/cerrar.php?d_s=<?php echo $d_s?>"><i class="fa fa-sign-out fa-fw"></i> Salir</a>
	                        </li>
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
                    <ul class="nav" id="sidebar">
                       <?php echo $_SESSION[$d_s]['links'];?>
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
             <div class="row" style="background-color:#3c7d34; color:#FBFBFB;">
                <div class="col-lg-12">
                    <h3 class="page-header" style="text-align:center;">Reportes</h3>
                </div>

                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                  <div class="col-lg-12" style="margin-top:20px;">
                    <div class="panel  panel-default" style="max-width:1100px !important; margin-left:auto; margin-right:auto;">
                        <div class="panel-heading">
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" style="background-color:#F6F6F6" id="pnlReportesMain">
                              <ul class="nav nav-tabs" role="tablist">
                                <li role="p" class="active"><a href="#tabCtes" aria-controls="tabCtes" role="tab" data-toggle="tab"><i class="fa fa-indent" aria-hidden="true"></i>&nbsp;Predios</a></li>
                                <li role="presentation"><a href="#tabGral" aria-controls="tabGral" role="tab" data-toggle="tab"><i class="fa fa-indent" aria-hidden="true"></i>&nbsp;Plantas</a></li>
                                	<li role="presentation"><a href="#tabRecibos" aria-controls="tabRecibos" role="tab" data-toggle="tab"><i class="fa fa-indent" aria-hidden="true"></i>&nbsp;Guías</a></li>
                                  
                              </ul>
                              <br>
                             <!-- Tab panes -->

                              <!---------------------------------------------------------------------------------------------------------------------->
                              <!---------------------------------------------------------------------------------------------------------------------->
                              <!---------------------------------------------------------------------------------------------------------------------->
                              <!-- TAB CTES -->
                              <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="tabCtes">
                                  <form class="form-horizontal" id="frm_maguey_reporte" role="form" action="" method="post">

                                    <div class="form-group">

                                      <label for="bFechaEmIn" class="col-lg-2 control-label">No. de Control:</label>
                                      <div class="col-lg-3">
                                        <input type="text" id="noControl" name="noControl" class="form-control">
                                      </div>
                                      <div class="col-lg-1"></div>
                                      <label  class="col-lg-5 control-label" style="text-align:left; color: #B40404" id="lblCliente"></label>
                                    </div>

                                    <div class="form-group">

                                      <label for="bFechaEmIn" class="col-lg-2 control-label">Fecha de Inicio:</label>
                                      <div class="col-lg-3">
                                        <input type="date" id="bFechaIni" name="bFechaIni" class="form-control">
                                      </div>

                                      <label for="bFechaEmFin" class="col-lg-2 control-label">Fecha de Fin:</label>
                                      <div class="col-lg-3">
                                        <input type="date" id="bFechaFin" name="bFechaFin" class="form-control">
                                      </div>


                                    </div>

                                    <div class="form-group">
                                      <label for="bFechaEmIn" class="col-lg-2 control-label">Exportar:</label>
                                      <div class="col-lg-3" id="divIcoRep1">
                                        <center><img src="../images/excell.png" onClick="rep_excel_predios()" alt="Generar Excel" style="cursor:pointer;" width="50px;"/></center>
                                      </div>
                                      <div class="col-lg-3">
                                         &nbsp;
                                      </div>
                                      <br>
                                      <div class="col-lg-12 col-centered">
                                          <div id="progressbar1" class="p_b"><div class="progress-label" id="lbl_pb_1"></div></div>
                                      </div>
                                    </div>

                                      <div class="form-group" style="margin-top:5px;">

                                        <div class="col-lg-12">
                                          <table id="tablaPredios" data-row-style="rowStyleI" data-header-style="headerStyle">
                                    		</table>
                                        </div>

                                      </div>
                                                                            
                                    </form>
                                </div>


                                <!---------------------------------------------------------------------------------------------------------------------->
                                <!---------------------------------------------------------------------------------------------------------------------->
                                <!---------------------------------------------------------------------------------------------------------------------->
                                <!-- TAB GRAL -->
                                
                                <div role="tabpanel" class="tab-pane" id="tabGral">
	                                <form class="form-horizontal" id="frm_plantas_reporte" role="form" action="" method="post">
                                    
                                    <div class="form-group">
                                      <label for="bFechaEmIn" class="col-lg-2 control-label">No. de Control:</label>
                                      <div class="col-lg-3">
                                        <input type="text" id="noControl2" name="noControl2" class="form-control">
                                      </div>
                                      <div class="col-lg-1"></div>
                                      <label  class="col-lg-5 control-label" style="text-align:left; color: #B40404" id="lblCliente2"></label>
                                    </div>

                                    <div class="form-group">

                                      <label for="bFechaEmIn" class="col-lg-2 control-label">Fecha de Inicio:</label>
                                      <div class="col-lg-3">
                                        <input type="date" id="bFechaIni2" name="bFechaIni2"  class="form-control">
                                      </div>

                                      <label for="bFechaEmFin" class="col-lg-2 control-label">Fecha de Fin:</label>
                                      <div class="col-lg-3">
                                        <input type="date" id="bFechaFin2" name="bFechaFin2"  class="form-control">
                                      </div>


                                    </div>

                                    

                                    <div class="form-group">
                                      <label for="bFechaEmIn" class="col-lg-2 control-label">Exportar:</label>
                                      <div class="col-lg-3" id="divIcoRep2">
                                        <center><img src="../images/excell.png" onClick="rep_excel_plantas()" alt="Generar Excel" style="cursor:pointer;" width="50px;"/></center>
                                      </div>
                                      <div class="col-lg-3">
                                         &nbsp;
                                      </div>
                                      <br>
                                      <div class="col-lg-12 col-centered">
                                          <div id="progressbar2" class="p_b"><div class="progress-label" id="lbl_pb_2"></div></div>
                                      </div>
                                    </div>

                                    <div class="form-group" style="margin-top:5px;">
                                      <div class="col-lg-12">
                                        <table id="tablaPlantas" data-row-style="rowStyleI" data-header-style="headerStyle">
                                      </table>
                                      </div>
                                    </div>
                                    
                                  </form>
                                </div>
                                <!---------------------------------------------------------------------------------------------------------------------->
                                <!---------------------------------------------------------------------------------------------------------------------->
                                <!---------------------------------------------------------------------------------------------------------------------->
                                <!-- TAB GRAL -->
                                <div role="tabpanel" class="tab-pane" id="tabRecibos"><!-- /.tab-pane ENTRADAS/EXISTENCIAS -->

                                    <form class="form-horizontal" id="frm_guias_reporte" role="form" action="" method="post">

                                      <div class="form-group">
                                        <label for="bnoControl" class="col-lg-2 control-label">No. Control:</label>
                                        <div class="col-lg-3">
                                          <input type="text" id="noControl3" name="noControl3" class="form-control">
                                        </div>
                                        <div class="col-lg-1"></div>
                                        <label  class="col-lg-5 control-label" style="text-align:left; color: #B40404" id="lblCliente3"></label>
                                      </div>

                                      <div class="form-group">
                                        <label for="bFechaEmIn" class="col-lg-2 control-label">Fecha de Inicio:</label>
                                        <div class="col-lg-3">
                                          <input type="date" id="bFechaIni3" name="bFechaIni3" class="form-control">
                                        </div>
                                        <label for="bFechaEmFin" class="col-lg-2 control-label">Fecha de Fin:</label>
                                        <div class="col-lg-3">
                                          <input type="date" id="bFechaFin3" name="bFechaFin3" class="form-control">
                                        </div>
                                      </div>

                                    <div class="form-group">

                                      <label for="bFechaEmIn" class="col-lg-2 control-label">Exportar:</label>
                                      <div class="col-lg-3" id="divIcoRep3">
                                        <center><img src="../images/excell.png" onClick="rep_excel_guias()" alt="Generar Excel" style="cursor:pointer;" width="50px;"/></center>
                                      </div>
                                      <div class="col-lg-3">
                                         &nbsp;
                                      </div>
                                      <br>
                                      <div class="col-lg-12 col-centered">
                                          <div id="progressbar3" class="p_b"><div class="progress-label" id="lbl_pb_3"></div></div>
                                      </div>

                                    </div>

                                      <div class="form-group" style="margin-top:5px;">

                                        <div class="col-lg-12">
                                          <table id="tablaGuias" data-row-style="rowStyleI" data-header-style="headerStyle">
                                    		</table>
                                        </div>

                                      </div>
                                      <br>
                                      
                                    </form>
                                </div>


                                

                              </div>

                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
               </div>
            </div>
            <!-- /.row -->

        </div>
        <!-- /#page-wrapper -->

    </div>

    <!--Modal observaciones-->
      <div class="modal fade " id="	" tabindex="-1" role="dialog" aria-hidden="true" >
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" >×</button>
                <span class="glyphicon glyphicon-list-alt"> </span> Información!!

               </h4>
            </div>
            <div class="modal-body">
              <p id="mdlObservacionesReporte" style="padding-left:5px !important;"></p>
            </div>
          </div>
        </div>
      </div>
      <!--Modal observaciones-->

    <!--Modal observaciones-->
      <div class="modal fade " id="modalSubir" tabindex="-1" role="dialog" aria-hidden="true" >
        <div class="modal-dialog" role="document">
          <div class="modal-content" style="height:450px; width:350px; ">
            <div class="modal-header">
              <h4 class="modal-title">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" >×</button>
                <span class="glyphicon glyphicon-list-alt"> </span> Subir Acuse

               </h4>
            </div>
            <div class="modal-body">
              <div class="form-group" style="margin-top:5px;">
                <div class="col-lg-12">
                  <input id="input-id" name="input-id" type="file" data-allowed-file-extensions='["pdf", "PDF"]' />
                </div>
                <br>
              </div>
            </div>
          </div>
        </div>
    </div>
    <!--Modal observaciones-->

    <!-- /#wrapper -->
    <div class="footer">
        <!--Sticky Footer-->
    </div>
    <!-- jQuery -->
    <script src="../js/jquery.min.js"></script>
    <script src="../js/jquery.cookie.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/bootstrap-toggle.js"></script>
    <!-- Custom Theme JavaScript -->
    <script src="../js/sb-admin-2.js"></script>
    <!-- Custom Theme JavaScript -->
    <script src="../js/jquery-ui.min.js"></script>
    <!-- This is the Javascript file of lenguaje español jqgrid -->
	<script src="../js/i18n/grid.locale-es.js"></script>
    <!-- This is the Javascript file of jqGrid -->
    <script src="../js/jquery.jqGrid.min.js"></script>
    <script src="js/reportes.js?81"></script>

    <link href="../registros_oc/etiquetas/plugins/bootstrap-table-master/src/bootstrap-table.css" rel="stylesheet">

    <script src="../registros_oc/etiquetas/plugins/bootstrap-table-master/src/bootstrap-table.js"></script>
    <!-- put your locale files after bootstrap-table.js -->
    <script src="../registros_oc/etiquetas/plugins/bootstrap-table-master/src/locale/bootstrap-table-es-MX.js"></script>
	<!-- para editar los registros en la table -->
    <link href="../registros_oc/etiquetas/plugins/bootstrap-table-master/editable/bootstrap-editable.css" rel="stylesheet">
    <script src="../registros_oc/etiquetas/plugins/bootstrap-table-master/editable/bootstrap-editable.js"></script>
    <script src="../registros_oc/etiquetas/plugins/bootstrap-table-master/editable/bootstrap-table-editable.js"></script>
    <script src="../registros_oc/etiquetas/plugins/bootstrap-table-master/src/extensions/export/bootstrap-table-export.js"></script> <!-- export -->

    <!--<script defer src="https://use.fontawesome.com/releases/v5.8.2/js/all.js" integrity="sha384-DJ25uNYET2XCl5ZF++U8eNxPWqcKohUUBUpKGlNLMchM7q4Wjg2CUpjHLaL8yYPH" crossorigin="anonymous"></script>-->

      <!-- PARA IMPORTACIÓN DE ARCHIVOS -->
    <link  href="../registros_oc/etiquetas/plugins/bootstrap-fileinput-master/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
    <script src="../registros_oc/etiquetas/plugins/bootstrap-fileinput-master/js/plugins/piexif.min.js" type="text/javascript"></script>
    <script src="../registros_oc/etiquetas/plugins/bootstrap-fileinput-master/js/plugins/sortable.min.js" type="text/javascript"></script>
    <script src="../registros_oc/etiquetas/plugins/bootstrap-fileinput-master/js/plugins/purify.min.js" type="text/javascript"></script>
    <!--<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>-->
    <script src="../registros_oc/etiquetas/plugins/bootstrap-fileinput-master/js/fileinput.min.js"></script>
    <script src="../registros_oc/etiquetas/plugins/bootstrap-fileinput-master/themes/fas/theme.min.js"></script>
    <script src="../registros_oc/etiquetas/plugins/bootstrap-fileinput-master/js/locales/LANG.js"></script>
    <script src="../registros_oc/etiquetas/plugins/bootstrap-fileinput-master/js/locales/es.js"></script>


	<?php include("../includes/acceso.php");?>

</body>

</html>


<?php
}
else
{
   header("location: http://".$svr_dir."/sigce/acceso/login.php");
}
?>
