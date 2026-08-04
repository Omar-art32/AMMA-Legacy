<?php
session_start();
session_set_cookie_params(0, "/", $_SERVER["HTTP_HOST"], 0);
$mod=1;
require_once('../common/cfg_server.php');
$d_s=$_GET['d_s'];
if(isset($_SESSION[$d_s]) && $_SESSION[$d_s]["seccion_9_1"] == "logged"){

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

	<title>Contraseña</title>

    <!-- Bootstrap Core CSS -->
    <link href="../css/bootstrap.css?29062018" rel="stylesheet">
    <!-- MetisMenu CSS -->
    <link href="../css/metisMenu.min.css" rel="stylesheet">    
    <!-- Custom CSS -->
    <link href="../css/sb-admin-2.css" rel="stylesheet">  
    <link href="../css/smoothness/jquery-ui.css" rel="stylesheet">
    <link href="../css/custom-style.css" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="../css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="../css/tooltipster.css" rel="stylesheet" type="text/css">
    
    <script type="text/javascript">
      var id_s="<?php echo $d_s; ?>";
      var id_depto="<?php echo $_SESSION[$d_s]['dpto']; ?>";
      var usr_cargo="<?php echo $_SESSION[$d_s]['cargo']; ?>";
      var user="<?php echo $_SESSION[$d_s]['s_username'];?>";
      var clvuser="<?php echo $_SESSION[$d_s]['id_us'];?>";
      var nivel="<?php echo $_SESSION[$d_s]['sec_lvl_9_1'];?>";
	  
	  var moduloAcceso=9;
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
                <a class="navbar-brand" href="../index.php?d_s=<?php echo $d_s?>"><i class="fa fa-lg fa-home" aria-hidden="true"></i> SIIG CRM V2.0</a>
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
                    <h3 class="page-header" style="text-align:center;">CONTRASEÑA</h3>                    
             </div>
                
                <!-- /.col-lg-12 -->
            </div>
            
            <!-- /.row -->
            <div class="row">
              <div class="col-lg-12" style="margin-top:20px;">                   
                    <div class="panel  panel-che" style="max-width:1100px; margin-left: auto !important; margin-right:auto !important; margin-bottom:75px;">
                        <div class="panel-heading">
                                                  
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                             <!-- Nav tabs -->
                              <ul class="nav nav-tabs" role="tablist">
                                <li role="user-data" class="active"><a href="#changePassword" aria-controls="changePassword" role="tab" data-toggle="tab"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;Contraseña</a></li>                                                        
                              </ul>
                              
                             <!-- Tab panes -->
                              <div class="tab-content">
                              <div role="tabpanel" class="tab-pane active" id="changePassword"><!-- /.tab-pane reg-form -->
                                  <!-- ////////////////////////.tab-pane cambio de contraseña///////////////////////////// -->
                                   <div style="margin-left:auto !important; margin-right:auto !important;">                            
                                    <div class="panel  panel-default" style="margin-bottom:0 !important; margin-top:5px !important;">
                                      <div class="panel-heading" style="text-align:center; font-size:16px; font-weight:bold;">                                           
                                           Cambiar contraseña
                                      </div>
                                      <!-- /.panel-heading -->
                                      <div class="panel-body">
                                         <div id="dvChangePass" style="overflow:auto; padding-bottom:15px;">   
                                           <form id="frmChangePassword" role="form" action="" method="post" class="form-horizontal">
                                             <div class="form-group" style="margin-left:0 !important; margin-right:0 !important;">
                                                <label for="frmChangePassword" class="col-md-5 control-label">Contraseña Actual:</label>
                                                <div class="col-md-7">
                                                  <input type="password" value="" id="old_pswd" name="old_pswd" class="form-control txt-short"/>
                                                </div>
                                             </div>                                             
                                             <div class="col-xs-12"><hr style="margin-bottom:15px; margin-top:2px;"></div>
                                             <div class="form-group" style="margin-left:0 !important; margin-right:0 !important;">                                                
                                                <label for="frmChangePassword" class="col-md-2 control-label">&nbsp;</label>
                                                <div class="col-md-9">
                                                   <p style="color:#448805;">La nueva contraseña debe tener 8 caracteres de longitud como minimo y contener al menos 1 letra mayúscula, 1 letra minúscula y 1 número.</p>
                                                </div>
                                             </div>
                                             <div class="form-group" style="margin-left:0 !important; margin-right:0 !important;">                                                
                                                <label for="frmChangePassword" class="col-md-5 control-label">Nueva Contraseña:</label>
                                                <div class="col-md-7">
                                                  <input type="password" value="" id="new_pswd" name="new_pswd" class="form-control txt-short"/>
                                                </div>
                                             </div>
                                             <div class="form-group" style="margin-left:0 !important; margin-right:0 !important;">                                                
                                                <label for="frmChangePassword" class="col-md-5 control-label">Repetir Nueva Contraseña:</label>
                                                <div class="col-md-7">
                                                  <input type="password" value="" id="re_new_pswd" name="re_new_pswd" class="form-control txt-short"/>
                                                </div>
                                             </div>
                                             <div class="form-group" style="margin-left:0 !important; margin-right:0 !important;">                                                
                                                <label for="frmChangePassword" class="col-md-5 control-label">&nbsp;</label>
                                                <div class="col-md-7">
                                                  <button type="submit" class="btn btn-success" id='btnChangePassword'><i class="fa fa-lg fa-key fa-fw"></i> Guardar Cambios</button>
                                                </div>
                                             </div>                                             
                                             <div class="form-group"  style="margin-left:0 !important; margin-right:0 !important;">
                                                  <h3 class="err" id="add_err"></h3>
                                              </div>
                                           </form>                               
                                         </div>
                                      </div>
                                   </div> 
                                  </div><!-- /.collg-12 panel-default -->
                                  <!-- ////////////////////////.tab-pane cambio de contraseña///////////////////////////// -->
                              </div><!-- /.tab-reg-form -->                            
                          </div><!-- /.tab-panes -->                              
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->  
               </div>              

  
            </div> <!-- /.row -->

        </div> <!-- /#page-wrapper -->

          <div class="modal fade" id="modalMsjs" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
              <div class="modal-content"  style="max-width:500px !important;">
                <div class="modal-header alert-primary">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title" id="msjTittle" style="text-align:center !important; font-size:24px;">Atención</h4>
                </div>
                <div class="modal-body" id="msjConfirmBody" style="text-align:center !important; display: table; overflow-y: auto; overflow-x: auto;font-size:18px; vertical-align:middle">
                 
                </div>
                <div class="modal-footer">
                  <button type="button" id="btnEntendido" class="btn btn-success" onClick="$('#modalMsjs').modal('hide');">Entendido</button>
                </div>
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
    <!-- Custom Theme JavaScript -->
    <script src="../js/sb-admin-2.js"></script>
    <!-- Custom Theme JavaScript -->
    <script src="../js/jquery-ui.min.js"></script>

        <!-- Metis Menu Plugin JavaScript -->
    <script src="js/metisMenu.min.js"></script>

    <!-- Validacion -->
    <script src="js/jquery.validate.min.js"></script> 
    <script src="js/additional-methods.js"></script>
    <script type="text/javascript" src="js/localization/messages_es.js"></script>
    <script src="js/jquery.tooltipster.js"></script> 
    <script src="js/e.js"></script> 
    <script src="js/input.js"></script>
    <script src="js/jquery.cropit.js"></script>
    <script src="js/settings.js"></script> 
	  
	 <?php include("../includes/acceso.php");?>

</body>

</html>
<?php 
}
else{
   header("location: http://".$svr_dir."/siigv2/acceso/login.php");	
}
?>