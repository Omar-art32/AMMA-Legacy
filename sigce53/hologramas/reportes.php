<?php
session_start();
session_set_cookie_params(0, "/", $_SERVER["HTTP_HOST"], 0);
$mod=1;
require_once(__DIR__ . '/../common/cfg_server.php');
$d_s=$_GET['d_s'];
if(isset($_SESSION[$d_s]) && $_SESSION[$d_s]["seccion_1_4"] == "logged")
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

    <title>REPORTES-SIGCE</title>
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
                                <li role="p" class="active"><a href="#tabCtes" aria-controls="tabCtes" role="tab" data-toggle="tab"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;Por No. de Control</a></li>
                                <li role="presentation"><a href="#tabGral" aria-controls="tabGral" role="tab" data-toggle="tab"><i class="fa fa-cubes" aria-hidden="true"></i>&nbsp;Concentrado</a></li>
                                <li role="presentation"><a href="#tabRecibos" aria-controls="tabRecibos" role="tab" data-toggle="tab"><i class="fa fa-indent" aria-hidden="true"></i>&nbsp;Recibos</a></li>
                                <li role="presentation"><a href="#tabPedidos" aria-controls="tabPedidos" role="tab" data-toggle="tab"><i class="fa fa-outdent" aria-hidden="true"></i>&nbsp;Pedidos a Proveedor</a></li>
                                <?php if($_SESSION[$d_s]['id_us']==="1" || $_SESSION[$d_s]['id_us']==="72"){?>
                                <li role="presentation"><a href="#tabGraficos" aria-controls="tabGraficos" role="tab" data-toggle="tab"><i class="fa fa-outdent" aria-hidden="true"></i>&nbsp;Gráficos</a></li>
                                <?php } ?>
                              </ul>
                              <br>
                             <!-- Tab panes -->

                              <!---------------------------------------------------------------------------------------------------------------------->
                              <!---------------------------------------------------------------------------------------------------------------------->
                              <!---------------------------------------------------------------------------------------------------------------------->
                              <!-- TAB CTES -->
                              <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="tabCtes"><!-- /.tab-pane POR CLIENTE -->
                                  <div  id="tabla_lista"><!-- Tabla lista-->
                                    <div id="buscar"><!-- Div buscar-->
                                      <form class="form-horizontal" id="frmBusca" role="form" action="" method="post">
                                        <div class="form-group">
                                          <label for="ejemplo_email_3" class="col-lg-2 control-label">No. de Control:</label>
                                          <div class="col-lg-10">
                                          <input type="text" name="txtbusca" id="txtbusca" class="form-control" style="max-width:150px" value="">
                                          </div>
                                        </div>
                                        <div class="form-group" id="filtros" style="display:none">
                                          <label for="ejemplo_email_3" class="col-lg-2 control-label">Filtrar por:</label>
                                          <div class="col-lg-4" style="overflow:hidden">
                                            <div id="cbo_m" style="vertical-align:bottom"></div>
                                          </div>
                                          <div id="div_mixto" style="visibility:hidden;" class="btn-group col-lg-6" data-toggle="buttons">
                                            <label class="btn btn-btnper2 active">
                                            <input type="radio" id="tipo_t" name="radio_marca_tipo" value="T" checked="checked" onChange="busc_multi()"/>Mixto
                                            </label>
                                            <label class="btn btn-btnper2">
                                            <input type="radio" id="tipo_g" name="radio_marca_tipo" value="G" onChange="busc_multi()"/>Genéricos
                                            </label>
                                            <label class="btn btn-btnper2">
                                            <input type="radio" id="tipo_p" name="radio_marca_tipo" value="P" onChange="busc_multi()"/>Personalizados
                                            </label>
                                          </div>
                                        </div>

                                        <div class="form-group" id="by_estado" style="display:none">
                                          <label for="ejemplo_email_3" class="col-lg-2 control-label">Estado:</label>
                                          <div class="col-lg-4" style="overflow:hidden">
                                            <div id="cbo_e" style="vertical-align:bottom"></div>
                                          </div>
                                        </div>

                                        <div class="form-group" id="by_categoria"  style="display:none">
                                          <label for="ejemplo_email_3" class="col-lg-2 control-label">Categoria:</label>
                                          <div class="col-lg-4" style="overflow:hidden">
                                            <div id="cbo_c" style="vertical-align:bottom"></div>
                                          </div>
                                        </div>

                                        <div class="form-group" id="by_fechas" style="margin-top:7px; display:none">
                                           <label for="form_informes" class="col-lg-2 control-label">Fecha(s):</label>
                                           <div class="col-sm-2">
                                              <input type="text" name="fecha_ini" id="fecha_ini" value="" class="form-control inpt_fecha_rpt" readonly>&nbsp;
                                              <button type="button" name="btn_borra_fini" id="btn_borra_fini" class="btn btn-warning btn-xs" onClick="borra_fecha(1)" >
                                                   <span class="glyphicon glyphicon-remove"></span>
                                              </button>
                                           </div>

                                           <div class="col-sm-7">
                                              <input type="text" name="fecha_fin" id="fecha_fin" value="" class="form-control inpt_fecha_rpt" readonly>&nbsp;
                                              <button type="button" name="btn_borra_ffin" id="btn_borra_ffin" class="btn btn-warning btn-xs" onClick="borra_fecha(2)" >
                                                   <span class="glyphicon glyphicon-remove"></span>
                                              </button>
                                           </div>
                                        </div>

                                        <div class="form-group" id="by_orden"  style="display:none">
                                          <label for="ejemplo_email_3" class="col-lg-2 control-label">Ordenar por:</label>
                                          <div class="col-lg-4">
                                            <select name="cboOrden" class="form-control" id="cboOrden" onchange="ordenarTabla();">
                                              <option value="">SELECCIONE</option>
                                              <option value="no_cliente">CLIENTE</option>
                                              <option value="marca">MARCA</option>
                                              <option value="edo">ESTADO</option>
                                              <option value="tipo">CATEGORÍA</option>
                                            </select>
                                          </div>
                                        </div>


                                      </form>
                                    </div><!-- Fin buscar-->

                                     <div id="grid" style="overflow:scroll; padding-bottom:15px;">
                                      <div style="margin-left:8px; margin-top:20px;">
                                        <table id="jqGrid"></table>
                                        <div id="jqGridPager" style="padding-bottom:5px;"></div>
                                      </div>
                                     </div>
                                     <div id="rpt_out"><!-- Div rpt_out-->
                                      <form class="form-horizontal" id="frm_buttons" role="form" action="" method="post">
                                        <div class="form-group" style="margin-top:5px;">
                                           <label for="frm_search" class="col-lg-4 control-label">Incluir resumen?</label>
                                           <div class="col-lg-6">
                                            <span id="demo-events"><input type="checkbox" id="resumen" checked data-toggle="toggle" data-size="normal" data-on="Si" data-off="No" data-onstyle="success" data-offstyle="danger" onChange=""></span>
                                           </div>
                                          <div class="col-lg-1 border-div">
                                            <img src="../images/pdf.png" onClick="rep_pdf()" alt="Generar PDF" style="cursor:pointer;" width="50px;"/>
                                          </div>
                                          <div class="col-lg-1 border-div">
                                          <img src="../images/excell.png" onClick="rep_excel()" alt="Generar Excel" style="cursor:pointer;" width="50px;"/>
                                          </div>
                                        </div>
                                        <br>
                                        <div class="col-lg-12 col-centered">
                                            <div id="progressbar1" class="p_b"><div class="progress-label" id="lbl_pb_1"></div></div>
                                        </div>
                                      </form>
                                    </div><!-- Fin rpt_out-->
                                  </div><!-- Fin Tabla lista-->
                                </div>

                                <!---------------------------------------------------------------------------------------------------------------------->
                                <!---------------------------------------------------------------------------------------------------------------------->
                                <!---------------------------------------------------------------------------------------------------------------------->
                                <!-- TAB GRAL -->
                                <div role="tabpanel" class="tab-pane" id="tabGral"><!-- /.tab-pane ENTRADAS/EXISTENCIAS -->
                                 <form class="form-horizontal" id="frmConcentrado" role="form" action="" method="post">
                                      <div class="form-group" id="filtros_con">
                                        <label for="frmConcentrado" class="col-lg-3 control-label">Filtrar por:</label>
                                        <div id="div_mixto_con" class="btn-group col-lg-6" data-toggle="buttons">
                                          <label class="btn btn-success active">
                                          <input type="radio" id="tipo_t_con" name="radio_tipo_con" value="T" checked="checked" onChange=""/>Mixto
                                          </label>
                                          <label class="btn btn-primary">
                                          <input type="radio" id="tipo_g_con" name="radio_tipo_con" value="G" onChange=""/>Genéricos
                                          </label>
                                          <label class="btn btn-warning">
                                          <input type="radio" id="tipo_p_con" name="radio_tipo_con" value="P" onChange=""/>Personalizados
                                          </label>
                                        </div>
                                      </div>
                                      <br>
                                      <div class="form-group" id="by_fechas_con">
                                         <label for="frmConcentrado" class="col-lg-3 control-label">Fecha(s):</label>
                                         <div class="col-sm-2">
                                            <input type="text" name="fecha_ini_con" id="fecha_ini_con" value="" class="form-control inpt_fecha_rpt" readonly>&nbsp;
                                            <button type="button" name="btn_borra_fini_con" id="btn_borra_fini_con" class="btn btn-warning btn-xs" onClick="borra_fecha_con(1)" >
                                                 <span class="glyphicon glyphicon-remove"></span>
                                            </button>
                                         </div>
                                         <div class="col-sm-7">
                                            <input type="text" name="fecha_fin_con" id="fecha_fin_con" value="" class="form-control inpt_fecha_rpt" readonly>&nbsp;
                                            <button type="button" name="btn_borra_ffin_con" id="btn_borra_ffin_con" class="btn btn-warning btn-xs" onClick="borra_fecha_con(2)" >
                                                 <span class="glyphicon glyphicon-remove"></span>
                                            </button>
                                         </div>
                                      </div>
                                    </form>
                                    <form class="form-horizontal" id="frm_buttons_con" role="form" action="" method="post">
                                      <div class="form-group" style="margin-top:5px;">
                                         <label for="frm_buttons_con" class="col-lg-3 control-label">Incluir resumen?</label>
                                         <div class="col-lg-9">
                                          <span id="demo-events"><input type="checkbox" id="resumen_con" checked data-toggle="toggle" data-size="normal" data-on="Si" data-off="No" data-onstyle="info" data-offstyle="danger" onChange=""></span>
                                         </div>
                                      </div>
                                      <br>
                                      <div class="form-group" style="margin-top:5px;">
                                        <div class="col-lg-3">
                                           &nbsp;
                                        </div>
                                        <div class="col-lg-1">
                                          <img src="../images/pdf.png" onClick="rep_pdf_con()" alt="Generar PDF" style="cursor:pointer;" width="50px;"/>
                                        </div>
                                        <div class="col-lg-1">
                                          <img src="../images/excell.png" onClick="rep_excel_con()" alt="Generar Excel" style="cursor:pointer;" width="50px;"/>
                                        </div>
                                        <div class="col-lg-3">
                                           &nbsp;
                                        </div>
                                      </div>
                                      <br>
                                      <div class="col-lg-12 col-centered">
                                          <div id="progressbar2" class="p_b"><div class="progress-label" id="lbl_pb_2"></div></div>
                                      </div>
                                    </form>
                                </div>

                                <!---------------------------------------------------------------------------------------------------------------------->
                                <!---------------------------------------------------------------------------------------------------------------------->
                                <!---------------------------------------------------------------------------------------------------------------------->
                                <!-- TAB GRAL -->
                                <!-- TAB GRAL -->
                                <div role="tabpanel" class="tab-pane" id="tabRecibos"><!-- /.tab-pane ENTRADAS/EXISTENCIAS -->

                                    <form class="form-horizontal" id="frm_buttons_recibo" role="form" action="" method="post">

                                      <!-- DATOS DE LA EMPRESA NOMBRE, DOMICILIO, RFC-->
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

                                      <!--<div class="col-lg-3">
                                           &nbsp;
                                      </div>-->
                                      <label for="bFechaEmIn" class="col-lg-2 control-label">Exportar:</label>
                                      <div class="col-lg-3">
                                        <center><img src="../images/excell.png" onClick="rep_excel_recibos()" alt="Generar Excel" style="cursor:pointer;" width="50px;"/></center>
                                      </div>
                                      <div class="col-lg-3">
                                         &nbsp;
                                      </div>

                                    </div>

                                      <div class="form-group" style="margin-top:5px;">

                                        <div class="col-lg-12">
                                          <table id="tablaRecibos" data-row-style="rowStyleI" data-header-style="headerStyle">
                                    		</table>
                                        </div>

                                      </div>
                                      <br>
                                      <div class="col-lg-12 col-centered">
                                          <div id="progressbar3" class="p_b"><div class="progress-label" id="lbl_pb_3"></div></div>
                                      </div>
                                    </form>
                                </div>


                                <!---------------------------------------------------------------------------------------------------------------------->
                                <!---------------------------------------------------------------------------------------------------------------------->
                                <!---------------------------------------------------------------------------------------------------------------------->
                                <!-- TAB PEDIDOS -->
                                <div role="tabpanel" class="tab-pane" id="tabPedidos"><!-- /.tab-pane ENTRADAS/EXISTENCIAS -->

                                    <form class="form-horizontal" id="frm_buttons_pedidos" role="form" action="" method="post">

                                    <div class="form-group">
                                      <label for="txtnocontrol" class="col-lg-2 control-label">No. de Control:</label>
                                      <div class="col-lg-10">
                                      <input type="text" name="txtnocontrol" id="txtnocontrol" class="form-control" style="max-width:150px" value="">
                                      </div>
                                    </div>

                                    <div class="form-group">
                                      <label for="bFechaEmIn" class="col-lg-2 control-label">Fecha de Inicio:</label>
                                      <div class="col-lg-3">
                                        <input type="date" id="bpFechaIni" name="bpFechaIni" class="form-control">
                                      </div>
                                      <label for="bFechaEmFin" class="col-lg-2 control-label">Fecha de Fin:</label>
                                      <div class="col-lg-3">
                                        <input type="date" id="bpFechaFin" name="bpFechaFin" class="form-control">
                                      </div>
                                    </div>

                                    <div class="form-group">

                                      <!--<div class="col-lg-3">
                                           &nbsp;
                                      </div>-->
                                      <label for="bFechaEmIn" class="col-lg-2 control-label">Exportar:</label>
                                      <div class="col-lg-3">
                                        <center><img src="../images/excell.png" onClick="rep_excel_pedidos()" alt="Generar Excel" style="cursor:pointer;" width="50px;"/></center>
                                      </div>
                                      <label for="bAcumulado" class="col-lg-2 control-label" id="bLabelAcumulado">Acumulado:</label>
                                      <div class="col-lg-3">
                                         <label for="mAcumulado" class="control-label" id="mAcumulado" style="color: #ffffff; font-weight: bold; background: green;"></label>
                                      </div>

                                    </div>

                                      <div class="form-group" style="margin-top:5px;">

                                        <div class="col-lg-12">
                                          <table id="tablaPedidos" data-row-style="rowStyleI" data-header-style="headerStyle">
                                        </table>
                                        </div>

                                      </div>
                                      <br>
                                      <div class="col-lg-12 col-centered">
                                          <div id="progressbar4" class="p_b"><div class="progress-label" id="lbl_pb_4"></div></div>
                                      </div>
                                    </form>
                                </div>
                                
                                <?php if($_SESSION[$d_s]['id_us']==="1" || $_SESSION[$d_s]['id_us']==="72"){?>
                                  <!---------------------------------------------------------------------------------------------------------------------->
                                  <!---------------------------------------------------------------------------------------------------------------------->
                                  <!---------------------------------------------------------------------------------------------------------------------->
                                  <!-- TAB GRAFICOS -->
                                  <div role="tabpanel" class="tab-pane" id="tabGraficos"><!-- /.tab-pane ENTRADAS/EXISTENCIAS -->
                                    <form class="form-horizontal" id="frm_buttons_graficos" role="form" action="" method="post">
                                      <div class="col-lg-12">
                                        <table class="table-mini-font" id="tablaAnios" >
                                        </table>
                                      </div>
                                      <div class="col-lg-12">
                                        <div id="piechart_barrasA" style="width: 900px; height: 500px;"></div>
                                      </div>
                                    </form>
                                  </div>
                                <?php } ?>
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
    <script src="js/reportes/reportes.js?<?php echo random_int(0, mt_getrandmax());?>"></script>
    <script src="js/reportes/estadisticas.js?<?php echo random_int(0, mt_getrandmax());?>"></script>

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
    <script src="../estadoCuenta/js/moment.min.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

	<?php include(__DIR__ . "/../includes/acceso.php");?>

</body>

</html>
<?php
} else {
    header("Location: /sigce/acceso/login.php");
    exit();
}
?>
