<?php
session_start();
session_set_cookie_params(0, "/", $_SERVER["HTTP_HOST"], 0);
$mod=1;
require_once('../common/cfg_server.php');
$d_s=$_GET['d_s'];
if(isset($_SESSION[$d_s]) && $_SESSION[$d_s]["seccion_1_2"] == "logged")
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
    <link href="../css/hologramas/inventario.css" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="../css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <script type="text/javascript">
	   var id_s="<?php echo $d_s; ?>";
	   var id_depto="<?php echo $_SESSION[$d_s]['dpto']; ?>";
	   var usr_cargo="<?php echo $_SESSION[$d_s]['cargo']; ?>";
	   var user="<?php echo $_SESSION[$d_s]['s_username'];?>";
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
                    <div class="panel  panel-default" style="max-width:1100px !important; margin-left:auto; margin-right:auto;">
                        <!-- /.panel-heading -->
                        <div class="panel-body" style="background-color:#F6F6F6" id="pnlInventarioMain">
                              <!-- Nav tabs -->
                              <ul class="nav nav-tabs" role="tablist"> 
                                <li role="p" class="active"><a href="#tabExs" aria-controls="tabExs" role="tab" data-toggle="tab"><i class="fa fa-sort-numeric-asc" aria-hidden="true"></i>&nbsp;Maquila</a></li>
                              </ul>
                              <br>
                             <!-- Tab panes -->
                              <div class="tab-content">
                              <div role="tabpanel" class="tab-pane active" id="tabExs"><!-- /.tab-pane ENTRADAS/EXISTENCIAS -->
                                 <!-- ////////////////////////.tab-pane ENTRADAS/EXISTENCIAS///////////////////////////// -->
                                  <!-- Modal AddMaquila -->
                                  <div class="modal fade" id="modalAddMaq" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                    <div class="modal-dialog" role="document">
                                      <div class="modal-content"  style="max-width:700px !important;">
                                        <div class="modal-header">
                                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                          <h4 class="modal-title" id="msjTittle" style="text-align:center !important;">Relaciones Productor-Envasador-Comercializador</h4>
                                        </div>
                                        <div class="modal-body" id="msjBody" style="text-align:center !important;">
                                             <form class="form-horizontal" id="formMaquila" name="formMaquila" target="_blank"  role="form" action='' method='post'>
                                                <input type="hidden" name='usr' id='usr' value='<?php echo $_SESSION[$d_s]['s_username'];?>'/>
                                                <input type="hidden" name='cargo' id='cargo' value='<?php echo $_SESSION[$d_s]['cargo'];?>'/>
                                                <div class="form-group">
                                                   <label for="formEntrada" class="col-lg-3 control-label">Asociado:</label>
                                                   <div class="col-lg-4">
                                                      <input type='text' name='txtCteProv' id='txtCteProv' value='' class='form-control txt-short auto' minlength="4" required="true">
                                                   </div>
                                                   <div class="col-lg-2">                                                     
                                                   </div>
                                                </div>
                                                <div class="form-group">
                                                   <label for="formEntrada" class="col-lg-3 control-label">Relacion:</label>
                                                   <div class="col-lg-6">
                                                      <select class="form-control" name="cboTipoRel" id="cboTipoRel" onChange="validaRel();">
                                                         <option value="0" selected>Seleccionar</option>
                                                         <option value="P">Productor</option>
                                                         <option value="E">Envasador</option>
                                                         <option value="PE">Productor y Envasador</option>
                                                      </select>
                                                   </div>                                                   
                                                </div>
                                                <div class="form-group" id="dvMarcasCbo" style="display:none">
                                                   <label for="formEntrada" class="col-lg-3 control-label">Marca:</label>
                                                   <div class="col-lg-7">
                                                      <select class="form-control" id="cboMarcaRec" name="cboMarcaRec" onChange=""></select>
                                                   </div>
                                                   <div class="col-lg-1">
                                                      <button type='button' name='btnAddMarcaMaq' id='btnAddMarcaMaq' value='' class='btn btn-md btn-success' onClick="addMarca();"><i class="fa fa-lg fa-plus" aria-hidden="true"></i></button>
                                                   </div>                                                   
                                                </div>
                                                <div class="form-group" id="dvMarcasArr" style="display:none">
                                                   <div class="col-lg-2">&nbsp;</div>
                                                   <div class="col-lg-9">
                                                     <table class="table table-bordered table-striped" id="tblMarcas">
                                                     <thead>
                                                     <th width="25%">Clave</th>
                                                     <th width="70%">Marca</th>
                                                     <th width="5%">--</th>
                                                     </thead>
                                                     <tbody>
                                                     </tbody>
                                                     </table>
                                                   </div>                                                   
                                                </div>
                                                 <div class="form-group">
                                                   <label for="formEntrada" class="col-lg-3 control-label">Observaciones:</label>
                                                   <div class="col-lg-6">
                                                      <textarea id="txtObs" class="form-control" rows="4" maxlength="200"></textarea>
                                                   </div>                                                   
                                                </div>
                                             </form>
                                        </div>
                                        <div class="modal-footer">
                                         <button type="button" id="btnGuardar" class="btn btn-success" onClick="validaDatos();">Guardar</button> <button type="button" id="btnCancelar" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                   <!-- Modal Confirm -->
                                 <div id="entradas" style="display:flex;">           
                                    <div class="col-lg-8">
                                          <div id="datos_entrega"><!-- Contenido Inventario-->
                                              <form class="form-horizontal" id="formEntrada" name="formEntrada" target="_blank"  role="form" action='' method='post'>
                                               <input type="hidden" name='usr' id='usr' value='<?php echo $_SESSION[$d_s]['s_username'];?>'/>
                                               <input type="hidden" name='cargo' id='cargo' value='<?php echo $_SESSION[$d_s]['cargo'];?>'/>
                                                <input type="hidden" name='nivel_inventario' id='nivel_inventario' value='<?php echo $_SESSION[$d_s]['sec_lvl_1_2'];?>'/>
                                                  <div class="form-group" id='txt_cliente'>
                                                    <label for="formEntrada" class="col-lg-7 control-label">Asociado:</label>
                                                    <div class="col-lg-2">
                                                    <input type='text' name='txtCteRec' id='txtCteRec' value='' class='form-control txt-short auto'>
                                                    </div>
                                                    <div class="col-lg-2">
                                                    <button type='button' name='btnAddMaquila' id='btnAddMaquila' value='' class='btn btn-md btn-success' onClick="$('#modalAddMaq').modal('show');"><i class="fa fa-lg fa-plus" aria-hidden="true"></i></button>
                                                    </div>
                                                  </div>
                                             
                                              </form>
                                          </div><!-- Fin Contenido Inventario-->
                                   </div>
                                 </div>  
                              </div><!-- /.tab Entradas Existencias --> 
               
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
    <script src="js/maquilas/maquilas.js"></script>

</body>

</html>
<?php 
}
else
{
   header("location: http://".$svr_dir."/sigce/acceso/login.php");	
}
?>