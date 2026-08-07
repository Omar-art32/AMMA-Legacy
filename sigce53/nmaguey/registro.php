<?php
/**
 * registro.php — Pantalla "Atributos" (Predios y Viveros). Cabecera migrada a 8.3.
 *
 * Cambios (mismos patrones aplicados en acceso/ e index.php):
 *  - Usa common/config.php en vez de reconstruir $svr_dir y $protocolo a mano.
 *  - Redirect corregido: apuntaba a "/sigce/" (404) -> ahora usa APP_BASE_PATH.
 *  - Dominio de cookie sin puerto (HTTP_HOST puede traer :puerto e invalidarla).
 *  - $_GET['d_s'] con ?? '' para no emitir warning si falta el parametro.
 */

require_once('../common/config.php');   // define APP_BASE_PATH, $svr_dir, $protocolo_actual

$cookie_domain = preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST'] ?? '');
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => $cookie_domain,
    'secure'   => ($protocolo_actual === 'https:'),
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

$mod = 1;
$d_s = $_GET['d_s'] ?? '';

if (isset($_SESSION[$d_s]) && $_SESSION[$d_s]["seccion_4_6"] == "logged") {
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="Description" content="Asociacion De Maguey Y Mezcal Artesanal A. C.">
  <meta name="author" content="">

  <title>SIGCE V1.0</title>

  <link href="css/bootstrap.css?<?php echo uniqid(); ?>" rel="stylesheet">
  <link href="../css/metisMenu.min.css" rel="stylesheet">
  <link href="../css/sb-admin-2.css" rel="stylesheet">
  <link href="../css/smoothness/jquery-ui.css" rel="stylesheet">
  <link href="../css/custom-style.css" rel="stylesheet">
  <link href="../css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <link href="../css/bootstrap-toggle.css" rel="stylesheet">
  <link href="css/cuentas.css?1" rel="stylesheet">
  
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script type="text/javascript">
   const user          = "<?php echo $_SESSION[$d_s]['s_username']; ?>";
   const id_usuario    = "<?php echo $_SESSION[$d_s]['id_us']; ?>";
   const id_session    = "<?php echo $d_s; ?>";
   const clvuser       = "<?php echo $_SESSION[$d_s]['id_us'];?>";
   const moduloAcceso  = 13;
	 const seccionAcceso = 2;
   const nivel         = "<?php echo $_SESSION[$d_s]['sec_lvl_4_6']; ?>";

   
  </script>
  <style>
    #modalPublicarN {
      z-index: 1000;
      /* Aumenta este valor si es necesario */
    }
    .terminada {
      background: #c8e6c9 !important;
    }
    .loader {
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999;
        background: url('https://lkp.dispendik.surabaya.go.id/assets/loading.gif') 50% 50% no-repeat rgb(249,249,249);
    }
  </style>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@100;200;300;400;500&display=swap" rel="stylesheet">
