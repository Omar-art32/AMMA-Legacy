<?php
  session_start();
  session_set_cookie_params(0, "/", $_SERVER["HTTP_HOST"], 0);
  require_once('../common/cfg_server.php');
  $d_s=$_GET['d_s'];
  if(isset($_SESSION[$d_s]) && $_SESSION[$d_s]["seccion_4_1"] == "logged")
  {
  require_once "comboeml/funciones.php";
  require_once("bus_especie.php");
  //require_once "idparaje.php";
  require_once("sumaranio.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIGCE</title>
  <link rel="icon" type="image/ico" href="../favicon.ico?2" />
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
  <script language="javascript" type="text/javascript" src="js/script.js?0009"></script> <!-- boton agregar tab1 -->
  <script type="text/javascript" src="js/magueys.js?0003"></script> <!-- boton agregar tab2 -->
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
    	obtenerId('P');

    	$('#tipoPla').change(function() {
	        var referencia = ($("#tipoPla").prop('checked')==false)?1:0;
	        if (referencia==1){
	        	$("#opcPredio").css("display", "none");
	        	$("#opcVivero").css("display", "block");
	        	obtenerId('V');
	        }else{
	        	$("#opcPredio").css("display", "block");
	        	$("#opcVivero").css("display", "none");
	        	obtenerId('P');
	        }
	    });

    	function obtenerId(tipo) {
	    	$.ajax({
				url:"idparaje.php",
				type: "POST",
				data:"tipo="+tipo,
				success: function(opciones){
					opciones = JSON.parse(opciones);
					//alert(opciones.id+"::"+tipo);
					if(tipo == "P")
						$("#id").val(opciones.id);
					else if(tipo == "V")
						$("#vid").val(opciones.id);
					//$("#wrapper").LoadingOverlay("hide");
				},
				error: function (xhr, ajaxOptions, thrownError) {
					alert(xhr.status);
					alert(thrownError);
					$("#wrapper").LoadingOverlay("hide");
			  	}
			});
    	}


    	// PREDIO 
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

	    // VIVERO
	    $('#referencia1v').change(function() {
			var referencia = ($("#referencia1v").prop('checked')==false)?1:0;
			if (referencia==1){
				$("#formularioreferenciav").css("display", "none");
			}else{
				$("#formularioreferenciav").css("display", "block");
			}
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
			if ( $('#municipio').val()!="" ) {
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
		        } else
			        alert("No ha escrito una localidad");
			} else
			    alert("No ha seleccionado un minicipio");
		    return false;
	  	});


	    //$('#cboxGuia').bootstrapToggle('off');
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
	      	} else{
                $('#divMS').hide();
                $('#divMP').hide();
	      	}
	    });


	    // VIVERO 
	    $("#vBtnNuevaLocalidad").click(function() {
		  	if ( $('#vmunicipio').val()!="" ) {
		    	if ( $('#vTxtNuevaLocalidad').val()!="" ){
		     		var localidad = $('#vTxtNuevaLocalidad').val();
				    var id_municipio = $('#vmunicipio').val();
				    console.log(localidad);
				    console.log(id_municipio);
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
			        $('#vTxtNuevaLocalidad').val("");
			        $("#vModalRutaNueva").modal("hide");
			        buscarLocalidades();
		        } else
		        	alert("No ha escrito una localidad");
		    } else
		    	alert("No ha seleccionado un minicipio");
		    return false; 
	  	});

    });

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
            <h3 class="page-header text-white">REGISTRO DE PLANTACIONES</h3>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12" style="margin-top:20px;">
            <div class="panel  panel-che" style="max-width:1100px; margin-left: auto !important; margin-right:auto !important; margin-bottom:75px;">
              <div class="panel-heading"></div>
              <div class="panel-body">
                <ul class="nav nav-tabs" role="tablist">
                  <li role="user-data" class="active"><a href="#tab0success" aria-controls="changePassword" role="tab" data-toggle="tab"><span class="fa fa-list-alt"></span> Listado de Predios</a> </li>
                  <li role="user-data" class=""><a href="#tab1success" aria-controls="changePassword" role="tab" data-toggle="tab"><span class="fa fa-list-alt"></span> Registro de Plantaciones</a></li>
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
                        <div class="panel-heading" style="text-align:center; font-size:16px; font-weight:bold;">Registro de Plantaciones
                        </div>

                        <style>
						  .slow .toggle-group { transition: left 0.7s; -webkit-transition: left 0.7s; }
						  .fast .toggle-group { transition: left 0.1s; -webkit-transition: left 0.1s; }
						  .quick .toggle-group { transition: none; -webkit-transition: none; }
						</style>
                        <div class="panel-body form-horizontal">
	                        <div class="form-group">
	                          <label class="col-md-3 col-sm-3 col-xs-3 control-label">Tipo:</label>
	                          <div class="col-md-4 col-sm-4 col-xs-4">
	                            <input class="form-control" type="checkbox" id="tipoPla" name="tipoPla" checked data-toggle="toggle" data-size="small" data-onstyle="success" data-on="Predio" data-off="Vivero" data-offstyle="primary" data-width="100" data-style="slow">
	                          </div>
	                          <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label"></label>
	                        </div>
	                    </div>

                        <div class="panel-body" id="opcPredio">
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
                                 <input type="text" name="id" id="id" disabled="disabled" class='form-control txt-short'  >
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
                                <input type="number" id="superficie" name="superficie" class='form-control txt-largo'/>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">Latitud Norte:</label>
                              <div class="col-md-4 col-sm-4 col-xs-4">
                                <input type="number" id="lat" name="lat" class='form-control txt-largo' />
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">Longitud Oeste:</label>
                              <div class="col-md-4 col-sm-4 col-xs-4">
                                <input type="number" id="lng" name="lng" class='form-control txt-largo' />
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
                            <div class="form-group">
                                <label for="cboxGuia" class="col-md-3 col-sm-3 col-xs-3 control-label"> Guías a generar: </label>
                                <div class="col-md-4 col-sm-4 col-xs-4">
                                  <select class="form-control" id="cboxGuia" name="cboxGuia" required="">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                  </select>
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

                            <!--<div class="btn-group" role="group" aria-label="...">
                              <button type="button" class="btn btn-default">1</button>
                              <button type="button" class="btn btn-default">2</button>
                              <button type="button" class="btn btn-default">1</button>
                              <button type="button" class="btn btn-default">2</button>
                            </div>-->

                              
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
                                  <input type="number" id="sc" name="sc" class='form-control txt-largo' />
                                </div>
                              </div>
                              <div class="form-group">
                                <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">Distancia (Plantas):</label>
                                <div class="col-md-4 col-sm-4 col-xs-4">
                                  <input type="number" id="sm" name="sm" class='form-control txt-largo' />
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
                                  <input type="number" id="plantas" name="plantas" class='form-control txt-largo'/>
                                </div>
                              </div>
                              <div class="form-group">
                                <label for="maguey" class="col-md-3 col-sm-3 col-xs-3 control-label">Edad:</label>
                                <div class="col-md-4 col-sm-4 col-xs-4">
                                  <input type="number" id="edad" name="edad" class='form-control txt-largo' />
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
                                    <th style='visibility:collapse; display:none;' >EspecieN</th>
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

                        <!-- ------------------------------------------------------------------------------------------------------------------------ -->
                        <!-- ------------------------------------------------------------------------------------------------------------------------ -->
                        <!-- ------------------------------------------------------------------------------------------------------------------------ -->

                        <div class="panel-body" id="opcVivero" style="display: none;">
							<form class="form-horizontal" id="vivero" action="" method="POST" name="vivero" enctype="multipart/form-data" >
							    <div class="form-group">
							       <input class="form-control" type="hidden" name='usr' id='usr' value='<?php echo $_SESSION[$d_s]['s_username']; ?>'/>
							    </div>
							    <fieldset>
							      <legend align="">Datos del Productor</legend>
							    </fieldset>
							    <div class="form-group">
							       <label for="maguey" class="col-lg-3 control-label">No. Vivero:</label>
							      <div class="col-md-3">
							        <input type="text" name="vid" id="vid" disabled="disabled"  class='form-control txt-short' >
							      </div>
							    </div>
							    <div class="form-group">
							      <label for="maguey" class="col-lg-3 control-label">No. de Control:</label>
							      <div class="col-md-3">
							        <input type="text" id="vstate"  name="vstate" class='form-control txt-short' />
							      </div>
							    </div>
							    <div class="form-group">
							      <label for="maguey" class="col-lg-3 control-label">Nombre:</label>
							      <div class="col-md-4">
							        <input readonly type="text" id="vabbrev" name="vabbrev" class='form-control'/>
							      </div>
							    </div>
							    <div>
							      <legend align="">Datos del Vivero</legend>
							    </div>
							    <div class="form-group">
							    	<label class="col-md-3 control-label">Propietario:</label>
							      	<div class="col-md-4">
							        	<input class="form-control" type="checkbox" id="referencia1v" name="referencia1v" data-size="small" data-toggle="toggle" data-size="normal" data-onstyle="success" data-on="Si" data-off="No" data-offstyle="danger">
							     	</div>
							      	<label for="maguey" class="col-md-3 control-label"></label>
							    </div>
							    <div class="form-group" id="formularioreferenciav" style="display: none;">
							      <label for="maguey" class="col-lg-3 control-label">Nombre completo:</label>
							      <div class="col-md-4">
							          <input type="text" name="vreferencia2" id="vreferencia2" class="form-control" onblur="vupperCase()">
							      </div>
							    </div>
							    <div class="form-group">
							      <label for="maguey" class="col-lg-3 control-label">Fecha de Registro:</label>
							      <div class="col-md-4">
							        <div class="input-group date form_date" data-date="" data-date-format="mm/dd/yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
							            <input class="form-control" size="16" type="text" value="" name="vfecha"  id="vfecha" readonly/>
							            <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
							            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
							        </div>
							      </div>
							    </div>
							    <div class="form-group">
							    	<label for="maguey" class="col-lg-3 control-label">Nombre del Vivero:</label>
							      <div class="col-md-4">
							        <input type="text" id="vparaje" name="vparaje" class='form-control txt-largo' />
							      </div>
							    </div>
							    <div class="form-group">
							      <label for="maguey" class="col-lg-3 control-label">Referencia de Ubicación:</label>
							      <div class="col-md-4">
							        <input class="form-control" type="text" id="vreferenciau" name="vreferenciau" onblur="vupperCase()" />
							      </div>
							    </div>
							    <div class="form-group">
							      <label for="maguey" class="col-lg-3 control-label">Estado:</label>
							      <div class="col-md-4">
							        <select class="form-control" name="vestado" id="vestado" />
							          <option value="">- Seleccione un Estado -</option>
							          <?php
							            $estados = dameEstado();
							            foreach($estados as $indice => $registro){
							              echo "<option value=".$registro['clave'].">".$registro['nombre']."</option>";
							            }
							          ?>
							        </select>
							      </div>
							    </div>
							    <div class="form-group">
							      <label for="maguey" class="col-lg-3 control-label">Municipio:</label>
							      <div class="col-md-4">
							        <select class="form-control" name="vmunicipio" id="vmunicipio">
							          <option value="">- primero seleccion un estado -</option>
							        </select>
							      </div>
							    </div>
							    <div class="form-group">
							      <label for="maguey" class="col-lg-3 control-label">Localidad:</label>
							      <div class="col-md-4" align="right">
							        <select class="form-control" name="vlocal" id="vlocal">
							          <option value="">- primero seleccion un municipio -</option>
							        </select>
							        <a href="#" id="vbtn_nueva_localidad" class="text-success"><span class="glyphicon glyphicon-plus"></span><!-- <img src="images/add.png"/> --></a>
							      </div>
							    </div>
							    <div  class="modal fade" id="vModalRutaNueva"  title="Nueva Localidad"   role="dialog" aria-hidden="true">
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
	                                        <input class="form-control" type="text" name="vTxtNuevaLocalidad" id="vTxtNuevaLocalidad" size="40">
	                                      </div>
	                                    </div>
	                                  </div>
	                                  <div class="modal-footer">
	                                    <button type="button" class="btn btn-primary" id="vBtnNuevaLocalidad">Aceptar</button>
	                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
	                                  </div>
	                                </div>
	                              </div>
	                            </div>
							    <div class="form-group">
							      <label for="maguey" class="col-lg-3 control-label">Latitud Norte:</label>
							      <div class="col-md-4">
							        <input type="text" id="vlat" name="vlat" class='form-control txt-largo' />
							      </div>
							    </div>
							    <div class="form-group">
							      <label for="maguey" class="col-lg-3 control-label">Longitud Oeste:</label>
							      <div class="col-md-4">
							        <input type="text" id="vlng" name="vlng" class='form-control txt-largo' />
							      </div>
							    </div>
							    <div class="form-group">
							      <label for="maguey" class="col-lg-3 control-label">Encargado:</label>
							      <div class="col-md-4">
							        <input type="text" id="vcampo" name="vcampo" class='form-control txt-largo' onblur="vupperCase()"/>
							      </div>
							    </div>
							    <div class="form-group">
							      <label for="maguey" class="col-lg-3 control-label">Foto 1(Plantas):</label>
							      <div class="col-md-4">
							      	 <span class="btn btn-default btn-file">
						                <input class="" type="file" id="vfoto1" name="vfoto1" onchange="verificarArchivo(this)" accept="image/jpeg">
						            </span>
							      </div>
							    </div>
							    <div class="form-group">
							      <label for="maguey" class="col-lg-3 control-label">Foto 2(Plantas):</label>
							      <div class="col-md-4"> 
							      	<span class="btn btn-default btn-file">
						                <input class="" type="file" id="vfoto2" name="vfoto2" onchange="verificarArchivo(this)" accept="image/jpeg">
						            </span>
							      </div>
							    </div>
							    <!--<div class="form-group">
							      <label for="status_predio" class="col-lg-3 control-label">Estatus Vivero:</label>
							      <div class="radio">
							        <label><input type="radio" id="predio_activo" name="status_predio" value="1"> Mostrar</label>
							        <label><input type="radio" id="predio_inactivo" name="status_predio" value="0"> Ocultar</label>
							      </div>
							    </div>-->
							    <!-- FOTOS DEL MAPA -->
							    <div class="form-group">
							      <label for="maguey" class="col-lg-3 control-label">Foto 3(Ubicación):</label>
							      <div class="col-md-4">
							      	 <span class="btn btn-default btn-file">
						                <input class="" type="file" id="vfoto3" name="vfoto3" onchange="verificarArchivo(this)" accept="image/jpeg">
						            </span>
							      </div>
							    </div>
							    <div class="form-group">
							      <label for="maguey" class="col-lg-3 control-label">Foto 4(Ubicación):</label>
							      <div class="col-md-4"> 
							      	<span class="btn btn-default btn-file">
						                <input class="" type="file" id="vfoto4" name="vfoto4" onchange="verificarArchivo(this)" accept="image/jpeg">
						            </span>
							      </div>
							    </div>
							    <!-- ---------------------------------------------------- -->
							    <div>
							      <fieldset>
							        <legend align="">Datos del Maguey</legend>
							      </fieldset>
							    </div>
							    <div id="contenedor">
							      <div class="form-group">
							        <label for="maguey" class="col-lg-3 control-label">Registro de Maguey:</label>
							        <div class="col-md-4">
							          <select class="form-control" name="vregistro" id="vregistro"style="max-width:250px;">
							            <option selected="selected" value="">SELECCIONAR</option>
							            <option value="ALMACIGO">ALMACIGO</option>
							            <option value="BOLSA O MACETA">BOLSA O MACETA</option>
							            <option value="CHAROLAS">CHAROLAS</option>
							          </select>
							        </div>
							      </div>
							      <div class="form-group">
							        <label for="maguey" class="col-lg-3 control-label">Origen del Maguey:</label>
							        <div class="col-md-4">
							          <select class="form-control" name="vorigen" id="vorigen"style="max-width:250px;">
							            <option selected="selected" value="">SELECCIONAR</option>
							            <option value="QUIOTE">QUIOTE</option>
							            <option value="SEMILLA">SEMILLA</option>
							            <option value="HIJUELO">HIJUELO</option>
							            <option value="OTRO">OTRO</option>
							          </select>
							        </div>
							      </div>
							      <div class="form-group">
							        <label for="maguey" class="col-lg-3 control-label">Especie: </label>
							        <div class="col-md-4">
							          <select class="form-control" name="vespecie" id="vespecie" />
							            <option value="">- Seleccione una Especie -</option>
							            <?php echo $combobit; ?>
							          </select>
							        </div>
							      </div>
							      <div class="form-group">
							        <label for="maguey" class="col-lg-3 control-label">No. de Plantas:</label>
							        <div class="col-md-3">
							         <input type="text" id="vplantas" name="vplantas" class='form-control txt-largo'/>
							        </div>
							      </div>
							      <div class="form-group">
							        <label for="maguey" class="col-lg-3 control-label">Fecha de Siembra:</label>
							        <div class="col-md-4">
							         <div class="input-group date form_date" data-date="" data-date-format="mm/dd/yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
							            <input class="form-control" size="16" type="text" value="" name="vfechavive"  id="vfechavive" readonly/>
							            <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
							            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
							          </div>
							        </div>
							      </div>
							      <div class="form-group" align="center">
							        <button type="button" name='vagregar' id='vagregar' class="btn btn-primary" onClick="vfn_agregar()">
							        	<span class="glyphicon glyphicon-plus"></span> Agregar
							        </button>
							      </div>
							      <table id="vgrilla" class="table table-hover table-success lista">
							        <thead>
							          <tr>
							            <th>Registro</th>
							            <th>Origen</th>
							            <th>Especie</th>
							            <th>No. de Plantas</th>
							            <th>Fecha de Siembra</th>
							          </tr>
							        </thead>
							        <tbody>
							        </tbody>
							      </table>
							    </div>
							    <div class="form-group" align="center">
							    	<button type="button" name='cancelar' id='cancelar' class="btn btn-danger" onClick="cancelar()" >
							        	<span class="glyphicon glyphicon-remove"></span> Cancelar
									</button>
									<button type="button"  name='vbtnTerminar' id='vbtnTerminar' class="btn btn-success"  onClick="">
										<span class="glyphicon glyphicon-ok"></span> Guardar
									</button>
							    </div>
							</form>
						</div>

                        <!-- ---------------------------------------- -->
                      </div>
                    </div>
                  </div>

                  

                    <!-- -->
                
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
  
    $(function() {
    	// PREDIOS

      	$('#grupoAtributos').hide();

      	$('#abbrev').val("");
      	//$('#abbre').val("");
        $("#state").autocomplete({
            source:  function(request, response) {
              $.getJSON(
                "bus_clientes.php",{ // consulta a esa pagina
                    term : request.term, // parametros extras
                    idus : clvuser
                },
                response
              );
            },
            select: function(event, ui) {
              //$('#state_id').val(ui.item.id);
              $('#abbrev').val(ui.item.abbrev);
              //$('#abbre').val(ui.item.abbre);
            },change: function (event, ui) {
              if (!ui.item) {
                  this.value = '';
                  $('#abbrev').val('');
                  //$('#abbre').val('');
              }
            }
      	});
        $("#state_abbrev").autocomplete({
            source: "bus_nomcli.php",
            minLength: 1
        });

		$("#mnocliente").autocomplete({
			  appendTo : "#modalPredio",
		  	//source: "bus_clientes.php",
        source:  function(request, response) {
          $.getJSON(
            "bus_clientes.php",{ // consulta a esa pagina
                term : request.term, // parametros extras
                idus : clvuser
            },
            response
          );
        },
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
            //source: "bus_clientes.php",
            source:  function(request, response) {
              $.getJSON(
                "bus_clientes.php",{ // consulta a esa pagina
                    term : request.term, // parametros extras
                    idus : clvuser
                },
                response
              );
            },
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
            if (e.keyCode === 13)
                return false;
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
              //$('#abbre').val(reg.paraje);
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
                    //$('#abbre').val('');
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

        
		/*$("#state_abbrev").autocomplete({
		    source: "bus_nomcli.php",
		    minLength: 1
		});
	  	$("#num").autocomplete({
		    source: "bus_viveronum.php",
		    select: function(event, ui) {
		      $('#nombrepre').val(ui.item.nombrepre);
		      $('#clientep').val(ui.item.clientep);
		    }
		});
		$("#num_nombrepre_clientep").autocomplete({
		    source: "bus_nomvivero.php",
		    minLength: 1
		});*/


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
                                      console.log($("#lat").val());
                                      if($("#lat").val() < 15 || $("#lat").val() > 24) {
                                          swal("Error en latitud","El valor de la latitud no se encuentra dentro del rango.");
                                          return false;
                                      }
                          					    if($("#lng").val()!=""){ // 5
                                          console.log($("#lng").val());
                                          if($("#lng").val() > -94 || $("#lng").val() < -104.5) {
                                              swal("Error en longitud","El valor de la longitud no se encuentra dentro del rango.");
                                              return false;
                                          }
                                					  if($("#cboxMCR").val()!="NS") { // 4
                                						  if( $("#cboxMCR").val()==1 || $("#cboxMCR").val()==2 ){ // 3
                                                  if( $("#cboxMCR").val()==2  && $("#SelServicio").val()==""){ // 2
                                                      alert("Seleccione un tipo de servicio.");
                                                      return false;
                                                  } else {
                                                      var superficie = Number($("#superficie").val());
                                                      if( $("#cboxMCR").val() == 2 && $("#SelServicio").val() == "EXCLUSIVO" && superficie > 2){
                                                          alert("Para un registro de tipo Documental Exclusivo la superficie debe ser menor o igual a 2.");
                                                          return false;
                                                      }


                                        							if(ArregloMaguey()!=""){ // 1
                                        								$("#wrapper").LoadingOverlay("show");
                                        								//var cboxGuia = ($("#cboxGuia").prop('checked')==true)?1:0;
                                                        var cboxGuia  =  $("#cboxGuia").val();
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



    // VIVERO

    $('#vabbrev').val("");
	//$('#vabbre').val("");
	$("#vstate").autocomplete({
	    source:  function(request, response) {
        $.getJSON(
          "bus_clientes.php",{ // consulta a esa pagina
              term : request.term, // parametros extras
              idus : clvuser
          },
          response
        );
      },
	    select: function(event, ui) {
	    	$('#vabbrev').val(ui.item.abbrev);
	    	//$('#abbre').val(ui.item.abbre);
	    }
	});

	function VArregloMaguey() {
	  	var myTableArray = [];
		$("table#vgrilla").find("tbody tr").each(function(){
		    var arrayOfThisRow = [];
		    var tableData = $(this).find('td');
		    if (tableData.length > 0) {
		      	tableData.each(function() {
		        	arrayOfThisRow.push($(this).text());
		      	});
		      	myTableArray.push(arrayOfThisRow);
		    }
		});
		return myTableArray;
	}

    $(document).ready(function(){


      $(".elimina").on('click', function() {
          alert("holiiis!!");
       /* idSel = ($(this).attr('id'));
        if(idSel == "HEpdf-prev") 
            selTipo = "HE";
        else if(idSel == "Bpdf-prev") 
            selTipo = "B";
        else 
            selTipo = "";

        if(__CURRENT_PAGE != 1)
            showPage(--__CURRENT_PAGE, selTipo);
        return false;*/
      });

		$("#vbtnTerminar").click(function(){
		  	if($("#vstate").val()!=""){
			    if($("#vparaje").val()!=""){
			      	if($("#vestado option:selected").attr('value')!=""){
			        	if($("#vmunicipio option:selected").attr('value')!=""){
			          		if($("#vlocal option:selected").attr('value')!=""){
			            		if($("#vlat").val()!=""){
			              			if($("#vlng").val()!=""){
			                //if ($('input[name="status_predio"]').is(':checked')){
			                  			if($("#vfoto1").val()!=""){
			                    			if($("#vfoto2").val()!=""){
			                        			if(VArregloMaguey()!=""){
						                        	$("#wrapper").LoadingOverlay("show");
													var datos = new FormData($('#vivero')[0]);
													datos.append('foto1',$('#vfoto1')[0].files[0]);
													datos.append('foto2',$('#vfoto2')[0].files[0]);
													datos.append('foto3',$('#vfoto3')[0].files[0]);
													datos.append('foto4',$('#vfoto4')[0].files[0]);
													datos.append('tMaguey',JSON.stringify(VArregloMaguey())); 
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
																url: "guardarvive.php",
																data:datos,
																cache: false,
																processData: false,
																contentType:false,
																success:function(response){
																	$("#wrapper").LoadingOverlay("hide");
																	//alert(response);
																	swal("Predios", response, "success");
																	location.reload();
																},
																error: function (xhr, ajaxOptions, thrownError){
																	$("#wrapper").LoadingOverlay("hide");
																	alert(xhr.status);
																	alert(thrownError);
																}
															});
														} else {
			        										swal("Operación cancelada.", "Registro no guardado. ");
			        										$("#wrapper").LoadingOverlay("hide");
			        										return false;
			        									}
			        								});

			                        			} else {
						                        	alert("No has agregado datos del maguey");
						                        	return false;
						                        }
											} else {
												alert("No has agregado la foto 2");
												return false;
											}
					                    } else {
					                      alert("No has agregado la foto 1");
					                      return false;
					                    }
					                /*}else{
					                    alert('No ha seleccionado el estatus del predio');
					                    return false;
					                  }*/
					                } else {
					                  alert("No ha introducido una longitud ");
					                  return false;
					                }
				              	} else {
				                	alert("No ha introducido una latitud ");
				                	return false;
				              	}
				            } else {
				              alert("No ha seleccionado una localidad");
				              return false;
				            }
				        } else {
				            alert("No ha seleccionado un municipio");
				            return false;
				        }
			        } else {
			          alert("No ha seleccionado un estado");
			          return false;
			        }
		      	} else {
			        alert("Falta nombre del predio");
			        return false; 
		      	}
		    } else {
		      	alert("Falta numero de asociado");
		      	return false;   
		    }
	  	});

		
		$("#vestado").on("change", VbuscarMunicipios);
		$("#vmunicipio").on("change", VbuscarLocalidades);
		$("#estado").on("change", buscarMunicipios);
		$("#municipio").on("change", buscarLocalidades);

	});



	function VbuscarMunicipios(){
	  	$("#vbtn_nueva_localidad").hide();
	  	$("#vlocal").html("<option value=''>- primero seleccione un municipio -</option>");
	  	$estado = $("#vestado").val();
	  	if($estado == "")
		    $("#vmunicipio").html("<option value=''>- primero seleccione un estado -</option>");
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
			      	$("#vmunicipio").html(respuesta.html);
			    },
			    error:	function(xhr,err){ 
			        alert("readyState: "+xhr.readyState+"\nstatus: "+xhr.status+"\n \n responseText: "+xhr.responseText);
			    }
		    });
	  	}
	}
		
	function VbuscarLocalidades(){
		$municipio = $("#vmunicipio").val();
		$.ajax({
	    	dataType: "json",
		    data: {"municipio": $municipio},
		    url:   'comboeml/buscar.php',
		    type:  'post',
		    beforeSend: function(){
		    //Lo que se hace antes de enviar el formulario
		    },
		    success: function(respuesta){
		    //lo que se si el destino devuelve algo
			    $("#vbtn_nueva_localidad").show();
			    $("#vlocal").html(respuesta.html);
		    },
		    error:	function(xhr,err){ 
		      	alert("readyState: "+xhr.readyState+"\nstatus: "+xhr.status+"\n \n responseText: "+xhr.responseText);
		    }
		});
	}

    
    
	function buscarMunicipios(){
	  	$("#btn_nueva_localidad").hide();
	  	$("#local").html("<option value=''>- primero seleccione un municipio -</option>");
	  	$estado = $("#estado").val();
	  	if($estado == "")
		    $("#municipio").html("<option value=''>- primero seleccione un estado -</option>");
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
			    error:	function(xhr,err){ 
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
		    url:   'comboeml/buscar.php',
		    type:  'post',
		    beforeSend: function(){
		    //Lo que se hace antes de enviar el formulario
		    },
		    success: function(respuesta){
		    //lo que se si el destino devuelve algo
			    $("#btn_nueva_localidad").show();
			    $("#local").html(respuesta.html);
		    },
		    error:	function(xhr,err){ 
		      	alert("readyState: "+xhr.readyState+"\nstatus: "+xhr.status+"\n \n responseText: "+xhr.responseText);
		    }
		});
	}

	function verificarArchivo (element) {
		var val = $(element).val(); 
		var ext = val.substring(val.lastIndexOf(".") + 1).toLowerCase(); 
		if (ext != "jpg"){
		    element.value = "";
		    alert("Debe seleccionar un archivo jpg");
		}
		var fileSize = ($(element)[0].files[0].size / 40960 / 40960);
		if (fileSize > 1){
		    element.value = "";
		    alert("Debe seleccionar un archivo con un tamaño maximo de 40 MB.")
		}
	}


	$('#vespecie').change(function() {
	    var especie=this.value;
	    $.ajax({
			type: "POST",
			url: "verificar_especie.php",
			contentType: "application/x-www-form-urlencoded;charset=UTF-8",
			data: "especie="+especie,
			datatype: 'json',
			success: function(response) {
				//alert(response);
				var j_existe=JSON.parse(response);
				if(j_existe.status=='correcto') {
				  if(j_existe.valido=='no')
					  alert("La especie que está seleccionando ya no se encuentra dentro de nuestro catálogo.");
				} else
					alert("ocurrio un error al consultar especie")
			},
			beforeSend:function(){
				$("#add_err").html("Loading...");
			}
		});
        //Todo el código aqui
    });

    //------------------------ FIN VALIDAR ESPECIA----------------------

	function editarPredio(idParaje) {
        //alert("Edit ");
        $.ajax({
            type: "POST",
            url: "php/loadPredios.php",
            contentType: "application/x-www-form-urlencoded;charset=UTF-8",
            data: {
                tipo: "buscarDPredio",
                idParaje: idParaje
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


    function vfn_agregar(){
    	$msj = "";
		if($("#vregistro").val()=="")
			$msj += "* Falta seleccionar el Registro de Maguey. \n";
		if($("#vorigen").val()=="")
			$msj += "* Falta seleccionar el Origen de Maguey. \n";
		if($("#vespecie").val()=="")
			$msj += "* Falta seleccionar la Especie de Maguey. \n";
		if($("#vplantas").val()=="")
			$msj += "* Falta insertar el no. de plantas. \n";
		if($("#vfechavive").val()=="")
			$msj += "* Falta insertar la fecha de siembra de vivero. \n";

		if($msj != "")
			alert($msj);
		else {
      		cadena = "<tr>";
			cadena = cadena + "<td>" + $("#vregistro").val() + "</td>";
			cadena = cadena + "<td>" + $("#vorigen").val() + "</td>";
			  // cadena = cadena + "<td>" + $("#sm").val() + "</td>";
			cadena = cadena + "<td>" + $("#vespecie").val() + "</td>";
			cadena = cadena + "<td>" + $("#vplantas").val() + "</td>";
			cadena = cadena + "<td>" + $("#vfechavive").val() + "</td>";

	  //cadena = cadena + "<td><a class='elimina'><img src='images/delete.png' /></a></td>";
	  //cadena = cadena + "<td><a class='text-danger elimina'><span class='glyphicon glyphicon-minus-sign'></span></a></td>";
		  	cadena = cadena + "<td><a class='text-danger velimina'><span class='glyphicon glyphicon-trash'></span></a></td>";
		  	$("#vgrilla tbody").append(cadena);
		
	  /*
	  aqui puedes enviar un conunto de tados ajax para agregar al usuairo
	  $.post("agregar.php", {ide_usu: $("#valor_ide").val(), nom_usu: $("#valor_uno").val()});
	  */
		  	$("#vregistro").val('');
		  	$("#vorigen").val('');
		  	$("#vespecie").val('');
		  	$("#vplantas").val('');
		  	$("#vfechavive").val('');
		  
		  	fn_dar_eliminarv();
		  	fn_cantidadv();
		  	//alert("Registro Agregado");
	  	}
	};

	function fn_cantidadv(){
	  cantidad = $("#vgrilla tbody").find("tr").length;
	  $("#span_cantidad").html(cantidad);
	};

	function fn_dar_eliminarv(){
	  $("a.velimina").click(function(){
	    id = $(this).parents("tr").find("td").eq(0).html();
	    respuesta = confirm("Desea Eliminar el Registro: " + id);
	    if (respuesta){
	      $(this).parents("tr").fadeOut("normal", function(){
	        $(this).remove();
	        //alert("Registro " + id + " Eliminado")
	        /*
	        aqui puedes enviar un conjunto de datos por ajax
	        $.post("eliminar.php", {ide_usu: id})
	        */
	      })
	    }
	  });
	};

	function vupperCase() {

	  var x=document.getElementById("vreferencia2").value
	  document.getElementById("vreferencia2").value=x.toUpperCase()
	  var x=document.getElementById("vreferenciau").value
	  document.getElementById("vreferenciau").value=x.toUpperCase()
	  var x=document.getElementById("vcampo").value
	  document.getElementById("vcampo").value=x.toUpperCase()
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
