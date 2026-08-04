<?php
  session_start();
  session_set_cookie_params(0, "/", $_SERVER["HTTP_HOST"], 0);
  require_once('../common/cfg_server.php');
  $d_s=$_GET['d_s'];
  if(isset($_SESSION[$d_s]) && $_SESSION[$d_s]["seccion_4_1"] == "logged")
  {
  require_once "comboeml/funciones.php";
  require_once("bus_especie.php");
  require_once "idparajea.php";
  //require_once("sumaranio.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIGCE</title>
  <link rel="icon" type="image/ico" href="../favicon.ico" />
  <link href="css/bootstrap.css" rel="stylesheet">
  <link href="css/metisMenu.min.css" rel="stylesheet">
  <link href="css/timeline.css" rel="stylesheet">
  <link href="css/sb-admin-2.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/e21e754a91.js"></script>
  <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="css/bootstrap-toggle.css">
  <link type="text/css" href="css/demo_table.css" rel="stylesheet">
  <link href="calendario/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
  <link rel="stylesheet" href="css/animate.css">
  <link rel="stylesheet" href="css/templatemo_misc.css">
  <link href="smoothness/jquery-ui.css" rel="stylesheet" type="text/css">
  <link href="css/estilo.css" rel="stylesheet" type="text/css">

 <!-- <script src="js/plugins.js"></script>
  <script src="js/main.js"></script>
  <script type="text/javascript" src="js/jquery.js"></script>
  <script src="js/vendor/modernizr-2.6.1-respond-1.1.0.min.js"></script>
  <script language="javascript" type="text/javascript" src="js/jquery.validate.1.5.2.js"></script><!-- carrito -->
  <script language="javascript" type="text/javascript" src="js/script.js"></script> <!-- boton agregar tab1 -->
  <script type="text/javascript" src="js/magueys.js?16"></script> <!-- boton agregar tab2 -->
  <!-- <script type="text/javascript" language="javascript" src="js/funciones.js"></script>
  <script type="text/javascript" language="javascript" src="js/funciones2.js"></script>-->
  <!--<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>-->
  <!--<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
  <script src="https://maps.google.com/maps/api/js?sensor=false&amp;v=3"></script>-->
  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAa7gTxPscQdpSsdmInTkrJ0uk-JPJ1As8&amp;v=3"></script>
  <!--<script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.0.min.js"><\/script>')</script>-->

  <script type="text/javascript" src="js/jquery.min.js"></script>
  <script type="text/javascript" src="js/jquery.cookie.js"></script>
  <script type="text/javascript" src="js/bootstrap.min.js"></script>
  <script type="text/javascript" src="js/jquery-ui.min.js"></script>
  <script type="text/javascript" src="js/sb-admin-2.js"></script>
  <script type="text/javascript" src="calendario/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
  <script type="text/javascript" src="calendario/js/locales/bootstrap-datetimepicker.es.js" charset="UTF-8"></script>
  <script type="text/javascript" src="js/bootstrap-toggle.js"></script>
  <script src="plugin/loadingoverlay.min.js"></script>

  <!-- sweet alert2 -->
  <script src="../registros_oc/etiquetas/plugins/sweetalert/sweetalert-dev.js"></script>
  <link rel="stylesheet" href="../registros_oc/etiquetas/plugins/sweetalert/sweetalert.css">
  <script src="js/jquery.togglebutton.min.js"></script>

  <!-- bootstrap table -->
  <link href="../registros_oc/etiquetas/plugins/bootstrap-table-master/src/bootstrap-table.css" rel="stylesheet">
  <script src="../registros_oc/etiquetas/plugins/bootstrap-table-master/src/bootstrap-table.js"></script>
  <script src="../registros_oc/etiquetas/plugins/bootstrap-table-master/src/locale/bootstrap-table-es-MX.js"></script>

  <script type="text/javascript">
      var id_s="<?php echo $d_s; ?>";
      var id_depto="<?php echo $_SESSION[$d_s]['dpto']; ?>";
      var usr_cargo="<?php echo $_SESSION[$d_s]['cargo']; ?>";
      var user="<?php echo $_SESSION[$d_s]['s_username'];?>";
      var clvuser="<?php echo $_SESSION[$d_s]['id_us'];?>";

      var moduloAcceso=4;
      var seccionAcceso=1;
    </script>

  <script>
    $(function() {
      $('#referencia1').change(function() {
         var referencia = ($("#referencia1").prop('checked')==false)?1:0;
        if (referencia==1){
          $("#formularioreferencia").css("display", "none");
        }else{
          $("#formularioreferencia").css("display", "block");
        }
      });

      $('#cboxMCR').change(function() {
        var registro = $("#cboxMCR").val();
        if (registro == "2")
          $("#seccDocumental").css("display", "block");
        else
          $("#seccDocumental").css("display", "none");
      });

    });
  </script>

  <script type="text/javascript">
    $(document).ready(function() {
      $("#cancelar").click(function() {
        $("form")[0].reset();
      });

  $("#btn_nueva_localidad").hide();

  $("#btn_nueva_localidad").click(function() {
    $("#ModalRutaNueva").modal("show");
  });

  loadPredios();

  $("#BtnNuevaLocalidad").click(function() {
  if ( $('#municipio').val()!="" )
  {
     if ( $('#TxtNuevaLocalidad').val()!="" ){
     var localidad = $('#TxtNuevaLocalidad').val();
     var id_municipio = $('#municipio').val();
           var parametros = {
           "localidad":localidad,
           "id_municipio" : id_municipio
                            };
            $.ajax({
                data:  parametros,
                url:   'libs/nuevalocalidad.php',
                type:  'post',
                 });
        alert("Localidad guardada");
        $('#TxtNuevaLocalidad').val("");
        $("#ModalRutaNueva").modal("hide");
        buscarLocalidades();
        }
       else{
        alert("No ha escrito una localidad");
        }
    }
    else
    {
    alert("No ha seleccionado un minicipio");
    }
    return false;

  });


    $('#cboxGuia').bootstrapToggle('off');
    $('#cboxGuiaM').bootstrapToggle('off');
    $('#cboxMSP').bootstrapToggle('off');
    $('#divMS').hide();
    $('#divMP').hide();

    $('#divMSP').hide();
    $('#divPoligono').hide();

    $('#cboxMSP').on('change', function() {
      if($(this).is(":checked")){
                $('#divMS').show();
                $('#divMP').show();
      }
      else{
                $('#divMS').hide();
                $('#divMP').hide();
      }
    });

    });
  </script>



  <script type="text/javascript">
    function idinsertar (){
      $consultaid="SELECT max(id_paraje)+1 FROM `paraje` WHERE 1";
    }
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
                <li><a href="../acceso/cerrar.php?d_s=<?php echo $d_s?>"><i class="fa fa-sign-out fa-fw"></i> Salir</a></li>
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
            <ul class="nav" id="sidebar"> <?php echo $_SESSION[$d_s]['links'];?> </ul>
          </div>
        <!-- /.sidebar-collapse -->
        </div>
        <!-- /.navbar-static-side -->
      </nav>
    <div id="page-wrapper">
        <div class="row panel-pincipal">
          <div class="col-lg-12" align="center">
            <h3 class="page-header text-white">REGISTRO DE PREDIO</h3>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12" style="margin-top:20px;">
            <div class="panel  panel-che" style="max-width:1100px; margin-left: auto !important; margin-right:auto !important; margin-bottom:75px;">
              <div class="panel-heading"></div>
              <div class="panel-body">
                <ul class="nav nav-tabs" role="tablist">
                  <li role="user-data" class="active"><a href="#tab0success" aria-controls="changePassword" role="tab" data-toggle="tab"><span class="fa fa-list-alt"></span> Listado de Predios</a> </li>
                  <li role="user-data" class=""><a href="#tab1success" aria-controls="changePassword" role="tab" data-toggle="tab"><span class="fa fa-list-alt"></span> Registro de Predio</a></li>
                  <!--<li role="user-data" class=""><a href="#tab2success" aria-controls="changePassword" role="tab" data-toggle="tab"><i class="fa fa-pagelines"></i> Entrada de Maguey</a></li>
                  <li role="user-data" class=""><a href="#tab3success" aria-controls="changePassword" role="tab" data-toggle="tab"><i class="fas fa-spa"></i> Atributos de Sustentabilidad</a></li>
                --></ul>
                <div class="tab-content">

				  <!-- TAB0 -->
                  <div role="tabpanel" class="tab-pane active" id="tab0success">
                    <div style="margin-left:auto !important; margin-right:auto !important;">
                      <div class="panel  panel-default" style="margin-bottom:0 !important; margin-top:5px !important;">
                        <div class="panel-heading" style="text-align:center; font-size:16px; font-weight:bold;">Listado de Predios</div>
                        <div class="panel-body">
                          <form class="form-horizontal" id="listamaguey" action="" method="POST" name="listamaguey" enctype="multipart/form-data" >
                            <div class="form-group">
                               <input class="form-control" type="hidden" name='usr' id='usr' value='<?php echo $_SESSION[$d_s]['s_username']; ?>'/>
                            </div>
                            <!--
                            <fieldset>
                              <legend align="">Datos del Productor</legend>
                            </fieldset>
                            <div class="form-group">
                              <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">No.Predio:</label>
                              <div class="col-md-3 col-sm-3 col-xs-3">
                                 <input type="text" name="id" id="id" disabled="disabled" class='form-control txt-short'  value="<?php echo $id; ?>">
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">No. Asociado:</label>
                              <div class="col-md-3 col-sm-3 col-xs-3">
                                <input type="text" id="state"  name="state" class='form-control txt-short' />
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">Nombre Asociado:</label>
                              <div class="col-md-4 col-sm-4 col-xs-4">
                                 <input readonly type="text" id="abbrev" name="abbrev" class='form-control'/>
                              </div>
                            </div>-->
							<div class="form-group">
							<div class="col-md-10 col-sm-12 col-xs-12">&nbsp;</div>
							<div class="col-md-2 col-sm-12 col-xs-12">
								<?php if ($_SESSION[$d_s]['id_us'] == 1 || $_SESSION[$d_s]['id_us'] == 22 || $_SESSION[$d_s]['id_us'] == 34) { ?>
								<button type="button" id="btnModificarSol" onclick="ImportarPredio();" class="btn btn-primary" style="font-size:11px; width:130px;border-radius:5px;">IMPORTAR PREDIO</button>
								<?php } ?>
							</div>
							</div>
                            <div class="form-group">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <table id="tablaPredios" data-row-style="rowStyleI" data-height="560">
                                    </table>
                                </div>
                            </div>

                          </form>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div role="tabpanel" class="tab-pane" id="tab1success">
                    <div style="margin-left:auto !important; margin-right:auto !important;">
                      <div class="panel  panel-default" style="margin-bottom:0 !important; margin-top:5px !important;">
                        <div class="panel-heading" style="text-align:center; font-size:16px; font-weight:bold;">Registro de Predio
                        </div>
                        <div class="panel-body">
                          <form class="form-horizontal" id="maguey" action="" method="POST" name="maguey" enctype="multipart/form-data" >
                            <div class="form-group">
                               <input class="form-control" type="hidden" name='usr' id='usr' value='<?php echo $_SESSION[$d_s]['s_username']; ?>'/>
                            </div>
                            <fieldset>
                              <legend align="">Datos del Productor</legend>
                            </fieldset>
                            <div class="form-group">
                              <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">No.Predio:</label>
                              <div class="col-md-3 col-sm-3 col-xs-3">
                                 <input type="text" name="id" id="id" disabled="disabled" class='form-control txt-short'  value="<?php echo $id; ?>">
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">No. de Control:</label>
                              <div class="col-md-3 col-sm-3 col-xs-3">
                                <input type="text" id="state"  name="state" class='form-control txt-short' />
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">Nombre:</label>
                              <div class="col-md-4 col-sm-4 col-xs-4">
                                 <input readonly type="text" id="abbrev" name="abbrev" class='form-control'/>
                              </div>
                            </div>
                            <div>
                              <legend align="">Datos del Predio</legend>
                            </div>

                            <?php //if ($_SESSION[$d_s]['id_us'] == 1) {  ?>
                            <div class="form-group">
                              <label class="col-md-3 col-sm-3 col-xs-3 control-label">Registro:</label>
                              <div class="col-md-4 col-sm-4 col-xs-4">
                                <select class="txt-short form-control" id="cboxMCR" name="cboxMCR">
                                  <option value="0">Seleccione</option>
                                  <option value="1">En Sitio</option>
                                  <option value="2">Documental</option>
                                </select>
                              </div>
                            </div>

                            <div class="form-group" id="seccDocumental" style="display: none;">
                              <label class="col-md-3 col-sm-3 col-xs-3 control-label">Servicio:</label>
                              <div class="col-md-4 col-sm-4 col-xs-4">
                                <select class="txt-short form-control" id="SelServicio" name="SelServicio">
                                  <option value="">Seleccione</option>
                                  <option value="NORMAL">Normal</option>
                                  <option value="EXCLUSIVO">Exclusivo</option>
                                </select>
                              </div>
                            </div>
                            <?php // } ?>
                            <div class="form-group">
                              <label class="col-md-3 col-sm-3 col-xs-3 control-label">Propietario:</label>
                              <div class="col-md-4 col-sm-4 col-xs-4">
                                <input class="form-control" type="checkbox" id="referencia1" name="referencia1" data-size="small" data-toggle="toggle" data-size="normal" data-onstyle="success" data-on="Si" data-off="No" data-offstyle="danger">
                              </div>
                              <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label"></label>
                            </div>
                            <div class="form-group" id="formularioreferencia" style="display: none;">
                              <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">Nombre completo:</label>
                              <div class="col-md-4 col-sm-4 col-xs-4">
                                  <input type="text" name="referencia2" id="referencia2" class="form-control" onblur="upperCase()">
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">Fecha de Registro:</label>
                              <div class="col-md-4 col-sm-4 col-xs-4">
                                <div class="input-group date form_date" data-date="" data-date-format="mm/dd/yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                                    <input class="form-control" size="16" type="text" value="" name="fecha"  id="fecha" readonly/>
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">Nombre del Predio:</label>
                              <div class="col-md-4 col-sm-4 col-xs-4">
                                <input type="text" id="paraje" name="paraje" class='form-control txt-largo' />
                              </div>
                            </div>
                            <!--<div class="form-group">
                              <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">Referencia de Ubicación:</label>
                              <div class="col-md-4 col-sm-4 col-xs-4">
                                <input class="form-control" type="text" id="referenciau" name="referenciau" onblur="upperCase()" />
                              </div>
                            </div>-->
                            <div class="form-group">
                              <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">Estado:</label>
                              <div class="col-md-4 col-sm-4 col-xs-4">
                                <select class="form-control" name="estado" id="estado">
                                  <option value="">- Seleccione un Estado -</option>
                                  <?php
                                    $estados = dameEstado();
                                    foreach ($estados as $indice => $registro) {
                                      echo "<option value=" . $registro['clave'] . ">" . $registro['nombre'] . "</option>";
                                    }
                                  ?>
                                </select>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">Municipio:</label>
                              <div class="col-md-4 col-sm-4 col-xs-4">
                                <select class="form-control" name="municipio" id="municipio"/>
                                  <option value="">- primero seleccion un estado -</option>
                                </select>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">Localidad:</label>
                              <div class="col-md-4 col-sm-4 col-xs-4" align="right">
                                <select class="form-control" name="local" id="local">
                                  <option value="">- primero seleccion un municipio -</option>
                                </select>
                                <a href="#" id="btn_nueva_localidad" class="text-success"><span class="glyphicon glyphicon-plus-sign"></span><!-- <img src="images/add.png"/> --></a>
                              </div>
                            </div>
                            <div  class="modal fade" id="ModalRutaNueva"  title="Nueva Localidad"   role="dialog" aria-hidden="true">
                              <div style="position:relative;width:300px;margin:10px;width:600px;margin:30px auto">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="titulo">Nueva Localidad</h4>
                                  </div>
                                  <div class="modal-body container-fluid">
                                    <div class="form-group">
                                      <label class="col-md-2 control-label">Localidad:</label>
                                      <div class="col-md-8">
                                        <input class="form-control" type="text" name="TxtNuevaLocalidad" id="TxtNuevaLocalidad" size="40">
                                      </div>
                                    </div>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" id="BtnNuevaLocalidad">Aceptar</button>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">Tenencia de la Tierra:</label>
                              <div class="col-md-4 col-sm-4 col-xs-4">
                               <select name="tenencia" id="tenencia" class="form-control" style="max-width:160px;">
                                  <option selected="selected" value="NS">SELECCIONAR</option>
                                  <option value="EJIDAL">EJIDAL</option>
                                  <option value="COMUNAL">COMUNAL</option>
                                  <option value="PRIVADA">PRIVADA</option>
                                </select>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">Comprobante de Propiedad:</label>
                              <div class="col-md-4 col-sm-4 col-xs-4">
                                 <span class="btn btn-default btn-file">
                                    <input type="file" id="archivo" name="archivo" size="50" class="">
                                </span>
                              </div>
                            </div>
                            <div class="form-group">
                               <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">Usufruto de la Tierra:</label>
                              <div class="col-md-4 col-sm-4 col-xs-4">
                                <select name="usufruto" id="usufruto"  class="form-control" style="max-width:200px;" />
                                  <option selected="selected" value="NS">SELECCIONAR</option></p>
                                  <option value="A MEDIAS">A MEDIAS</option>
                                  <option value="RENTADO">RENTADO</option>
                                  <option value="PROPIEDAD">PROPIEDAD</option>
                                  <option value="PRESTADO">PRESTADO</option>
                                </select>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">Superficie (Hectáreas):</label>
                              <div class="col-md-4 col-sm-4 col-xs-4">
                                <input type="text" id="superficie" name="superficie" class='form-control txt-largo'/>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">Latitud Norte:</label>
                              <div class="col-md-4 col-sm-4 col-xs-4">
                                <input type="text" id="lat" name="lat" class='form-control txt-largo' />
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">Longitud Oeste:</label>
                              <div class="col-md-4 col-sm-4 col-xs-4">
                                <input type="text" id="lng" name="lng" class='form-control txt-largo' />
                              </div>
                            </div>
                            <div align="center">
                              <button class="btn btn-success" id="submit" type="button" value="Ubícame" name="ubicame" id="ubicame" onclick="pan()"><span class="glyphicon glyphicon-map-marker"></span> Ubícame</button>
                            </div>
                            <br>
                            <div class="" align="center">
                              <div class="" id="map-canvas"></div>
                            </div>
                            <br>


                            <!-- <div class="form-group">
                                <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">Tipo de Registro de Maguey :</label>
                                <div class="col-md-4 col-sm-4 col-xs-4">
                                  <select name="cboxMCR" id="cboxMCR"  class="form-control" style="max-width:200px;" />

                                    <option selected="selected" value=1>NORMAL</option>

                                  </select>
                                </div>
                            </div> -->

                            <div class="form-group" id="divPoligono">
                              <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">Polígono:</label>
                              <div class="col-md-4 col-sm-4 col-xs-4">
                                <span class="btn btn-default btn-file">
                                  <input type="file" id="poligono" name="poligono" class="" size="50">
                                </span>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">Representante en Campo:</label>
                              <div class="col-md-4 col-sm-4 col-xs-4">
                                 <input type="text" id="campo" name="campo" class='form-control txt-largo' onblur="upperCase()"/>
                              </div>
                            </div>

                            <!-- IMPORTAR IMÁGENES -->
                            
                            <div class="form-group">
                              <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">Imagen 1:</label>
                              <div class="col-md-4 col-sm-4 col-xs-4">
                                 <span class="btn btn-default btn-file">
                                    <input type="file" id="imagen1" name="imagen1" size="50" class="">
                                </span>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">Imagen 2:</label>
                              <div class="col-md-4 col-sm-4 col-xs-4">
                                 <span class="btn btn-default btn-file">
                                    <input type="file" id="imagen2" name="imagen2" size="50" class="">
                                </span>
                              </div>
                            </div>
                            
                            <!-- ------------------------------ -->
                            <!--<div class="form-group">
                              <label for="status_predio" class="col-md-3 col-sm-3 col-xs-3 control-label">Estatus Predio:</label>
                              <div class="radio">
                                <label><input type="radio" id="predio_activo" name="status_predio" value="1"> Mostrar</label>
                                <label><input type="radio" id="predio_inactivo" name="status_predio" value="0"> Ocultar</label>
                              </div>
                            </div>-->
                            <div>
                              <fieldset>
                                <legend align="">Datos del Maguey</legend>
                              </fieldset>
                            </div>
                            <div id="contenedor">
                              <div class="form-group">
                                <label for="cboxGuia" class="col-md-3 col-sm-3 col-xs-3 control-label">Generar una sola guia : </label>
                                <div class="col-md-4 col-sm-4 col-xs-4">
                                  <input class="form-control" type="checkbox" id="cboxGuia" name="cboxGuia" data-size="small" checked data-toggle="toggle" data-size="normal" data-onstyle="success" data-on="Si" data-off="No" data-offstyle="danger">
                                </div>
                              </div>
                               <div class="form-group">
                                <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">Registro de Maguey:</label>
                                <div class="col-md-4 col-sm-4 col-xs-4">
                                  <select class="form-control" name="registro" id="registro"style="max-width:250px;">
                                    <option selected="selected" value="">SELECCIONAR</option>
                                    <option value="CULTIVADO">CULTIVADO</option>
                                    <option value="SEMICULTIVADO">SEMICULTIVADO</option>
                                    <option value="SILVESTRE">SILVESTRE</option>
                                  </select>
                                </div>
                              </div>
                              <div class="form-group">
                                <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">Distancia (Surcos):</label>
                                <div class="col-md-4 col-sm-4 col-xs-4">
                                  <input type="text" id="sc" name="sc" class='form-control txt-largo' />
                                </div>
                              </div>
                              <div class="form-group">
                                <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">Distancia (Plantas):</label>
                                <div class="col-md-4 col-sm-4 col-xs-4">
                                  <input type="text" id="sm" name="sm" class='form-control txt-largo' />
                                </div>
                              </div>
                              <div class="form-group">
                                <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">Especie: </label>
                                <div class="col-md-4 col-sm-4 col-xs-4">
                                  <select class="form-control" name="especie" id="especie">
                                    <option value="">- Seleccione una Especie -</option>
                                    <?php echo $combobit; ?>
                                  </select>
                                </div>
                              </div>
                              <div class="form-group">
                                <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">No. de Plantas:</label>
                                <div class="col-md-4 col-sm-4 col-xs-4">
                                  <input type="text" id="plantas" name="plantas" class='form-control txt-largo'/>
                                </div>
                              </div>
                              <div class="form-group">
                                <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">Edad:</label>
                                <div class="col-md-4 col-sm-4 col-xs-4">
                                  <input type="text" id="edad" name="edad" class='form-control txt-largo' />
                                </div>
                              </div>
                              <div class="form-group" align="center">
                                <button type="button" name='agregar' id='agregar' class="btn btn-primary" onClick="fn_agregar()">
                                  <span class="glyphicon glyphicon-plus"></span> Agregar
                                </button>
                              </div>
                              <table id="grilla" class="table table-hover table-success lista">
                                <thead>
                                  <tr>
                                    <th>Registro</th>
                                    <th>Distancia (Surcos)</th>
                                    <th>Distancia (Plantas)</th>
                                    <th>Especie</th>
                                    <th>No. de Plantas</th>
                                    <th>Edad</th>
                                  </tr>
                                </thead>
                                <tbody></tbody>
                              </table>
                            </div>
                            <div class="form-group" align="center">
                              <button type="button" name='cancelar' id='cancelar' class="btn btn-danger" onClick="cancelar()" >
                                <span class="glyphicon glyphicon-remove"></span> Cancelar
                              </button>
                              <button type="button"  name='btnTerminar' id='btnTerminar'  value="btnTerminar" class="btn btn-success"  onClick="">
                                <span class="glyphicon glyphicon-ok"></span> Guardar
                              </button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div role="tabpanel" class="tab-pane" id="tab2success">
                    <div style="margin-left:auto !important; margin-right:auto !important;">
                      <div class="panel  panel-default" style="margin-bottom:0 !important; margin-top:5px !important;">
                        <div class="panel-heading" style="text-align:center; font-size:16px; font-weight:bold;">Entrada de Maguey
                        </div>
                        <div class="panel-body">
                          <form class="form-horizontal" id="magueys" action="" method="POST" name="magueys" enctype="multipart/form-data">
                            <div class="form-group"><input hidden="true"></div>
                            <fieldset>
                              <legend align="">Datos del Predio</legend>
                            </fieldset>
                            <div class="form-group">
                              <label for="magueys" class="col-md-3 col-sm-3 col-xs-3 control-label">No. Predio:</label>
                              <div class="col-md-4 col-sm-4 col-xs-4">
                                <input type="text" id="num"  name="num" class='form-control txt-short'/>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="magueys" class="col-md-3 col-sm-3 col-xs-3 control-label">Nombre del Predio:</label>
                              <div class="col-md-4 col-sm-4 col-xs-4">
                                <input readonly type="text" id="nombrepre" name="nombrepre" class='form-control'/>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="magueys" class="col-md-3 col-sm-3 col-xs-3 control-label">No. de Control:</label>
                              <div class="col-md-4 col-sm-4 col-xs-4">
                                <input readonly type="text" id="clientep" name="clientep" class='form-control'/>
                              </div>
                            </div>

                             <div class="form-group" id="divMSP">
                                <label for="cboxGuia" class="col-md-3 col-sm-3 col-xs-3 control-label">Modificar Superficie del predio : </label>
                                <div class="col-md-4 col-sm-4 col-xs-4">
                                  <input class="form-control" type="checkbox" id="cboxMSP" name="cboxMSP" data-size="small" checked data-toggle="toggle" data-size="normal" data-onstyle="success" data-on="Si" data-off="No" data-offstyle="danger">
                                </div>
                              </div>

                            <div class="form-group" id="divMS">
                              <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">Superficie (Hectáreas):</label>
                              <div class="col-md-4 col-sm-4 col-xs-4">
                                <input type="text" id="superficieM" name="superficieM" class='form-control txt-largo'/>
                              </div>
                            </div>

                            <div class="form-group" id="divMP">
                              <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">Polígono:</label>
                              <div class="col-md-4 col-sm-4 col-xs-4">
                                <span class="btn btn-default btn-file">
                                  <input type="file" id="poligonoM" name="poligonoM" class="" size="50">
                                </span>
                              </div>
                            </div>

                            <fieldset>
                              <legend align="">Datos del Maguey</legend>
                            </fieldset>
                            <div id="contenedor">
                              <div class="form-group">
                                <label for="magueys" class="col-md-3 col-sm-3 col-xs-3 control-label">Generar una sola guia : </label>
                                <div class="col-md-4 col-sm-4 col-xs-4">
                                  <input class="form-control" type="checkbox" id="cboxGuiaM" name="cboxGuiaM" data-size="small" checked data-toggle="toggle" data-size="normal" data-onstyle="success" data-on="Si" data-off="No" data-offstyle="danger">
                                </div>
                              </div>
                              <div class="form-group">
                                <label for="magueys" class="col-md-3 col-sm-3 col-xs-3 control-label">Registro de Maguey:</label>
                                <div class="col-md-4 col-sm-4 col-xs-4">
                                  <select class="form-control" name="registros" id="registros"style="max-width:250px;" />
                                    <option selected="selected" value="">SELECCIONAR</option>
                                    <option value="CULTIVADO">CULTIVADO</option>
                                    <option value="SEMICULTIVADO">SEMICULTIVADO</option>
                                    <option value="SILVESTRE">SILVESTRE</option>
                                  </select>
                                </div>
                              </div>
                              <div class="form-group">
                                <label for="magueys" class="col-md-3 col-sm-3 col-xs-3 control-label">Distancia (Surcos):</label>
                                <div class="col-md-4 col-sm-4 col-xs-4">
                                  <input type="text" id="scs" name="scs" class='form-control txt-largo' />
                                </div>
                              </div>
                              <div class="form-group">
                                <label for="magueys" class="col-md-3 col-sm-3 col-xs-3 control-label">Distancia (Plantas):</label>
                                <div class="col-md-4 col-sm-4 col-xs-4">
                                  <input type="text" id="sms" name="sms" class='form-control txt-largo' />
                                </div>
                              </div>
                              <div class="form-group">
                                <label for="magueys" class="col-md-3 col-sm-3 col-xs-3 control-label">Especie: </label>
                                <div class="col-md-4 col-sm-4 col-xs-4">
                                  <select class="form-control" name="especies" id="especies">
                                    <option value="">- Seleccione una Especie -</option>
                                      <?php echo $combobit; ?>
                                  </select>
                                </div>
                              </div>
                              <div class="form-group">
                                <label for="magueys" class="col-md-3 col-sm-3 col-xs-3 control-label">No. de Plantas:</label>
                                <div class="col-md-4 col-sm-4 col-xs-4">
                                  <input type="text" id="plantass" name="plantass" class='form-control txt-largo'/>
                                </div>
                              </div>
                              <div class="form-group">
                                <label for="magueys" class="col-md-3 col-sm-3 col-xs-3 control-label">Edad:</label>
                                <div class="col-md-4 col-sm-4 col-xs-4">
                                  <input type="text" id="edads" name="edads" class='form-control txt-largo' />
                                </div>
                              </div>
                              <div class="form-group" align="center">
                                <button type="button" name='agregar' id='agregar' class="btn btn-primary" onClick="fn_agregare()">
                                  <span class="glyphicon glyphicon-plus"></span> Agregar
                                </button>
                              </div>
                              <table id="grillas" class="table table-hover table-success lista">
                                <thead>
                                  <tr>
                                    <th>Registro</th>
                                    <th>Distancia (Surcos)</th>
                                    <th>Distancia (Plantas)</th>
                                    <th>Especie</th>
                                    <th>No. de Plantas</th>
                                    <th>Edad</th>
                                  </tr>
                                </thead>
                                <tbody>
                                </tbody>
                              </table>
                            </div>
                            <div class="form-group" align="center">
                              <button type="reset" name='reset1' id='reset1' class="btn btn-danger" onClick="reset1()" >
                                <span class="glyphicon glyphicon-remove"></span> Cancelar
                              </button>
                              <button type="button"  name='btnTerminars' id='btnTerminars' class="btn btn-success"  onClick="">
                                <span class="glyphicon glyphicon-ok"></span> Guardar
                              </button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>

                    <!-- -->
                    <div role="tabpanel" class="tab-pane" id="tab3success">
                    <div style="margin-left:auto !important; margin-right:auto !important;">
                      <div class="panel  panel-default" style="margin-bottom:0 !important; margin-top:5px !important;">
                        <div class="panel-heading" style="text-align:center; font-size:16px; font-weight:bold;">Atributos de Sustentabilidad
                        </div>
                        <div class="panel-body">
                          <div class="form">
                            <form class="form-horizontal" action="" onsubmit="" id="formbuscar" method="POST" name="formbuscar" enctype="multipart/form-data" onSubmit="return limpiar()">
                              <input type="hidden" name='usr' id='usr' value='<?php echo $_SESSION[$d_s]['s_username'];?>'>
                              <fieldset>
                                <legend align="">Buenas Prácticas de Manejo del Maguey</legend>
                              </fieldset>
                              <div class="form-group">
                                <label for="asociado" class="col-md-3 control-label">No. Asociado:</label>
                                <div class="col-md-2">
                                  <input type="text" id="noasociado"  name="noasociado" class='form-control txt-short'>
                                </div>
                                <label for="asociado" class="col-md-2 control-label">No. Predio:</label>
                                <div class="col-md-2">
                                  <input type="text" id="nopredio"  name="nopredio" class='form-control txt-short'>
                                </div>
                              </div>
                              <div class="form-group">
                                <label for="asociado" class="col-md-3 control-label">Nombre Asociado:</label>
                                <div class="col-md-6">
                                  <input readonly type="text" id="nomasociado" name="nomasociado" class='form-control'>
                                </div>
                              </div>
                              <div class="form-group">
                                 <label for="asociado" class="col-md-3 control-label">Predio o Vivero:</label>
                                <div class="col-md-6">
                                  <select class="form-control" id="predios" name="predios" >
                                    <option value="">Elige un Predio o Vivero</option>
                                  </select>
                                </div>
                              </div>

                              <!-- CAMBIOS -->
                              <!--<div class="conteiner" align="center">
                                <div class="form-group" >
                                  <button type="button" name='cancelar' id='cancelar' class="btn btn-danger" onClick="cancelar()" ><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                                  <button type="submit" id='btbuscar' name="btbuscar" class="btn btn-success"><span class="glyphicon glyphicon-search"></span> Buscar</button>
                                </div>
                              </div>-->
                              <br><br>
                              <div>
                                <section id="resultado"></section>
                              </div>
                              <br><br>

                              <style>
                                  /* azul*/
                                    .btn-group .notActive{
                                        color: #BDBDBD;
                                        background-color: #fff;
                                    }
                                    .btn-group .activeNA{
                                        color: #fff;
                                        background-color: #33c4ff;
                                    }
                                    /* verde -- #A9F5A9 */
                                    .btn-group .activeC{
                                        color: #fff;
                                        background-color: #088A29;
                                    }
                                    /* rojo  -- #FA5858 */
                                    .btn-group .activeNC{
                                        color: #fff;
                                        background-color: #DF0101;
                                    }

                                    .btn-group-sel .notActive{
                                        color: #31B06D;
                                        border: 1px solid #088A29;
                                        background-color: #fff;
                                    }

                                    .btn-group-sel .active{
                                        color: #fff;
                                        background-color: #088A29;
                                    }
                              </style>
                              <div id="grupoAtributos" class="groupAtributos">

                                <!-- PRIMER PANEL -->
                              <div role="tabpanel" class="tab-pane" id="tab3success">
                                <div style="margin-left:auto !important; margin-right:auto !important;">
                                  <div class="panel  panel-default" style="margin-bottom:0 !important; margin-top:5px !important;">
                                    <div class="panel-heading" style="text-align:center; font-size:16px; font-weight:bold;">
                                        Manejo Integrado de Plagas y Enfermedades
                                    </div>
                                      <fieldset>
                                          <label for="asociado" style="margin-left: 10px" class="control-label">Manejo Integrado de Plagas y Enfermedades:</label><br>
                                          <span style="margin-left: 10px">Manejo Integrado de Plagas y Enfermedades.</span>
                                      </fieldset>
                                      <div class="panel-body">
                                          <div class="form-group">
                                              <div class="col-md-6"><label for="txt1" class="control-label">Evaluación:</label></div>
                                              <div class="col-md-6"><label for="txt1" class="control-label">Observaciones:</label></div>
                                          </div>
                                         <div class="form-group">
                                            <!--<label for="asociado" class="col-md-3 control-label">Manejo Integrado de Placas y Enfermedades:</label>-->
                                            <div class="col-md-1"></div>
                                            <div class="col-md-5">
                                             <!--<input class="form-control" type="checkbox" id="indicador0" name="indicador0" data-size="small" data-toggle="toggle" data-size="normal" data-onstyle="warning" data-on="No Cumple" data-off="Cumple" data-offstyle="success" data-width="100">-->
                                             <div id="radioBtnTM" class="btn-group-sel">
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador0" data-title="">Sin Evaluar</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador0" data-title="6">6</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador0" data-title="7">7</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador0" data-title="8">8</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador0" data-title="9">9</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador0" data-title="10">10</a>
                                             </div>
                                             <input type="hidden" name="indicador0" id="indicador0">
                                            </div>
                                            <div class="col-md-5">
                                                <textarea class="form-control" rows="1"  name="txt0" id="txt0"></textarea>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" id='btnGuardar0' name="btnGuardar0" class="btn btn-primary GuardaI"><center><i class="fas fa-save"></i></center></button>
                                            </div>
                                         </div>
                                      </div>
                                  </div>
                                </div>
                              </div>

                              <!-- SEGUNDO PANEL -->
                              <br><br>
                              <div role="tabpanel" class="tab-pane" id="tab3success">
                                <div style="margin-left:auto !important; margin-right:auto !important;">
                                  <div class="panel  panel-default" style="margin-bottom:0 !important; margin-top:5px !important;">
                                    <div class="panel-heading" style="text-align:center; font-size:16px; font-weight:bold;">
                                        Preservación de la Diversidad Biológica de Magueyes
                                    </div>
                                      <fieldset>
                                          <span style="margin-left: 10px">Que haya diversidad de especies y presencia de plantas en floración y fructificación.</span>
                                      </fieldset>

                                      <fieldset>
                                          <label for="asociado" style="margin-left: 10px" class="control-label">Especies Presentes en el predio:</label><br>
                                          <!--<span style="margin-left: 10px">Manejo Integrado de Placas y Enfermedades.</span>-->
                                      </fieldset>
                                      <div class="panel-body">
                                          <div class="form-group">
                                              <div class="col-md-6"><label for="txt1" class="control-label">Evaluación:</label></div>
                                              <div class="col-md-6"><label for="txt1" class="control-label">Observaciones:</label></div>
                                          </div>
                                         <div class="form-group">
                                            <div class="col-md-1"></div>
                                           <div class="col-md-5">
                                             <div id="radioBtnTM" class="btn-group-sel">
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador1" data-title="">Sin Evaluar</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador1" data-title="6">6</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador1" data-title="7">7</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador1" data-title="8">8</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador1" data-title="9">9</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador1" data-title="10">10</a>
                                             </div>
                                             <input type="hidden" name="indicador1" id="indicador1">
                                           </div>
                                           <div class="col-md-5">
                                               <textarea class="form-control" rows="1"  name="txt1" id="txt1"></textarea>
                                           </div>
                                            <div class="col-md-1">
                                                <button type="button" id='btnGuardar1' name="btnGuardar1" class="btn btn-primary GuardaI"><center><i class="fas fa-save"></i></center></button>
                                            </div>
                                         </div>
                                      </div>

                                      <fieldset>
                                          <label for="asociado" style="margin-left: 10px" class="control-label">Magueyes en Floración:</label><br>
                                      </fieldset>
                                      <div class="panel-body">
                                          <div class="form-group">
                                              <div class="col-md-6"><label for="txt1" class="control-label">Evaluación:</label></div>
                                              <div class="col-md-6"><label for="txt1" class="control-label">Observaciones:</label></div>
                                          </div>
                                         <div class="form-group">
                                            <div class="col-md-1"></div>
                                           <div class="col-md-5">
                                             <div id="radioBtnTM" class="btn-group-sel">
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador2" data-title="">Sin Evaluar</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador2" data-title="6">6</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador2" data-title="7">7</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador2" data-title="8">8</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador2" data-title="9">9</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador2" data-title="10">10</a>
                                             </div>
                                             <input type="hidden" name="indicador2" id="indicador2">
                                           </div>
                                           <div class="col-md-5">
                                               <textarea class="form-control" rows="1"  name="txt2" id="txt2"></textarea>
                                           </div>
                                            <div class="col-md-1">
                                                <button type="button" id='btnGuardar2' name="btnGuardar2" class="btn btn-primary GuardaI"><center><i class="fas fa-save"></i></center></button>
                                            </div>
                                         </div>
                                      </div>

                                  </div>
                                </div>
                              </div>

                              <!-- TERCER PANEL -->
                              <br><br>
                              <div role="tabpanel" class="tab-pane" id="tab3success">
                                <div style="margin-left:auto !important; margin-right:auto !important;">
                                  <div class="panel  panel-default" style="margin-bottom:0 !important; margin-top:5px !important;">
                                    <div class="panel-heading" style="text-align:center; font-size:16px; font-weight:bold;">
                                        Conservación de Suelo y Agua
                                    </div>
                                      <fieldset>
                                          <span style="margin-left: 10px">Que no genere pérdidas de suelo (erosión) y conserva humedad, ademas facilita la infiltración
                                              del agua de lluvia y facilita el manejo de arvenses competidores.</span>
                                      </fieldset>

                                      <fieldset>
                                          <label for="asociado" style="margin-left: 10px" class="control-label">Curvas a Nivel:</label><br>
                                      </fieldset>
                                      <div class="panel-body">
                                          <div class="form-group">
                                              <div class="col-md-6"><label for="txt1" class="control-label">Evaluación:</label></div>
                                              <div class="col-md-6"><label for="txt1" class="control-label">Observaciones:</label></div>
                                          </div>
                                         <div class="form-group">
                                            <div class="col-md-1"></div>
                                           <div class="col-md-5">
                                             <div id="radioBtnTM" class="btn-group-sel">
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador3" data-title="">Sin Evaluar</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador3" data-title="6">6</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador3" data-title="7">7</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador3" data-title="8">8</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador3" data-title="9">9</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador3" data-title="10">10</a>
                                             </div>
                                             <input type="hidden" name="indicador3" id="indicador3">
                                           </div>
                                           <div class="col-md-5">
                                               <textarea class="form-control" rows="1"  name="txt3" id="txt3"></textarea>
                                           </div>
                                            <div class="col-md-1">
                                                <button type="button" id='btnGuardar3' name="btnGuardar3" class="btn btn-primary GuardaI"><center><i class="fas fa-save"></i></center></button>
                                           </div>
                                         </div>
                                      </div>

                                      <fieldset>
                                          <label for="asociado" style="margin-left: 10px" class="control-label">No remosión del suelo:</label><br>
                                      </fieldset>
                                      <div class="panel-body">
                                          <div class="form-group">
                                              <div class="col-md-6"><label for="txt1" class="control-label">Evaluación:</label></div>
                                              <div class="col-md-6"><label for="txt1" class="control-label">Observaciones:</label></div>
                                          </div>
                                         <div class="form-group">
                                            <div class="col-md-1"></div>
                                           <div class="col-md-5">
                                             <div id="radioBtnTM" class="btn-group-sel">
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador4" data-title="">Sin Evaluar</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador4" data-title="6">6</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador4" data-title="7">7</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador4" data-title="8">8</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador4" data-title="9">9</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador4" data-title="10">10</a>
                                             </div>
                                             <input type="hidden" name="indicador4" id="indicador4">
                                           </div>
                                           <div class="col-md-5">
                                               <textarea class="form-control" rows="1"  name="txt4" id="txt4"></textarea>
                                           </div>
                                            <div class="col-md-1">
                                                <button type="button" id='btnGuardar4' name="btnGuardar4" class="btn btn-primary GuardaI"><center><i class="fas fa-save"></i></center></button>
                                            </div>
                                         </div>
                                      </div>

                                      <fieldset>
                                          <label for="asociado" style="margin-left: 10px" class="control-label">Suelo con Cobertura:</label><br>
                                      </fieldset>
                                      <div class="panel-body">
                                          <div class="form-group">
                                              <div class="col-md-6"><label for="txt1" class="control-label">Evaluación:</label></div>
                                              <div class="col-md-6"><label for="txt1" class="control-label">Observaciones:</label></div>
                                          </div>
                                         <div class="form-group">
                                            <div class="col-md-1"></div>
                                           <div class="col-md-5">
                                             <div id="radioBtnTM" class="btn-group-sel">
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador5" data-title="">Sin Evaluar</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador5" data-title="6">6</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador5" data-title="7">7</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador5" data-title="8">8</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador5" data-title="9">9</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador5" data-title="10">10</a>
                                             </div>
                                             <input type="hidden" name="indicador5" id="indicador5">
                                           </div>
                                           <div class="col-md-5">
                                               <textarea class="form-control" rows="1"  name="txt5" id="txt5"></textarea>
                                           </div>
                                            <div class="col-md-1">
                                                <button type="button" id='btnGuardar5' name="btnGuardar5" class="btn btn-primary GuardaI"><center><i class="fas fa-save"></i></center></button>
                                            </div>
                                         </div>
                                      </div>



                                  </div>
                                </div>
                              </div>

                              <!-- CUARTO PANEL -->
                              <br><br>
                              <div role="tabpanel" class="tab-pane" id="tab3success">
                                <div style="margin-left:auto !important; margin-right:auto !important;">
                                  <div class="panel  panel-default" style="margin-bottom:0 !important; margin-top:5px !important;">
                                    <div class="panel-heading" style="text-align:center; font-size:16px; font-weight:bold;">
                                        Manejo Orgánico
                                    </div>
                                      <fieldset>
                                          <span style="margin-left: 10px">Que se demuestre la no contaminación con agroquímicos no permitidos y el manejo orgánico del predio.</span>
                                      </fieldset>


                                      <fieldset>
                                          <label for="asociado" style="margin-left: 10px" class="control-label">Seguridad y Protocolos:</label><br>
                                      </fieldset>
                                      <div class="panel-body">
                                          <div class="form-group">
                                              <div class="col-md-6"><label for="txt1" class="control-label">Evaluación:</label></div>
                                              <div class="col-md-6"><label for="txt1" class="control-label">Observaciones:</label></div>
                                          </div>
                                         <div class="form-group">
                                            <div class="col-md-1"></div>
                                           <div class="col-md-5">
                                             <div id="radioBtnTM" class="btn-group-sel">
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador6" data-title="">Sin Evaluar</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador6" data-title="6">6</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador6" data-title="7">7</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador6" data-title="8">8</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador6" data-title="9">9</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador6" data-title="10">10</a>
                                             </div>
                                             <input type="hidden" name="indicador6" id="indicador6">
                                           </div>
                                           <div class="col-md-5">
                                               <textarea class="form-control" rows="1"  name="txt6" id="txt6"></textarea>
                                           </div>
                                            <div class="col-md-1">
                                                <button type="button" id='btnGuardar6' name="btnGuardar6" class="btn btn-primary GuardaI"><center><i class="fas fa-save"></i></center></button>
                                            </div>
                                         </div>
                                      </div>

                                      <fieldset>
                                          <label for="asociado" style="margin-left: 10px" class="control-label">Manejo Orgánico:</label><br>
                                      </fieldset>
                                      <div class="panel-body">
                                          <div class="form-group">
                                              <div class="col-md-6"><label for="txt1" class="control-label">Evaluación:</label></div>
                                              <div class="col-md-6"><label for="txt1" class="control-label">Observaciones:</label></div>
                                          </div>
                                         <div class="form-group">
                                            <div class="col-md-1"></div>
                                           <div class="col-md-5">
                                             <div id="radioBtnTM" class="btn-group-sel">
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador7" data-title="">Sin Evaluar</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador7" data-title="6">6</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador7" data-title="7">7</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador7" data-title="8">8</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador7" data-title="9">9</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador7" data-title="10">10</a>
                                             </div>
                                             <input type="hidden" name="indicador7" id="indicador7">
                                           </div>
                                           <div class="col-md-5">
                                               <textarea class="form-control" rows="1"  name="txt7" id="txt7"></textarea>
                                           </div>
                                            <div class="col-md-1">
                                                <button type="button" id='btnGuardar7' name="btnGuardar7" class="btn btn-primary GuardaI"><center><i class="fas fa-save"></i></center></button>
                                            </div>
                                         </div>
                                      </div>


                                  </div>
                                </div>
                              </div>


                              <!-- QUINTO PANEL -->
                              <br><br>
                              <div role="tabpanel" class="tab-pane" id="tab3success">
                                <div style="margin-left:auto !important; margin-right:auto !important;">
                                  <div class="panel  panel-default" style="margin-bottom:0 !important; margin-top:5px !important;">
                                    <div class="panel-heading" style="text-align:center; font-size:16px; font-weight:bold;">
                                        Aprovechamiento Controlado de Magueyes Silvestres
                                    </div>
                                      <fieldset>
                                          <span style="margin-left: 10px">Que la extracción sea solo de magueyes maduros y que no genere una disminución drástica de la población.</span>
                                      </fieldset>


                                      <fieldset>
                                          <label for="asociado" style="margin-left: 10px" class="control-label">Extracción de Magueyes Maduros:</label><br>
                                      </fieldset>
                                      <div class="panel-body">
                                          <div class="form-group">
                                              <div class="col-md-6"><label for="txt1" class="control-label">Evaluación:</label></div>
                                              <div class="col-md-6"><label for="txt1" class="control-label">Observaciones:</label></div>
                                          </div>
                                         <div class="form-group">
                                            <div class="col-md-1"></div>
                                           <div class="col-md-5">
                                             <div id="radioBtnTM" class="btn-group-sel">
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador8" data-title="">Sin Evaluar</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador8" data-title="6">6</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador8" data-title="7">7</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador8" data-title="8">8</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador8" data-title="9">9</a>
                                                <a class="btn btn-primary btn-sm notActive" data-toggle="indicador8" data-title="10">10</a>
                                             </div>
                                             <input type="hidden" name="indicador8" id="indicador8">
                                           </div>
                                           <div class="col-md-5">
                                               <textarea class="form-control" rows="1"  name="txt8" id="txt8"></textarea>
                                           </div>
                                            <div class="col-md-1">
                                                <button type="button" id='btnGuardar8' name="btnGuardar8" class="btn btn-primary GuardaI"><center><i class="fas fa-save"></i></center></button>
                                            </div>
                                         </div>
                                      </div>

                                  </div>
                                </div>
                              </div>

                              <br><br>
                                <div class="conteiner" align="center">
                                    <div class="form-group" >
                                        <button type="button" id='btnGuardarA' name="btnGuardarA" class="btn btn-success"><strong> Guardar Atributos</strong></button>
                                    </div>
                                </div>

                              </div>

                            </form>
                          </div>
                          <br>
                        </div>
                      </div>
                    </div>
                  </div>
                    <!-- -->

                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
  </div>


	<!-- Modal -->
        <div id="mySumGuia" class="modal fade">
          <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background: #2F5D62;color: #DFEEEA;text-align: center;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
						<span style="font-size: 16px;font-weight: 400;">AGREGAR GUÍAS</span>
					</div>

					<div class="modal-body container-fluid" style="max-height: calc(100vh - 210px);     overflow-y: auto;">


                        <form class="form-horizontal" autocomplete="off" >

							<div class="col-lg-12" style="margin-top: 10px;">
                              <label class="control-label col-lg-3" style="font-size: 12px;">PARAJE:</label>
                              <div class="col-lg-2">
                                 <input type="text" class="form-control" id="mParaje" name="mParaje" disabled>

                              </div>


                            </div>

							<div class="col-lg-12" style="margin-top: 10px;">
                              <label class="control-label col-lg-3" style="font-size: 12px;">GUÍAS GENERADAS:</label>
                              <div class="col-lg-2">
                                 <input type="text" class="form-control" id="mGgeneradas" name="mGgeneradas" disabled>

                              </div>
							  <label class="control-label col-lg-3" style="font-size: 12px;">GUÍAS OCUPADAS:</label>
                              <div class="col-lg-2">

								<input type="text" class="form-control" id="mGocupadas" name="mGocupadas" disabled>

                              </div>

                            </div>

                            <div class="col-lg-12" style="margin-top: 10px;">
							  <label class="control-label col-lg-3" style="font-size: 12px;">GUÍAS A AGREGAR:</label>
                              <div class="col-lg-2">

								<select class="form-control" id="mGagregar" name="mGagregar" required="">
									<option value="1">1</option>
									<option value="2">2</option>
								</select>

                              </div>

                            </div>


                        </form>



                    </div>
                    <div class="modal-footer">
                      <button type="button" id="btnModificarSol" onclick="agregarG();" class="btn btn-primary" style="font-size:11px; width:120px;border-radius:5px;">AGREGAR</button>
                      <button type="button" class="btn btn-danger" data-dismiss="modal" style="font-size:11px; width:120px;border-radius:5px;">CERRAR</button>
                    </div>




              </div>
            </div>
        </div>
	<!-- End Modal -->

	<!-- Modal -->
        <div id="modalPredio" class="modal fade">
          <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background: #2F5D62;color: #DFEEEA;text-align: center;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
						<span style="font-size: 16px;font-weight: 400;">IMPORTAR PREDIO</span>

					</div>

					<div class="modal-body container-fluid" style="max-height: calc(100vh - 210px);     overflow-y: auto;">


                        <form class="form-horizontal" autocomplete="off" id="formPredio">

							<div class="col-lg-12" style="margin-top: 10px;">
                              <label class="control-label col-lg-3" style="font-size: 12px;">No. CONTROL:</label>
                              <div class="col-lg-2">
                                 <input type="text" class="form-control" id="mnocliente" name="mnocliente" >

                              </div>
							  <div class="col-lg-1">&nbsp;</div>
							  <label class="control-label col-lg-3" style="font-size: 12px;">No. CONTROL (ANTERIOR):</label>
                              <div class="col-lg-2">
                                 <input type="text" class="form-control" id="mnoclientea" name="mnoclientea" disabled>
                                 <input type="hidden" class="form-control" id="mnoclienteah" name="mnoclienteah">
                              </div>

                            </div>

                            <div class="col-lg-12" style="margin-top: 10px;">
                              <label class="control-label col-lg-3" style="font-size: 12px;">NOMBRE:</label>
                              <div class="col-lg-8">
                                 <input type="text" class="form-control" id="mnombre" name="mnombre" disabled>

                              </div>


                            </div>

							<div class="col-lg-12" style="margin-top: 10px;">
                              <label class="control-label col-lg-3" style="font-size: 12px;">PREDIO A IMPORTAR:</label>
                              <div class="col-lg-8">
                                <select class="form-control" id="mpredio" name="mpredio" >
									<option value="">Elige un Predio</option>
								</select>
                              </div>
                            </div>

							<div class="col-lg-12" style="margin-top: 5px;" id="mresultado">
								<div class="col-lg-1">&nbsp;</div>
								<div class="col-lg-10">
									<h4>Información del Predio</h4>
									<div class="card-header">
										Plantas
									</div>
									<div class="card-header">
										Guías
									</div>
								</div>
								<div class="col-lg-1">&nbsp;</div>
                            </div>



                        </form>

                    </div>
                    <div class="modal-footer">
                      <button type="button" id="btnTransferirPredio" onclick="TransferirPredio();" class="btn btn-primary" style="font-size:11px; width:200px;border-radius:5px;">REALIZAR TRANSFERENCIA</button>
                      <button type="button" class="btn btn-danger" data-dismiss="modal" style="font-size:11px; width:120px;border-radius:5px;">CERRAR</button>
                    </div>




              </div>
            </div>
        </div>
	<!-- End Modal -->

  <script type="text/javascript">
    $('.form_date').datetimepicker({
      language:  'es',
      weekStart: 1,
      todayBtn:  1,
      autoclose: 1,
      todayHighlight: 1,
      startView: 2,
      minView: 2,
      forceParse: 0
    });
  </script>

  <script type="text/javascript">
    $(function() {
      $('#grupoAtributos').hide();

      $('#abbrev').val("");
      $('#abbre').val("");
        $("#state").autocomplete({
            source: "bus_clientes.php",
            //minLength: 1,
            select: function(event, ui) {
              //$('#state_id').val(ui.item.id);
              $('#abbrev').val(ui.item.abbrev);
              $('#abbre').val(ui.item.abbre);
            },change: function (event, ui) {
              if (!ui.item) {
                  this.value = '';
                  $('#abbrev').val('');
                  $('#abbre').val('');
              }
            }
          });
        $("#state_abbrev").autocomplete({
            source: "bus_nomcli.php",
            minLength: 1
        });
        //});

		$("#mnocliente").autocomplete({
			appendTo : "#modalPredio",
			  source: "bus_clientes.php",
			  select: function(event, ui) {

				$('#mnombre').val(ui.item.abbrev);
				$('#mnoclientea').val(ui.item.cliente_crm);
				$('#mnoclienteah').val(ui.item.cliente_crm);
				$("#wrapper").LoadingOverlay("show");
				if(ui.item.cliente_crm != "") {
				  $.ajax({
					  url:"php/procesaa.php",
					  type: "POST",
					  data:"clienteno="+ui.item.cliente_crm,
					  success: function(opciones){
						$("#mpredio").html(opciones);
						$("#wrapper").LoadingOverlay("hide");
					  },
					  error: function (xhr, ajaxOptions, thrownError) {
										alert(xhr.status);
										alert(thrownError);
										$("#wrapper").LoadingOverlay("hide");
					  }

					});
				} else {
					alert("Sin registro de cliente anterior.");
					$("#wrapper").LoadingOverlay("hide");
				}

			  },change: function (event, ui) {
				  if (!ui.item) {
					  this.value = '';
					  $('#mnombre').val('');
					  $('#mnoclientea').val('');
					  $('#mnoclienteah').val('');
					  $("#mpredio").html('<option value="0"> Elige un Predio o Vivero</option>');

				  }

				}
				}).keypress(function(e) {
				  if (e.keyCode === 13){
					return false;
				}
		  });

		$('#mpredio').change(function() {
			// $(this).val() will work here
			valsel = $(this).val()

			if(valsel != 0){
			  $("#wrapper").LoadingOverlay("show");
			  //alert($(this).serialize());
				//event.preventDefault();
				$.ajax({
				  url: "consultaa.php",
				  type: "POST",
				  data: $("#formPredio").serialize(),
				  //dataType: "html"
				}).done(function(response){
					$("#mresultado").html(response);
					$("#wrapper").LoadingOverlay("hide");
				}).fail(function(jqXHR, textStatus){
					console.log(textStatus);
				});
			} else
				$("#mresultado").html('');
		});

        $("#noasociado").autocomplete({
                source: "bus_clientes.php",
                select: function(event, ui) {
                  $('#nomasociado').val(ui.item.abbrev);
                  //$('#abbre').val(ui.item.abbre);
                  $("#wrapper").LoadingOverlay("show");
                    $.ajax({
                    url:"php/procesa.php",
                    type: "POST",
                    data:"clienteno="+ui.item.value,
                    success: function(opciones){
                      $("#predios").html(opciones);
                      $("#wrapper").LoadingOverlay("hide");
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                                      alert(xhr.status);
                                      alert(thrownError);
                                      $("#wrapper").LoadingOverlay("hide");
                    }

                  });

                },change: function (event, ui) {
                    if (!ui.item) {
                        this.value = '';
                        $('#abbrev2').val('');
                        //$('#abbre').val('');
                        $("#criterio").html('<option value="0"> Elige un Predio o Vivero</option>');

                    }

                  }
            }).keypress(function(e) {
              if (e.keyCode === 13){
                return false;
            }
          });

          $("#state_abbrev2").autocomplete({
            source: "bus_nomcli.php",
            minLength: 1
          });

            $('.btn-group-sel a').on('click', function(){
                var sel = $(this).data('title');
                var tog = $(this).data('toggle');
                //alert("TOG:"+tog + " -- SEL:"+sel);
                btnCambios(sel, tog);

            });

            function btnCambios(sel, tog) {
                //alert("TOG:"+tog + " -- SEL:"+sel);
                $('#'+tog).prop('value', sel);
                $('a[data-toggle="'+tog+'"]').not('[data-title="'+sel+'"]').removeClass('active').addClass('notActive');
                $('a[data-toggle="'+tog+'"][data-title="'+sel+'"]').removeClass('notActive').addClass('active');
            }

          $("#nopredio").autocomplete({
            source:  function(request, response) {
                $.getJSON(
                    "php/procesa.php",{ // consulta a esa pagina
                        term:request.term // parametros extras
                    },
                    response
                );
            },
            select: function(event, ui) {
              reg = ui.item;
              $('#nomasociado').val(reg.nombrec);
              $('#abbre').val(reg.paraje);
              $("#wrapper").LoadingOverlay("show");

              tipo = (reg.tipo == 1)?'Predio':'Vivero';
              criterio = '<option value="'+reg.value+'-'+reg.tipo+'">'+reg.paraje+' ('+reg.value+'-'+tipo+')<\/option>';
              $("#predios").html(criterio);
              $("#noasociado").val(reg.nocliente);
              $("#wrapper").LoadingOverlay("hide");

              buscaPredio();
            },change: function (event, ui) {
                //alert(ui);
                if (!ui.item) {
                    this.value = '';
                    $('#nomasociado').val('');
                    $('#abbre').val('');
                    $("#predios").html('<option value="0"> Elige un Predio o Vivero</option>');

                }

            }
          }).keypress(function(e) {
                if (e.keyCode === 13){
                  return false;
              }
          });

        $( "#predios" ).change(function() {
            buscaPredio();
        });

        //$("#formbuscar").on("submit", function(event){
        function buscaPredio() {
          if($("#noasociado").val()!="" || $("#nopredio").val()!=""){
            if($("#predios option:selected").attr('value')!=0){
              $("#wrapper").LoadingOverlay("show");
              event.preventDefault();
              $.ajax({
                  url: "consultaPredio.php",
                  type: "POST",
                  dataType: "json",
                  data: $("#formbuscar").serialize(),
                  //dataType: "html"
              }).done(function(response){
                    console.log(response);
                    $("#resultado").html(response.mensaje);
                    $("#wrapper").LoadingOverlay("hide");
                    $('#grupoAtributos').show();

                    atributos = response.atributos;
                    console.log(atributos);

                    // Reiniciar elementos
                    for(i=0; i< 9; i++) {
                        $('#txt'+i).val('');
                        //$('#indicador'+i).bootstrapToggle('off');
                        //tog = $('#indicador'+i);
                        tog = 'indicador'+i;
                        btnCambios("", tog);
                    }
                    // txt indicador
                    $.each( atributos, function( key, value ) {
                        $('#txt'+key).val(value.observaciones);
                        tog = 'indicador'+key;
                        //alert("sel: "+value.valor+" -- tog:" + tog);
                        btnCambios(value.valor, tog);
                        /*if(value.valor == 1 || value.valor == 10)
                            $('#indicador'+key).bootstrapToggle('off');//$('#indicador'+key).prop('checked', false);
                        else if(value.valor == 0 || value.valor == 5)
                            $('#indicador'+key).bootstrapToggle('on');//$('#indicador'+key).prop('checked', true);
                        */
                    });
              }).fail(function(jqXHR, textStatus){
                  console.log(textStatus);
              });
            }else{
              alert("Falta predio/vivero");
              return false;
            }
          }else{
            alert("Falta numero de asociado");
            return false;
          }
        //});
        }


    });

    $(".GuardaI").click(function(){
        nomId = this.id;
        tam = nomId.length;
        valId = nomId.substr((tam-1), 1);
        //
        swal({
            title: "<h3>¿Está seguro de Guardar los Datos?</h3><br><p>Esta acción no se puede deshacer</p>",
            showCancelButton: true,
            confirmButtonColor: "#5cb85c",
            confirmButtonText: "¡Sí, Guardar datos!",
            cancelButtonText: "¡Cancelar!",
            content: "input",
            closeOnConfirm: false,
            closeOnCancel: false,
            //showLoaderOnConfirm: true,
            allowEscapeKey: false,
            html: true
        },
        function(isConfirm) {
            if (isConfirm) {
                //swal("Aprobando Informe...");
                $.ajax({
                    type: "POST",
                    url: "php/procesa.php",
                    contentType: "application/x-www-form-urlencoded;charset=UTF-8",
                    data: $("#formbuscar").serialize()+"&funcion=guardarAtributosI&valID="+valId+"&idUs="+clvuser ,
                    datatype: 'json',
                    success: function(response) {
                        response = JSON.parse(response);
                        txtr = response.text;
                        if(response.status=="1"){
                          // TERMINANDO APROBACIÓN
                          swal("Realizado",response.text, "success");
                          /*$('#tablaInformes').bootstrapTable('refresh');
                          reiniciar();
                          // CAMBIAR PESTAÑA
                          $('#tabs a[href=#tabMostrar]').tab('show');*/
                        } else
                          swal("Error",response.text, "error");
                        //return true;
                    },
                    beforeSend: function() {
                    }
                });
            } else {
                swal("Operación cancelada.", "Informe no Editado");
                return false;
            }
        });
    });

    $("#btnGuardarA").click(function(){
        //alert($(this).serialize());
        moreinfo = "";
        $('input[type=checkbox]').each(function() {
            if (!this.checked)
                moreinfo += '&'+this.name+'='+$(this).attr("data-off");
            else
                moreinfo += '&'+this.name+'='+$(this).attr("data-on");
        });
        //alert(moreinfo);
        //
        swal({
            title: "<h3>¿Está seguro de Guardar los Datos?</h3><br><p>Esta acción no se puede deshacer</p>",
            showCancelButton: true,
            confirmButtonColor: "#5cb85c",
            confirmButtonText: "¡Sí, Guardar datos!",
            cancelButtonText: "¡Cancelar!",
            content: "input",
            closeOnConfirm: false,
            closeOnCancel: false,
            //showLoaderOnConfirm: true,
            allowEscapeKey: false,
            html: true
        },
        function(isConfirm) {
            if (isConfirm) {
                //swal("Aprobando Informe...");
                $.ajax({
                    type: "POST",
                    url: "php/procesa.php",
                    contentType: "application/x-www-form-urlencoded;charset=UTF-8",
                    data: $("#formbuscar").serialize()+"&funcion=guardarAtributos"+moreinfo+"&idUs="+clvuser,
                    datatype: 'json',
                    success: function(response) {
                        response = JSON.parse(response);
                        txtr = response.text;
                        if(response.status=="1"){
                          // TERMINANDO APROBACI�N
                          swal("Realizado",response.text, "success");
                          /*$('#tablaInformes').bootstrapTable('refresh');
                          reiniciar();
                          // CAMBIAR PESTA�A
                          $('#tabs a[href=#tabMostrar]').tab('show');*/
                        } else
                          swal("Error",response.text, "error");
                        //return true;
                    },
                    beforeSend: function() {
                    }
                });
            } else {
                swal("Operación cancelada.", "Informe no Editado");
                return false;
            }
        });
    });
  </script>

  <script type="text/javascript">
    $(function() {
      $('#nombrepre').val("");
      $("#num").autocomplete({
        source: "bus_predionum.php",
        //minLength: 1,
        select: function(event, ui) {
          //$('#state_id').val(ui.item.id);
          $('#divMSP').hide();
          $('#divMS').hide();
          $('#divMP').hide();
          $('#cboxMSP').bootstrapToggle('off');
          $('#nombrepre').val(ui.item.nombrepre);
          $('#clientep').val(ui.item.clientep);
          $('#superficieM').val(ui.item.superficie);

         if(ui.item.maguey_con_registro == 1 || ui.item.poligono == 1){
          $('#divMSP').show();
          }
        },

      change: function(event, ui) {
            if (!ui.item) {
                this.value = '';
                $('#nombrepre').val('');
                $('#clientep').val('');
                $('#superficieM').val('');
                $('#divMSP').hide();
                $('#divMS').hide();
                $('#divMP').hide();
                $('#cboxMSP').bootstrapToggle('off');
            }
        }

      });
      $("#num_nombrepre_clientep").autocomplete({
        source: "bus_nompredio.php",
        minLength: 1
      });
    });
  </script>

  <script type="text/javascript">
    function ArregloMaguey() {
      var myTableArray = [];
      $("table#grilla").find("tbody tr").each(function() {
        var arrayOfThisRow = [];
        var tableData = $(this).find('td');
        if (tableData.length > 0) {
          tableData.each(function() { arrayOfThisRow.push($(this).text()); });
          myTableArray.push(arrayOfThisRow);
        }
      });
      return myTableArray;
    }
  </script>

  <script type="text/javascript">
  $(document).ready(function(){
    $("#btnTerminar").click(function(){
  		if($('#archivo').val() != "") {
    			archivo = $('#archivo')[0].files[0].name;
          ext = $('#archivo')[0].files[0].type;
    			arrar = archivo.split('.');
    			formatoimg = arrar[(arrar.length)-1];
  		}

      // IMÁGENES UBICACIÓN
      ext1 = ""; 
      if($('#imagen1').val() != "") {
          archivo = $('#imagen1')[0].files[0].name;
          ext1 = $('#imagen1')[0].files[0].type;
      }
      ext2 = "";
      if($('#imagen2').val() != "") {
          archivo = $('#imagen2')[0].files[0].name;
          ext2 = $('#imagen2')[0].files[0].type;
      }    

    //$("#cboxMCR").val(1);
      if( $('#archivo').val() == "" || (archivo != "" && (formatoimg == "png" || formatoimg == "jpg" || formatoimg == "jpeg" || formatoimg == "pdf") ) ) { // 13
          if( (ext1 == "" || ext2 == "") || (ext1 != "" && ext1 == "image/jpeg") || (ext2 != "" && ext2 == "image/jpeg") ) { // 12
  			      if($("#state").val()!=""){ // 11
  			          if($("#paraje").val()!=""){ // 10
  				            if($("#estado option:selected").attr('value')!="") { // 9
                				  if($("#municipio option:selected").attr('value')!="") { // 8
                      				  if($("#local option:selected").attr('value')!="") { // 7
                      					    if($("#lat").val()!=""){ // 6
                          					    if($("#lng").val()!=""){ // 5
                                					  if($("#cboxMCR").val()!="NS") { // 4
                                						  if( $("#cboxMCR").val()==1 || $("#cboxMCR").val()==2 ){ // 3
                                                  if( $("#cboxMCR").val()==2  && $("#SelServicio").val()==""){ // 2
                                                      alert("Seleccione un tipo de servicio.");
                                                      return false;
                                                  } else {
                                						
                                        							if(ArregloMaguey()!=""){ // 1
                                        								$("#wrapper").LoadingOverlay("show");
                                        								var cboxGuia = ($("#cboxGuia").prop('checked')==true)?1:0;
                                        								var cboxMCR  =  $("#cboxMCR").val();
                                                        var SelServicio = $("#SelServicio").val();
                                        								var datos = new FormData($('#maguey')[0]);
                                        								datos.append('archivo',$('#archivo')[0].files[0]);
                                        								datos.append('poligono',$('#poligono')[0].files[0]);
                                        								datos.append('tMaguey',JSON.stringify(ArregloMaguey()));
                                        								datos.append('cboxGuia',cboxGuia);
                                        								datos.append('cboxMCR',cboxMCR);
                                                        datos.append('SelServicio',SelServicio);
                                                        datos.append('idus', clvuser);

                                        								swal({
                                        									title: "<h3>¿Está seguro de Guardar los Datos?</h3>",
                                        									showCancelButton: true,
                                        									confirmButtonColor: "#5cb85c",
                                        									confirmButtonText: "¡Sí, Guardar los Datos!",
                                        									cancelButtonText: "¡No Guardar!",
                                        									closeOnConfirm: false,
                                        									closeOnCancel: false,
                                        									showLoaderOnConfirm: true,
                                        									allowEscapeKey: false,
                                        									html: true
                                        								},
                                        								function(isConfirm) {
                                        									if (isConfirm) {
                                        										swal("Guardando Datos...");
                                        										$.ajax({
                                        											  async: false,
                                        											  type: "POST",
                                        											  url: "guardar.php",
                                        											  data:datos,
                                        											  cache: false,
                                        											  processData: false,
                                        											  contentType:false,
                                        											  success:function(response) {
                                            												$("#wrapper").LoadingOverlay("hide");
                                            												//alert(response);
                                            												swal("Predios", response, "success");
                                            												location.reload();
                                        											  },
                                        											  error: function (xhr, ajaxOptions, thrownError) {
                                        												//alert(xhr.status);
                                        												//alert(thrownError);
                                        												    $("#wrapper").LoadingOverlay("hide");
                                        											  }
                                        										});
                                        									} else {
                                        										swal("Operación cancelada.", "Registro no guardado. ");
                                        										$("#wrapper").LoadingOverlay("hide");
                                        										return false;
                                        									}
                                        								});
                                        							}else{
                                        							  alert("No has agregado datos del maguey");
                                        							  return false;
                                        							} // 1
                                                  } // 2
                                						  } else{
                                    						  alert("Seleccione un tipo de registro");
                                    						   return false;
                                						  } // 3

                                				    }else{
                                						  alert("Selecione el tipo de Registro de Maguey ");
                                						   return false;
                                					  } // 4

                            						}else{
                            						  alert("No ha introducido una longitud ");
                            						  return false;
                            						} // 5
                        					  } else {
                        						alert("No ha introducido una latitud ");
                        						return false;
                        					  } // 6
                      					} else {
                      					  alert("No ha seleccionado una localidad");
                      					  return false;
                      					} // 7
                				  } else {
                    					alert("No ha seleccionado un municipio");
                    					return false;
                				  } // 8
              				} else {
              				  alert("No ha seleccionado un estado");
              				  return false;
              				} // 9
          			  } else {
              				alert("Falta nombre del predio");
              				return false;
          			  } // 10
        			} else {
          			  alert("Falta numero de asociado");
          			  return false;
        			} // 11
          } else {
              alert("Sólo se aceptan archivos con formato jpg, jpeg, png ó pdf para el comprobante; y sólo jpg ó jpeg para las imágenes.");
              return false;
          } // 12
  		} else {
          alert("Sólo se aceptan archivos con formato jpg, jpeg, png ó pdf para el comprobante; y sólo jpg ó jpeg para las imágenes.");
          return false;
      } // 13
    });
  });
  </script>

  <script type="text/javascript">
    function ArregloMagueys() {
      var myTableArray = [];
      $("table#grillas").find("tbody tr").each(function() {
        var arrayOfThisRow = [];
        var tableData = $(this).find('td');
        if (tableData.length > 0) {
          tableData.each(function() { arrayOfThisRow.push($(this).text()); });
          myTableArray.push(arrayOfThisRow);
        }
      });
      return myTableArray;
    }
  </script>

  <script type="text/javascript">
    $(document).ready(function(){
      $("#btnTerminars").click(function(){
        if($("#num").val()!=""){
          if($("#nombrepre").val()!=""){
            if(ArregloMagueys()!=""){
              $("#wrapper").LoadingOverlay("show");
              var cboxGuiaM= ($("#cboxGuiaM").prop('checked')==true)?1:0;
              var cboxMSP= ($("#cboxMSP").prop('checked')==true)?1:0;
              /*var datos={'tMagueys': JSON.stringify(ArregloMagueys())};
              datos = $("#magueys").serialize() + "&" + $.param(datos) + "&cboxGuiaM=" + cboxGuiaM;*/

              var datos = new FormData($('#magueys')[0]);
              datos.append('poligonoM',$('#poligonoM')[0].files[0]);
              datos.append('tMagueys',JSON.stringify(ArregloMagueys()));
              datos.append('cboxGuiaM',cboxGuiaM);
              datos.append('cboxMSP',cboxMSP);

              $.ajax({
                async: false,
                type: "POST",
                url: "guardarm.php",
                data:datos,
                cache: false,
                processData: false,
                contentType:false,
                success:function(response) {
                  $("#wrapper").LoadingOverlay("hide");
                  alert(response);
                  location.reload();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $("#wrapper").LoadingOverlay("hide");
                    alert(xhr.status);
                    alert(thrownError);
                }
              });
            }else{
              alert("No has agregado maguey");
              return false;
            }
          }else{
            alert("Numero de predio erroneo");
            return false;
          }
        }else{
          alert("Ingrese un numero de predio");
          return false;
        }
      });
    });
  </script>

  <script type="text/javascript">
    $(function() {
      $('#abbre').val("");
      $("#stat").autocomplete({
        source: "bus_especie.php",
        minLength: 1,
        select: function(event, ui) {
          $('#stat_id').val(ui.item.id);
        }
      });
    });
  </script>

  <script>
    $("#estado").on("change", buscarMunicipios);
    $("#municipio").on("change", buscarLocalidades);
    function buscarMunicipios(){
      $("#btn_nueva_localidad").hide();
      $("#local").html("<option value=''>- primero seleccione un municipio -</option>");
      $estado = $("#estado").val();
      if($estado == ""){
        $("#municipio").html("<option value=''>- primero seleccione un estado -</option>");
      }
      else {
        $.ajax({
          dataType: "json",
          data: {"estado": $estado},
          url:   'comboeml/buscar.php',
          type:  'post',
          beforeSend: function(){
            //Lo que se hace antes de enviar el formulario
            },
          success: function(respuesta){
            //lo que se si el destino devuelve algo
            $("#municipio").html(respuesta.html);
          },
          error:  function(xhr,err){
            alert("readyState: "+xhr.readyState+"\nstatus: "+xhr.status+"\n \n responseText: "+xhr.responseText);
          }
        });
      }
    }
    function buscarLocalidades(){
      $municipio = $("#municipio").val();
      $.ajax({
        dataType: "json",
        data: {"municipio": $municipio},
        url: 'comboeml/buscar.php',
        type:  'post',
        beforeSend: function(){
          //Lo que se hace antes de enviar el formulario
        },
        success: function(respuesta){
          $("#btn_nueva_localidad").show();
          $("#local").html(respuesta.html);
        },
        error:  function(xhr,err){
        alert("readyState: "+xhr.readyState+"\nstatus: "+xhr.status+"\n \n responseText: "+xhr.responseText);
        }
      });
    }

          $('#especie').change(function() {

      var especie=this.value;
        $.ajax({

      type: "POST",
      url: "verificar_especie.php",
      contentType: "application/x-www-form-urlencoded;charset=UTF-8",
      data: "especie="+especie,
      datatype: 'json',
      success: function(response){
        //alert(response);
        var j_existe=JSON.parse(response);
        if(j_existe.status=='correcto')
        {
          if(j_existe.valido=='no')
          {
            alert("La especie que está seleccionando ya no se encuentra dentro de nuestro catálogo.");
          }

        }

        else
        {
          alert("ocurrio un error al consultar especie")
        }
      },
      beforeSend:function()
      {

         $("#add_err").html("Loading...");
      }
       });
        //Todo el código aqui
      } );

    //------------------------ FIN VALIDAR ESPECIA----------------------

	function editarPredio(idParaje) {
        //alert("Edit ");
        $.ajax({
            type: "POST",
            url: "php/loadPredios.php",
            contentType: "application/x-www-form-urlencoded;charset=UTF-8",
            data: {
                tipo: "buscarDPredio",
                idParaje: idParaje,
            },
            datatype: 'json',
            success: function(response) {
                response = JSON.parse(response);
                //console.log(response.rows);
                //alert(response.exito);
                row = response.rows;
                if(response.exito=="si"){
                  //swal("Procesado !", "El Registro ha sido eliminado", "success");
                  //$('#tablaPredios').bootstrapTable('refresh');
                  // id, state, abbrev
                  //alert(response.rows.id_paraje);
                  $("#id").val(row.id_paraje);
                  $("#state").val(row.id_cliente);
                  $("#abbrev").val(row.nombrep);

                  //$("#referencia1").val(row.nombrep);
                  $("#fecha").val(row.fecha);
                  $("#paraje").val(row.paraje);
                  $("#referenciau").val(row.referencia);
                  $("#estado").val(row.mestado);
                  buscarMunicipios();
                  $("#municipio").val(row.municipio);
                  $("#local").val(row.local);

                  $("#tenencia").val(row.tenencia);
                  $("#usufruto").val(row.usufruto);
                  $("#superficie").val(row.superficie);
                  $("#lat").val(row.lat);
                  $("#lng").val(row.lng);

                  $("#cboxMCR").val(row.maguey_con_registro);
                  $("#campo").val(row.rcampo);
                  //$("#status_predio").val(row.rcampo);
                }else{
                  swal("Error","Ha ocurrido un error interno", "error");
                }
            },
            beforeSend: function() {
                console.log("Send");
            }
        });
    }
  </script>

   <script>
    var map;
    function initialize() {
      var mapOptions = {
        center: new google.maps.LatLng(23.6266557,-102.5377501),
        mapTypeId: google.maps.MapTypeId.ROADMAP
      };
      map = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);
      map.setZoom(5);
    }
    var image = 'iconocrm.png';
    google.maps.event.addDomListener(window, 'load', initialize);
    function pan() {
      var panPoint = new google.maps.LatLng(document.getElementById("lat").value, document.getElementById("lng").value);
      map.panTo(panPoint)
      //map.setZoom(17);
      map.setZoom(13);
      var marker = new google.maps.Marker({
        //icon: 'map.png',
        position: panPoint,
        map: map,
        animation: google.maps.Animation.DROP,
        icon: image
      });
    }
  </script>

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