</head>
<body>
  <div id="pageLoading"></div>
  <header>
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation" style="margin-bottom: 0">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="../index.php?d_s=<?php echo $d_s ?>"><i class="fa fa-lg fa-home" aria-hidden="true"></i> SIGCE V1.0</a>
        <div class="menu-toggler sidebar-toggler">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </div>
        <ul class="nav navbar-top-links navbar-right">
          <li class="dropdown">
            <a name="link-admin" class="dropdown-toggle" data-toggle="dropdown" href="#">
              <i class="fa fa-envelope fa-fw"></i>
              <i class="fa fa-caret-down"></i>
            </a>
          </li>
          <li class="dropdown">
            <a name="link-config" class="dropdown-toggle" data-toggle="dropdown" href="#">
              <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
            </a>
            <ul class="dropdown-menu dropdown-user">
              <li>
                <a href="#"><i class="fa fa-gear fa-fw"></i> Configuraciones</a>
              </li>
              <li class="divider"></li>
              <li>
                <a href="../acceso/cerrar.php?d_s=<?php echo $d_s ?>"><i class="fa fa-sign-out fa-fw"></i> Salir</a>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  <div id="wrapper">
    <nav role="navigation" style="margin-bottom: 0; margin-top: -1px;">
      <div class="navbar-default sidebar" role="navigation">
        <div class="sidebar-nav navbar-collapse" id="sidebar-area">
          <ul class="nav" id="sidebar">
            <?php echo $_SESSION[$d_s]['links']; ?>
          </ul>
        </div>
      </div>
    </nav>
    <div id="page-wrapper">
      <div class="row" style="background-color: #064420;color:#FBFBFB;box-shadow: 1px 1px 5px #999;margin-top: 7px;border-radius: 5px;">
        <div class="col-lg-12 col-md-12 col-xs-12">
          <h3 class="page-header" style="text-align:center;">PREDIOS Y VIVEROS</h3>
        </div>
      </div>
      <div class="col-lg-12 col-md-12 col-xs-12 filtros" id="filtrosPrincipal" style=" margin-top: 10px;margin-bottom: 10px;">
        <form class="form-horizontal">
          <div class="form-group">
            <label class="col-lg-1 col-md-2 col-sm-1 control-label" style="font-weight: 500;font-size: 12px;">Nº DE CONTROL:</label>
            <div class="col-lg-1 col-md-2 col-sm-1">
              <input type='text' v-model="filters.no_control" name='fil_control' id='fil_control' class='form-control' maxlength="6" autocomplete="off"/>
            </div>
            <!--<div class="col-lg-2 col-md-2 col-sm-2" style="margin-top:7px">
              <span id="nomEmpresa" style="background: #AED5FC;padding: 5px;color: #060D25;font-weight: 500;">{{filters.nombre}}</span>
            </div>-->
            <label class="col-lg-1 col-md-2 col-sm-2 control-label" style="font-weight: 500;font-size: 12px;">CLIENTE, PREDIO, PRODUCTOR, REPRESENTANTE, LOCALIDAD:</label>
            <div class="col-lg-2 col-md-3 col-sm-3">
              <input type='text' v-model="filters.texto" class='form-control' maxlength="6" autocomplete="off"/>
            </div>
            <label class="col-lg-1 col-md-2 col-sm-1 control-label" style="font-weight: 500;font-size: 12px;">TIPO DE REGISTRO:</label>
            <div class="col-lg-1 col-md-2 col-sm-2">
              <select class="form-control nuevo_cmb prod_cmb" v-model="filters.tiporegistro">
                <option value=null selected props="disabled" disabled>Selecciona</option>
                <option value="1">EN SITIO</option>
                <option value="2">DOCUMENTAL NORMAL</option>
                <option value="3">DOCUMENTAL EXCLUSIVO</option>
              </select>
            </div>
            <label class="col-lg-1 col-md-2 col-sm-1 control-label" style="font-weight: 500;font-size: 12px;">ATRIBUTOS:</label>
            <div class="col-lg-1 col-md-2 col-sm-2">
              <select class="form-control nuevo_cmb prod_cmb" v-model="filters.atributo">
                <option value=null selected props="disabled" disabled>Selecciona</option>
                <option v-for="atributo in gatributos" :value="atributo.id" :key="atributo.id">{{atributo.value}} </option>
              </select>
            </div>
            <div class="col-lg-2 col-md-1 col-sm-1" style="text-align: center;">
              <button type="button" class="btn btn-success btn-circle btn-lg" @click="actualizaRegistros"><i class="fa fa-search"></i></button>&nbsp;
              <button type="button" class="btn btn-warning btn-circle btn-lg" @click="restart"><i class="fa fa-refresh"></i></button>&nbsp;
              <button type="button" class="btn btn-primary btn-circle btn-lg" alt="Registrar predio" @click="nuevoRegistro=true"><i class="fa fa-floppy-o"></i></button>
            </div>
            <!--<div class="col-lg-1 col-md-1 col-sm-1" id="div_btn_reinicia" style="text-align: right;">
              <button type="button" class="btn btn-primary" style="font-size:11px; width:130px;border-radius:5px;" @click="nuevoRegistro=true" >REGISTRAR PREDIO</button>
            </div>-->
          </div>
        </form>
      </div>
            <!--<div id="seccion_principal" class="desactiva_seccion">-->
     
      <div >
        
            <div class="col-lg-12">
              <table class="table-mini-font" id="predios" >
              </table>
            </div>
        
      </div>


    </div>

    <modal-registro v-if="nuevoRegistro" :idregistro="idRegistro" @cierra="actualizaRegistros" ></modal-registro>
  </div>

  <div id="modalContinua" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" >
          <h5>GENERAR ESTADO DE CUENTA</h5>
        </div>
        <div class="modal-body" style="max-height: calc(100vh - 210px); overflow-y: auto; overflow-x: auto;">
          <form class="form-horizontal" action="/action_page.php">
            <div class="form-group">
              <div class="col-sm-12" id="cn_tbl_desc">

              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-4" for="saldo_anterior">Total Saldo Anterior:</label>
              <!--<div class="col-sm-5">
                <input type="number" class="form-control" value="0" id="saldo_anterior" disabled>
              </div>-->
              <label class="control-label col-sm-5" for="saldo_anterior" id="saldo_anterior"></label>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-4" for="fecha_limite">Fecha límite de Pago:</label>
              <div class="col-sm-5">
                <input type="text" class="form-control" id="fecha_limite">
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-offset-4 col-sm-5">
                <div class="checkbox">
                  <label><input type="checkbox" id="cbx_pago_inmediato"> Pago Inmediato</label>
		</div>
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-offset-4 col-sm-5">
                <div class="checkbox">
                  <label><input type="checkbox" id="cbx_saldo_anterior"> Incluir saldo anterior</label>
                </div>
              </div>
            </div>
            <hr>
            <div class="form-group">
              <div class="col-sm-offset-2 col-sm-8">
                <div class="col-lg-6" style="text-align: right;">
                  <button style="width: 150px;" type="button" class="btn btn-primary" onclick="vistaPrevia();">Vista previa</button>
                </div>
                <div class="col-lg-6" style="text-align: left;">
                  <button style="width: 150px;" type="button" class="btn btn-success" onclick="generarEdoCuenta();">Generar</button>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
        </div>
      </div>
    </div>
  </div>

  <div id="modalPublicar" class="modal fade" role="dialog">
    <div class="modal-dialog ">
      <div class="modal-content">
        <div class="modal-header" >
          <h4>PUBLICAR ESTADO DE CUENTA</h4>
        </div>
        <div class="modal-body " style="max-height: calc(100vh - 210px); overflow-y: auto; overflow-x: auto;">
          <form class="form-horizontal" action="/action_page.php">
            <div class="form-group">
              <div class="col-sm-12" id="cn_tbl_desc">

              </div>
            </div>
            <style>
              .slow .toggle-group { transition: left 0.7s; -webkit-transition: left 0.7s; }
              .glyphicon-refresh-animate {
                  -animation: spin .7s infinite linear;
                  -webkit-animation: spin2 .7s infinite linear;
              }
              @-webkit-keyframes spin2 {
                  from { -webkit-transform: rotate(0deg);}
                  to { -webkit-transform: rotate(360deg);}
              }
              @keyframes spin {
                  from { transform: scale(1) rotate(0deg);}
                  to { transform: scale(1) rotate(360deg);}
              }
            </style>
            <div class="form-group">
              <div class="col-sm-offset-1 col-sm-2">
                <div class="checkbox" id="paso1">
                  <input type="checkbox" checked data-toggle="toggle1" data-style="slow" data-size="small" data-on="Sí" data-off="No" data-onstyle="success" data-offstyle="default">
		            </div>
              </div>
              <div class="col-sm-6">
                  <label><b>Plataforma de Certificación</b></label><br>
                  <span id="anotaplataforma" style="font-size: 9px;font-weight: bold;background-color: #ffc133;"></span>
              </div>
              <div class="col-sm-2">
                  <label><b>Publicado</b></label><br>
                  <span id="pcPublicado" style="font-size: 9px;font-weight: bold;background-color: #ffc133;"></span>
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-offset-1 col-sm-2">
                <div class="checkbox" id="paso2" >
                  <input type="checkbox" checked data-toggle="toggle2" data-style="slow" data-size="small" data-on="Sí" data-off="No" data-onstyle="success" data-offstyle="default">
		            </div>
              </div>
              <div class="col-sm-5">
                  <label><b>Whatsapp</b></label><br>
                  <select class="form-control" id="numTelefono" onchange="cambiaPeriodo(this.value)" style="width:200px">
                  </select>
                  <div ><span id="numWP" style="font-weight: bold;background-color: #A9F5A9;">Whatsapp</span></div>
              </div>
              <div class="col-sm-1">
                <label><b>-</b></label><br>
              </div>
              <div class="col-sm-2">
                  <label><b>Último Envío</b></label><br>
                  <span id="wpPublicado" style="font-size: 9px;font-weight: bold;background-color: #ffc133;"></span>
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-offset-1 col-sm-2">
                <div class="checkbox" id="paso3">
                  <input type="checkbox" checked data-toggle="toggle3" data-style="slow" data-size="small" data-on="Sí" data-off="No" data-onstyle="success" data-offstyle="default">
                </div>
              </div>
              <div class="col-sm-6">
                  <label><b>Correo Electrónico</b></label><br>
                  <span id="correoe" style="font-weight: bold;background-color: #A9F5A9;"></span><br>
                  <span id="anotacorreoe" style="font-size: 9px;font-weight: bold;background-color: #ffc133;"></span>
              </div>
              <div class="col-sm-2">
                  <label><b>Último Envío</b></label><br>
                  <span id="cePublicado" style="font-size: 9px;font-weight: bold;background-color: #ffc133;"></span>
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-offset-1 col-sm-10">
                  <label>Comentarios Adicionales:</label>
                  <textarea class="form-control" id="obsPublica" placeholder="Observaciones para el correo y plataforma." rows="2" maxlength="200"> </textarea>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success" id="btnPublicar" onclick="promptPublica();">PUBLICAR</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
        </div>
      </div>
    </div>
  </div>

  <div id="modalPDFVier" class="modal fade">
      <div class="modal-dialog modal-lg modal-fullscreen" role="document">
        <div class="modal-content content-fullscreen">
          <div id="divAnima"></div>
          <div class="modal-body" id="divPDF" style="max-height: calc(100vh - 210px);overflow-y: auto;">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>

  <div id="modalPrueba" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" >
          <span id="title-modal"></span>
        </div>
        <div class="modal-body" style="max-height: calc(100vh - 210px); overflow-y: auto; overflow-x: auto;">
          <form class="form-horizontal">
            <div class="col-lg-12" id="divTable">
              <div class="table-responsive">
                <table class="table-mini-font" id="tablaTickets" >
                </table>
	      </div>
              <br>
              <div class="col-lg-12" id="divExtra">
                <div class="table-responsive">
                  <table class="table-mini-font" id="tablaTicketsR" >
                  </table>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
        </div>
      </div>
    </div>
  </div>

  <div id="modalInformacion" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" >
          <span>INFORMACIÓN </span>
        </div>
        <div class="modal-body" style="max-height: calc(100vh - 210px); overflow-y: auto; overflow-x: auto;">
          <form class="form-horizontal">
            <div class="col-lg-12">
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead style="background: #11324D;color: #F8F1F1;">
                    <tr>
                      <th>#</th>
                      <th>FECHA</th>
                      <th>MONTO</th>
                      <th>USUARIO</th>
                      <th>OBSERVACIONES</th>
                    </tr>
                  </thead>
                  <tbody id="movs-body">
                    <tr>
                      <td colspan="4" style="text-align: center;">
                        <div style="padding: 50px;font-size: 16px;">No se encontraron movimientos</div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
        </div>
      </div>
    </div>
  </div>

  <div id="modalServManual" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" >
          <div class="row">
            <span id="codigo-modal-manual"></span>
          </div>
          <div class="row">
            <span id="title-modal-manual"></span>
          </div>
        </div>
        <div class="modal-body" style="max-height: calc(100vh - 210px); overflow-y: auto; overflow-x: auto;">
          <form class="form-horizontal">
            <div class="form-group">
              <label class="control-label col-sm-3" for="m_fecha">Fecha:<span class="requerido">*</span></label>
              <div class="col-sm-4">
                <input type="text" class="form-control" id="m_fecha">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-3" for="m_unidades">Unidad Lts /Pzas:<span class="requerido">*</span></label>
              <div class="col-sm-4">
                <input type="number" class="form-control" id="m_unidades" value="1">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-3" for="email">Precio unitario:<span class="requerido">*</span></label>
              <div class="col-sm-4">
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-usd"></i></span>
                  <input id="m_unitario" type="number" class="form-control" name="m_unitario" value="0">
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-3" for="m_concepto">Concepto:<span class="requerido">*</span></label>
              <div class="col-sm-8">
                <textarea class="form-control" id="m_concepto"> </textarea>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-3" for="email">Total:<span class="requerido">*</span></label>
              <div class="col-sm-4">
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-usd"></i></span>
                  <input id="m_total" onchange="calculaDiferencia()" type="number" class="form-control" name="m_total" value="0">
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-3" for="email">Pagado:<span class="requerido">*</span></label>
              <div class="col-sm-4">
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-usd"></i></span>
                  <input id="m_pagado" onchange="calculaDiferencia()" type="number" class="form-control" name="m_pagado" value="0" >
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-3" for="email">Pendiente:<span class="requerido">*</span></label>
              <div class="col-sm-4">
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-usd"></i></span>
                  <input id="m_pendiente" type="number" class="form-control" name="m_pendiente" value="0" disabled>
                </div>
              </div>
            </div>
            <hr>
            <div class="form-group">
              <label class="control-label col-sm-3" for="m_comm_internos"><img src="images/comentarios_int.png" width="16 " /> Comentarios Internos:</label>
              <div class="col-sm-8">
                <textarea class="form-control" id="m_comm_internos"> </textarea>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-3" for="m_comm_internos"><img src="images/comentarios_ext.png" width="16 " /> Comentarios en estado de cuenta:</label>
              <div class="col-sm-8">
                <textarea class="form-control" id="m_comm_externos"> </textarea>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer"  id="btns_manual">

        </div>
      </div>
    </div>
  </div>
  
  <div class="footer"></div>

  <script src="../js/jquery.min.js"></script>
  <script src="../js/jquery.cookie.js"></script>
  <script src="../js/bootstrap.min.js"></script>
  <script src="../js/sb-admin-2.js"></script>
  <script src="../js/jquery-ui.min.js"></script>
  <link href="../solicitudes_ocui/plugins/bootstrap-table-master/src/bootstrap-table.css" rel="stylesheet">
  <script src="../solicitudes_ocui/plugins/bootstrap-table-master/src/bootstrap-table.js"></script>
  <script src="../solicitudes_ocui/plugins/bootstrap-table-master/src/locale/bootstrap-table-es-MX.js"></script>
  <script src="../js/bootstrap-toggle.js"></script>
  <script src="../js/nbootstrap.js?<?php echo uniqid(); ?>"></script>
  <link href="../solicitudes_ocui/css/confirm.css" rel="stylesheet">
  <script src="../solicitudes_ocui/js/confirm.js?711"></script>
  <script src="js/index.js?<?php echo uniqid(); ?>" type="module"></script>
  <script src="../solicitudes_ocui/plugins/loader/loadingoverlay_progress.min.js"></script>
  <script src="../solicitudes_ocui/plugins/loader/loadingoverlay.min.js"></script>
  <script src="js/moment.min.js"></script>
  <script src="js/moment-with-locales.min.js"></script>
  <script type="text/javascript" src="../registros_oc/etiquetas/plugins/jquery.blockUI.js"></script>

  <!-- <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script> -->
  <script src="js/vue.global.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  
      
    <!-- <link href="css/style2.css?015" rel="stylesheet"> -->
    <?php include("../includes/acceso.php"); ?>

   </body>
</html>
<?php
} else {
    // Antes: "/sigce/acceso/login.php" (404). Ahora ruta correcta via APP_BASE_PATH.
    header('Location: ' . $protocolo_actual . '//' . $svr_dir . '/acceso/login.php');
    exit;
}
?>
