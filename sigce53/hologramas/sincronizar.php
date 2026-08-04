<?php
session_start();
session_set_cookie_params(0, "/", $_SERVER["HTTP_HOST"], 0);
$mod=1;
require_once('../common/cfg_server.php');
$d_s=$_GET['d_s'];
if(isset($_SESSION[$d_s]) && $_SESSION[$d_s]["seccion_1_3"] == "logged")
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
    <link rel="stylesheet" type="text/css" media="screen" href="../css/ui.jqgrid-bootstrap2.css" /> 
    <link href="../css/custom-style.css" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="../css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <script type="text/javascript">
	   var id_depto="<?php echo $_SESSION[$d_s]['dpto']; ?>";
	   var usr_cargo="<?php echo $_SESSION[$d_s]['cargo']; ?>";
	   var user="<?php echo $_SESSION[$d_s]['s_username']; ?>";
	   var clvuser="<?php echo $_SESSION[$d_s]['id_us'];?>";

	   var moduloAcceso=1;
	   var seccionAcceso=3;
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
                    <h3 class="page-header" style="text-align:center;">Sincronizar BD en Linea</h3>                    
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
                                <div id="buscar">
                                  <form class="form-horizontal" id="frmBusca" role="form" action='' method='post'>
                                    <div class="form-group">
                                      <label class="col-lg-2 control-label">No Recibo:</label>
                                      <div class="col-lg-2">
                                        <input type='text' name='txtByRecibo' id='txtByRecibo' class="form-control" style="max-width:200px" value=''>
                                      </div>
                                      <label class="col-lg-2 control-label">No. de Control:</label>
                                      <div class="col-lg-2">
                                        <input type='text' name='txtByCliente' id='txtByCliente' class="form-control" style="max-width:200px" value=''>
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <label class="col-lg-2 control-label">Marca:</label>
                                      <div class="col-lg-4">
                                        <input type='text' name='txtByMarca' id='txtByMarca' class="form-control" style="max-width:360px" value=''>
                                      </div>
                                      <div class="col-md-2" style="padding-left:2px; margin-left:auto;">
                                              <button type="button"  name='btnReload_online' id='btnReload_online' class="btn btn-warning btn-md pull-right" style="margin-right:5px;" onClick="reload_sinc()"> 
                                                  <span class="glyphicon glyphicon-refresh"></span> Actualizar           
                                              </button>&nbsp; 
                                      </div>
                                    </div>
                                  </form>
                                </div>                                 
                                <div id="grid_sinc" style="overflow:scroll; padding-bottom:15px;">  
                                  <div style="margin-top:20px; width:auto !important;">
                                    <table id="jqGrid_sinc" style="font-size:12px !important; width:auto !important;"></table>
                                    <div id="jqGridPager_sinc" style="padding-bottom:5px;"></div>
                                  </div>
                                </div> 
                                <!--MODAL PARA REGISTRAR LA PUESTA EN LINEA-->
                                 <div class="modal fade" id="modalSalida" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                    <div class="modal-dialog" role="document">
                                      <div class="modal-content"  style="max-width:800px !important;">
                                        <div class="modal-header alert-primary">
                                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                          <h4 class="modal-title" id="msjTittle" style="text-align:center !important; font-size:24px;">Detalle de Salida</h4>
                                        </div>
                                        <div class="modal-body">
                                               <form class="form-horizontal" id="frmDetalle_salida" name="frmDetalle_salida" action="" method='post'>
                                                     
                                                     <input type="hidden" name='cve_marca' id='cve_marca' value=''/>
                                                     <input type="hidden" name='id_salidas' id='id_salidas' value=''/>
                                                     <input type="hidden" name='tipo' id='tipo' value=''/>
                                                     <input type="hidden" name='vfini' id='vfini' value=''/>
                                                     <input type="hidden" name='vffin' id='vffin' value=''/>
                                                     <input type="hidden" name='mer_ent' id='mer_ent' value=''/>
                                                     <input type="hidden" name='mer_ent_num' id='mer_ent_num' value=''/>
                                                     <input type="hidden" name='mer_rep_num' id='mer_rep_num' value=''/>
                                                     <input type="hidden" name='usr' id='usr' value='<?php echo $_SESSION[$d_s]['s_username'];?>'/>
                                                    
                                                     <div class="form-group">
                                                        <label class="col-sm-3 control-label">ID Recibo:</label>
                                                        <div class="col-sm-3">
                                                          <input type='text' name='recibo' id='recibo' value='' class='form-control inpt-detalle' readonly/>
                                                        </div>
                                                        <label class="col-sm-4 control-label">No. de Control:</label>
                                                        <div class="col-sm-2">
                                                          <input type='text' name='cliente' id='cliente' value='' class='form-control inpt-detalle-min' readonly/>
                                                        </div>
                                                      </div>
                                                      
                                                      <div class="form-group">
                                                        <label class="col-sm-3 control-label">Marca:</label>
                                                        <div class="col-sm-5">
                                                           <input type='text' name='marca' id='marca' value='' class='form-control inpt-detalle' readonly/>
                                                        </div>
                                                        <label class="col-sm-2 control-label">Serie:</label>
                                                        <div class="col-sm-2">
                                                          <input type='text' name='serie' id='serie' value='' class='form-control inpt-detalle' readonly readonly/>
                                                        </div>
                                                      </div>
                                                      
                                                      <div class="form-group">
                                                        <label class="col-sm-3 control-label">No Solicitud:</label>
                                                        <div class="col-sm-9">
                                                          <input type='text' id="n_solicitud" name="n_solicitud" class='form-control inpt-detalle'/>
                                                        </div>
                                                       
                                                       </div>
                                                                                                         
                                                      <div class="form-group">
                                                        <label class="col-sm-3 control-label">Destino:</label>
                                                        <div class="col-sm-9">
                                                           <input type='text' name='destino' id='destino' value='' class='form-control inpt-detalle' readonly/>
                                                        </div>
                                                      </div>  
                                                      
                                                      <div class="form-group">                                  
                                                        <label class="col-sm-3 control-label">F. Entrega:</label>
                                                        <div class="col-sm-3">
                                                          <input type='text' name='fecha_e' id='fecha_e' value='' class='form-control inpt-detalle-short' readonly/>
                                                        </div>
                                                        <label class="col-sm-2 control-label">Cantidad:</label>
                                                        <div class="col-sm-4">
                                                          <input type='text' name='cantidad' id='cantidad' value='' class='form-control inpt-detalle-short' readonly/> 
                                                        </div>
                                                      </div>
                                                     <div class="form-group" id="mermas_ent" style="display:none;">
                                                        <label class="col-sm-3 control-label">Mermas Entrega:</label>
                                                        <div class="col-sm-9">
                                                          <input type='text' name='mer_ent_fol' id='mer_ent_fol' value='' class='form-control inpt-detalle-auto-warning' readonly/> 
                                                        </div> 
                                                      </div>
                                                                          
                                                      <div class="form-group">
                                                        <label class="col-sm-3 control-label">Folios:</label>
                                                        <div class="col-sm-4">
                                                          <input type='text' name='fini' id='fini' value='' class='form-control inpt-detalle no-border' style="padding-left:6px; font-size:18px;" readonly/> 
                                                        </div>
                                                        <label class="col-sm-1 control-label" style="text-align:left;">al</label>
                                                        <div class="col-sm-4">
                                                          <input type='text' name='ffin' id='ffin' value='' class='form-control inpt-detalle no-border' style="padding-left:6px; font-size:18px" readonly/> 
                                                        </div>
                                                      </div>
                                                           
                                                     <div class="form-group">
                                                     <label class="col-sm-12 control-label" style="text-align:center; padding-top:6px; padding-bottom:8px; font-size:16px;">Mermas reportadas por el cliente</label>
                                                       <label class="col-sm-3 control-label">Motivo Mermas:</label>
                                                        <div class="col-sm-9">
                                                          <select name="mtvo_merma" id="mtvo_merma" class="cbo-medium form-control" onChange="ver_add();">
                                                          <option selected="selected" value="NS">SELECCIONAR</option>
                                                          <option value="CODIGO NO VISIBLE">Código no visible</option>
                                                          <option value="SIN CODIGO">Sin código</option>
                                                          <option value="SE SALTA FOLIOS">Se salta folios</option>
                                                          <option value="DESPRENDIMIENTO DE SELLOS">Desprendimiento de sellos</option>
                                                          </select> 
                                                        </div>
                                                       </div>
                                                      <div class="form-group" id="div_mermas" style="display:none"><!--FOLIOS MERMAS-->
                                                          <label class="col-sm-3 control-label">Indicar mermas:</label>
                                                          <div class="col-sm-4">
                                                            <input type='text' name='fol_ini_mermas' id='fol_ini_mermas' value='' class='txt-short form-control' style="float:left;"/>
                                                             <button type="button" name='btnAddMerma' id='btnAddMerma' class="btn btn-success btn-xs" onClick="addMerma(1)">
                                                              <span class="glyphicon glyphicon-plus"></span>
                                                             </button>
                                                          </div>
                                                          <div class="col-sm-5">
                                                            <input type='text' name='fol_fin_mermas' id='fol_fin_mermas' value='' class='txt-short form-control' style="float:left;"/>
                                                             <button type="button" name='btnAddMerma2' id='btnAddMerma2' class="btn btn-success btn-xs" onClick="addMerma(2)">
                                                              <span class="glyphicon glyphicon-plus"></span>
                                                             </button>
                                                          </div>
                                                      </div>
                                                      <div class="form-group" id="div_fols" style="display:none">
                                                          <label class="col-sm-3 control-label">Folios mermas:</label>
                                                          <div class="col-sm-9">                                    
                                                            <textarea name='mer_rep_fol' id='mer_rep_fol' class="form-control" rows="2" style="float:left; max-width:90%;" readonly></textarea>
                                                             <button type="button" name='btnDelMermas' id='btnDelMermas' class="btn btn-danger btn-xs" onClick="delMermas()" style="visibility:visible">
                                                              <span class="glyphicon glyphicon-minus"></span>
                                                             </button>
                                                          </div>
                                                      </div>
                                                                       
                                                      <div class="form-group">
                                                        <label class="col-sm-3 control-label">No Mermas:</label>
                                                        <div class="col-sm-9">
                                                          <input type='text' name='mer_rep' id='mer_rep' value='' onKeyPress="return acceptNum(event,this);" class='form-control inpt-detalle' readonly/> 
                                                        </div>
                                                      </div>
                                                      
                                                      <div class="form-group">
                                                         <label class="col-sm-3 control-label">Sellos Entregados:</label>
                                                          <div class="col-sm-9">
                                                            <input type='text' name='total' id='total' value='' class='form-control inpt-detalle-short' readonly/> 
                                                          </div>
                                                       </div>
                                                                         
                                                      <div class="form-group">
                                                        <label class="col-sm-3 control-label">Observaciones:</label>
                                                        <div class="col-sm-9">
                                                          <textarea rows="3" cols="10" id="observ" name="observ" class='form-control' style="float:left; max-width:90%;"></textarea>
                                                        </div>
                                                      </div>
                                                                   
                                                 </form>
                                                
                                              
                                        </div>
                                        <div class="modal-footer">
                                          <button type="button" id="btnSinc" onClick="sinc_salida();" class="btn btn-success">Continuar</button> <button type="button" id="btnCancelDelete" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                                        </div>
                                      </div>
                                    </div>
                                  </div> <!--END MODAL PARA REGISTRAR LA PUESTA EN LINEA--> 
      
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
    <!-- This is the Javascript file of lenguaje español jqgrid --> 
	<script src="../js/i18n/grid.locale-es.js"></script>
    <!-- This is the Javascript file of jqGrid -->   
    <script src="../js/jquery.jqGrid.min.js"></script>
    <script src="js/sincronizar/sincronizar.js?1"></script>
	
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