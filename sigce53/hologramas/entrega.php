<?php
session_start();
session_set_cookie_params(0, "/", $_SERVER["HTTP_HOST"], 0);
$mod=1;
require_once(__DIR__ . '/../common/cfg_server.php');
$d_s=$_GET['d_s'];
if(isset($_SESSION[$d_s]) && $_SESSION[$d_s]["seccion_1_1"] == "logged")
{
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SIGCE</title>
    <link rel="icon" type="image/ico" href="../favicon.ico" />
    <!-- Bootstrap Core CSS -->
    <link href="../css/bootstrap.css" rel="stylesheet">
    <!-- MetisMenu CSS -->
    <link href="../css/metisMenu.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../css/sb-admin-2.css" rel="stylesheet">
    <link href="../css/smoothness/jquery-ui.css" rel="stylesheet">
    <link href="../css/custom-style.css" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="../css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <script type="text/javascript">
	  var user="<?php echo $_SESSION[$d_s]['s_username']; ?>";
	  var clvuser="<?php echo $_SESSION[$d_s]['id_us'];?>";

      var moduloAcceso=1;
      var seccionAcceso=1;
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
                    <h3 class="page-header" style="text-align:center;">Entrega de hologramas</h3>
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
                        <div class="panel-body" style="background-color:#F6F6F6">
                                                    <div id="opciones"><!-- Inicia Opciones -->
                              <form class="form-horizontal" id="fromOptions" role="form" action='' method='post'>
                              <div class="form-group">
                                  <label for="ejemplo_email_3" class="col-lg-2 control-label">No de Recibo:</label>
                                  <div class="col-lg-5">
                                    <input type='text' name='id_recibo' id='id_recibo' value='' class='recibo no-border'/>
                                  </div>
                              </div>

                              <div class="form-group">

                              </div>
                              </form>
                          </div><!-- Termina Opciones -->
                          <div id="datos_entrega"><!-- Contenido Hologramas-->
                              <form class="form-horizontal" id="formEntrega" name="formEntrega" target="_blank"  role="form" action='' method='post'>
                                <div class="form-group">
                                  <label for="ejemplo_email_3" class="col-lg-2 control-label">No. de Control:</label>
                                  <div class="col-lg-10">
                                    <input type='text' name='cliente' id='cliente' value='' class='txt-short form-control'/>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label for="ejemplo_password_3" class="col-lg-2 control-label">Marca:</label>
                                  <div class="col-lg-4">
                                    <div id="cbo_m" style="display:table-row; line-height:38px;">-------</div>
                                  </div>
                                </div>
                                <div class="form-group" id="dvEdoCat" style="display:none;">
                                  <label for="ejemplo_password_3" class="col-lg-2 control-label">Estado:</label>
                                  <div class="col-lg-4">
                                    <select class="form-control" id="cboEdo" name="cboEdo" onChange="getCategorias();"></select>
                                  </div>
                                   <label for="ejemplo_password_3" class="col-lg-2 control-label">Categoria:</label>
                                  <div class="col-lg-3">
                                    <select class="form-control" id="cboTipo" name="cboTipo" onChange="limpiar_datos('nivCategoria');"></select>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label for="ejemplo_email_3" class="col-lg-2 control-label">No Solicitud:</label>
                                  <div class="col-lg-3">
                                    <input type='text' name='n_solicitud' id='n_solicitud' value='' class='txt-large form-control'/>
                                  </div>
                                  <label for="ejemplo_email_3" class="col-lg-2 control-label">Fecha Entrega:</label>
                                  <div class="col-lg-5">
                                    <input type='text' name='txtFechaEntrega' id='txtFechaEntrega' value='' class='form-control inpt_fecha'/>&nbsp;
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label for="ejemplo_password_3" class="col-lg-2 control-label">Destino:</label>
                                  <div class="col-lg-4">
                                    <select name="destino" class="cbo-medium form-control" id="destino" onChange="cargaExistencias();">
                                    <option selected="selected" value="0">SELECCIONAR</option>
                                    <option value="NACIONAL">NACIONAL</option>
                                    <option value="EXPORTACION">EXPORTACI&Oacute;N</option>
                                    <option value="NACIONAL Y EXPORTACION">NACIONAL Y EXPORTACI&Oacute;N</option>
                                    </select>
                                  </div>
                                </div>
                                 <!-- Modal Mensaje Envío -->
                                <div class="modal fade" id="modalMsjs" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                  <div class="modal-dialog" role="document">
                                    <div class="modal-content"  style="max-width:500px !important;">
                                      <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="msjTittle" style="text-align:center !important;">Atención</h4>
                                      </div>
                                      <div class="modal-body" id="msjBody" style="text-align:center !important;">

                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" id="btn_entendido" class="btn btn-primary">Entendido</button>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                               <div class="form-group"  id="dvExistencias" style="display:none;">
                                 <label for="ejemplo_password_3" class="col-lg-2 control-label">&nbsp;</label>
                                 <div class="col-lg-10">
                                    <!-- Mostrar estatus de la marca -->
                                    <table id="tblEstatus" class="table table-striped table-bordered success" style="max-width:600px; background-color:#FFF !important">
                                        <thead>
                                            <tr>
                                                <th>Estatus</th>
                                                <th>Historial</th>
                                            </tr>
                                        </thead>
                                        <!-- Cuerpo de la tabla con los campos -->
                                        <tbody style="background-color:#fff">
                                        </tbody>
                                   </table>


                                    <table id="tblExistencias" class="table table-striped table-bordered success" style="max-width:600px; background-color:#FFF !important">
                                        <!-- Cabecera de la tabla -->
                                        <thead>
                                            <tr>
                                                <th>Folio Inicial</th>
                                                <th>Folio Final</th>
                                                <th>Existencias</th>
                                            </tr>
                                        </thead>
                                        <!-- Cuerpo de la tabla con los campos -->
                                        <tbody style="background-color:#fff">
                                        </tbody>
                                   </table>
                                 </div>
                                </div>

                                <div class="form-group"><!--HOLOGRAMAS A ENTREGAR-->
                                  <label for="ejemplo_password_3" class="col-lg-2 control-label">Cantidad:</label>
                                  <div class="col-lg-10">
                                    <input type='text' name='txtCantidad' id='txtCantidad' value='' class='txt-short form-control' disabled/>
                                  </div>
                                </div>
                                <div class="form-group"  id="dvAsignados" style="display:none;">
                                 <label for="ejemplo_password_3" class="col-lg-2 control-label">&nbsp;</label>
                                 <div class="col-lg-10">
                                    <table id="tblAsignados" class="table table-striped table-bordered" style="max-width:600px;">
                                        <!-- Cabecera de la tabla -->
                                        <thead>
                                            <tr>
                                                <th>Folio Inicial</th>
                                                <th>Folio Final</th>
                                                <th>Cantidad</th>
                                            </tr>
                                        </thead>
                                        <!-- Cuerpo de la tabla con los campos -->
                                        <tbody>
                                        </tbody>
                                   </table>
                                 </div>
                                </div>
                                <div class="form-group"><!--MERMAS-->
                                  <label for="ejemplo_password_3" class="col-lg-2 control-label">Motivo Mermas:</label>
                                  <div class="col-lg-10">
                                    <select name="cbo_mtvo" id="cbo_mtvo" class="cbo-medium form-control" onChange="ver_add();" disabled>
                                    <option value="0">SELECCIONAR</option>
                                    <option value="1">Código no visible</option>
                                    <option value="2">Sin código</option>
                                    <option value="3">Se salta folios</option>
                                    <option value="4">Desprendimiento de sellos</option>
                                    </select>
                                  </div>
                                </div>
                                 <div class="form-group" id="div_mermas" style="display:none"><!--FOLIOS MERMAS-->
                                    <label for="ejemplo_password_3" class="col-lg-2 control-label">Indicar mermas:</label>
                                    <div class="col-lg-3">
                                      <input type='text' name='fol_ini_mermas' id='fol_ini_mermas' value='' class='txt-short form-control' style="float:left;"/>
                                       <button type="button" name='btnAddMerma' id='btnAddMerma' class="btn btn-success btn-xs" onClick="addMerma(1)">
                                        <span class="glyphicon glyphicon-plus"></span>
                                       </button>
                                    </div>
                                    <div class="col-lg-7">
                                      <input type='text' name='fol_fin_mermas' id='fol_fin_mermas' value='' class='txt-short form-control' style="float:left;"/>
                                       <button type="button" name='btnAddMerma2' id='btnAddMerma2' class="btn btn-success btn-xs" onClick="addMerma(2)">
                                        <span class="glyphicon glyphicon-plus"></span>
                                       </button>
                                    </div>
                                    <label for="ejemplo_password_3" class="col-lg-2 control-label">Folios mermas:</label>
                                    <div class="col-lg-10">
                                      <textarea name='fol_mermas' id='fol_mermas' class="form-control" rows="2" style="float:left; max-width:500px;" disabled></textarea>
                                       <button type="button" name='btnDelMermas' id='btnDelMermas' class="btn btn-danger btn-xs" onClick="delMermas()" style="visibility:hidden">                                        <span class="glyphicon glyphicon-minus"></span>
                                       </button>
                                    </div>
                                </div>
                                <div class="form-group"><!--TOTALESS DE LA ENTREGA-->
                                    <label for="ejemplo_password_3" class="col-lg-2 control-label">Total Mermas:</label>
                                    <div class="col-lg-1">
                                      <input type='text' name='mermas' id='mermas' value='0' class='txt-min form-control' disabled/>
                                    </div>
                                    <label for="ejemplo_password_3" class="col-lg-3 control-label">Total de Sellos a Entregar:</label>
                                    <div class="col-lg-6">
                                      <input type='text' name='txtTotal' id='txtTotal' value='' class='txt-short form-control' disabled/>
                                    </div>
                                 </div>
                                <div class="form-group">
                                    <label for="ejemplo_password_3" class="col-lg-2 control-label">Observaciones Entrega:</label>
                                    <div class="col-lg-10">
                                      <textarea name='obs_entrega' id='obs_entrega' class="form-control" rows="2" style="float:left; max-width:500px;"></textarea>
                                    </div>
                                </div>
                                <br>
                                 <div class="form-group">
                                  <label for="ejemplo_password_3" class="col-lg-3 control-label"></label>
                                  <div class="col-lg-7">
                                    <div id="btns_recibo" style="display:none;">
                                     <button type="button"  name='btnRecibo' id='btnRecibo' class="btn btn-success" onClick="reciboSimple()">
                                      <span class="glyphicon glyphicon-saved"></span>&nbsp;Generar Recibo
                                     </button>
                                     <button type="button" name='btnAgrega' id='btnAgrega' class="btn btn-primary" onClick="addCarrito()">
                                      <span class="glyphicon glyphicon-plus-sign"></span>&nbsp;Agregar a Carrito
                                     </button>
                                     <button type="button" name='btncCancel' id='btncCancel' class="btn btn-danger" onClick="confirma_canc()" >
                                      <span class="glyphicon glyphicon-remove-sign"></span>&nbsp;Cancelar
                                     </button>
                                  </div>
                                  </div>
                                  <div class="col-lg-10">
                                    <div id="msjs" style="display:none; color:#900; font-size:16px;"></div>
                                  </div>
                                </div>
                                 <div id="dialog-confirm" title="Cancelar Salida de Hologramas?" style="display:none;">
                                   <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;">
                                       </span>Al cancelar se perderan los datos que no hayan sido guardardos, Desea continuar?
                                   </p>
                                 </div>
                              </form>

                             <div id='dvCarrito' align="center" style="overflow:scroll;display:none">
                                  <table id="tblCarrito" class="table table-striped table-bordered" style="max-width:1000px;">
                                    <!-- Cabecera de la tabla -->
                                    <thead>
                                        <tr style="font-weight:bold; font-size:14px; background-color:#cee8e7;">
                                            <th>Marca</th>
                                            <th>Estado</th>
                                            <th>Tipo</th>
                                            <th>Folio Inicial</th>
                                            <th>Folio Final</th>
                                            <th>Cantidad</th>
                                            <th>&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <!-- Cuerpo de la tabla con los campos -->
                                    <tbody>
                                    </tbody>
                                  </table>
                                   <div class="col-lg-12"><!--Botones carrito -->
                                      <div id="btns_carrito" style="display:none;">
                                       <button type="button"  name='btnTerminar' id='btnTerminar' class="btn btn-success" onClick="reciboMultiple()">
                                       <span class="glyphicon glyphicon glyphicon-saved"></span>&nbsp;Recibo Carrito
                                       </button>
                                       <button type="button" name='btnEsc_Carrito' id='btnEsc_Carrito' class="btn btn-danger" onClick="canc_carrito()" >
                                       <span class="glyphicon glyphicon-remove-sign"></span>&nbsp;Cancelar carrito
                                       </button>
                                       </div>
                                       <div id="conf_esc_carrito" title="Cancelar carrito?" style="display:none;">
                                         <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;">
                                         </span>Todas las salidas del carrito se eliminarán, Desea continuar?
                                         </p>
                                       </div>
                                   </div> <!--Fin Botones carrito-->
                              </div><!--Fin tabla carrito-->
                          </div><!-- Fin Contenido Hologramas-->

                          <?php
                          //if($_SESSION[$d_s]['id_us']==1) { ?>
                          <!-- Modal -->
                          <div id="myHistorico" class="modal fade">
                            <div class="modal-dialog modal-lg" role="document">
                                  <div class="modal-content">
                                      <div class="modal-header">
                                              <h5 class="modal-title alert alert-info" style="padding:5px;">
                                                  <b>HISTÓRICO DE ENTREGA DE HOLOGRAMAS</b>
                                              </h5>
                                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                  <span aria-hidden="true">&times;</span>
                                              </button>
                                          </div>
                                      <div class="modal-body">
                                          <p id="pHistorico" style="padding-left:5px !important;"></p>

                                      </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                  </div>


                                </div>
                              </div>
                          </div>
                          <!-- End Modal -->
                          <?php //} ?>

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
    <!-- /#wrapper -->
    <div class="footer">
        Sticky Footer
    </div>
    <!-- jQuery -->
    <script src="../js/jquery.min.js"></script>
    <script src="../js/jquery.cookie.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="../js/bootstrap.min.js"></script>
    <!-- Custom Theme JavaScript -->
    <script src="../js/sb-admin-2.js"></script>
    <!-- Custom Theme JavaScript -->
    <script src="../js/jquery-ui.min.js"></script>
    <link href="../solicitudes_ocui/css/confirm.css" rel="stylesheet">
    <script src="../solicitudes_ocui/js/confirm.js?711"></script>
    <script src="js/recibos/recibos.js?<?php echo uniqid(); ?>"></script>


    <?php include(__DIR__ . "/../includes/acceso.php");?>

</body>

</html>
<?php
}
else
{
   header("location: http://".$svr_dir."/sigce/acceso/login.php");
}
?>
