<?php
session_start();
session_set_cookie_params(0, "/", $_SERVER["HTTP_HOST"], 0);
$mod=1;
require_once('../common/cfg_server.php');
$d_s=$_GET['d_s'];
if(isset($_SESSION[$d_s]) && $_SESSION[$d_s]["seccion_1_2"] == "logged")
{
  if($_SESSION[$d_s]['cargo']==13 && $_SESSION[$d_s]['sec_lvl_1_2']==1)
  {
    $title_1='Entradas';
  }
  else
  {
    $title_1='Existencias';
  }
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
    <link href="../css/smoothness/jquery-ui.css" rel="stylesheet"/>
    <link href="../css/ui.jqgrid-bootstrap2.css" rel="stylesheet" type="text/css" media="screen"/>
    <link href="../css/bootstrap-toggle.css" rel="stylesheet">
    <link href="../css/tooltipster.css" rel="stylesheet" type="text/css">
    <link href="../css/hologramas/inventario.css?15082018" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="../css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <script type="text/javascript">
     var id_s="<?php echo $d_s; ?>";
     var id_depto="<?php echo $_SESSION[$d_s]['dpto']; ?>";
     var usr_cargo="<?php echo $_SESSION[$d_s]['cargo']; ?>";
     var user="<?php echo $_SESSION[$d_s]['s_username'];?>";
    var clvuser="<?php echo $_SESSION[$d_s]['id_us'];?>";
    var nivel = "<?php echo $_SESSION[$d_s]['sec_lvl_1_2'];?>";
  var moduloAcceso=1;
  var seccionAcceso=2;

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
                    <h3 class="page-header" style="text-align:center;">Inventario de Hologramas</h3>
                </div>

                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                  <div class="col-lg-12" style="margin-top:20px;">
                    <div class="panel  panel-default" style="max-width:1200px !important; margin-left:auto; margin-right:auto;">
                        <!-- /.panel-heading -->
                        <div class="panel-body" style="background-color:#F6F6F6" id="pnlInventarioMain">
                              <!-- Nav tabs -->
                              <ul class="nav nav-tabs" role="tablist">
                                <li role="p" class="active"><a href="#tabExs" aria-controls="tabExs" role="tab" data-toggle="tab"><i class="fa fa-sort-numeric-asc" aria-hidden="true"></i>&nbsp;<?php echo $title_1?></a></li>
                <?php if($_SESSION[$d_s]['cargo']==14||$_SESSION[$d_s]['cargo']==13||$_SESSION[$d_s]['cargo']==12){?>
                                <li role="presentation" style="display:none;"><a href="#tabReq" aria-controls="tabReq" role="tab" data-toggle="tab"><i class="fa fa-shopping-basket" aria-hidden="true"></i>&nbsp;Requisición</a></li>
                                <?php }
                                if($_SESSION[$d_s]['cargo']==14||$_SESSION[$d_s]['cargo']==20||$_SESSION[$d_s]['cargo']==7||$_SESSION[$d_s]['cargo']==13||$_SESSION[$d_s]['cargo']==28 ||$_SESSION[$d_s]['cargo']==12){?>
                                <li role="presentation"><a href="#tabSolicitados" aria-controls="tabSolicitados" role="tab" data-toggle="tab"><i class="fa fa-hourglass-half" aria-hidden="true"></i>&nbsp;Solicitados al Proveedor</a></li>
                                <li role="presentation"><a href="#tabOnline" aria-controls="tabOnline" role="tab" data-toggle="tab"><i class="fa fa-cloud-download" aria-hidden="true"></i>&nbsp;Pedidos Online</a></li>
                                <?php }?>
                              </ul>
                              <br>
                             <!-- Tab panes -->
                              <div class="tab-content">
                              <div role="tabpanel" class="tab-pane active" id="tabExs"><!-- /.tab-pane ENTRADAS/EXISTENCIAS -->
                                 <!-- ////////////////////////.tab-pane ENTRADAS/EXISTENCIAS///////////////////////////// -->
                                 <div id="entradas" style="display:flex;">
                                    <div class="col-lg-8">
                                          <div id="opt_invent"><!-- Inicia Opciones -->
                                            <form class="form-horizontal" id="formOpt" role="form" action='' method='post'>
                                              <div class="form-group">
                                               <label for="formOpt" class="col-lg-7 control-label">Tipo Hologramas:</label>
                                                <div class="col-lg-5">
                                                  <span id="demo-events"><input type="checkbox" id="tipo_hol" checked data-toggle="toggle" data-size="normal" data-on="Personalizados" data-off="Genericos" data-onstyle="success" data-offstyle="info" onChange="tipo_holograma()"></span>
                                                </div>
                                              </div>
                                            </form>
                                          </div><!-- Termina Opciones -->
                                          <div id="datos_entrega"><!-- Contenido Inventario-->
                                              <form class="form-horizontal" id="formEntrada" name="formEntrada" target="_blank"  role="form" action='' method='post'>
                                               <input type="hidden" name='usr' id='usr' value='<?php echo $_SESSION[$d_s]['s_username'];?>'/>
                                               <input type="hidden" name='cargo' id='cargo' value='<?php echo $_SESSION[$d_s]['cargo'];?>'/>
                                                <input type="hidden" name='nivel_inventario' id='nivel_inventario' value='<?php echo $_SESSION[$d_s]['sec_lvl_1_2'];?>'/>
                                                  <div class="form-group" id='txt_cliente'>
                                                    <label for="formEntrada" class="col-lg-7 control-label">No. de Control:</label>
                                                    <div class="col-lg-5">
                                                    <input type='text' name='cliente' id='cliente' value='' class='form-control txt-short auto'>
                                                    </div>
                                                  </div>
                                              <?php 
                                                if($_SESSION[$d_s]['cargo']==13 && $_SESSION[$d_s]['sec_lvl_1_2']==1){
                                              	//if($_SESSION[$d_s]['cargo']==13 ) {
                                              	?>
                                              <div id="h_per"><!--Hologramas Personalizados -->
                                                <div class="form-group">

                                                    <label for="formEntrada" class="col-lg-7 control-label">Marca:</label>
                                                    <div class="col-lg-5">
                                                      <div id="cbo_m" style="display:table-cell">-------</div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="formEntrada" class="col-lg-7 control-label">Serie:</label>
                                                    <div class="col-lg-5">
                                                    <input type='text' name='serie' id='serie' value='' class='form-control txt-short auto' >
                                                    </div>
                                                </div>
                                              </div><!--Fin Hologramas Personalizados -->
                                              <!--Existencias-->
                                              <div id="d_existe" class="form-group" style="display:none">
                                                <label for="formEntrada" class="col-lg-7 control-label">Existencias:</label>
                                                <div class="col-lg-5">
                                                <input type='text' name='existencias' id='existencias' value='' class='no-border'>
                                                </div>
                                                <label for="formEntrada" class="col-lg-7 control-label">Del Folio:</label>
                                                <div class="col-lg-5">
                                                <input type='text' name='folExt' id='folExt' value='' class='no-border'>
                                                </div>
                                                <label for="formEntrada" class="col-lg-7 control-label">Hasta:</label>
                                                <div class="col-lg-5">
                                                <input type='text' name='folExt2' id='folExt2' value='' class='no-border'>
                                                </div>
                                              </div>
                                              <!--Fin Existencias-->
                                              <div class="form-group">
                                                <label for="formEntrada" class="col-lg-7 control-label">Entrada:</label>
                                                <div class="col-lg-5">
                                                <input type='text' name='entrada' id='entrada' value='' class='form-control txt-short'>
                                                </div>
                                              </div>
                                              <div class="form-group">
                                                <label for="formEntrada" class="col-lg-7 control-label">Fol Inicial:</label>
                                                <div class="col-lg-5">
                                                <input type='text' name='fi_entrada' id='fi_entrada' value='' class='form-control text-folio' readonly>
                                                </div>
                                              </div>
                                              <div class="form-group">
                                                <label for="formEntrada" class="col-lg-7 control-label">Fol Final:</label>
                                                <div class="col-lg-5">
                                                <input type='text' name='ff_entrada' id='ff_entrada' value='' class='form-control text-folio' readonly>
                                                </div>
                                              </div>
                                              <div class="form-group">
                                                <label for="formEntrada" class="col-lg-7 control-label"></label>
                                                <div class="col-lg-5">
                                                  <div id="addEntrada" style="display:none;"><input type='button' class="btn btn-success" name='btnAddInv' id='btnAddInv' value='Guardar Entrada' onClick="addEntrada()" class='auto'></div>
                                                </div>
                                              </div>
                                              <div class="form-group">
                                                <div class="col-lg-10">
                                                  <div id="msjs" style="display:block; color:#900; font-size:16px;"></div>
                                                </div>
                                              </div>
                                              <?php } else {?>
                                               <div id="d_existe" class="form-group" style="display:none">
                                                  <label for="formEntrada" class="col-lg-7 control-label">Actual:</label>
                                                  <div class="col-lg-5">
                                                  <input type='text' name='existencias' id='existencias' value='' class='no-border'>
                                                  </div>
                                                  <label for="formEntrada" class="col-lg-7 control-label">Del Folio:</label>
                                                  <div class="col-lg-5">
                                                  <input type='text' name='folExt' id='folExt' value='' class='no-border'>
                                                  </div>
                                                  <label for="formEntrada" class="col-lg-7 control-label">Hasta:</label>
                                                  <div class="col-lg-5">
                                                  <input type='text' name='folExt2' id='folExt2' value='' class='no-border'>
                                                  </div>
                                               </div>
                                             <div class="form-group" >
                                                <div class="col-lg-4"  style="padding-left:5px; padding-right:5px;">
                                                    <div id="tot_existe" class="" style="display:none; font-size:12px;">
                                                    </div>
                                                </div>
                                              </div>
                                              <?php }?>
                                              </form>
                                          </div><!-- Fin Contenido Inventario-->
                                    </div>
                                         
                                    	                                     
                                 </div>
                                 <div class="col-lg-4">
                                       <div id="toggle_vista" class="toggler" style="width:100%; display:none;">
                                             <button type="button" name='btnVerLista' id='btnVerLista' class="btn btn-info" onClick="runEffect()" >
                                                <span id="btn_ver" class="glyphicon glyphicon-chevron-down"></span>&nbsp;Ver Existencias
                                             </button>
                                             <br><br>
                                             <div class="col-lg-6">
                                         		<div id="lista_exist" class="" style="display:none; font-size:12px;"></div>
                                         	</div>
                                       </div>
                                </div><!-- colo-lg-4 -->
                              </div><!-- /.tab Entradas Existencias -->
                              <?php if($_SESSION[$d_s]['cargo']==14||$_SESSION[$d_s]['cargo']==13){?><!-- Si el usuario tiene permisos mostrar la opcion de requisicion -->
                              <!--///////////////////////////////////////////////////////REQUISICION/////////////////////////////////////////-->
                              <div role="tabpanel" class="tab-pane" id="tabReq"><!-- /.tab-pane Requisicion-->
                                    <div id="requisicion" class="container" style="width:100% !important; margin:0;">  <!--Inicio Requisición-->
                                        <div class="col-lg-12" style="float:none">
                                          <div class="col-lg-8"><!--Cuerpo Requisición-->
                                              <div id="opt_invent"><!-- Inicia Opciones Req -->
                                                  <form class="form-horizontal" id="formReq" name="formReq" target="_blank"  role="form" action='' method='post'>
                                                  <div class='col-lg-12'>
                                                  <div class="form-group">
                                                      <label for="formOpt_req" class="col-lg-3 control-label">No Pedido:</label>
                                                      <div class="col-lg-3">
                                                          <input type='text' name='no_pedido' id='no_pedido' value='56' class='recibo no-border'/>
                                                      </div>
                                                      <label for="formOpt_req" class="col-lg-2 control-label" style="visibility:hidden">Tipo:</label>
                                                      <div class="col-lg-4" style="visibility:hidden;">
                                                          <span id="demo-events"><input type="checkbox" id="tipo_hol_req" checked data-toggle="toggle" data-size="normal" data-on="Personalizados" data-off="Genericos" data-onstyle="success" data-offstyle="info" onChange="tipo_holograma_req()"></span>
                                                      </div>
                                                  </div>
                                                    <input type="hidden" name='usr' id='usr' value='<?php echo $_SESSION[$d_s]['s_username'];?>'/>
                                                    <input type="hidden" name='cargo' id='cargo' value='<?php echo $_SESSION[$d_s]['cargo'];?>'/>
                                                    <div class="form-group" id='txt_cliente_req'>
                                                        <label for="formReq" class="col-lg-3 control-label">No. de Control:</label>
                                                        <div class="col-lg-9">
                                                            <input type='text' name='cliente_req' id='cliente_req' value='' class='form-control txt-short auto'>
                                                        </div>
                                                    </div>
                                                    <div id="h_per_req"><!--Hologramas Personalizados -->
                                                        <div class="form-group">
                                                            <label for="formReq" class="col-lg-3 control-label">Marca:</label>
                                                            <div class="col-lg-4">
                                                                <div id="cbo_m_req" style="display:table-cell">-------</div>
                                                            </div>
                                                            <label for="formReq" class="col-lg-1 control-label">Serie:</label>
                                                            <div class="col-lg-3">
                                                                <input type='text' name='serie_req' id='serie_req' value='' class='form-control txt-short auto' readonly/>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">

                                                        </div>
                                                    </div><!--Fin Hologramas Personalizados -->
                                                    </div>
                                                  <div class='col-lg-12'>
                                                    <div class="form-group">
                                                        <label for="formReq" class="col-lg-3 control-label">Estado:</label>
                                                        <div class="col-lg-3">
                                                            <select name="cbo_edos" id="cbo_edos" onChange="check_estado();" class="form-control" style="width:100% !important">
                                                              <option selected="selected" value="NS">SELECCIONAR</option>
                                                              <option value="OAXACA">OAXACA</option>
                                                              <option value="GUERRERO">GUERRERO</option>
                                                              <option value="DURANGO">DURANGO</option>
                                                              <option value="GUANAJUATO">GUANAJUATO</option>
                                                              <option value="MICHOACAN">MICHOACAN</option>
                                                              <option value="SAN LUIS POTOSI">SAN LUIS POTOSI</option>
                                                              <option value="ZACATECAS">ZACATECAS</option>
                                                              <option value="TAMAULIPAS">TAMAULIPAS</option>
                                                              <option value="PUEBLA">PUEBLA</option>
                                                            </select>
                                                        </div>
                                                       <label for="formReq" class="col-lg-2 control-label">Tipo:</label>
                                                        <div class="col-lg-4">
                                                           <select name="cbo_tipo" id="cbo_tipo" onChange="$('#requeridos').focus();" class="form-control" style="width:100% !important">
                                                            <option value="0" selected>SELECCIONAR</option>
                                                            <option value="2">M. Artesanal</option>
                                                            <option value="1">Mezcal</option>
                                                            <option value="3">M. Ancestral</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="formReq" class="col-lg-3 control-label">Cantidad:</label>
                                                        <div class="col-lg-3">
                                                            <input type='text' name='requeridos' id='requeridos' value='' class='form-control text-folio' readonly/>
                                                        </div>
                                                        <div class="col-lg-6">
                                                           &nbsp;
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="formReq" class="col-lg-3 control-label">Letras:</label>
                                                        <div class="col-lg-9">
                                                            <input type='text' name='c_letras' id='c_letras' value='' class='form-control txt-upper txt-vis' readonly/>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="formReq" class="col-lg-3 control-label">Fol Inicial:</label>
                                                        <div class="col-lg-3">
                                                            <input type='text' name='fi_req' id='fi_req' value='' class='form-control text-folio' readonly/>
                                                        </div>
                                                        <label for="formReq" class="col-lg-2 control-label">Fol Final:</label>
                                                        <div class="col-lg-4">
                                                            <input type='text' name='ff_req' id='ff_req' value='' class='form-control text-folio' readonly/>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="formReq" class="col-lg-3 control-label">Pago:</label>
                                                        <div class="col-lg-3">
                                                           <select name="cbo_pago" id="cbo_pago" onChange="muestra_boton();" class="form-control" style="width:100% !important">
                                                            <option selected="selected" value="NS">SELECCIONAR</option>
                                                            <option value="1">PAGADO</option>
                                                            <option value="0">PENDIENTE</option>
                                                           </select>
                                                        </div>
                                                        <label for="formReq" class="col-lg-2 control-label">Urgente:</label>
                                                        <div class="col-lg-4">
                                                           <span id="demo-events"><input type="checkbox" id="urge" checked data-toggle="toggle" data-size="normal" data-on="No" data-off="Si" data-onstyle="btnper1" data-offstyle="info" onChange="tipo_holograma()"></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group" style="margin-top:20px;">
                                                        <label for="formReq" class="col-lg-8 control-label">&nbsp;</label>
                                                        <div class="col-lg-4">
                                                            <div id="addReq" style="display:none;"><input type='button' class="btn btn-warning" name='btnAddInv' id='btnAddInv' value='Agregar a Requisición' onClick="addCarritoReq()" class='auto'></div>
                                                        </div>
                                                    </div>
                                                  </div>
                                                  </form>
                                              </div><!-- Fin Contenido Inventario-->
                                          </div><!--Fin Cuerpo Requisición-->
                                          <div class="col-lg-4">
                                             <div id="toggle_vista2" class="toggler" style="width:100%;">
                                                   <button type="button" name='btnVerLista_req' id='btnVerLista' class="btn btn-info" onClick="show_exists()" >
                                                      <span id="btn_ver_req" class="glyphicon glyphicon-chevron-down"></span>&nbsp;Ver Existencias
                                                   </button>
                                                  <div id="lista_exist_req" class="ui-widget-content ui-corner-all" style="margin-right:-3px; padding:0 !important; display:none; font-size:12px; background-color:#F4F4F4;">
                                                  <table class="table" id="tabla_ext" width="100%">
                                                    <thead>
                                                      <tr class="success">
                                                        <th width="40%" style="text-align:right !important;">Existencias:</th>
                                                        <th width="60%" id="existencias_req">&nbsp;</th>
                                                      </tr>
                                                    </thead>
                                                    <tbody>
                                                      <tr>
                                                        <td width="40%" align="right">Del Folio:</td>
                                                        <td width="60%" align="center" id="folExt_req">&nbsp;</td>
                                                      </tr>
                                                      <tr>
                                                        <td width="40%"  align="right">Hasta:</td>
                                                        <td width="60%" align="center" id="folExt2_req">&nbsp;</td>
                                                      </tr>

                                                    </tbody>
                                                  </table>
                                                   <table class="table" id="tabla_last_ep" width="100%">
                                                    <thead>
                                                      <tr class="success">
                                                        <th style="text-align:center !important;" id="msj_last" colspan="2">&nbsp</th>

                                                      </tr>
                                                    </thead>
                                                    <tbody>
                                                      <tr>
                                                        <td width="40%" align="right">Del Folio:</td>
                                                        <td width="60%" align="center" id="fol_last_i">&nbsp;</td>
                                                      </tr>
                                                      <tr>
                                                        <td width="40%"  align="right">Hasta:</td>
                                                        <td width="60%" align="center" id="fol_last_f">&nbsp;</td>
                                                      </tr>

                                                    </tbody>
                                                  </table>

                                                  </div>
                                             </div><!--toggle_vista2-->
                                          </div><!--col-lg-4-->
                                        </div><!--Fin Row-->
                                        <div class="clearfix"></div>
                                    </div><!--FIN Requisición-->
                                                 <!--CAJA DE DIALOGO PARA CONFIRMAR PAGO
                                  ////////////////////////////////////////////////////////////////////////////////
                                  -->
                                  <div id="dialog_pago" title="" style="display:none; width:400px;">
                                  <div style="max-width:450px;">
                                    <div class="col-sm-12" id="msj_confirm">
                                      Desea confirmar el pago de estos hologramas?
                                    </div>
                                   </div>
                                  </div>
                                  <!--FIN CAJA DE DIALOGO PARA CONFIRMAR PAGO
                                  ////////////////////////////////////////////////////////////////////////////////
                                  -->
                              </div><!-- /.tab-Requisicion -->
                               <?php
                }
                if($_SESSION[$d_s]['cargo']==14||$_SESSION[$d_s]['cargo']==7||$_SESSION[$d_s]['cargo']==20||$_SESSION[$d_s]['cargo']==13||$_SESSION[$d_s]['cargo']==28 ||$_SESSION[$d_s]['cargo']==12)
                {
                 ?>
                               <!--///////////////////////////////////////////////////////SOLICITADOS A PROVEEDOR/////////////////////////////////////////-->
                               <div role="tabpanel" class="tab-pane" id="tabSolicitados"><!-- /.tab-pane Solicitados-->
                                   <form class="form-horizontal" id="frm_search" name="frm_search" role="form" method='post'>
                                            <div class="form-group" id="search_by_noc">
                                                <label for="frm_search" class="col-md-2 control-label">No pedido:</label>
                                                <div class="col-md-2" style="padding-left:2px; margin-left:auto;">
                                                  <input type='text' name='by_pedido_list' id='by_pedido_espera' value='' class='form-control txt-short'>
                                                </div>
                                                <label for="frm_search" class="col-md-2 control-label">No. de Control:</label>
                                                <div class="col-md-2" style="padding-left:2px; margin-left:auto;">
                                                  <input type='text' name='by_asoc_list' id='by_asoc_espera' value='' class='form-control'>
                                                </div>
                                            </div>
                                            <div class="form-group" id="search_by_noc">
                                                <label for="frm_search" class="col-md-2 control-label">Marca:</label>
                                                <div class="col-md-4" style="padding-left:2px; margin-left:auto;">
                                                  <input type='text' name='by_marca' id='by_marca' value='' class='form-control'>
                                                </div>
                                                <div class="col-md-2" style="padding-left:2px; margin-left:auto;">
                                                  <button type="button"  name='btnReload_list' id='btnReload_list' class="btn btn-warning btn-md pull-right" style="margin-right:5px;" onClick="sinc_list()">
                                                        <span class="glyphicon glyphicon-refresh"></span> Actualizar
                                                  </button>&nbsp;
                                                </div>
                                            </div>
                                            <div class="form-group" id="search_by_status">
                                            <label for="frm_search" class="col-md-2 control-label">Estatus:</label>
                                            <div class="col-md-4" style="padding-left:2px; margin-left:auto;">
                                              <select class="form-control" id="sp_estatus" name="sp_estatus">
                                                <option value="0">Seleccionar</option>
                                                <option value="1">Solicitado</option>
                                                <option value="2">Recibido</option>
                                                <option value="3">Procesando</option>
                                                <option value="4">Impreso</option>
                                                <option value="5">Entregado</option>
                                                <option value="6">En Inventario</option>
                                            </select>
                                            </div>
                                            <div class="col-md-2" style="padding-left:2px; margin-left:auto;">
                                              <button type="button"  name='btnReload_online' id='btnReload_online' class="btn btn-info btn-md pull-right" style="margin-right:5px;" onClick="reiniciar_list()">
                                                  <span class=" glyphicon glyphicon-repeat"></span> Reiniciar
                                              </button>&nbsp;
                                            </div>
                                          </div>
                                             <div class="form-group" id="search_by_noc">
                                                <label for="frm_search" class="col-md-2 control-label">&nbsp;</label>
                                                <label for="frm_search" class="col-md-6 control-label" id="lbl_resp_list" style="text-align:left;"></label>
                                            </div>
                                    </form>
                                   <div id="grid_list" style="overflow:scroll; padding-bottom:15px;">
                                    <div style="margin-top:20px; width:auto !important;">
                                      <table id="jqGrid_list" style="font-size:12px !important; width:auto !important;"></table>
                                      <div id="jqGridPager_list" style="padding-bottom:5px;"></div>
                                    </div>
                                  </div>
                              </div><!-- /.tab-Solicitados -->
                              <?php
                }
                if($_SESSION[$d_s]['cargo']==14||$_SESSION[$d_s]['cargo']==20||$_SESSION[$d_s]['cargo']==7||$_SESSION[$d_s]['cargo']==13||$_SESSION[$d_s]['cargo']==28 ||$_SESSION[$d_s]['cargo']==12){
                ?>
                              <!--///////////////////////////////////////////////////////SOLICITADOS A PEDIDOS ONLINE/////////////////////////////////////////-->
                              <div role="tabpanel" class="tab-pane" id="tabOnline"><!-- /.tab-pane Pedidos Online-->
                                      <div id="filtros_grid" style="padding-bottom:15px;">
                                        <form class="form-horizontal" id="frm_search" name="frm_search" role="form" method='post'>
                                          <div class="form-group" id="search_by_noc">
                                            <label for="frm_search" class="col-md-2 control-label">No. Folio:</label>
                                            <div class="col-md-2" style="padding-left:2px; margin-left:auto;">
                                              <input type='text' name='by_pedido_online' id='by_pedido_online' value='' class='form-control txt-short'>
                                            </div>
                                            <label for="frm_search" class="col-md-2 control-label">No. de Control:</label>
                                            <div class="col-md-2" style="padding-left:2px; margin-left:auto;">
                                              <input type='text' name='by_asoc_online' id='by_asoc_online' value='' class='form-control'>
                                            </div>
                                          </div>
                                          <div class="form-group" id="search_by_noc">
                                            <label for="frm_search" class="col-md-2 control-label">Marca:</label>
                                            <div class="col-md-4" style="padding-left:2px; margin-left:auto;">
                                              <input type='text' name='by_marca_online' id='by_marca_online' value='' class='form-control'>
                                            </div>
                                            <div class="col-md-2" style="padding-left:2px; margin-left:auto;">
                                              <button type="button"  name='btnReload_online' id='btnReload_online' class="btn btn-warning btn-md pull-right" style="margin-right:5px;" onClick="reload_online()">
                                                  <span class="glyphicon glyphicon-refresh"></span> Actualizar
                                              </button>&nbsp;
                                            </div>
                                          </div>
                                          <div class="form-group" id="search_by_noc">
                                            <label for="frm_search" class="col-md-2 control-label">Estatus:</label>
                                            <div class="col-md-4" style="padding-left:2px; margin-left:auto;">
                                              <select class="form-control" id="by_estatus" name="by_estatus">
                                                <option value="0">Seleccionar</option>
                                                <option value="1">Revisión</option>
                                                <option value="2">Autorizado</option>
                                                <option value="3">En Lista</option>
                                                <option value="4">Solicitado a Proveedor</option>
                                                <option value="7">Cancelado</option>
                                            </select>
                                            </div>
                                            <div class="col-md-2" style="padding-left:2px; margin-left:auto;">
                                              <button type="button"  name='btnReload_online' id='btnReload_online' class="btn btn-info btn-md pull-right" style="margin-right:5px;" onClick="reiniciar_online()">
                                                  <span class=" glyphicon glyphicon-repeat"></span> Reiniciar
                                              </button>&nbsp;
                                            </div>
                                          </div>
                                        </form>
                                      </div><!-- filtros_grid -->



                                                <div style="padding-bottom:15px;"><!-- div para el carrito -->

                                                <div id="tabla_req" style="display:none; overflow:scroll; font-size:12px;">
                                                    <table id="tbl_carrito" class="table table-bordered table-striped" style="width:100%">
                                                    <!-- Cabecera de la tabla -->
                                                    <thead>
                                                    <tr style="background-color:#FFF !important; color:#114764; font-weight:bold; font-size:12px">
                                                    <th>No. de Control</th>
                                                    <th>Marca</th>
                                                    <th>Tipo</th>
                                                    <th>Estado</th>
                                                    <th>Folio Inicial</th>
                                                    <th>Folio Final</th>
                                                    <th>Cantidad</th>
                                                    <th>Pagado</th>
                                                    <th>Versión</th>
                                                    <th>Prioridad</th>
                                                    <th>&nbsp;</th>
                                                    </tr>
                                                    </thead>
                                                    <!-- Cuerpo de la tabla con los campos -->
                                                    <tbody>
                                                    </tbody>
                                                    </table>
                                                </div><!--fin tabla_req -->
                                                <div class="col-lg-12 col-xs-offset-4"><!--Botones carrito -->
                                                    <div id="btns_req" style="display:none;">
                                                        <button type="button"  name='btnTerminar' id='btnTerminar' class="btn btn-success" onClick="genReq()">
                                                        <span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Generar Requisición
                                                        </button>
                                                        <button type="button" name='btnEsc_Carrito' id='btnEsc_Carrito' class="btn btn-danger" onClick="conf_canc_req()" >
                                                        <span class="glyphicon glyphicon-remove-sign"></span>&nbsp;Cancelar
                                                        </button>
                                                    </div>
                                                    <div id="conf_esc_req" title="Cancelar Requisición?" style="display:none;">
                                                          <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;">
                                                          </span>La lista de la requisición se eliminara completamente, Desea continuar?
                                                          </p>
                                                    </div> <!--Fin conf_esc_reqo-->
                                                </div><!--Fin Botones carrito-->

                                            <div class="col-lg-12">
                                                <div id="variables"></div>
                                            </div>
                                        </div><!--Fin div para el carrito-->





                                      <div id="grid_list2" style="padding-bottom:15px;">
                                        <div style="margin-top:20px; width:auto !important;">
                                          <table id="jqGrid_online" style="font-size:12px !important; width:auto !important;"></table>
                                          <div id="jqGridPager_online" style="padding-bottom:5px;"></div>
                                        </div>
                                      </div><!-- grid_list -->

                                        <!-- Modal Agregar Prueba Mezcal -->
                                         <div class="modal fade" id="modalEditPO" tabindex="-1" role="dialog" aria-labelledby="titulo" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                      <h4 class="modal-title" id="title_frm" style="text-align:center; font-size:18px; font-weight:bold;">Editar Detalle de Pedido</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                      <div class="container-fluid" id="modalAddBody">
                                                          <form class="form-horizontal" id="frmEditPO" name="frmEditPO" target="_blank"  role="form" action='' method='post'>
                                                              <input type="hidden" name='txtIdEditarPO' id='txtIdEditarPO' value=''/>
                                                              <div class='col-lg-12'>
                                                                <div class="form-group" id='div_socio'>
                                                                   <label for="formReq" class="col-lg-4 control-label">No. de Control:</label>
                                                                   <div class="col-lg-7">
                                                                     <h3 id="noCtePO" class="lbl-texto importe"></h3>
                                                                   </div>
                                                                </div>
                                                                <div class="form-group" id='txt_cliente_req'>
                                                                   <label for="formReq" class="col-lg-4 control-label">Marca:</label>
                                                                    <div class="col-lg-7">
                                                                        <h3 id="marcaPO" class="lbl-texto importe"></h3>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group" id='txt_cliente_req'>
                                                                   <label for="formReq" class="col-lg-4 control-label">Tipo:</label>
                                                                    <div class="col-lg-7">
                                                                      <select name="cbo_tipoEPO" id="cbo_tipoEPO" onChange="$('#cbo_edos').focus();" class="form-control" style="width:100% !important">
                                                                      <option value="0" selected>SELECCIONAR</option>
                                                                      <option value="2">M. Artesanal</option>
                                                                      <option value="1">Mezcal</option>
                                                                      <option value="3">M. Ancestral</option>
                                                                      </select>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group" id='txt_cliente_req'>
                                                                   <label for="formReq" class="col-lg-4 control-label">Estado:</label>
                                                                    <div class="col-lg-7">
                                                                        <select name="cbo_edosEPO" id="cbo_edosEPO" onChange="" class="form-control" style="width:100% !important">
                                                                        <option selected="selected" value="0">SELECCIONAR</option>
                                                                        <option value="OAXACA">OAXACA</option>
                                                                        <option value="GUERRERO">GUERRERO</option>
                                                                        <option value="DURANGO">DURANGO</option>
                                                                        <option value="GUANAJUATO">GUANAJUATO</option>
                                                                        <option value="MICHOACAN">MICHOACAN</option>
                                                                        <option value="SAN LUIS POTOSI">SAN LUIS POTOSI</option>
                                                                        <option value="ZACATECAS">ZACATECAS</option>
                                                                        <option value="TAMAULIPAS">TAMAULIPAS</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group" id='txt_cliente_req'>
                                                                   <label for="formReq" class="col-lg-4 control-label">Cantidad:</label>
                                                                    <div class="col-lg-7">
                                                                        <h3 id="cantidadPO" class="lbl-texto importe"></h3>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group" id='txt_cliente_req'>
                                                                   <label for="formReq" class="col-lg-4 control-label">Observaciones:</label>
                                                                    <div class="col-lg-7">
                                                                         <textarea class="form-control" rows="3" id="txtObsPO" name="txtObsPO"></textarea>
                                                                    </div>
                                                                </div>
                                                              </div><!-- End col-lg-12 -->
                                                           </form>
                                                      </div>
                                                    </div>
                                                   <div class="modal-footer">
                                                      <button type="button" id="btnGuardar" class="btn btn-success" onClick="$('#frmEditPO').submit();">Guardar</button> &nbsp;&nbsp;<button type="button" id="btnCancelPO" class="btn btn-danger" onClick="$('#modalEditPO').modal('hide');">Cancelar</button>
                                                   </div>
                                                </div>
                                            </div>
                                          </div> <!-- End Modal Agregar Prueba Mezcal -->

                                      <div id="dialog_pago_online" title="" style="display:none; width:400px;">
                                        <div style="max-width:450px;">
                                          <div class="col-sm-12" id="msj_confirm">
                                            <i class="fa fa-2x fa-exclamation-triangle" aria-hidden="true" style="color:#C7861B; vertical-align:middle"></i>&nbsp;&nbsp;Desea confirmar el pago de estos hologramas?
                                          </div>
                                          <br>
                                          <!--<div class="form-group" id="modif_tipop" style="display:none; width:400px;">
                                            <label for="frm_search" class="col-md-6 control-label">Prioridad:</label>
                                            <div class="col-md-6" style="padding-left:2px; margin-left:auto;">
                                              <select class="form-control" id="prioridad_opcion" name="pago_opcion">
                                                <option value="" selected disabled>Seleccionar</option>
                                                <option value="0">Normal</option>
                                                <option value="1">Urgente</option>
                                              </select>
                                            </div>
                                          </div>
                                          <br>-->
                                          <div class="form-group" id="modif_tipop" style="display:none; width:400px;">
                                            <label for="frm_search" class="col-md-6 control-label">Tipo de Pago:</label>
                                            <div class="col-md-6" style="padding-left:2px; margin-left:auto;">
                                              <select class="form-control" id="pago_opcion" name="pago_opcion">
                                                <option value="0" selected disabled>Seleccionar</option>
                                                <option value="1">Normal</option>
                                                <option value="2">Paquete Emprendedor</option>
                                                <option value="3">Cargo a Estado de Cuenta</option>
                                                <option value="4">Sefader</option>
                                                <option value="5">Autorizado por UT</option>
                                              </select>
                                            </div>
                                          </div>
                                          <br>
                                          <div class="form-group" id="search_by_status">
                                            <label for="frm_search" class="col-md-6 control-label">Opción de pago:</label>
                                            <div class="col-md-6" style="padding-left:2px; margin-left:auto;">
                                              <select class="form-control" id="pago_opcion" name="pago_opcion">
                                                <option value="0" selected disabled>Seleccionar</option>
                                                <option value="1">Normal</option>
                                                <option value="2">Paquete Emprendedor</option>
                                                <option value="3">Cargo a Estado de Cuenta</option>
                                                <option value="4">Sefader</option>
                                                <option value="5">Autorizado por UT</option>
                                              </select>
                                            </div>
                                          </div>
                                          <div class="form-group" id="upload_file" style="display:none; width:400px;">
                                            <label for="frm_search" class="col-md-12 control-label">Adjuntar comprobante:</label>
                                            <input type="file" name="input-id" id="input-id">
                                          </div>
                                        </div><!-- Fin Tab 4 -->
                                        <div id="dialog_cancelar_solicitud" title="" style="display:none; width:400px;">
                                          <div style="max-width:450px;">
                                            <div class="col-sm-12" id="">
                                              <i class="fa fa-2x fa-exclamation-triangle" aria-hidden="true" style="color:#C7861B; vertical-align:middle"></i>&nbsp;&nbsp;Desea cancelar la solicitud de hologramas?
                                            </div>
                                          </div>
                                        </div><!-- Fin Tab 4 -->
                                      </div><!-- Fin Tab 4 -->

                                      <style>
                                        :root {
                                                  --primary-color: #2c5aa0;
                                                  --warning-color: #C7861B;
                                                  --light-bg: #f8f9fa;
                                                  --border-color: #dee2e6;
                                              }
                                              
                                              .dialog-container {
                                                  max-width: 500px;
                                                  margin: 0 auto;
                                                  
                                              }
                                              
                                              .dialog-header {
                                                  display: flex;
                                                  align-items: center;
                                                  margin-bottom: 20px;
                                                  padding-bottom: 15px;
                                                  border-bottom: 1px solid var(--border-color);
                                              }
                                              
                                              .dialog-icon {
                                                  color: var(--warning-color);
                                                  margin-right: 12px;
                                                  font-size: 1.8rem;
                                              }
                                              
                                              .dialog-title {
                                                  font-weight: 600;
                                                  color: #333;
                                                  margin: 0;
                                              }
                                              
                                              .form-section {
                                                  margin-bottom: 10px;
                                                  padding: 10px;
                                                  background-color: var(--light-bg);
                                                  border-radius: 8px;
                                              }
                                              
                                              .form-section-title {
                                                  font-weight: 600;
                                                  margin-bottom: 10px;
                                                  color: var(--primary-color);
                                                  font-size: 1.1rem;
                                              }
                                              
                                              .file-upload-container {
                                                  margin-top: 10px;
                                              }
                                              
                                              .file-upload-label {
                                                  display: block;
                                                  margin-bottom: 8px;
                                                  font-weight: 500;
                                              }
                                              
                                              .file-input-wrapper {
                                                  position: relative;
                                                  overflow: hidden;
                                                  display: inline-block;
                                                  width: 100%;
                                              }
                                              
                                              .file-input-wrapper input[type=file] {
                                                  position: absolute;
                                                  left: 0;
                                                  top: 0;
                                                  opacity: 0;
                                                  width: 100%;
                                                  height: 100%;
                                                  cursor: pointer;
                                              }
                                              
                                              .file-input-button {
                                                  display: inline-block;
                                                  padding: 8px 16px;
                                                  background-color: #e9ecef;
                                                  border: 1px solid var(--border-color);
                                                  border-radius: 4px;
                                                  cursor: pointer;
                                                  width: 100%;
                                                  text-align: center;
                                              }
                                              
                                              .file-name {
                                                  margin-top: 8px;
                                                  font-size: 0.9rem;
                                                  color: #666;
                                              }
                                              
                                              .dialog-actions {
                                                  display: flex;
                                                  justify-content: flex-end;
                                                  gap: 10px;
                                                  margin-top: 10px;
                                                  padding-top: 10px;
                                                  border-top: 1px solid var(--border-color);
                                              }
                                              
                                              #btn-cancel {
                                                  background-color: #6c757d !important;
                                                  color: white !important;
                                                  border: none !important;
                                              }
                                              
                                              #btn-confirm {
                                                  background-color: var(--primary-color) !important;
                                                  color: white !important;
                                                  border: none !important;
                                              }
                                              
                                              #btn-cancel:hover, #btn-confirm:hover {
                                                  opacity: 0.9;
                                              }
                                              
                                              .hidden {
                                                  display: none !important;
                                              }
                                      </style>

                                      <div id="dialog_pago_online_otro" class="dialog-container" style="display:none; width:400px;">
                                        
                                        <!--<div class="form-section">
                                            <h3 class="form-section-title">Prioridad</h3>
                                            <div class="mb-2">
                                                <select class="form-select" id="prioridad_opcion" name="prioridad_opcion">
                                                    <option value="" selected disabled>Seleccionar</option>
                                                    <option value="0">Normal</option>
                                                    <option value="1">Urgente</option>
                                                </select>
                                            </div>
                                        </div>-->

                                        <div class="form-section">
                                            <h3 class="form-section-title">Forma de pago</h3>
                                            <div class="mb-2">
                                                <select class="form-select" id="tipo_pago_otro" name="tipo_pago_otro">
                                                    <option value="" selected disabled>Seleccionar forma de pago</option>
                                                    <option value="1">Pago en efectivo</option>
                                                    <option value="2">Transferencia Bancaria</option>
                                                    <option value="3">Cheque</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="form-section">
                                            <h3 class="form-section-title">Tipo de pago:</h3>
                                            <div class="mb-2">
                                                <select class="form-select" id="pago_opcion_otro" name="pago_opcion_otro">
                                                    <option value="" selected disabled>Seleccionar tipo de pago</option>
                                                    <option value="1">Pago Normal</option>
                                                    <option value="2">Paquete Emprendedor</option>
                                                    <option value="3">Cargo a Estado de Cuenta</option>
                                                    <option value="4">Sefader</option>
                                                    <option value="5">Autorizado por UT</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div id="comprobante_section_otro" class="form-section hidden">
                                            <h3 class="form-section-title">Comprobante de pago</h3>
                                            <div class="file-upload-container">
                                                <!--<label class="file-upload-label">Adjuntar comprobante:</label>
                                                <div class="file-input-wrapper">
                                                    <div class="file-input-button">
                                                        <i class="fas fa-cloud-upload-alt me-2"></i>Seleccionar archivo
                                                    </div>-->
                                                    <input type="file" id="input-file-otro" name="comprobante">
                                                <!--</div>-->
                                                <div id="file-name" class="file-name"></div>
                                            </div>
                                        </div>

                                      </div>

                               <?php }?>
                          </div><!-- /.tab-panes -->
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
      <div class="modal fade bs-example-modal-sm" id="mdlObservaciones" tabindex="-1" role="dialog" aria-hidden="true" >
        <div class="modal-dialog modal-sm">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" >×</button>
                <span class="glyphicon glyphicon-list-alt"> </span> Observaciones!!

               </h4>
            </div>
            <div class="modal-body">
              <p id="mdlObservacionesPedido" style="padding-left:5px !important;"></p>
            </div>
          </div>
        </div>
      </div>
      <!--Modal observaciones-->


      <!--Modal observaciones-->
      <div class="modal fade bs-example-modal-sm" id="mdlObsPedido" tabindex="-1" role="dialog" aria-hidden="true" >
        <div class="modal-dialog modal-sm">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" >×</button>
                <span class="glyphicon glyphicon-list-alt"> </span> Observaciones!!

               </h4>
            </div>
            <div class="modal-body">
              <p id="mdlBodyObservacionesP" style="padding-left:5px !important;"></p>
            </div>
          </div>
        </div>
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
          <script src="../js/bootstrap-toggle.js"></script>
          <!-- Custom Theme JavaScript -->
          <script src="../js/sb-admin-2.js"></script>

          <!-- Validaddor de formularios -->
          <script src="../js/validate/jquery.validate.min.js"></script>
        <script src="../js/validate/additional-methods.js"></script>
          <script src="../js/validate/localization/messages_es.js" type="text/javascript"></script>
          <script src="../js/validate/jquery.tooltipster.js"></script>
          <!-- Custom Theme JavaScript -->
          <script src="../js/jquery-ui.min.js"></script>
          <script src="../js/jquery.jqGrid.min.js" type="text/javascript"></script>
          <script src="../js/i18n/grid.locale-es.js" type="text/javascript"></script>
           <!-- FUNCIONES DEL INVENTARIO -->
          <script src="js/inventario/pedidos_online.js?<?php echo uniqid(); ?>"></script>
          <script src="js/inventario/entradas.js?2"></script>
          <script src="js/inventario/comun.js"></script>

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

        <?php if($_SESSION[$d_s]['cargo']==14 || $_SESSION[$d_s]['cargo']==13 || $_SESSION[$d_s]['cargo']==12 || $_SESSION[$d_s]['id_us']==1){?>
         <script src="js/inventario/requisicion.js?16"></script>
        <?php }
        if($_SESSION[$d_s]['cargo']==14||$_SESSION[$d_s]['cargo']==7||$_SESSION[$d_s]['cargo']==20||$_SESSION[$d_s]['cargo']==13||$_SESSION[$d_s]['cargo']==28 ||$_SESSION[$d_s]['cargo']==12 || $_SESSION[$d_s]['id_us']==1){?>
         <script src="js/inventario/listado.js?3"></script>
        <?php }?>


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
