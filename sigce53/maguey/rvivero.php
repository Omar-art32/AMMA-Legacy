<?php
	session_start();
	session_set_cookie_params(0, "/", $_SERVER["HTTP_HOST"], 0);
    require_once('../common/cfg_server.php');
    $d_s=$_GET['d_s'];
    if(isset($_SESSION[$d_s]) && $_SESSION[$d_s]["seccion_4_2"] == "logged")
    {
	require_once("comboeml/funciones.php");
	require_once("bus_especie.php");
	require_once("idparaje_vivero.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>SIGCE</title>
	<link rel="shortcut icon" href="../favicon.ico">
	<link href="css/bootstrap.css" rel="stylesheet">
	<link href="css/metisMenu.min.css" rel="stylesheet">
	<link href="css/timeline.css" rel="stylesheet">
	<link href="css/sb-admin-2.css" rel="stylesheet">
	<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="css/bootstrap-toggle.css">
	<link type="text/css" href="css/demo_table.css" rel="stylesheet">
	<link href="calendario/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
	<link rel="stylesheet" href="css/animate.css">
	<link rel="stylesheet" href="css/templatemo_misc.css">
	<link href="css/estilo.css" rel="stylesheet" type="text/css">

	<!--<script src="js/bootstrap.js"></script>
	<script src="js/plugins.js"></script>
	<script src="js/main.js"></script>-->
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui.min.js"></script>
	<!--<script type="text/javascript" src="js/jquery.js"></script>-->
	<script src="js/vendor/modernizr-2.6.1-respond-1.1.0.min.js"></script>
	<script language="javascript" type="text/javascript" src="js/jquery.validate.1.5.2.js"></script><!-- carrito -->
	<script language="javascript" type="text/javascript" src="js/scriptvive.js"></script> <!-- boton agregar tab1 -->
	<script type="text/javascript" src="js/magueyvive.js"></script> <!-- boton agregar tab2 -->
	<!--<script type="text/javascript" language="javascript" src="js/funciones.js"></script>
	<script type="text/javascript" language="javascript" src="js/funciones2.js"></script>-->
	<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
	<!--<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
	<script src="https://maps.google.com/maps/api/js?sensor=false&amp;v=3"></script>-->
	<!-- <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAa7gTxPscQdpSsdmInTkrJ0uk-JPJ1As8&sensor=false&amp;v=3"></script>-->
	<!--<script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.0.min.js"><\/script>')</script>-->

	<script src="js/jquery.min.js"></script>
	<script src="js/jquery.cookie.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery-ui.min.js"></script>
	<script src="js/sb-admin-2.js"></script>
	<script src="js/plugins.js"></script>
    <script src="js/main.js"></script>  
	<script type="text/javascript" src="calendario/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
	<script type="text/javascript" src="calendario/js/locales/bootstrap-datetimepicker.es.js" charset="UTF-8"></script>
	<script type="text/javascript" src="js/bootstrap-toggle.js"></script>
	<link href="smoothness/jquery-ui.css" rel="stylesheet" type="text/css">
	<script src="plugin/loadingoverlay.min.js"></script>

	<!-- sweet alert2 -->
	<script src="../registros_oc/etiquetas/plugins/sweetalert/sweetalert-dev.js"></script>
	<link rel="stylesheet" href="../registros_oc/etiquetas/plugins/sweetalert/sweetalert.css">
	<script src="js/jquery.togglebutton.min.js"></script>


    <script type="text/javascript">
      var id_s="<?php echo $d_s; ?>";
      var id_depto="<?php echo $_SESSION[$d_s]['dpto']; ?>";
      var usr_cargo="<?php echo $_SESSION[$d_s]['cargo']; ?>";
      var user="<?php echo $_SESSION[$d_s]['s_username'];?>";
      var clvuser="<?php echo $_SESSION[$d_s]['id_us'];?>";
      
      var moduloAcceso=4;
      var seccionAcceso=2;
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

		  $("#BtnNuevaLocalidad").click(function() {
		  if ( $('#municipio').val()!="" )
		  {
		     if ( $('#TxtNuevaLocalidad').val()!="" ){
		     var localidad = $('#TxtNuevaLocalidad').val();
		     var id_municipio = $('#municipio').val();
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


		});
	</script>

	<!--<script>
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
	</script>-->

	<script type="text/javascript">
		function idinsertar (){
		  $consultaid="SELECT max(id_paraje)+1 FROM `paraje_vivero` WHERE 1";
		}
	</script>  

	<script>
		$(function() {
		  $( "#tabs" ).tabs();
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
	            <h3 class="page-header text-white">REGISTRO DE VIVERO</h3>                    
	          </div>
	        </div>
	        <div class="row">
	          <div class="col-lg-12" style="margin-top:20px;">                   
	            <div class="panel  panel-che" style="max-width:1100px; margin-left: auto !important; margin-right:auto !important; margin-bottom:75px;">
	              <div class="panel-heading"></div>
	              <div class="panel-body">
	                <ul class="nav nav-tabs" role="tablist">
	                  <li role="user-data" class="active"><a href="#tab1success" aria-controls="changePassword" role="tab" data-toggle="tab"><span class="fa fa-list-alt"></span> Registro de Vivero</a></li>
	                  <li role="user-data" class=""><a href="#tab2success" aria-controls="changePassword" role="tab" data-toggle="tab"><span class="fa fa-pagelines"></span> Entrada de Maguey</a></li>
	                </ul>
	                <div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="tab1success">
							<div style="margin-left:auto !important; margin-right:auto !important;">
								<div class="panel  panel-default" style="margin-bottom:0 !important; margin-top:5px !important;">
									<div class="panel-heading" style="text-align:center; font-size:16px; font-weight:bold;">Registro de Vivero
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
										       <label for="maguey" class="col-lg-3 control-label">No.Vivero:</label>
										      <div class="col-md-3">
										        <input type="text" name="id" id="id" disabled="disabled"  class='form-control txt-short' value="<?php echo $id; ?>">
										      </div>
										    </div>
										    <div class="form-group">
										      <label for="maguey" class="col-lg-3 control-label">No. Asociado:</label>
										      <div class="col-md-3">
										        <input type="text" id="state"  name="state" class='form-control txt-short' />
										      </div>
										    </div>
										    <div class="form-group">
										      <label for="maguey" class="col-lg-3 control-label">Nombre Asociado:</label>
										      <div class="col-md-4">
										        <input readonly type="text" id="abbrev" name="abbrev" class='form-control'/>
										      </div>
										    </div>
										    <div>
										      <legend align="">Datos del Vivero</legend>
										    </div>
										    <div class="form-group">
										    	<label class="col-md-3 control-label">Propietario:</label>
										      	<div class="col-md-4">
										        	<input class="form-control" type="checkbox" id="referencia1" name="referencia1" data-size="small" data-toggle="toggle" data-size="normal" data-onstyle="success" data-on="Si" data-off="No" data-offstyle="danger">
										     	</div>
										      	<label for="maguey" class="col-md-3 control-label"></label>
										    </div>
										    <div class="form-group" id="formularioreferencia" style="display: none;">
										      <label for="maguey" class="col-lg-3 control-label">Nombre completo:</label>
										      <div class="col-md-4">
										          <input type="text" name="referencia2" id="referencia2" class="form-control" onblur="upperCase()">
										      </div>
										    </div>
										    <div class="form-group">
										      <label for="maguey" class="col-lg-3 control-label">Fecha de Registro:</label>
										      <div class="col-md-4">
										        <div class="input-group date form_date" data-date="" data-date-format="mm/dd/yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
										            <input class="form-control" size="16" type="text" value="" name="fecha"  id="fecha" readonly/>
										            <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
										            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
										        </div>
										      </div>
										    </div>
										    <div class="form-group">
										    	<label for="maguey" class="col-lg-3 control-label">Nombre del Vivero:</label>
										      <div class="col-md-4">
										        <input type="text" id="paraje" name="paraje" class='form-control txt-largo' />
										      </div>
										    </div>
										    <div class="form-group">
										      <label for="maguey" class="col-lg-3 control-label">Referencia de Ubicación:</label>
										      <div class="col-md-4">
										        <input class="form-control" type="text" id="referenciau" name="referenciau" onblur="upperCase()" />
										      </div>
										    </div>
										    <div class="form-group">
										      <label for="maguey" class="col-lg-3 control-label">Estado:</label>
										      <div class="col-md-4">
										        <select class="form-control" name="estado" id="estado" />
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
										        <select class="form-control" name="municipio" id="municipio">
										          <option value="">- primero seleccion un estado -</option>
										        </select>
										      </div>
										    </div>
										    <div class="form-group">
										      <label for="maguey" class="col-lg-3 control-label">Localidad:</label>
										      <div class="col-md-4" align="right">
										        <select class="form-control" name="local" id="local">
										          <option value="">- primero seleccion un municipio -</option>
										        </select>
										        <a href="#" id="btn_nueva_localidad" class="text-success"><span class="glyphicon glyphicon-plus"></span><!-- <img src="images/add.png"/> --></a>
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
										      <label for="maguey" class="col-lg-3 control-label">Latitud Norte:</label>
										      <div class="col-md-4">
										        <input type="text" id="lat" name="lat" class='form-control txt-largo' />
										      </div>
										    </div>
										    <div class="form-group">
										      <label for="maguey" class="col-lg-3 control-label">Longitud Oeste:</label>
										      <div class="col-md-4">
										        <input type="text" id="lng" name="lng" class='form-control txt-largo' />
										      </div>
										    </div>
										    <div class="form-group">
										      <label for="maguey" class="col-lg-3 control-label">Encargado:</label>
										      <div class="col-md-4">
										        <input type="text" id="campo" name="campo" class='form-control txt-largo' onblur="upperCase()"/>
										      </div>
										    </div>
										    <div class="form-group">
										      <label for="maguey" class="col-lg-3 control-label">Foto 1:</label>
										      <div class="col-md-4">
										      	 <span class="btn btn-default btn-file">
									                <input class="" type="file" id="foto1" name="foto1" onchange="verificarArchivo(this)" accept="image/jpeg">
									            </span>
										      </div>
										    </div>
										    <div class="form-group">
										      <label for="maguey" class="col-lg-3 control-label">Foto 2:</label>
										      <div class="col-md-4"> 
										      	<span class="btn btn-default btn-file">
									                <input class="" type="file" id="foto2" name="foto2" onchange="verificarArchivo(this)" accept="image/jpeg">
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
										      <label for="maguey" class="col-lg-3 control-label">Foto 3:</label>
										      <div class="col-md-4">
										      	 <span class="btn btn-default btn-file">
									                <input class="" type="file" id="foto3" name="foto3" onchange="verificarArchivo(this)" accept="image/jpeg">
									            </span>
										      </div>
										    </div>
										    <div class="form-group">
										      <label for="maguey" class="col-lg-3 control-label">Foto 4:</label>
										      <div class="col-md-4"> 
										      	<span class="btn btn-default btn-file">
									                <input class="" type="file" id="foto4" name="foto4" onchange="verificarArchivo(this)" accept="image/jpeg">
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
										          <select class="form-control" name="registro" id="registro"style="max-width:250px;">
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
										          <select class="form-control" name="origen" id="origen"style="max-width:250px;">
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
										          <select class="form-control" name="especie" id="especie" />
										            <option value="">- Seleccione una Especie -</option>
										            <?php echo $combobit; ?>
										          </select>
										        </div>
										      </div>
										      <div class="form-group">
										        <label for="maguey" class="col-lg-3 control-label">No. de Plantas:</label>
										        <div class="col-md-3">
										         <input type="text" id="plantas" name="plantas" class='form-control txt-largo'/>
										        </div>
										      </div>
										      <div class="form-group">
										        <label for="maguey" class="col-lg-3 control-label">Fecha de Siembra:</label>
										        <div class="col-md-4">
										         <div class="input-group date form_date" data-date="" data-date-format="mm/dd/yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
										            <input class="form-control" size="16" type="text" value="" name="fechavive"  id="fechavive" readonly/>
										            <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
										            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
										          </div>
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
												<button type="button"  name='btnTerminar' id='btnTerminar' class="btn btn-success"  onClick="">
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
										      <legend align="">Datos del Vivero</legend>
										    </fieldset>
										    <div class="form-group">
										      <label for="magueys" class="col-lg-3 control-label">No.Vivero:</label>
										      <div class="col-md-4">
										        <input type="text" id="num"  name="num" class='form-control txt-short'/>
										      </div>
										    </div>
										    <div class="form-group">
										      <label for="magueys" class="col-lg-3 control-label">Nombre del Vivero:</label>
										      <div class="col-md-4">
										        <input readonly type="text" id="nombrepre" name="nombrepre" class='form-control'/>
										      </div>
										    </div>
										    <div class="form-group">
										      <label for="magueys" class="col-lg-3 control-label">No. Cliente:</label>
										      <div class="col-md-4">
										        <input readonly type="text" id="clientep" name="clientep" class='form-control'/>
										      </div>
										    </div>
										    <div>
										      <fieldset>
										        <legend align="">Datos del Maguey</legend>
										      </fieldset>
										    </div>
										    <div id="contenedor">
										      <div class="form-group">
										        <label for="magueys" class="col-lg-3 control-label">Registro de Maguey:</label>
										        <div class="col-md-4">
										          <select class="form-control" name="registros" id="registros"style="max-width:250px;">
										            <option selected="selected" value="">SELECCIONAR</option>
										            <option value="ALMACIGO">ALMACIGO</option>
										            <option value="BOLSA O MACETA">BOLSA O MACETA</option>
										            <option value="CHAROLAS">CHAROLAS</option>
										          </select>  
										        </div>
										      </div>
										      <div class="form-group">
										        <label for="magueys" class="col-lg-3 control-label">Origen del Maguey:</label>
										        <div class="col-md-4">
										          <select class="form-control" name="origens" id="origens"style="max-width:250px;">
										            <option selected="selected" value="">SELECCIONAR</option>
										            <option value="QUIOTE">QUIOTE</option>
										            <option value="SEMILLA">SEMILLA</option>
										            <option value="HIJUELO">HIJUELO</option>
										            <option value="OTRO">OTRO</option>
										          </select> 
										        </div>
										      </div>
										      <div class="form-group">
										        <label for="magueys" class="col-lg-3 control-label">Especie: </label>
										        <div class="col-md-4">
										          <select class="form-control" name="especies" id="especies">
										            <option value="">- Seleccione una Especie -</option>
										            <?php echo $combobit; ?>
										          </select>
										        </div>
										      </div>
										      <div class="form-group">
										        <label for="magueys" class="col-lg-3 control-label">No. de Plantas:</label>
										        <div class="col-md-4">
										          <input type="text" id="plantass" name="plantass" class='form-control txt-largo'/>
										        </div>
										      </div>
										      <div class="form-group">
										        <label for="magueys" class="col-lg-3 control-label">Fecha de Siembra:</label>
										        <div class="col-md-4">
										          <div class="input-group date form_date" data-date="" data-date-format="mm/dd/yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
										            <input class="form-control" size="16" type="text" value="" name="fechavives"  id="fechavives" readonly/>
										            <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
										            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
										          </div>
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
										      	<button type="button"  name='btnTerminars' id='btnTerminars' class="btn btn-success"  onClick="">
										        	<span class="glyphicon glyphicon-ok"></span> Guardar
										      	</button>
										    </div>
										</form>
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
		$('#abbrev').val("");
		$('#abbre').val("");
		  $("#state").autocomplete({
		    source: "bus_clientes.php",
		    select: function(event, ui) {
		    $('#abbrev').val(ui.item.abbrev);
		      $('#abbre').val(ui.item.abbre);
		    }
		  });
		  $("#state_abbrev").autocomplete({
		    source: "bus_nomcli.php",
		    minLength: 1
		  });
		});		
	</script>

	<script>
		$(function() {
		  $('#nombrepre').val("");
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
		  });
		});		
	</script>

	<script type="text/javascript">
		function ArregloMaguey() {
		  var myTableArray = [];
		  $("table#grilla").find("tbody tr").each(function(){
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
	</script>

	<script type="text/javascript"> 
		$(document).ready(function(){
		$("#btnTerminar").click(function(){
		  if($("#state").val()!=""){
		    if($("#paraje").val()!=""){
		      if($("#estado option:selected").attr('value')!=""){
		        if($("#municipio option:selected").attr('value')!=""){
		          if($("#local option:selected").attr('value')!=""){
		            if($("#lat").val()!=""){
		              if($("#lng").val()!=""){
		                //if ($('input[name="status_predio"]').is(':checked')){
		                  	if($("#foto1").val()!=""){
		                    	if($("#foto2").val()!=""){
			                        if(ArregloMaguey()!=""){
			                        	$("#wrapper").LoadingOverlay("show");
										var datos = new FormData($('#maguey')[0]);
										datos.append('foto1',$('#foto1')[0].files[0]);
										datos.append('foto2',$('#foto2')[0].files[0]);
										datos.append('tMaguey',JSON.stringify(ArregloMaguey())); 
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

			                        }else{
			                          alert("No has agregado datos del maguey");
			                          return false;
			                        }
								}else{
									alert("No has agregado la foto 2");
									return false;
								}
		                    }else{
		                      alert("No has agregado la foto 1");
		                      return false;
		                    }
		                  /*}else{
		                    alert('No ha seleccionado el estatus del predio');
		                    return false;
		                  }*/
		                }else{
		                  alert("No ha introducido una longitud ");
		                  return false;
		                }
		              }else{
		                alert("No ha introducido una latitud ");
		                return false;
		              }
		            }else{
		              alert("No ha seleccionado una localidad");
		              return false;
		            }
		          }else{
		            alert("No ha seleccionado un municipio");
		            return false;
		          }
		        }else{
		          alert("No ha seleccionado un estado");
		          return false;
		        }
		      }else{
		        alert("Falta nombre del predio");
		        return false; 
		      }
		    }else{
		      alert("Falta numero de asociado");
		      return false;   
		    }
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
						if($("#clientep").val()!=""){
							$("#wrapper").LoadingOverlay("show");
							var datos={'tMagueys': JSON.stringify(ArregloMagueys())};
						    datos = $("#magueys").serialize() + "&" + $.param(datos);
						    $.ajax({
						      async: false,
						      type: "POST",
						      url: "guardarvive2.php",
						      data:datos,
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
							alert("Falta número del cliente");
		          			return false;
		          		}
					}else{
						alert("Falta nombre del predio");
		          		return false;
		          	}	
				}else{
					alert("Falta número del predio");
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
		  }else{
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