<?php
declare(strict_types=1);

require_once __DIR__ . '/../common/cfg_server.php';

/*
 * PHP 8.3:
 * - session_set_cookie_params() debe ejecutarse antes de session_start().
 * - Se evita utilizar HTTP_HOST como dominio de la cookie.
 * - Se utilizan opciones de seguridad para la cookie de sesión.
 * - Se evita acceder directamente a $_GET['d_s'] cuando no existe.
 */
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => '',
    'secure'   => ($protocolo_actual === 'https:'),
    'httponly' => true,
    'samesite' => 'Lax',
]);

session_start();

$d_s = $_GET['d_s'] ?? '';

if (
    $d_s !== ''
    && isset($_SESSION[$d_s]['seccion_4_4'])
    && $_SESSION[$d_s]['seccion_4_4'] === 'logged'
) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/ico" href="../favicon.ico" />
  <title>SIGCE</title>
  <!--CSS-->
  <link href="css/bootstrap.css" rel="stylesheet">
  <link href="css/metisMenu.min.css" rel="stylesheet">
  <link href="css/sb-admin-2.css" rel="stylesheet">
  <link href="css/estilo.css" rel="stylesheet">
  <link href="media/css/dataTables.bootstrap.min.css" rel="stylesheet">
  <link href="media/font-awesome/css/font-awesome.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="smoothness/jquery-ui.css">

  <!--Javascript-->
  <script src="js/vendor/modernizr-2.6.1-respond-1.1.0.min.js"></script>
 <!-- <script language="javascript" type="text/javascript" src="js/jquery.validate.1.5.2.js"></script>-->
  <script language="javascript" type="text/javascript" src="js/script.js"></script>
  <script src="js/jquery.min.js"></script>
  <script src="js/jquery.cookie.js" type="text/javascript"></script>
  <script type="text/javascript" src="js/sb-admin-2.js"></script>
  <script src="js/bootstrap.js"></script>
  <script src="js/plugins.js"></script>
  <script src="js/main.js"></script>
<!--  <script type="text/javascript" src="js/jquery.js"></script>-->
  <script type="text/javascript" src="js/jquery.addfield.js"></script>
 <!-- <script type="text/javascript" src="js/jquery.min.js"></script>-->

 <script src="plugin/loadingoverlay.min.js"></script>


  <script type="text/javascript">
      // json_encode genera literales JS seguros (antes: echo directo → XSS)
      var id_s=<?php echo json_encode($d_s); ?>;
      var id_depto=<?php echo json_encode($_SESSION[$d_s]['dpto'] ?? ''); ?>;
      var usr_cargo=<?php echo json_encode($_SESSION[$d_s]['cargo'] ?? ''); ?>;
      var user=<?php echo json_encode($_SESSION[$d_s]['s_username'] ?? ''); ?>;
      var clvuser=<?php echo json_encode($_SESSION[$d_s]['id_us'] ?? ''); ?>;

      var moduloAcceso=4;
      var seccionAcceso=4;
    </script>



  <script type="text/javascript">
   /* $(document).ready(function(){
      $("#state").change(function(){
        $.ajax({
          url:"php/procesa.php",
          type: "POST",
          data:"clienteno="+$("#state").val(),
          success: function(opciones){
            $("#criterio").html(opciones);
            $("#wrapper").LoadingOverlay("hide");
          },
          error: function (xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
            $("#wrapper").LoadingOverlay("hide");
          }
        });
      });
    });*/

  </script>

  <script type="text/javascript">
    $(document).ready(function() {
      $("#cancelar").click(function() {
        $("form")[0].reset();
      });
    });
  </script>

  <script type="text/javascript" src="js/jquery-ui.min.js"></script>

  <script type="text/javascript">
    $(function() {
      $('#abbrev').val("");
      $('#abbre').val("");
      $("#state").autocomplete({
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
            $("#nopredio").val("");
            $("#noguia").val("");
            $('#abbrev').val(ui.item.abbrev);
            $('#abbre').val(ui.item.abbre);
            $("#wrapper").LoadingOverlay("show");
              $.ajax({
              url:"php/procesa.php",
              type: "POST",
              data:"clienteno="+ui.item.value+'&idus=' + clvuser,
              success: function(opciones){
                $("#criterio").html(opciones);
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
                  $('#abbrev').val('');
                  $('#abbre').val('');
                  $("#criterio").html('<option value="0"> Elige un Predio o Vivero</option>');

              }

          }
      }).keypress(function(e) {
          if (e.keyCode === 13){
            return false;
        }
      });


      $("#nopredio").autocomplete({
        source:  function(request, response) {
            $.getJSON(
                "php/procesa.php",{ // consulta a esa pagina
                    term : request.term, // parametros extras
                    idus : clvuser
                },
                response
            );
        },
        select: function(event, ui) {
          reg = ui.item;
          $('#abbrev').val(reg.nombrec);
          $('#abbre').val(reg.paraje);
          $("#wrapper").LoadingOverlay("show");
          $("#nopredio").val("");
          $("#noguia").val("");

          tipo = (reg.tipo == 1)?'Predio':'Vivero';
          criterio = '<option value="'+reg.value+'-'+reg.tipo+'">'+reg.paraje+' ('+reg.value+'-'+tipo+')<\/option>';
          $("#criterio").html(criterio);
          $("#state").val(reg.nocliente);
          $("#wrapper").LoadingOverlay("hide");
        },change: function (event, ui) {
            //alert(ui);
            if (!ui.item) {
                this.value = '';
                $('#abbrev').val('');
                $('#abbre').val('');
                $("#nopredio").html('<option value="0"> Elige un Predio o Vivero</option>');

            }

        }
      }).keypress(function(e) {
            if (e.keyCode === 13){
              return false;
          }
      });

      $("#state_abbrev").autocomplete({
        source: "bus_nomcli.php",
        minLength: 1
      });

      $("#noguia").autocomplete({
        source:  function(request, response) {
            $.getJSON(
                "php/procesa.php",{ // consulta a esa pagina
                    guia:request.term, // parametros extras
                    idus : clvuser
                },
                response
            );
        },
        select: function(event, ui) {
          reg = ui.item;
          $('#abbrev').val(reg.nombrec);
          $('#abbre').val(reg.paraje);
          $("#nopredio").val(reg.id_paraje);
          $("#wrapper").LoadingOverlay("show");

          tipo = (reg.tipo == 1)?'Predio':'Vivero';
          criterio = '<option value="'+reg.id_paraje+'-'+reg.tipo+'">'+reg.paraje+' ('+reg.id_paraje+'-'+tipo+')<\/option>';
          $("#criterio").html(criterio);
          $("#state").val(reg.nocliente);
          $("#wrapper").LoadingOverlay("hide");
        },change: function (event, ui) {
            //alert(ui);
            if (!ui.item) {
                this.value = '';
                $('#abbrev').val('');
                $('#abbre').val('');
                $("#nopredio").html('<option value="0"> Elige un # de Guía</option>');

            }

        }
      }).keypress(function(e) {
            if (e.keyCode === 13){
              return false;
          }
      });

    });
  </script>

  <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.0.min.js"><\/script>')</script>

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
          <a class="navbar-brand" href="../index.php?d_s=<?php echo urlencode($d_s)?>"><i class="fa fa-lg fa-home" aria-hidden="true"></i> SIGCE</a>
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
                <li><a href="../acceso/cerrar.php?d_s=<?php echo urlencode($d_s)?>"><i class="fa fa-sign-out fa-fw"></i> Salir</a></li>
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
            <ul class="nav" id="sidebar"> <?php echo $_SESSION[$d_s]['links'] ?? ''; // HTML generado por acceso, se emite tal cual ?> </ul>
          </div>
        <!-- /.sidebar-collapse -->
        </div>
        <!-- /.navbar-static-side -->
      </nav>

    <div id="page-wrapper">
      <div class="row panel-pincipal">
        <div class="col-lg-12" align="center">
          <h3 class="page-header text-white">CONSULTA DE PREDIO</h3>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12" style="margin-top:20px;">
          <div class="panel  panel-che" style="max-width:1100px; margin-left: auto !important; margin-right:auto !important; margin-bottom:75px;">
            <div style="margin-left:auto !important; margin-right:auto !important;">
              <div class="panel  panel-default" style="margin-bottom:0 !important; margin-top:5px !important;">
                <div class="panel-heading" style="text-align:center; font-size:16px; font-weight:bold;">Consultas de Predio
                </div>
                <div class="panel-body">
                  <div class="form">
                    <form class="form-horizontal" action="" onsubmit="" id="formbuscar" method="POST" name="formbuscar" enctype="multipart/form-data" onSubmit="return limpiar()">
                      <input type="hidden" name='usr' id='usr' value='<?php echo htmlspecialchars($_SESSION[$d_s]['s_username'] ?? '', ENT_QUOTES)?>'>
                      <fieldset>
                        <legend align="">Buscar</legend>
                      </fieldset>
                      <div class="form-group">
                        <div class="col-md-1"></div>
                        <label for="asociado" class="col-md-2 control-label">No. de Control:</label>
                        <div class="col-md-1">
                          <input type="text" id="state"  name="state" class='form-control txt-short'>
                        </div>
                        <label for="asociado" class="col-md-2 control-label">No. Predio:</label>
                        <div class="col-md-1">
                          <input type="text" id="nopredio"  name="nopredio" class='form-control txt-short'>
                        </div>
                        <label for="asociado" class="col-md-2 control-label">No. Guía:</label>
                        <div class="col-md-1">
                          <input type="text" id="noguia"  name="noguia" class='form-control txt-short'>
                        </div>
                        <div class="col-md-2"></div>
                      </div>
                      <div class="form-group">
                        <label for="asociado" class="col-md-3 control-label">Nombre:</label>
                        <div class="col-md-7">
                          <input readonly type="text" id="abbrev" name="abbrev" class='form-control'>
                        </div>
                      </div>
                      <div class="form-group">
                         <label for="asociado" class="col-md-3 control-label">Predio o Vivero:</label>
                        <div class="col-md-7">
                          <select class="form-control" id="criterio" name="criterio" >
                            <option value="">Elige un Predio o Vivero</option>
                          </select>
                        </div>
                      </div>
                      <div class="conteiner" align="center">
                        <div class="form-group" >
                          <button type="button" name='cancelar' id='cancelar' class="btn btn-danger" onClick="cancelar()" ><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                          <button type="submit" id='btbuscar' name="btbuscar" class="btn btn-success"><span class="glyphicon glyphicon-search"></span> Buscar</button>
                        </div>
                      </div>
                      <div class="conteiner">
                        <label for="asociado" class="col-lg-3 control-label">Descarga Reporte: </label>
                        <a href="reporteexcel.php?aleat=<?php echo urlencode((string)($_SESSION[$d_s]["id_us"] ?? ""))?>" target="_blank">
                          <img src="images/excell.png" alt="Descarga" width="35px">
                        </a>
                      </div>
                    </form>
                  </div>
                  <br>
                  <div>
                    <section id="resultado"></section>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    $("#formbuscar").on("submit", function(event){
      if($("#state").val()!="" || $("#nopredio").val()!=""){
        if($("#criterio option:selected").attr('value')!=0){
          $("#wrapper").LoadingOverlay("show");
          //alert($(this).serialize());
          event.preventDefault();
          $.ajax({
              url: "consulta.php",
              type: "POST",
              data: $(this).serialize(),
              //dataType: "html"
          }).done(function(response){
          $("#resultado").html(response);
          $("#wrapper").LoadingOverlay("hide");
            }).fail(function(jqXHR, textStatus){
              console.log(textStatus);
          });
        }else{
          alert("Falta predio/vivero");
          return false;
        }
      }else{
        alert("Falta Número de Control");
        return false;
      }
    });
  </script>

  <script>
    function limpiar() {
      setTimeout('document.formbuscar.reset()',2000);
      return false;
    }
  </script>

   <?php include("../includes/acceso.php");?>

</body>
</html>

<?php
  } else {
    header(
        'Location: ' . $protocolo_actual . '//' .
        $svr_dir . '/acceso/login.php'
    );
    exit;
}
?>
