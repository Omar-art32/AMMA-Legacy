var SOLICITUD_SELECCIONADA = 0;
var SOLICITUD_SELECCIONADA_NUM = '';
var ACTIVIDAD_SOLICITUD = 0;
var _LISTA_DOCS_A_MODIFICAR = [];
var _LISTA_DOCS_A_MODIFICAR_UI = [];
var _PERMISOS_ = {};
var _TIPO_MODAL = '';
var _INSERT_ = {};
var _INSERT_TIPO = '';
$(document).ready(function() {




  (function($) {
    $.fn.serializefiles = function() {
      var obj = $(this);
      var formData = new FormData();
      $.each($(obj).find("input[type=\"file\"]"), function(i, tag) {
        $.each($(tag)[0].files, function(i, file) {
          formData.append(tag.name, file);
        });
      });
      var params = $(obj).serializeArray();
      $.each(params, function(i, val) {
        formData.append(val.name, val.value);
      });
      return formData;
    };
  })(jQuery);



  putTable();
  $("#imp_inicio_vig").datepicker({
    changeMonth: true,
    changeYear: true,
    firstDay: 1,
    onClose: function(selectedDate) {
      $("#imp_fin_vig").datepicker("option", "minDate", selectedDate);
    }
  });

  $("#imp_fin_vig").datepicker({
    changeMonth: true,
    changeYear: true,
    firstDay: 1,
    onClose: function(selectedDate) {}
  });

  $("#imp_fecha_prop").datepicker({
    changeMonth: true,
    changeYear: true,
    firstDay: 1,
    onClose: function(selectedDate) {}
  });

  $("#filFechaIni").datepicker({
    changeMonth: true,
    changeYear: true,
    firstDay: 1,
    onClose: function(selectedDate) {

    }
  });
  $("#filFechaIni").val('2020-01-01');

  $("#filFechaFin").datepicker({
    changeMonth: true,
    changeYear: true,
    firstDay: 1,
    onClose: function(selectedDate) {

    }
  });
  $("#filFechaFin").val(getFechaActual(0));


  $("#filverificador").autocomplete({
    source: function (request, response) {
      var texto = request.term;
      $.ajax({
        url: "php/solicitudes_ocuv.php",
				dataType: "json",
        type: "POST",
				data: {
          action:'suggestUIAcredita',
					term: texto
				},
				success: function (data) {
					response(data);
					return false;
				}
			});
		},
    minLength: 1,
    maxRows: 15,
    select: function(e, ui) {
      $("#filverificador").defaultBorder();
    },change: function (event, ui) {
      if (!ui.item) {
        this.value = '';
        $("#filverificador").redBorder();
        $("#filverificador").focus();
        $(`#p_fil_inspector`).css('color', 'red');
        $(`#p_fil_inspector`).text("SELECCIONA UN INSPECTOR DE LA LISTA");
      }else{
        $(`#p_fil_inspector`).css('color', '#1f405d');
        $(`#p_fil_inspector`).text("");
        $("#filverificador").defaultBorder();

      }
    }
  });


  $("#inp_actividad").change(function() {
    var actividad = this.value;

    $.ajax({
      type: "POST",
      url: "php/solicitudes_ocuv.php",
      contentType: "application/x-www-form-urlencoded;charset=UTF-8",
      data:{
        action:'opcionesComboSolicitud',
        actividad:actividad
      },
      dataType: "json",
      success: function(data, textStatus, jqXHR) {
        if (data.status == "correcto") {
          var itemVariables = data.datos;

          $('#txtReqConfirmacion').bootstrapToggle('off');

          for (var i = 0; i < itemVariables.length; i++) {
            var row = itemVariables[i];
            /************************************/
            if (row.nombre === "clienteEnvia") {
              if (row.visible === 'N') {
                $("#div_cliente_envia").slideUp();
                $("#imp_cli_envia").val('');
              }else{
                $("#div_cliente_envia").slideDown();
              }
            }
            /************************************/
            if (row.nombre === "clienteRecibe") {
              if (row.visible === 'N') {
                $("#div_cliente_recibe").slideUp();
                $("#imp_cli_recibe").val('');
              }else{
                $("#div_cliente_recibe").slideDown();
              }
            }
            /************************************/
            if (row.nombre === "maquilador") {
              if (row.visible === 'N') {
                $("#div_cliente_maquilador").slideUp();
                $("#imp_maquila").val('');
              }else{
                $("#div_cliente_maquilador").slideDown();
              }
            }
            /************************************/
            if (row.nombre === "responsableUno") {
              if (row.visible === 'N') {
                $("#div_responsable_uno").slideUp();
                $("#imp_responsable_uno").val('');
              }else{
                $("#div_responsable_uno").slideDown();
              }
            }
            /************************************/
            if (row.nombre === "responsableDos") {
              if (row.visible === 'N') {
                $("#div_responsable_dos").slideUp();
                $("#imp_responsable_dos").val('');
              }else{
                $("#div_responsable_dos").slideDown();
              }
            }
            /************************************/
            if (row.nombre === "direccionUno") {
              if (row.visible === 'N') {
                $("#div_domicilio_uno").slideUp();
                $("#imp_domicilio_uno").val('');
              }else{
                $("#div_domicilio_uno").slideDown();
              }
            }
            /************************************/
            if (row.nombre === "direccionDos") {
              if (row.visible === 'N') {
                $("#div_domicilio_dos").slideUp();
                $("#imp_domicilio_dos").val('');
              }else{
                $("#div_domicilio_dos").slideDown();
              }
            }
            /************************************/
            if (row.nombre === "telefonoUno") {
              if (row.visible === 'N') {
                $("#div_telefono_uno").slideUp();
                $("#imp_telefono_uno").val('');
              }else{
                $("#div_telefono_uno").slideDown();
              }
            }
            /************************************/
            if (row.nombre === "telefonoDos") {
              if (row.visible === 'N') {
                $("#div_telefono_dos").slideUp();
                $("#imp_telefono_dos").val('');
              }else{
                $("#div_telefono_dos").slideDown();
              }
            }
            /************************************/
            if (row.nombre === "vigencia") {
              if (row.visible === 'N') {
                $("#div_vigencia").slideUp();
                $("#imp_inicio_vig").val('');
                $("#imp_fin_vig").val('');
              }else{
                $("#div_vigencia").slideDown();
              }
            }
            /************************************/
            if (row.nombre === "descActividad") {
              $("#imp_descripcion").val();
            }
            /************************************/
            if (row.nombre === "cliProductor") {
              if (row.visible === 'N') {
                $("#div_cliente_productor").slideUp();
                $("#imp_productor").val('');
              }else{
                $("#div_cliente_productor").slideDown();
              }
            }
            /************************************/
            if (row.nombre === "tipoTraslado") {
              if (row.visible === 'N') {
                $("#div_tipo_traslado").slideUp();
                $('#toogleTR').bootstrapToggle('off');
              }else{
                $("#div_tipo_traslado").slideDown();

                if (row.value === 'S') {
                  $('#toogleTR').bootstrapToggle('on');
                }else{
                  $('#toogleTR').bootstrapToggle('off');
                }
              }
            }
            /************************************/
            if (row.nombre === "reqHolograma") {
              if (row.visible === 'N') {
                $("#div_requiere_holo").slideUp();
                $('#toogleReqHolo').bootstrapToggle('off');
              }else{
                $("#div_requiere_holo").slideDown();
              }
            }
            /************************************/
            if (row.nombre === "hologramaEnt") {
              if (row.visible === 'N') {
                $("#div_holo_entrega").slideUp();
                $('#toogleHoloEntrega').bootstrapToggle('off');
              }else{
                $("#div_holo_entrega").slideDown();
              }
            }
            /************************************/
            if (row.nombre === "tipoBaja") {
              if (row.visible === 'N') {
                $("#div_tipo_baja").slideUp();
                $("input[name=optradio_baja]").prop('checked', false);
              }else{
                $("#div_tipo_baja").slideDown();
              }
            }

            if (row.nombre === "direccionIns") {
              if (row.visible === 'N') {
                $("#div_domicilio_ins").slideUp();
                $("#tabla_ins").empty();
                $('#imp_domicilio_ins').empty();
              }else{
                $("#div_domicilio_ins").slideDown();
              }
            }
            /************************************/

            if (row.nombre === "detActividades") {
              if (row.visible === 'N') {
                $("#div_det_actividades").slideUp();
                $( "#form-sol input:checked" ).prop( "checked", false );
              }else{
                $("#div_det_actividades").slideDown();
              }
            }
            /************************************/

            /************************************/
            if (row.nombre === "actProductor") {
              if (row.visible === 'N') {
                $("#div_act_productor").slideUp();
                $('#toogleActProductor').bootstrapToggle('off');
              }else{
                $("#div_act_productor").slideDown();
              }
            }
            /************************************/

          }


          if(actividad == 31 || actividad == 32 || actividad == 33 || actividad == 34 || actividad == 35){
            $("#div_requiere_maquila").slideDown();
            $("#div_requiere_vigencia").slideDown();         
          } else {
            $("#div_requiere_maquila").slideUp();
            $("#div_requiere_vigencia").slideUp();
          }

          $('#toogleReqMaquila').bootstrapToggle('off'); 
          $('#toogleReqVigencia').bootstrapToggle('off');  
          $('#toogleActProductor').bootstrapToggle('off');      


        }
      },error: function(jqxhr, status, errorGenerado) {
        alert("Ha ocurrido un error al cargar las actividades: " + jqxhr.responseText);
      }
    });

  });

  $("#toogleReqMaquila").change(function() {

    if($("#inp_actividad").val() == 31 || $("#inp_actividad").val() == 32 || $("#inp_actividad").val() == 33 || $("#inp_actividad").val() == 34 || $("#inp_actividad").val() == 35){
    
      $("#imp_domicilio_ins").enable();
      $("#tabla_ins").empty();
      $('#imp_domicilio_ins').empty();
      $("#div_estado_maquila").hide();
      $("#labelEstadoClienteMaq").empty();

      if(!$('#toogleReqMaquila').is(':checked')){
        $("#div_cliente_maquilador").slideUp();
        $("#imp_maquila").val('');
        $(`#labelMDClienteMaq`).text('');
        if($("#inp_solicitante").val()!=""){
          loadInstalaciones(0,[]);
        }   
      }else{
        $("#div_cliente_maquilador").slideDown();
        $("#imp_domicilio_ins").disabled();
      }
    }

  });

  $("#toogleReqVigencia").change(function() {

  if($("#inp_actividad").val() == 31 || $("#inp_actividad").val() == 32 || $("#inp_actividad").val() == 33 || $("#inp_actividad").val() == 34 || $("#inp_actividad").val() == 35){

    if(!$('#toogleReqVigencia').is(':checked')){
      $("#div_vigencia").slideUp();
      $("#imp_inicio_vig").val('');
      $("#imp_fin_vig").val('');
    }else{
      $("#div_vigencia").slideDown();
    }
  }
   
   });

  $("#imp_domicilio_uno").autocomplete({
    source: function (request, response) {
      var texto = request.term;
      var consulta_direccion = ($("#inp_actividad").val() == 31 || $("#inp_actividad").val() == 32 || $("#inp_actividad").val() == 33 || $("#inp_actividad").val() == 34 || $("#inp_actividad").val() == 35 ) ? "suggestDomicilio" : "suggestInstalaciones";
      $.ajax({
        url: "php/solicitudes_ocuv.php",
				dataType: "json",
        type: "POST",
				data: {
          action: consulta_direccion,
					term: texto,
          cliente:$("#inp_solicitante").val()
				},
				success: function (data) {
					response(data);
					return false;
				}
			});
		},
    minLength: 1,
    maxRows: 15,
    select: function(e, ui) {
      $("#imp_domicilio_uno").val(ui.item.value);
    },change: function (event, ui) {}
  });

  $("#inp_solicitante").autocomplete({
    source: function (request, response) {
      var texto = request.term;
      $.ajax({
        url: "php/solicitudes_ocuv.php",
				dataType: "json",
        type: "POST",
				data: {
          action:'suggestCliente',
					term: texto
				},
				success: function (data) {
					response(data);
					return false;
				}
			});
		},
    minLength: 1,
    maxRows: 15,
    select: function(e, ui) {
      $("#labelMDClienteSol").text(ui.item.razon);
      $(`#labelMDClienteSol`).css('color', '#1f405d');
      $("#inp_solicitante").defaultBorder();   
      if(ui.item.estatus != ""){
        $("#div_estado_cliente").show();
        $("#labelEstadoClienteSol").html(ui.item.estatus);
      }else {
        $("#div_estado_cliente").hide();
        $("#labelEstadoClienteSol").empty();
      }
    },change: function (event, ui) {
      $("#tabla_ins").empty();
      $("#imp_domicilio_uno").clear();
      $("#labelEstadoClienteSol").empty();
      $("#div_estado_cliente").hide();
      if (!ui.item) {
        this.value = '';
        $("#inp_solicitante").redBorder();
        $("#inp_solicitante").focus();
        $(`#labelMDClienteSol`).css('color', 'red');
        $(`#labelMDClienteSol`).text("<-- SELECCIONA UN CLIENTE DE LA LISTA");
        $('#imp_domicilio_ins').empty();
      }else{
        $(`#labelMDClienteSol`).css('color', '#1f405d');
        $("#inp_solicitante").defaultBorder();
        $(`#labelMDClienteSol`).text(ui.item.razon);
        loadInstalaciones(0,[]);
        if(ui.item.estatus != ""){
          $("#div_estado_cliente").show();
          $("#labelEstadoClienteSol").html(ui.item.estatus);
        }
      }
    }
  });

  $("#imp_maquila").autocomplete({
    source: function (request, response) {
      var texto = request.term;
      $.ajax({
        url: "php/solicitudes_ocuv.php",
				dataType: "json",
        type: "POST",
				data: {
          action:'suggestCliente',
					term: texto
				},
				success: function (data) {
					response(data);
					return false;
				}
			});
		},
    minLength: 1,
    maxRows: 15,
    select: function(e, ui) {
      $("#labelMDClienteMaq").text(ui.item.razon);
      $(`#labelMDClienteMaq`).css('color', '#1f405d');
      $("#imp_maquila").defaultBorder();
      if(ui.item.estatus != ""){
        $("#div_estado_maquila").show();
        $("#labelEstadoClienteMaq").html(ui.item.estatus);
      } else {
        $("#div_estado_maquila").hide();
        $("#labelEstadoClienteMaq").empty();
      }
    },change: function (event, ui) {
      $("#tabla_ins").empty();
      $("#labelEstadoClienteMaq").empty();
      $("#div_estado_maquila").hide();
      if (!ui.item) {
        this.value = '';
        $("#imp_maquila").redBorder();
        $("#imp_maquila").focus();
        $(`#labelMDClienteMaq`).css('color', 'red');
        $(`#labelMDClienteMaq`).text("<-- SELECCIONA UN CLIENTE DE LA LISTA");
        $('#imp_domicilio_ins').empty();
        $("#imp_domicilio_ins").disabled();
      }else{
        $(`#labelMDClienteMaq`).css('color', '#1f405d');
        $("#imp_maquila").defaultBorder();
        $(`#labelMDClienteMaq`).text(ui.item.razon);
        $("#imp_domicilio_ins").enable();
        loadInstalaciones(0,[]);
        if(ui.item.estatus != ""){
          $("#div_estado_maquila").show();
          $("#labelEstadoClienteMaq").html(ui.item.estatus);
        }
      }


    }
  });

  $("#imp_cli_recibe").autocomplete({
    source: function (request, response) {
      var texto = request.term;
      $.ajax({
        url: "php/solicitudes_ocuv.php",
				dataType: "json",
        type: "POST",
				data: {
          action:'suggestCliente',
					term: texto
				},
				success: function (data) {
					response(data);
					return false;
				}
			});
		},
    minLength: 1,
    maxRows: 15,
    select: function(e, ui) {
      $("#labelMDClienteRec").text(ui.item.razon);
      $(`#labelMDClienteRec`).css('color', '#1f405d');
      $("#imp_cli_recibe").defaultBorder();
    },change: function (event, ui) {
      if (!ui.item) {
        this.value = '';
        $("#imp_cli_recibe").redBorder();
        $("#imp_cli_recibe").focus();
        $(`#labelMDClienteRec`).css('color', 'red');
        $(`#labelMDClienteRec`).text("<-- SELECCIONA UN CLIENTE DE LA LISTA");
      }else{
        $(`#labelMDClienteRec`).css('color', '#1f405d');
        $("#imp_cli_recibe").defaultBorder();
        $(`#labelMDClienteRec`).text(ui.item.razon);
      }
    }
  });

  $("#imp_cli_envia").autocomplete({
    source: function (request, response) {
      var texto = request.term;
      $.ajax({
        url: "php/solicitudes_ocuv.php",
				dataType: "json",
        type: "POST",
				data: {
          action:'suggestCliente',
					term: texto
				},
				success: function (data) {
					response(data);
					return false;
				}
			});
		},
    minLength: 1,
    maxRows: 15,
    select: function(e, ui) {
      $("#labelMDClienteEnv").text(ui.item.razon);
      $(`#labelMDClienteEnv`).css('color', '#1f405d');
      $("#imp_maquila").defaultBorder();
    },change: function (event, ui) {
      if (!ui.item) {
        this.value = '';
        $("#imp_cli_envia").redBorder();
        $("#imp_cli_envia").focus();
        $(`#labelMDClienteEnv`).css('color', 'red');
        $(`#labelMDClienteEnv`).text("<-- SELECCIONA UN CLIENTE DE LA LISTA");
      }else{
        $(`#labelMDClienteEnv`).css('color', '#1f405d');
        $("#imp_cli_envia").defaultBorder();
        $(`#labelMDClienteEnv`).text(ui.item.razon);
      }
    }
  });


  $("#imp_productor").autocomplete({
    source: function (request, response) {
      var texto = request.term;
      $.ajax({
        url: "php/solicitudes_ocuv.php",
				dataType: "json",
        type: "POST",
				data: {
          action:'suggestCliente',
					term: texto
				},
				success: function (data) {
					response(data);
					return false;
				}
			});
		},
    minLength: 1,
    maxRows: 15,
    select: function(e, ui) {
      $("#labelMDClienteProd").text(ui.item.razon);
      $(`#labelMDClienteProd`).css('color', '#1f405d');
      $("#imp_productor").defaultBorder();
    },change: function (event, ui) {
      if (!ui.item) {
        this.value = '';
        $("#imp_productor").redBorder();
        $("#imp_productor").focus();
        $(`#labelMDClienteProd`).css('color', 'red');
        $(`#labelMDClienteProd`).text("<-- SELECCIONA UN CLIENTE DE LA LISTA");
      }else{
        $(`#labelMDClienteProd`).css('color', '#1f405d');
        $("#imp_productor").defaultBorder();
        $(`#labelMDClienteProd`).text(ui.item.razon);
      }
    }
  });



  $("#cmb_personal_at").load("php/loadPersonalAT.php");

  $('#tablaSolicitudes').on('all.bs.table', function(e, name, args) {

  }).on('dbl-click-row.bs.table', function(e, row, $element) {
    SOLICITUD_SELECCIONADA  = row.id;
    SOLICITUD_SELECCIONADA_NUM  = row.solicitud;
    ACTIVIDAD_SOLICITUD     = row.idActividad;
    getInformacionSolicitud();

    if (_PERMISOS_.comentarios === "S") {
      $('#li_tabComm').show();
      setTimeout(function(){ getComentarios(); }, 1000);
    }else{
      $('#li_tabComm').hide();
    }

    // if (_PERMISOS_.movimientos === "S") {
    //   $('#li_tabMovs').show();
    // }else{
    //   $('#li_tabMovs').hide();
    // }
    $('#li_tabMovs').show();

    if (_PERMISOS_.reveca === "S") {
      $('#li_tabReveca').show();
      setTimeout(function(){ getComentariosReveca(); }, 1000);
    }else{
      $('#li_tabReveca').hide();
    }

  });


  $(document).on('change', '.chboxOpciones', function(e) {
    e.preventDefault();

    var id = $(this).attr('id');
    var sobrante = id.lastIndexOf("-");
    var campoReal = id.substring(sobrante + 1, id.length);
    var Numactividad = $("#inp_actividad").val();

    $.ajax({
      type: "POST",
      url: "php/actualizaOpciones.php",
      contentType: "application/x-www-form-urlencoded;charset=UTF-8",
      data: {
        campo: campoReal,
        estatus:this.checked,
        actividad:Numactividad,
        user:id_usuario
      },
      dataType: "json",
      success: function(data, textStatus, jqXHR) {
        $("#inp_actividad").val(Numactividad).change();
      },error: function(jqxhr, status, errorGenerado) {
      }
    });
  });

  $("#in_solicitud").keyup(function(event) {
    $("#in_solicitud").hideMessage();
    $("#in_solicitud").defaultBorder();
    if ($("#in_solicitud").val().length === 10 ) {
      if ((event.which !== 37) && (event.which !== 39)) {
        var sol = $("#in_solicitud").val().substring(0, 10);
        consultaSeguimientos(sol);
      }
    }
  });

  $("#ui_inspector").autocomplete({
    source: function (request, response) {
      var texto = request.term;
      $.ajax({
        url: "php/solicitudes_ocuv.php",
				dataType: "json",
        type: "POST",
				data: {
          action:'suggestUIAcredita',
					term: texto
				},
				success: function (data) {
					response(data);
					return false;
				}
			});
		},
    minLength: 1,
    maxRows: 15,
    select: function(e, ui) {
      $("#ui_inspector").defaultBorder();
    },change: function (event, ui) {
      if (!ui.item) {
        this.value = '';
        $("#ui_inspector").redBorder();
        $("#ui_inspector").focus();
        $(`#p_ui_inspector`).css('color', 'red');
        $(`#p_ui_inspector`).text("SELECCIONA UN INSPECTOR DE LA LISTA");
      }else{
        $(`#p_ui_inspector`).css('color', '#1f405d');
        $(`#p_ui_inspector`).text("");
        $("#ui_inspector").defaultBorder();

      }
    }
  });

  $("#ui_capacitacion").autocomplete({
    source: function (request, response) {
      var texto = request.term;
      $.ajax({
        url: "php/solicitudes_ocuv.php",
				dataType: "json",
        type: "POST",
				data: {
          action:'suggestUINoAcredita',
					term: texto
				},
				success: function (data) {
					response(data);
					return false;
				}
			});
		},
    minLength: 1,
    maxRows: 15,
    select: function(e, ui) {
      $("#ui_capacitacion").defaultBorder();
    },change: function (event, ui) {
      if (!ui.item) {
        this.value = '';
        $("#ui_capacitacion").redBorder();
        $("#ui_capacitacion").focus();
        $(`#p_ui_capacitacion`).css('color', 'red');
        $(`#p_ui_capacitacion`).text("SELECCIONA UN INSPECTOR O AUXILIAR DE LA LISTA");
      }else{
        $(`#p_ui_capacitacion`).css('color', '#1f405d');
        $(`#p_ui_capacitacion`).text("");
        $("#ui_capacitacion").defaultBorder();

      }
    }
  });

  $("#ui_fecha").datepicker({
    changeMonth: true,
    changeYear: true,
    firstDay: 1,
    onClose: function(selectedDate) {}
  });

  $("#documentos_file_ui").fileinput({
    browseClass: "btn btn-primary btn-block",
    allowedFileExtensions: ["jpg", "jpeg", "pdf", "png"],
    showCaption: false,
    showRemove: false,
    showUpload: false
  });

  $("#btnFS").click(function() {
    $("#filtrosPrincipal").toggle(function() {
      $(this).is(":visible") ? $("#btnFS").html('<img src="images/eye.svg" width="20">') : $("#btnFS").html('<img src="images/invisible.svg" width="20">');
    });
  });

  $('#tablaNotificaciones').on('all.bs.table', function(e, name, args) {

  }).on('dbl-click-row.bs.table', function(e, row, $element) {
    SOLICITUD_SELECCIONADA  = row.id_solicitud;
    SOLICITUD_SELECCIONADA_NUM  = row.num_solicitud;
    ACTIVIDAD_SOLICITUD     = row.idActividad;

    getInformacionSolicitud();
    actualizaNotificacion(row.idNot);

    if (_PERMISOS_.comentarios === "S") {
      $('#li_tabComm').show();
      setTimeout(function(){ getComentarios(); }, 1000);
    }else{
      $('#li_tabComm').hide();
    }

    if (_PERMISOS_.movimientos === "S") {
      $('#li_tabMovs').show();
    }else{
      $('#li_tabMovs').hide();
    }

    if (_PERMISOS_.reveca === "S") {
      $('#li_tabReveca').show();
      setTimeout(function(){ getComentariosReveca(); }, 1000);
    }else{
      $('#li_tabReveca').hide();
    }
  });

});

/************************************************************************************************************
************************************************************************************************************/
function getInformacionSolicitud(){

  _LISTA_DOCS_A_MODIFICAR_UI = [];
  $("#tb_one").tab("show");
  $("#mensajeSuspension1OCUI, #mensajeSuspension2OCUI").hide();
  $("#clienteSuspendido1OCUI, #fechaSuspension1OCUI, #clienteSuspendido2OCUI, #fechaSuspension2OCUI").html("");

  $.ajax({
    type: "POST",
    url: "php/solicitudes_ocuv.php",
    contentType: "application/x-www-form-urlencoded;charset=UTF-8",
    data:{
      action:'getSolicitud',
      solicitud:SOLICITUD_SELECCIONADA,
      actividad:ACTIVIDAD_SOLICITUD
    },
    dataType: "json",
    success: function(data, textStatus, jqXHR) {
      if (data.status === 'correcto') {
        consultaHistorial(SOLICITUD_SELECCIONADA);
        var solicitud =  data.solicitud;
        var datosSuspension = solicitud.datosSuspension[0];
        var estatus = '';
        switch (solicitud.estatus) {
          case 'P': estatus = 'PENDIENTE DE ASIGNACION'; break;
          case 'R': estatus = 'INSPECCIÓN REALIZADA'; break;
          case 'O': estatus = 'PENDIENTE DE PAGO'; break;
          case 'G': estatus = 'PENDIENTE DE REASIGNACION'; break;
          case 'V': estatus = 'INSPECTOR ASIGNADO'; break;
          case 'N': estatus = 'INSPECCIÓN NO REALIZADA'; break;
          case 'C': estatus = 'CANCELADA'; break;
          case 'Z': estatus = 'RECHAZADA'; break;
          case 'W': estatus = 'CORREGIDA'; break;
          case 'T': estatus = 'TERMINADA'; break;
        }


        var nombrePlanEvaluacion = '';
        if (solicitud.ui_planE !== null) {
          var planEvaluacionURI = solicitud.ui_planE;
          var slashPE = planEvaluacionURI.lastIndexOf("/");
          nombrePlanEvaluacion = planEvaluacionURI.substring(slashPE + 1, planEvaluacionURI.length);
        }

        var nombreSolicitudServicio = '';
        if (solicitud.ui_solicitudS !== null) {
          var solicitudServicioURI = solicitud.ui_solicitudS;
          var slashSS = solicitudServicioURI.lastIndexOf("/");
          nombreSolicitudServicio = solicitudServicioURI.substring(slashSS + 1, solicitudServicioURI.length);
        }




        if (_PERMISOS_.modifica === 'N') {
          $("#btnModificarSol").hide();
        }else if (_PERMISOS_.modifica === 'S'){
          if (((solicitud.estatus === 'P')||(solicitud.estatus === 'Z'))) {
            $("#btnModificarSol").show();
          }else{
            $("#btnModificarSol").hide();
          }
        }else{
          $("#btnModificarSol").hide();
        }

        var divContentDocs   = '';
        var divContentDocsUI = '';
        for (var d = 0; d < solicitud.documentos.length; d++) {
          var itemDocs = solicitud.documentos[d];
          if (itemDocs.origen === 'O') {
            var rutaDoc = itemDocs.ruta;
            var slash = rutaDoc.lastIndexOf("/");
            var nombrePDF = rutaDoc.substring(slash + 1, rutaDoc.length);

            divContentDocs += `<div class="col-lg-12" style="border-bottom: 1px solid #ccd4d8;">
                                <div class="col-lg-1" style="text-align: center;">
                                  <img src="images/circle.svg" width=10 />
                                </div>
                                <div class="col-lg-11">
                                  <a target="_blank"  href="./php/documentos.php?f=${nombrePDF}&d_s=${id_s}" style="font-weight: 600;cursor: pointer;color: #0d47a1;">${nombrePDF}</a>
                                </div>
                              </div>`;
          }else if(itemDocs.origen === 'UG'){
            divContentDocs += `<div class="col-lg-12" style="border-bottom: 1px solid #ccd4d8;">
                                <div class="col-lg-1" style="text-align: center;">
                                  <img src="images/circle.svg" width=10 />
                                </div>
                                <div class="col-lg-11">
                                  <a target="_blank" href="./php/documentos.php?f=${itemDocs.clave}&d_s=${id_s}"  style="font-weight: 600;cursor: pointer;color: #0d47a1;">${itemDocs.original}</a>
                                </div>
                              </div>`;
          }else if(itemDocs.origen === 'UI' && _PERMISOS_.cargo !== 11){

            _LISTA_DOCS_A_MODIFICAR_UI = [... _LISTA_DOCS_A_MODIFICAR_UI,itemDocs];
            divContentDocsUI += `<div class="col-lg-12" style="border-bottom: 1px solid #ccd4d8;font-size: 12px;">
                                <div class="col-lg-1" style="text-align: center;">
                                  <img src="images/circle.svg" width=10 />
                                </div>
                                <div class="col-lg-11">
                                  <a target="_blank" href="./php/documentos.php?f=${itemDocs.clave}&d_s=${id_s}" style="font-weight: 600;cursor: pointer;color: #0d47a1;">${itemDocs.original}</a>
                                </div>
                              </div>`;
          }
        }

        var divContentIns   = '';
        for (var x = 0; x < solicitud.instalaciones.length; x++) {
          var itemIns = solicitud.instalaciones[x];
          divContentIns += `<div class="col-lg-12" style="border-bottom: 1px solid #ccd4d8;">
                                <div class="col-lg-3" style="text-align: center; overflow-wrap: break-word;">
                                  ${itemIns.tipo}
                                </div>
                                <div class="col-lg-6">
                                  ${itemIns.domicilio}
                                </div>
                                <div class="col-lg-3">
                                  ${itemIns.observaciones}
                                </div>
                            </div>`;       
        }



        var contenido = `<div class="form-group">
                          <span class="labeltr" style="font-size: 15px">ESTATUS: </span>
                          <span style="font-weight: bold;text-transform: uppercase;font-size: 15px;color: #01579b;">${estatus}</span>
                        </div>
                        <table style="font-size: 12px;line-height: 20px;">
                          <tbody>
                            <tr class="style_tr">
                              <td class="labeltr">Actividad:</td>
                              <td style="font-weight: bold;text-transform: uppercase;font-size: 13px;color: #E64A19;">${solicitud.actividad}</td>
                            </tr>
                            <tr class="style_tr">
                              <td class="labeltr">N&uacutemero de Solicitud:</td>
                              <td style="font-weight: bold;text-transform: uppercase;font-size: 13px;color: #E64A19;">${solicitud.solicitud}</td>
                            </tr>
                            ${(solicitud.numTraslado !== null)?
                              `<tr class="style_tr">
                                <td class="labeltr">Número de Traslado:</td>
                                <td style="font-weight: bold;text-transform: uppercase;font-size: 13px;color: #E64A19;">${solicitud.numTraslado}</td>
                              </tr>
                              `:''}

                            <tr class="style_tr">
                              <td class="labeltr">Solicitante:</td>
                              <td style="font-weight: bold;text-transform: uppercase;">(${solicitud.clienteSol}) - ${solicitud.noClienteSol}</td>
                            </tr>
                            <tr class="style_tr">
                              <td class="labeltr">FECHA DE REGISTRO:</td>
                              <td style="font-weight: bold;text-transform: uppercase;">${solicitud.fecha}</td>
                            </tr>
                            <tr class="style_tr">
                              <td class="labeltr">FECHA PROPUESTA:</td>
                              <td style="font-weight: bold;text-transform: uppercase;">${solicitud.fechaPropuesta}</td>
                            </tr>
                            ${((solicitud.resTraslado !== null) && (solicitud.resTraslado.length > 0))?
                              `<tr class="style_tr">
                                <td class="labeltr">Responsable de Traslado:</td>
                                <td style="font-weight: bold;text-transform: uppercase;">${solicitud.resTraslado}</td>
                              </tr>`
                              :''}
                              ${((solicitud.cliProductor !== null) && (solicitud.cliProductor.length > 0))?
                                `<tr class="style_tr">
                                  <td class="labeltr">PRODUCTOR:</td>
                                  <td style="font-weight: bold;text-transform: uppercase;">(${solicitud.cliProductor}) - ${solicitud.razonProductor}</td>
                                </tr>`
                                :''}
                            ${((solicitud.maquilador !== null) && (solicitud.maquilador.length > 0))?
                              `<tr class="style_tr">
                                <td class="labeltr">MAQUILA:</td>
                                <td style="font-weight: bold;text-transform: uppercase;">(${solicitud.maquilador}) - ${solicitud.razonMaquila}</td>
                              </tr>`
                              :''}
                            ${((solicitud.resPago !== null)&&(solicitud.resPago.length > 0))?
                              `<tr class="style_tr">
                                <td class="labeltr">Responsable de pago:</td>
                                <td style="font-weight: bold;text-transform: uppercase;">${solicitud.resPago}</td>
                              </tr>
                              `:''}
                            ${((solicitud.clienteEnvia !== null)&&(solicitud.clienteEnvia.length > 0))?
                              `<tr class="style_tr">
                                <td class="labeltr">Cliente envia:</td>
                                <td style="font-weight: bold;text-transform: uppercase;">${solicitud.clienteEnvia}</td>
                              </tr>
                              `:''}
                            ${((solicitud.responsableUno !== null)&&(solicitud.responsableUno.length > 0))?
                              `<tr class="style_tr" >
                                <td class="labeltr">Responsable:</td>
                                <td style="font-weight: bold;text-transform: uppercase;">${solicitud.responsableUno}</td>
                              </tr>
                              `:''}
                            ${((solicitud.direccionUno !== null)&&(solicitud.direccionUno.length > 0))?
                              `<tr class="style_tr" >
                                <td class="labeltr">Domicilio:</td>
                                <td style="font-weight: bold;text-transform: uppercase;">${solicitud.direccionUno}</td>
                              </tr>
                              `:''}

                            ${((solicitud.instalaciones.length > 0))?
                              `
                              <tr class="style_tr">
                                <td class="labeltr">Domicilio Instalaciones:</td>
                                <td>
                                  <div class="col-lg-12" style="border: 3px double #337ab759;border-radius: 5px;padding: 5px;">
                                    ${divContentIns}
                                  </div>
                                </td>
                              </tr>`:''}

                           ${((_PERMISOS_.asignaUI === 'S' && solicitud.instalaciones.length == 0 && solicitud.numeroIns == 0  && (ACTIVIDAD_SOLICITUD == 25 || ACTIVIDAD_SOLICITUD == 26|| ACTIVIDAD_SOLICITUD == 31 || ACTIVIDAD_SOLICITUD == 32 || ACTIVIDAD_SOLICITUD == 33 || ACTIVIDAD_SOLICITUD == 34 || ACTIVIDAD_SOLICITUD == 35)))?
                              `                            
                              <tr class="style_tr">
                                <td class="labeltr">Registrar Instalaciones:</td>
                                <td>
                                  <div class="col-lg-12" style="border: 3px double #337ab759;border-radius: 5px;padding: 5px;">
                                    <button style="font-size: 8px;" id="btnAddIns" onclick="addInstalacion('${solicitud.clienteSol}','${solicitud.maquilador}');" type="button" class="btn btn-success"><i aria-hidden="true" class="fa fa-lg fa-plus"></i></button>
                                  </div>
                                </td>
                              </tr>`:``}

                              ${((solicitud.detActividades !== null)&&(solicitud.detActividades.length > 0))?
                                `<tr class="style_tr" >
                                  <td class="labeltr">Detalle de las Actividades:</td>
                                  <td style="font-weight: bold;text-transform: uppercase;">${solicitud.detActividades.slice(0,-2)}</td>
                                </tr>
                                `:''}
                                ${
                                  (solicitud.actProductor !== null && ACTIVIDAD_SOLICITUD == 32)?
                                  `<tr class="style_tr" >
                                    <td class="labeltr">Actualización del certificado:</td>
                                    <td style="font-weight: bold;text-transform: uppercase;">${(solicitud.actProductor === 'N')?'NO':'SI'}</td>
                                  </tr>
                                  `:''
                                }
                            ${((solicitud.telefonoUno !== null)&&(solicitud.telefonoUno.length > 0))?
                              `<tr class="style_tr" >
                                <td class="labeltr">telefono:</td>
                                <td style="font-weight: bold;text-transform: uppercase;">${solicitud.telefonoUno}</td>
                               </tr>
                              `:''}
                            ${((solicitud.clienteRecibe !== null)&&(solicitud.clienteRecibe.length > 0))?
                              `<tr class="style_tr" >
                                <td class="labeltr">Cliente recibe:</td>
                                <td style="font-weight: bold;text-transform: uppercase;">${solicitud.clienteRecibe}</td>
                              </tr>
                              `:''}
                            ${((solicitud.responsableDos !== null)&&(solicitud.responsableDos.length > 0))?
                              `<tr class="style_tr" >
                                <td class="labeltr">Responsable:</td>
                                <td style="font-weight: bold;text-transform: uppercase;">${solicitud.responsableDos}</td>
                              </tr>
                              `:''}
                            ${((solicitud.direccionDos !== null)&&(solicitud.direccionDos.length > 0))?
                              `<tr class="style_tr" >
                                <td class="labeltr">Domicilio:</td>
                                <td style="font-weight: bold;text-transform: uppercase;">${solicitud.direccionDos}</td>
                              </tr>
                              `:''}
                            ${((solicitud.telefonoDos !== null)&&(solicitud.telefonoDos.length > 0))?
                              `<tr class="style_tr" >
                                <td class="labeltr">telefono:</td>
                                <td style="font-weight: bold;text-transform: uppercase;">${solicitud.telefonoDos}</td>
                               </tr>
                              `:''}
                            ${((solicitud.prioridad !== null)&&(solicitud.prioridad.length > 0))?
                              `<tr class="style_tr" >
                                <td class="labeltr">Tipo de servicio:</td>
                                <td style="font-weight: bold;text-transform: uppercase;color: ${(solicitud.prioridad === 'N')?'#0d47a1':'#b71c1c'};">${(solicitud.prioridad === 'N')?'NORMAL':'EXCLUSIVO'}</td>
                               </tr>
                              `:''}
                            ${((solicitud.tipoBaja !== null)&&(solicitud.tipoBaja.length > 0))?
                              `<tr class="style_tr" >
                                <td class="labeltr">Tipo de Baja:</td>
                                <td style="font-weight: bold;text-transform: uppercase;">${(solicitud.tipoBaja === 'T')?'BAJA TEMPORAL':'BAJA DEFINITIVA'}</td>
                               </tr>
                              `:''}
                            ${((solicitud.tipoTraslado !== null)&&(solicitud.tipoTraslado.length > 0))?
                              `<tr class="style_tr" >
                                <td class="labeltr">Tipo de traslado:</td>
                                <td style="font-weight: bold;text-transform: uppercase;">${solicitud.tipoTraslado}</td>
                              </tr>
                              `:''}
                            ${((solicitud.tipoTraslado !== null)&&(solicitud.tipoTraslado.length > 0))?
                              `<tr class="style_tr" >
                                <td class="labeltr">Tipo de traslado:</td>
                                <td style="font-weight: bold;text-transform: uppercase;">${solicitud.tipoTraslado}</td>
                              </tr>
                              `:''}
                            <tr class="style_tr">
                              <td class="labeltr">Descripción de la actividad:</td>
                              <td style="font-weight: bold;text-transform: uppercase;">${solicitud.descActividad}</td>
                            </tr>
                            <tr class="style_tr" ${((solicitud.inicioVig !== null)&&(solicitud.inicioVig !== '0000-00-00'))?'':'hidden'}>
                              <td class="labeltr">Inicio de vigencia:</td>
                              <td style="font-weight: bold;text-transform: uppercase;">${solicitud.inicioVig}</td>
                            </tr>
                            <tr class="style_tr" ${((solicitud.finVig !== null)&&(solicitud.finVig !== '0000-00-00'))?'':'hidden'}>
                              <td class="labeltr">Fin de vigencia:</td>
                              <td style="font-weight: bold;text-transform: uppercase;">${solicitud.finVig}</td>
                            </tr>
                            <tr class="style_tr">
                              <td class="labeltr">Solicitud PDF:</td>
                              <td>
                                <div class="col-lg-12" style="border: 3px double #337ab759;border-radius: 5px;padding: 5px;">
                                  ${divContentDocs}
                                </div>
                              </td>
                            </tr>
                            <tr class="style_tr">
                              <td class="labeltr">Registro:</td>
                              <td style="font-weight: bold;text-transform: uppercase;">${solicitud.resSolicitud}</td>
                            </tr>
                          </tbody>
                         </table>
                         ${
                           ((solicitud.estatus==='O' && _PERMISOS_.pago === 'S'))?
                           `<div class="col-lg-12" id="divEstado" style="border: 1px solid #f0ae4e;padding: 10px;border-radius: 10px;margin: 20px 0;">
                             <div align="center"; class="col-xs-12">
                               <button style="width: 150px;" type="button" class="btn btn-warning botones_rd" onclick="confirmaPago()">CONFIRMAR PAGO</button>
                             </div>
                           </div>
                           `
                           :
                           ''
                         }
                         ${
                           ((solicitud.reqHolograma==='S' && solicitud.hologramaEnt==='N') && solicitud.estatus !== 'O')?
                           `<div class="col-lg-12" id="divEstado" style="border: 1px solid #f0ae4e;padding: 10px;border-radius: 10px;margin: 20px 0;">
                             <div align="center"; class="col-xs-12">
                               <button style="width: 250px;" type="button" class="btn btn-warning botones_rd" onclick="confirmaEntrgaHologramas()">CONFIRMAR ENTREGA DE HOLOGRAMAS</button>
                             </div>
                           </div>
                           `
                           :
                           ''
                         }
                         ${
                           ((_PERMISOS_.cancela === 'S') && (solicitud.estatus==='P' || solicitud.estatus==='V' || solicitud.estatus==='O' || solicitud.estatus==='Z' || solicitud.estatus==='W'))?
                            `<div class="form-group" style="margin-top: 20px;">
                               <button style="border-radius: 17px;padding: 3px 4px;font-size: 11px;" id="btnCancelaNormal" type="button" class="btn btn-danger" onclick="cancelarSolicitud();">CANCELAR SOLICITUD</button>
                            </div>
                            `
                            :
                            ''
                         }
                         ${
                           ((_PERMISOS_.rechaza === 'S') && (solicitud.estatus==='P' || solicitud.estatus==='V' || solicitud.estatus==='O' ||  solicitud.estatus==='W'))?
                            `<div class="form-group" style="margin-top: 20px;">
                               <button style="border-radius: 17px;padding: 3px 4px;font-size: 11px;" id="btnCancelaNormal" type="button" class="btn btn-danger" onclick="rechazarSolicitud();">RECHAZAR SOLICITUD</button>
                            </div>
                            `
                            :
                            ''
                         }
                         ${
                           ((_PERMISOS_.reagenda==='S') && (solicitud.estatus === 'N'))?
                           `<div class="col-lg-12" id="divEstado" style="border: 1px solid #337ab7;padding: 10px;border-radius: 10px;margin: 20px 0;">
                             <div align="center"; class="col-xs-12">
                               <button style="width: 250px;" type="button" class="btn btn-primary botones_rd" onclick="confirmaEntrgaHologramas()">SOLICITAR REAGENDACIÓN</button>
                             </div>
                           </div>
                           `
                           :
                           ''
                         }
                         ${
                          ((_PERMISOS_.termina === 'S') && (solicitud.estatus==='R') && (ACTIVIDAD_SOLICITUD == 25 || ACTIVIDAD_SOLICITUD == 26|| ACTIVIDAD_SOLICITUD == 31 || ACTIVIDAD_SOLICITUD == 32 || ACTIVIDAD_SOLICITUD == 34 || ACTIVIDAD_SOLICITUD == 35 || ACTIVIDAD_SOLICITUD == 9))?
                           `<div class="form-group" style="margin-top: 20px;">
                              <button style="border-radius: 17px;padding: 3px 4px;font-size: 11px;" id="btnTerminaNormal" type="button" class="btn btn-success" onclick="terminarSolicitud();">TERMINA SOLICITUD</button>
                           </div>
                           `
                           :
                           ''
                        }
                         `;
                                    
                         $("#tabUIP").empty();

                         if (solicitud.estatus !=='P' && solicitud.estatus !=='O') {
                             $("#tabUIP").append(`<form class="form-horizontal" autocomplete="off" >
                                                    <div class="col-lg-12" style="margin-top: 10px;padding: 5px;border: 3px double #37474f54;">
                                                      <div class="col-lg-12" style="font-weight: 600;text-align: center;">INSPECTOR(ES) ASIGNADOS</div>
                                                    </div>
                                                    <div class="col-lg-12" style="margin-top: 10px;;border-bottom: 1px dashed #80808059;">
                                                      <label class="control-label col-lg-4" style="font-size: 12px;">INSPECTOR ACREDITADO:</label>
                                                      <div class="col-lg-8">
                                                        <label class="control-label" id="lbl_ui_inspectorA" style="font-size: 12px;font-weight: 500;">${(solicitud.ui_inspectorA === null)?'---':solicitud.ui_inspectorA}</label>
                                                      </div>
                                                    </div>
                                                    <div class="col-lg-12" style="border-bottom: 1px dashed #80808059;">
                                                      <label class="control-label col-lg-4" style="font-size: 12px;">INSPECTOR EN CAPACITACIÓN O AUXILIAR DE UI:</label>
                                                      <div class="col-lg-8">
                                                        <label class="control-label" id="lbl_ui_inspectorC" style="font-size: 12px;font-weight: 500;">${(solicitud.ui_inspectorA2 === null || solicitud.ui_inspectorA2.length === 0 )?'---':solicitud.ui_inspectorA2}</label>
                                                      </div>
                                                    </div>
                                                    <div class="col-lg-12" style="border-bottom: 1px dashed #80808059;">
                                                      <label class="control-label col-lg-4" style="font-size: 12px;">FECHA PROGRAMADA:</label>
                                                      <div class="col-lg-3">
                                                        <label class="control-label" id="lbl_ui_fechaPro" style="font-size: 12px;font-weight: 500;">${(solicitud.ui_fechaProgra === null)?'---':solicitud.ui_fechaProgra}</label>
                                                      </div>
                                                    </div>
                                                    <div class="col-lg-12" style="border-bottom: 1px dashed #80808059;">
                                                      <label class="control-label col-lg-4" style="font-size: 12px;">NÚMERO DE ACTA O DICTAMEN:</label>
                                                      <div class="col-lg-3">
                                                        <label class="control-label" id="lbl_ui_acta" style="font-size: 12px;font-weight: 500;">${(solicitud.ui_acta === null)?'---':solicitud.ui_acta}</label>
                                                      </div>
                                                    </div>
                                                    <div class="col-lg-12" style="border-bottom: 1px dashed #80808059;">
                                                      <label class="control-label col-lg-4" style="font-size: 12px;">OBSERVACIÓNES:</label>
                                                      <div class="col-lg-8">
                                                        <label class="control-label" id="lbl_ui_observaciones" style="font-size: 12px;font-weight: 500;text-align: justify;">${(solicitud.ui_observaciones === null)?'---':solicitud.ui_observaciones}</label>
                                                      </div>
                                                    </div>
                                                    <div class="col-lg-12"  style="border-bottom: 1px dashed #80808059;">
                                                      <label class="control-label col-lg-4" style="font-size: 12px;">DOCUMENTO(S) ADICIONAL(ES):</label>
                                                      <div class="col-lg-8" ${(divContentDocsUI.length === 0)?'':`style="border: 3px double #337ab759;border-radius: 5px;padding: 5px;"`} >
                                                      ${(divContentDocsUI.length === 0)?'---':divContentDocsUI}
                                                      </div>
                                                    </div>
                                                    <div class="col-lg-12" style="border-bottom: 1px dashed #80808059;">
                                                      <label class="control-label col-lg-4" style="font-size: 12px;">PLAN DE EVALUACIÓN:</label>
                                                      <div class="col-lg-8">
                                                        <a target="_blank" href="./php/cplaneva.php?f=${nombrePlanEvaluacion}&d_s=${id_s}" style="font-weight: 600;cursor: pointer;color: #0d47a1;font-size: 12px;">Clic para ver</a>
                                                      </div>
                                                    </div>
                                                    <div class="col-lg-12" style="border-bottom: 1px dashed #80808059;">
                                                      <label class="control-label col-lg-4" style="font-size: 12px;">SOLICITUD DE SERVICIO:</label>
                                                      <div class="col-lg-8">
                                                        <a target="_blank" href="./php/csolicitudser.php?f=${nombreSolicitudServicio}&d_s=${id_s}" style="font-weight: 600;cursor: pointer;color: #0d47a1;font-size: 12px;">Clic para ver</a>
                                                      </div>
                                                    </div>
                                                    <div class="col-lg-12" style="border-bottom: 1px dashed #80808059;">
                                                      <label class="control-label col-lg-4" style="font-size: 12px;">ASIGNACIÓN DE SERVICIO:</label>
                                                      <div class="col-lg-8">
                                                        <a target="_blank" href="../../../publicFolder/ocui/generaArchivoASOCUI.php?d=${SOLICITUD_SELECCIONADA}&d_s=${id_s}" style="font-weight: 600;cursor: pointer;color: #0d47a1;font-size: 12px;">Clic para ver</a>
                                                      </div>
                                                    </div>
                                                    ${(solicitud.estatus ==='R' || solicitud.estatus === 'N')
                                                      ?`
                                                        <div class="col-lg-12"  style="border-bottom: 1px dashed #80808059;margin-top: 10px;border-top: 3px double #bdc2c5;">
                                                          <label class="control-label col-lg-4" style="font-size: 12px;">FECHA REAL DE INSPECCIÓN:</label>
                                                          <div class="col-lg-8">
                                                            <label class="control-label" id="lbl_ui_observaciones" style="font-size: 12px;font-weight: 500;">${(solicitud.ui_fechaFinal === null)?'---':solicitud.ui_fechaFinal}</label>
                                                          </div>
                                                        </div>
                                                        <div class="col-lg-12" style="border-bottom: 1px dashed #80808059;">
                                                          <label class="control-label col-lg-4" style="font-size: 12px;">COMENTARIO FINAL:</label>
                                                          <div class="col-lg-8">
                                                            <label class="control-label" id="lbl_ui_observaciones" style="font-size: 12px;font-weight: 500;text-align: justify;">${(solicitud.ui_commFinal === null)?'---':solicitud.ui_commFinal}</label>
                                                          </div>
                                                        </div>
                                                       `
                                                      :``
                                                    }
                                                  </form>`);

                           }else{
                             $("#tabUIP").append(`<form class="form-horizontal" autocomplete="off" >
                                                    <div class="col-lg-12" style="margin-top: 10px;padding: 5px;border: 3px double #ffc107;background: #fff8e1;">
                                                      <div class="col-lg-12" style="font-weight: 600;text-align: center;">NO SE HA REALIZADO LA ASIGNACIÓN DE INSPECTORES</div>
                                                    </div>
                                                  </form>`);
                           }

                         if(_PERMISOS_.asignaUI === 'S' && (solicitud.estatus ==='P' || solicitud.estatus ==='V' || solicitud.estatus ==='W')){
                           $("#ui_inspector").val(solicitud.ui_inspectorA);
                           $("#ui_capacitacion").val(solicitud.ui_inspectorA2);
                           $("#ui_fecha").val(solicitud.ui_fechaProgra);
                           $("#ui_acta").val(solicitud.ui_acta);
                           $("#ui_obervaciones").val(solicitud.ui_observaciones);
                           $("#ui_obervaciones").keyup();
                           $("#docsUi_PESE").empty();
                           $("#docsUi_PESE").append(`<div class="col-lg-12">
                                                      <label class="control-label col-lg-3" style="font-size: 12px;">PLAN DE EVALUACIÓN:</label>
                                                      <div class="col-lg-7">
                                                        <a target="_blank" href="./php/cplaneva.php?f=${nombrePlanEvaluacion}&d_s=${id_s}" style="font-weight: 600;cursor: pointer;color: #0d47a1;font-size: 12px;">Clic para ver</a>
                                                      </div>
                                                     </div>
                                                     <div class="col-lg-12">
                                                      <label class="control-label col-lg-3" style="font-size: 12px;">SOLICITUD DE SERVICIO</label>
                                                      <div class="col-lg-7">
                                                        <a target="_blank" href="./php/csolicitudser.php?f=${nombreSolicitudServicio}&d_s=${id_s}" style="font-weight: 600;cursor: pointer;color: #0d47a1;font-size: 12px;">Clic para ver</a>
                                                      </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                      <label class="control-label col-lg-3" style="font-size: 12px;">ASIGNACIÓN DE SERVICIO	</label>
                                                      <div class="col-lg-7">
                                                        <a target="_blank" href="../../../publicFolder/ocui/generaArchivoASOCUI.php?d=${SOLICITUD_SELECCIONADA}&d_s=${id_s}" style="font-weight: 600;cursor: pointer;color: #0d47a1;font-size: 12px;">Clic para ver</a>
                                                      </div>
                                                    </div>
                                                    `);
                           var editDocumentosUI = '';
                           var countRowsUI      = 1;
                           for (var u = 0; u < _LISTA_DOCS_A_MODIFICAR_UI.length; u++) {
                             editDocumentosUI += `<tr>
                                                    <td style="padding: 4px;">${countRowsUI}</td>
                                                    <td style="padding: 4px;"><a target="_blank" href="./php/documentos.php?f=${_LISTA_DOCS_A_MODIFICAR_UI[u].clave}&d_s=${id_s}" style="font-size: 12px;font-weight: 500;text-decoration: revert;">${_LISTA_DOCS_A_MODIFICAR_UI[u].original}</a></td>
                                                    <td style="padding: 4px;text-align: center;">
                                                      <div class="checkbox">
                                                        <label style="font-size: 12px;">
                                                          <input type="checkbox" id="md_${_LISTA_DOCS_A_MODIFICAR_UI[u].id}" name="md_${_LISTA_DOCS_A_MODIFICAR_UI[u].id}" value=${_LISTA_DOCS_A_MODIFICAR_UI[u].id} class="documentosUICheck" ${(_LISTA_DOCS_A_MODIFICAR_UI[u].estatus === 'A'?'checked':'')}/></label>
                                                      </div>
                                                    </td>
                                                   </tr>
                                                 `;
                              countRowsUI++;

                           }
                           $("#div_documentos_actualesUI").empty();
                           var trDocumentosDivUI = '';
                           if (_LISTA_DOCS_A_MODIFICAR_UI.length > 0) {
                             trDocumentosDivUI = `<div class="col-lg-12">
                                                 <label class="control-label col-sm-3" style="font-size: 12px;">DOCUMENTOS ADJUNTOS: </label>
                                                 <div class="col-lg-8" style="border: 3px double #337ab759;border-radius: 5px;padding: 5px;">
                                                   <div class="col-lg-12">
                                                     <div class="table-responsive">
                                                       <table class="table" style="margin-bottom: 0px;">
                                                        <thead>
                                                           <tr>
                                                             <th style="padding: 4px;">#</th>
                                                             <th style="padding: 4px;">DOCUMENTO</th>
                                                             <th style="padding: 4px;text-align: center;">ELIMINAR</th>
                                                           </tr>
                                                         </thead>
                                                         <tbody>
                                                           ${editDocumentosUI}
                                                         </tbody>
                                                       </table>
                                                     </div>
                                                   </div>
                                                 </div>
                                               </div>`;
                             $("#div_documentos_actualesUI").append(trDocumentosDivUI);

                             $('.documentosUICheck').change(function(e){
                               var idCBX     = $(this).attr("id");
                               var idCBX_VAL = $(this).val();

                               if($(this).is(":checked")) {
                                 for (var o = 0; o < _LISTA_DOCS_A_MODIFICAR_UI.length; o++) {
                                   if (_LISTA_DOCS_A_MODIFICAR_UI[o].id === Number(idCBX_VAL)) {
                                     _LISTA_DOCS_A_MODIFICAR_UI[o]['estatus'] = 'A';
                                   }
                                 }
                               }else{
                                 $.confirm({
                                   title: 'Eliminar Documento',
                                   content: '¿Estas seguro(a) de que quieres aplicar estos cambios?',
                                   type: 'orange',
                                   buttons: {
                                     aceptar: function () {
                                       for (var o = 0; o < _LISTA_DOCS_A_MODIFICAR_UI.length; o++) {
                                         if (Number(_LISTA_DOCS_A_MODIFICAR_UI[o].id) === Number(idCBX_VAL)) {
                                           _LISTA_DOCS_A_MODIFICAR_UI[o]['estatus'] = 'I';
                                         }
                                       }
                                     },
                                     cancelar: function () {
                                       $(`input[type="checkbox"][id="${idCBX}"]`).prop("checked", true).change();
                                     }
                                   }
                                 });
                               }
                             });
                           }
                           $("#btn_mod_inspeccion").empty();
                           if (solicitud.estatus === 'P') {
                             $("#btn_mod_inspeccion").append(` <button type="button" id="btnUIAsigna" onclick="dgasignacion();" class="btn btn-success" style="text-transform: uppercase;font-size: 12px;width: 200px;">Guardar Asignación</button>`);
                           }else if(solicitud.estatus === 'V' || solicitud.estatus === 'G' || solicitud.estatus === 'W'){
                             $("#btn_mod_inspeccion").append(` <button type="button" id="btnUIAsignaMod" onclick="mdAsignacion();" class="btn btn-warning" style="text-transform: uppercase;font-size: 12px;width: 200px;">Modificar Asignación</button>
                                                               <button type="button" id="btnUIFinaliza"   onclick="finalizaSolicitud()" class="btn btn-primary" style="text-transform: uppercase;font-size: 12px;width: 200px;">Finalizar Solicitud</button>`);
                           }
                         }

                         if(_PERMISOS_.asignaUI === 'S' && (solicitud.estatus ==='P' || solicitud.estatus ==='V' || solicitud.estatus ==='W')){
                           $('#li_tabUIEdita').show();
                           $('#li_tabUIPanel').hide();
                         }else{
                           $('#li_tabUIEdita').hide();
                           $('#li_tabUIPanel').show();
                         }


                         if(datosSuspension.clienteIngreso.suspendido){
                          $("#mensajeSuspension1OCUI").show();
                          $("#clienteSuspendido1OCUI").html('(' + solicitud.clienteSol + ')');
                          $("#fechaSuspension1OCUI").html(datosSuspension.clienteIngreso.fecha); 
                        }
                
                        if ( datosSuspension.clienteMaquilador && datosSuspension.clienteMaquilador.suspendido) {
                          $("#mensajeSuspension2OCUI").show();
                          $("#clienteSuspendido2OCUI").html('(' + solicitud.maquilador + ')');
                          $("#fechaSuspension2OCUI").html(datosSuspension.clienteMaquilador.fecha);
                        }


                        $("#tabInfoGeneral").html(contenido);
                        $("#modalSolicitud").modal("show");



      }
    },error: function(jqxhr, status, errorGenerado) {
      alert("Ha ocurrido un error al cargar las actividades: " + jqxhr.responseText);
    }
  });
}
/********************************************************************************************************************************
********************************************************************************************************************************/
function comboActividades(){
  $("#filActividad option").remove();
  $.ajax({
    type: "POST",
    url: "php/solicitudes_ocuv.php",
    contentType: "application/x-www-form-urlencoded;charset=UTF-8",
    data:{
      action:'getActividades'
    },
    dataType: "json",
    success: function(data, textStatus, jqXHR) {
      if (data.status == "correcto") {
        $("#filActividad").append($("<option>", {
          value: '0',
          text: "TODAS",
          selected:true
        }).prop('disabled', false));

        for (var i = 0; i < data.actividades.length; i++) {
          $("#filActividad").append($("<option>", {
            value: data.actividades[i].id,
            text: data.actividades[i].nombre
          }));
        }
      }
      else {
        alert("Ha ocurrido un error al cargar las actividades: " + data.msj);
      }
    },error: function(jqxhr, status, errorGenerado) {
      alert("Ha ocurrido un error al cargar las actividades: " + jqxhr.responseText);
    }
  });
}
/********************************************************************************************************************************
********************************************************************************************************************************/
function putTable2(){
  $('#tablaSolicitudes').bootstrapTable('destroy');
  var tableTest = $('#tablaSolicitudes').bootstrapTable({
    url: "php/solicitudes_ocuv.php",
    method: "POST",
    contentType: "application/x-www-form-urlencoded;charset=UTF-8",
    queryParams: function(p) {
      return {
        "action": (_PERMISOS_.cargo === 11)?"solicitudesUI":"solicitudes",
        limit       : p.limit,
        offset      : p.offset,
        usuario     : id_usuario,
        inspector   : user,
        cliente     : $("#filAsociado").val(),
        solicitud   : $("#filSolicitud").val(),
        prioridad   : $("#filPrioridad").val(),
        estatus     : $("#filEstatus").val(),
        verificador : $("#filverificador").val(),
        actividad   : $("#filActividad").val(),
        fechaI      : $("#filFechaIni").val(),
        fechaF      : $("#filFechaFin").val(),
        tipofecha   : $("input:radio[name='optradiofechas']:checked").val(),


      };
    },
    // showRefresh: true,
    columns: [{
      field: 'id',
      title: 'ID',
      visible: false,
      min_width:'150px',
    },{
      field: 'solicitud',
      title: 'SOLICITUD',
      formatter:'formatterSolicitud',
      cellStyle: function(value,row) {
        return {css: {'min-width':'160px','vertical-align': 'middle'}};
      }
    },{
      field: 'nombreActividad',
      title: 'ACTIVIDAD',
      cellStyle: function(value,row) {
        return {css: {'min-width':'300px','vertical-align': 'middle'}};
      }
    },{
      field: 'cliente',
      title: 'SOLICITANTE',
      cellStyle: function(value,row) {
        return {css: {'min-width':'100px','vertical-align': 'middle','text-align': 'center'}};
      }

    },{
      field: 'clienteNombre',
      title: 'RAZON SOCIAL',
      cellStyle: function(value,row) {
        return {css: {'min-width':'300px','vertical-align': 'middle'}};
      }
    },{
      field: 'registro',
      title: 'FECHA',
      cellStyle: function(value,row) {
        return {css: {'min-width':'100px','vertical-align': 'middle','text-align': 'center'}};
      }
    },{
      field: 'propuesta',
      title: 'FECHA PROPUESTA',
      cellStyle: function(value,row) {
        return {css: {'min-width':'100px','vertical-align': 'middle','text-align': 'center'}};
      }
    },{
      field: 'hologramas',
      title: 'HOLOGRAMAS',
      width:'5%',
      cellStyle: function(value){
        // if (value == "PENDIENTES DE ENTREGA") {
        //   return { classes: 'danger',css: {'min-width':'200px','font-size': '11px;'} };
        // }else if (value == "HOLOGRAMAS ENTREGADOS"){
        //   return { classes: 'success',css: {'min-width':'200px','font-size': '11px;'} };
        // }
        return { css: {'min-width':'100px','font-size': '11px;'} };
      }
    },{
      field: 'prioridad',
      title: 'TIPO DE SERVICIO',
      cellStyle: function(value,row) {
        if (value == "EXCLUSIVO") {
          return { classes: 'danger',css: {'vertical-align': 'middle','text-align': 'center','font-size': '11px;','font-weight': '500'} };
        }
        return { classes: 'info',css: {'vertical-align': 'middle','text-align': 'center','font-size': '11px;','font-weight': '500'} };

      }
    },{
      field: 'estatus',
      title: 'ESTATUS',
      cellStyle: function(value,row) {
        switch (value) {
          case 'PENDIENTE DE ASIGNACIÓN':return { classes: 'warning',css: {'min-width':'200px','vertical-align': 'middle','text-align': 'center','font-size': '11px;','font-weight': '500'} };break;
          case 'INSPECCIÓN REALIZADA':return { classes: 'success',css: {'min-width':'200px','vertical-align': 'middle','text-align': 'center','font-size': '11px;','font-weight': '500'} };break;
          case 'PENDIENTE DE PAGO':return { classes: 'danger',css: {'min-width':'200px','vertical-align': 'middle','text-align': 'center','font-size': '11px;','font-weight': '500'} };break;
          case 'INSPECCIÓN NO REALIZADA':return { classes: 'danger',css: {'min-width':'200px','vertical-align': 'middle','text-align': 'center','font-size': '11px;','font-weight': '500'} };break;
          case 'REASIGNACIÓN PENDIENTE':return { classes: 'warning',css: {'min-width':'200px','vertical-align': 'middle','text-align': 'center','font-size': '11px;','font-weight': '500'} };break;
          case 'CANCELADA':return { classes: 'danger',css: {'min-width':'200px','vertical-align': 'middle','text-align': 'center','font-size': '11px;','font-weight': '500'} };break;
          case 'INSPECTOR ASIGNADO':return { classes: 'info ',css: {'min-width':'200px','vertical-align': 'middle','text-align': 'center','font-size': '11px;','font-weight': '500'} };break;
          case 'RECHAZADA':return { classes: 'danger',css: {'min-width':'200px','vertical-align': 'middle','text-align': 'center','font-size': '11px;','font-weight': '500'} };break;
          case 'CORREGIDA':return { classes: 'estatusCorregida',css: {'min-width':'200px','vertical-align': 'middle','text-align': 'center','font-size': '11px;','font-weight': '500'} };break;
          case 'TERMINADA':return { classes: 'success',css: {'min-width':'200px','vertical-align': 'middle','text-align': 'center','font-size': '11px;','font-weight': '500'} };break;
        }
        return {css: {'min-width':'200px'}};
      }
    },{
      field: 'inicioVig',
      title: 'INICIO VIGENCIA',
      width:'5%',
      visible:(_PERMISOS_.cargo === 11)?false:true
    },{
      field: 'finVig',
      title: 'FIN VIGENCIA',
      width:'5%',
      visible:(_PERMISOS_.cargo === 11)?false:true
    },{
      field: 'diasRestantes',
      title: 'DIAS VIGENTES',
      width:'5%',
      formatter:'diasRestantes',
      visible:(_PERMISOS_.cargo === 11)?false:true,
      cellStyle: function(value,row) {
        if (row.estatus === 'INSPECTOR ASIGNADO') {
          var itemColor = '';
          if (value !== null) {
            if (value === 1) {
              itemColor = `#ffecb3`;
            }else if(value > 1 && value < 11){
              itemColor = `#ffecb3`;
            }else if(value > 11){
              itemColor = `#c8e6c9`;
            }else{
              itemColor = `#ffcdd2`;
            }
          }
          return {css: {'vertical-align': 'middle','text-align': 'center','background': itemColor,'min-width':'150px'}};
        }
        return {css: {'vertical-align': 'middle','text-align': 'center'}};
      }
    },{
      field: 'inspectores',
      title: 'INSPECTORES',
      cellStyle: function(value,row) {
        return {css: {'min-width':'300px','vertical-align': 'middle'}};
      }
    },{
      field: 'fechaInspeccion',
      title: 'FECHA PROGRAMADA',
      cellStyle: function(value,row) {
        return {css: {'min-width':'100px','vertical-align': 'middle','text-align': 'center'}};
      }
    }],
    // sortStable: true,
    sortOrder:"desc",
    pageNumber: 1, // pagina q se muestra por default
    pageSize: 10,
    pageList: [10, 25, 50, 100], //
    // smartDisplay: true,
    sidePagination: "server",
    paginationVAlign: "bottom", //formato de botones en paginacion
    cache: false,
    maintainSelected: true,
    // showColumns: true,
    pagination: true,
  });
}
/********************************************************************************************************************************
********************************************************************************************************************************/
function putTable(){
  $('#tablaSolicitudes').bootstrapTable('destroy');
  var tableTest = $('#tablaSolicitudes').bootstrapTable({
    url: "php/solicitudes_ocuv.php",
    method: "POST",
    contentType: "application/x-www-form-urlencoded;charset=UTF-8",
    queryParams: function(p) {
      return {
        "action": (_PERMISOS_.cargo === 11)?"solicitudesUI":"solicitudes",
        limit       : p.limit,
        offset      : p.offset,
        usuario     : id_usuario,
        inspector   : user,
        cliente     : $("#filAsociado").val(),
        solicitud   : $("#filSolicitud").val(),
        prioridad   : $("#filPrioridad").val(),
        estatus     : $("#filEstatus").val(),
        verificador : $("#filverificador").val(),
        actividad   : $("#filActividad").val(),
        fechaI      : $("#filFechaIni").val(),
        fechaF      : $("#filFechaFin").val(),
        tipofecha   : $("input:radio[name='optradiofechas']:checked").val(),
        registro      : $("#cmb_personal_at").val(),


      };
    },
    // showRefresh: true,
    columns: [{
      field: 'id',
      title: 'ID',
      visible: false,
      min_width:'150px',
    },{
      field: 'solicitud',
      title: 'SOLICITUD',
      formatter:'formatterSolicitud',
      cellStyle: function(value,row) {
        return {css: {'min-width':'130px','vertical-align': 'middle'}};
      }
    },{
      field: 'nombreActividad',
      title: 'ACTIVIDAD',
      cellStyle: function(value,row) {
        return {css: {'min-width':'200px','vertical-align': 'middle'}};
      }
    },{
      field: 'cliente',
      title: 'SOLICITANTE',
      cellStyle: function(value,row) {
        return {css: {'vertical-align': 'middle','text-align': 'center'}};
      }

    },{
      field: 'clienteNombre',
      title: 'RAZON SOCIAL',
      cellStyle: function(value,row) {
        return {css: {'min-width':'200px','vertical-align': 'middle'}};
      }
    },{
      field: 'registro',
      title: 'FECHA',
      cellStyle: function(value,row) {
        return {css: {'min-width':'80px','vertical-align': 'middle','text-align': 'center'}};
      }
    },{
      field: 'propuesta',
      title: 'FECHA <br>PROPUESTA',
      cellStyle: function(value,row) {
        return {css: {'vertical-align': 'middle','text-align': 'center'}};
      }
    },{
      field: 'prioridad',
      title: 'TIPO DE <br>SERVICIO',
      cellStyle: function(value,row) {
        if (value == "EXCLUSIVO") {
          return { classes: 'danger',css: {'vertical-align': 'middle','text-align': 'center','font-size': '11px;','font-weight': '500'} };
        }
        return { classes: 'info',css: {'vertical-align': 'middle','text-align': 'center','font-size': '11px;','font-weight': '500'} };

      }
    },{
      field: 'estatus',
      title: 'ESTATUS',
      cellStyle: function(value,row) {
        switch (value) {
          case 'PENDIENTE DE ASIGNACIÓN':return { classes: 'warning',css: {'min-width':'100px','vertical-align': 'middle','text-align': 'center','font-size': '11px;','font-weight': '500'} };break;
          case 'INSPECCIÓN REALIZADA':return { classes: 'success',css: {'min-width':'100px','vertical-align': 'middle','text-align': 'center','font-size': '11px;','font-weight': '500'} };break;
          case 'PENDIENTE DE PAGO':return { classes: 'danger',css: {'min-width':'100px','vertical-align': 'middle','text-align': 'center','font-size': '11px;','font-weight': '500'} };break;
          case 'INSPECCIÓN NO REALIZADA':return { classes: 'danger',css: {'min-width':'100px','vertical-align': 'middle','text-align': 'center','font-size': '11px;','font-weight': '500'} };break;
          case 'REASIGNACIÓN PENDIENTE':return { classes: 'warning',css: {'min-width':'100px','vertical-align': 'middle','text-align': 'center','font-size': '11px;','font-weight': '500'} };break;
          case 'CANCELADA':return { classes: 'danger',css: {'min-width':'100px','vertical-align': 'middle','text-align': 'center','font-size': '11px;','font-weight': '500'} };break;
          case 'INSPECTOR ASIGNADO':return { classes: 'info ',css: {'min-width':'100px','vertical-align': 'middle','text-align': 'center','font-size': '11px;','font-weight': '500'} };break;
          case 'RECHAZADA':return { classes: 'danger',css: {'min-width':'100px','vertical-align': 'middle','text-align': 'center','font-size': '11px;','font-weight': '500'} };break;
          case 'CORREGIDA':return { classes: 'estatusCorregida',css: {'min-width':'100px','vertical-align': 'middle','text-align': 'center','font-size': '11px;','font-weight': '500'} };break;
          case 'TERMINADA':return { classes: 'success',css: {'min-width':'100px','vertical-align': 'middle','text-align': 'center','font-size': '11px;','font-weight': '500'} };break;
        }
        return ;
      }
    },{
      field: 'inicioVig',
      title: 'INICIO <br>VIGENCIA',
      visible:(_PERMISOS_.cargo === 11)?false:true,
      formatter:'formatterVigencia',
      cellStyle: function(value,row) {
        return {css: {'min-width':'80px','vertical-align': 'middle','text-align': 'center'}};
      }
    },{
      field: 'finVig',
      title: 'FIN <br>VIGENCIA',
      visible:(_PERMISOS_.cargo === 11)?false:true,
      formatter:'formatterVigencia',
      cellStyle: function(value,row) {
        return {css: {'min-width':'80px','vertical-align': 'middle','text-align': 'center'}};
      }

    },{
      field: 'diasRestantes',
      title: 'DIAS <br>VIGENTES',
      formatter:'diasRestantes',
      visible:(_PERMISOS_.cargo === 11)?false:true,
      cellStyle: function(value,row) {
        if (row.estatus === 'INSPECTOR ASIGNADO') {
          var itemColor = '';
          if (value !== null) {
            if (value === 1) {
              itemColor = `#ffecb3`;
            }else if(value > 1 && value < 11){
              itemColor = `#ffecb3`;
            }else if(value > 11){
              itemColor = `#c8e6c9`;
            }else{
              itemColor = `#ffcdd2`;
            }
          }
          return {css: {'vertical-align': 'middle','text-align': 'center','background': itemColor,'min-width':'80px'}};
        }
        return {css: {'vertical-align': 'middle','text-align': 'center'}};
      }
    },{
      field: 'inspectores',
      title: 'INSPECTORES',
      cellStyle: function(value,row) {
        return {css: {'min-width':'250px','vertical-align': 'middle'}};
      }
    },{
      field: 'fechaInspeccion',
      title: 'FECHA <br>PROGRAMADA',
      cellStyle: function(value,row) {
        return {css: {'vertical-align': 'middle','text-align': 'center'}};
      }
    }],
    // sortStable: true,
    sortOrder:"desc",
    pageNumber: 1, // pagina q se muestra por default
    pageSize: 10,
    pageList: [10, 25, 50, 100], //
    // smartDisplay: true,
    sidePagination: "server",
    paginationVAlign: "bottom", //formato de botones en paginacion
    cache: false,
    maintainSelected: true,
    // showColumns: true,
    pagination: true,
  });
}
/********************************************************************************************************************************
********************************************************************************************************************************/
function diasRestantes(value, row) {
  // if (row.estatus === 'INSPECTOR ASIGNADO') {
    var item = '--';
    if (value !== null) {
      if (value === 1) {
        item = `<span><img src="images/c3.svg" width="5">${value} Dia</span>`;
      }else if(value > 1 && value < 11){
        item = `<span><img src="images/c3.svg" width="5"> ${value} Dias</span>`;
      }else if(value >= 11){
        item = `<span><img src="images/c2.svg" width="5"> ${value} Dias</span>`;
      }else{
        item = `<span><img src="images/c1.svg" width="5"> Vencio hace ${value} Dia(s)</span>`;
      }

    }
  // }
  return item;
}
/********************************************************************************************************************************
********************************************************************************************************************************/
function formatterVigencia(value, row) {
  return (value !== '0000-00-00')?value:'--';
}
/********************************************************************************************************************************
********************************************************************************************************************************/
function formatterSolicitud(value, row) {
  var items = '';
  return items = `<div class="col-lg-12">
                    <div class="col-lg-2" style="-webkit-user-select: none;-khtml-user-select: none;-moz-user-select: none;-ms-user-select: none;-o-user-select: none;user-select: none;">
                      ${row.comentarios > 0?`<img src="images/message.svg"  width="10">`:''} ${row.reveca > 0?`&nbsp;&nbsp;<img src="images/s.svg"  width="10">`:''}
                    </div>
                    <div class="col-lg-10">
                      ${value}
                    </div>
                  </div>`;

}
/********************************************************************************************************************************
********************************************************************************************************************************/
function modificar(){

  $("#tabla_ins").empty();
  $("#btnModalButtons").empty();
  $( "#form-sol input:checked" ).prop( "checked", false );
  _TIPO_MODAL = 'M';
  $("#inp_solicitante").disabled();
  $("#inp_actividad").disabled();
  $("#btnOpcionesSol").disabled();

  loadActividadesModal();

  $("#documentos_file").fileinput({
    browseClass: "btn btn-primary btn-block",
    allowedFileExtensions: ["jpg", "jpeg", "pdf", "png"],
    showCaption: false,
    showRemove: false,
    showUpload: false
  });

  $("#btnModalButtons").append(`<button style="width: 150px;font-size: 12px;" id="btnModificaSol" onclick="modificarSolicitud();" type="button" class="btn btn-success">MODIFICAR</button>
                                <button style="width: 150px;font-size: 12px;" type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>`);
  $("#modalRegistroModifica").modal("show");

  $.ajax({
    type: "POST",
    url: "php/solicitudes_ocuv.php",
    contentType: "application/x-www-form-urlencoded;charset=UTF-8",
    data:{
      action:'getSolicitudModifica',
      solicitud:SOLICITUD_SELECCIONADA
    },
    dataType: "json",
    success: function(data, textStatus, jqXHR) {
      if (data.status == "correcto") {
        var item = data.solicitud;
        var itemVariables = data.datos;

        $("#inp_actividad").val(item.idActividad);
        $("#imp_fecha_prop").val(item.fechaPropuesta);
        $("#inp_solicitante").val(item.clienteSol);
        $("#labelMDClienteSol").text(item.noClienteSol);
        $('#txtReqConfirmacion').bootstrapToggle('off');
        $("#mdlTitle").text(item.solicitud);


        $('#txtprioridad').bootstrapToggle('off');
        if (item.prioridad === 'U') {
          $('#txtprioridad').bootstrapToggle('on');
        }

        for (var i = 0; i < itemVariables.length; i++) {
          var row = itemVariables[i];



          /************************************/
          if (row.nombre === "clienteEnvia") {
            $("#imp_cli_envia").defaultBorder();
            if (row.visible === 'N') {
              $("#div_cliente_envia").slideUp();
              $("#imp_cli_envia").val('');
            }else{
              $("#div_cliente_envia").slideDown();
              $("#imp_cli_envia").val(row.value);
            }
          }
          /************************************/
          if (row.nombre === "clienteRecibe") {
            $("#imp_cli_recibe").defaultBorder();
            if (row.visible === 'N') {
              $("#div_cliente_recibe").slideUp();
              $("#imp_cli_recibe").val('');
            }else{
              $("#div_cliente_recibe").slideDown();
              $("#imp_cli_recibe").val(row.value);
            }
          }
          /************************************/
          if (row.nombre === "maquilador") {
            $("#imp_maquila").defaultBorder();
            if (row.visible === 'N') {
              $("#div_cliente_maquilador").slideUp();
              $("#imp_maquila").val('');
              $("#labelMDClienteMaq").text('');
            }else{
              $("#div_cliente_maquilador").slideDown();
              $("#imp_maquila").val(row.value);
              $("#labelMDClienteMaq").text(row.razon);
            }
          }/************************************/
          if (row.nombre === "responsableUno") {
            $("#imp_responsable_uno").defaultBorder();
            if (row.visible === 'N') {
              $("#div_responsable_uno").slideUp();
              $("#imp_responsable_uno").val('');
            }else{
              $("#div_responsable_uno").slideDown();
              $("#imp_responsable_uno").val(row.value);
            }
          }
          /************************************/
          if (row.nombre === "responsableDos") {
            $("#imp_responsable_dos").defaultBorder();
            if (row.visible === 'N') {
              $("#div_responsable_dos").slideUp();
              $("#imp_responsable_dos").val('');
            }else{
              $("#div_responsable_dos").slideDown();
              $("#imp_responsable_dos").val(row.value);
            }
          }
          /************************************/
          if (row.nombre === "direccionUno") {
            $("#imp_domicilio_uno").defaultBorder();
            if (row.visible === 'N') {
              $("#div_domicilio_uno").slideUp();
              $("#imp_domicilio_uno").val('');
            }else{
              $("#div_domicilio_uno").slideDown();
              $("#imp_domicilio_uno").val(row.value);
            }
          }

          /************************************/
          if (row.nombre === "direccionIns") {
            $("#imp_domicilio_ins").defaultBorder();
            if (row.visible === 'N') {
              $("#div_domicilio_ins").slideUp();
              $("#tabla_ins").empty();
              $('#imp_domicilio_ins').empty();
            }else{
              loadInstalaciones(1,row.value);

              $("#div_domicilio_ins").slideDown();

              for (var x = 0; x < row.value.length; x++) {
                var itemIns = row.value[x];
                $('#tabla_ins').append(`<tr id="ins-${itemIns.id_tabla}" style="background-color: #fff"><td width="45%">${itemIns.domicilio}</td><td width="25%">${itemIns.observaciones}</td><td width="10%" align="center"><img class="delete" src="images/delete.svg"  height="15" width="15" style="cursor:pointer"></td></tr>`);

              }

              
              //$("#imp_domicilio_ins").val(row.value);
            }
          }

          /************************************/
          if (row.nombre === "detActividades") {
            $("#det_actividades").defaultBorder();
            if (row.visible === 'N') {
              $("#div_det_actividades").slideUp();
              $( "#form-sol input:checked" ).prop( "checked", false );
            }else{
              $("#div_det_actividades").slideDown();
              for (var x = 0; x < row.value.length; x++) {
                var itemDetActividades = row.value[x];    
                (itemDetActividades.productor === "S")? $( "#chkProductor" ).prop( "checked", true ):'';
                (itemDetActividades.envasador === "S")? $( "#chkEnvasador" ).prop( "checked", true ):'';
                (itemDetActividades.comercializador === "S")? $( "#chkComercializador" ).prop( "checked", true ):'';
                (itemDetActividades.produccion === "S")? $( "#chkUProduccion" ).prop( "checked", true ):'';
                (itemDetActividades.envasado === "S")? $( "#chkUEnvasado" ).prop( "checked", true ):'';
                (itemDetActividades.almacen === "S")? $( "#chkUAlmacen" ).prop( "checked", true ):'';
                (itemDetActividades.comercializadorBCM === "S")? $( "#chkComercializadorBCM" ).prop( "checked", true ):'';
              }
              
            }
          }

          /************************************/
          if (row.nombre === "direccionDos") {
            $("#imp_domicilio_dos").defaultBorder();
            if (row.visible === 'N') {
              $("#div_domicilio_dos").slideUp();
              $("#imp_domicilio_dos").val('');
            }else{
              $("#div_domicilio_dos").slideDown();
              $("#imp_domicilio_dos").val(row.value);
            }
          }
          /************************************/
          if (row.nombre === "telefonoUno") {
            $("#imp_telefono_uno").defaultBorder();
            if (row.visible === 'N') {
              $("#div_telefono_uno").slideUp();
              $("#imp_telefono_uno").val('');
            }else{
              $("#div_telefono_uno").slideDown();
              $("#imp_telefono_uno").val(row.value);
            }
          }
          /************************************/
          if (row.nombre === "telefonoDos") {
            $("#div_telefono_dos").defaultBorder();
            if (row.visible === 'N') {
              $("#div_telefono_dos").slideUp();
              $("#imp_telefono_dos").val('');
            }else{
              $("#div_telefono_dos").slideDown();
              $("#imp_telefono_dos").val(row.value);
            }
          }
          /************************************/
          if (row.nombre === "vigencia") {
            $("#imp_fin_vig").defaultBorder();
            $("#imp_inicio_vig").defaultBorder();
            if (row.visible === 'N') {
              $("#div_vigencia").slideUp();
              $("#imp_inicio_vig").val('');
              $("#imp_fin_vig").val('');
            }else{
              $("#div_vigencia").slideDown();
              $("#imp_inicio_vig").val(row.value);
              $("#imp_fin_vig").val(row.value2);
            }
          }
          /************************************/
          if (row.nombre === "descActividad") {
            $("#imp_descripcion").defaultBorder();
            $("#imp_descripcion").val(row.value);
          }
          /************************************/
          if (row.nombre === "cliProductor") {
            $("#imp_productor").defaultBorder();
            if (row.visible === 'N') {
              $("#div_cliente_productor").slideUp();
              $("#imp_productor").val('');
              $("#labelMDClienteProd").text('');
            }else{
              $("#div_cliente_productor").slideDown();
              $("#imp_productor").val(row.value);
              $("#labelMDClienteProd").text(row.razon);
            }
          }
          /************************************/
          if (row.nombre === "tipoTraslado") {
            if (row.visible === 'N') {
              $("#div_tipo_traslado").slideUp();
              $('#toogleTR').bootstrapToggle('off');
            }else{
              $("#div_tipo_traslado").slideDown();
              if (row.value === 'S') {
                $('#toogleTR').bootstrapToggle('on');
              }else{
                $('#toogleTR').bootstrapToggle('off');
              }
            }
          }
          /************************************/
          if (row.nombre === "reqHolograma") {
            if (row.visible === 'N') {
              $("#div_requiere_holo").slideUp();
              $('#toogleReqHolo').bootstrapToggle('off');
            }else{
              $("#div_requiere_holo").slideDown();
              if (row.value === 'N') {
                $('#toogleReqHolo').bootstrapToggle('off');
              }else{
                $('#toogleReqHolo').bootstrapToggle('on');
              }
            }
          }
          /************************************/
          if (row.nombre === "hologramaEnt") {
            if (row.visible === 'N') {
              $("#div_holo_entrega").slideUp();
              $('#toogleHoloEntrega').bootstrapToggle('off');
            }else{
              $("#div_holo_entrega").slideDown();
              if (row.value === 'N') {
                $('#toogleHoloEntrega').bootstrapToggle('off');
              }else{
                $('#toogleHoloEntrega').bootstrapToggle('on');
              }
            }
          }
          /************************************/
          if (row.nombre === "tipoBaja") {
            if (row.visible === 'N') {
              $("#div_tipo_baja").slideUp();
              $("input[name=optradio_baja]").prop('checked', false);
            }else{
              $("#div_tipo_baja").slideDown();
              if (row.value === null) {
                $("input[name=optradio_baja]").prop('checked', false);
              }else if(row.value === 'D'){
                $("#opBajaDefinitiva").prop('checked', true);
              }else if(row.value === 'T'){
                $("#opBajaTemporal").prop('checked', true);
              }
            }
          }
          /************************************/


          if(item.idActividad == 31 || item.idActividad == 32 || item.idActividad == 33 || item.idActividad == 34 || item.idActividad == 35){
            if (row.nombre === "maquilador") {
              $("#imp_maquila").defaultBorder();
              if((row.value !== null) && (row.value.length > 0)){     
                $('#toogleReqMaquila').bootstrapToggle('on'); 
                $("#div_cliente_maquilador").slideDown();
                $("#imp_maquila").val(row.value);
                $("#labelMDClienteMaq").text(row.razon);
                $("#imp_domicilio_ins").enable();
              } else {           
                $('#toogleReqMaquila').bootstrapToggle('off'); 
                $("#div_cliente_maquilador").slideUp();
                $("#imp_maquila").val('');
                $("#labelMDClienteMaq").text('');      
              }
            }

            if (row.nombre === "vigencia") {
              $("#imp_fin_vig").defaultBorder();
              $("#imp_inicio_vig").defaultBorder();
              if((row.value !== null)&&(row.value !== '0000-00-00')){            
                $('#toogleReqVigencia').bootstrapToggle('on'); 
                $("#div_vigencia").slideDown();
                $("#imp_inicio_vig").val(row.value);
                $("#imp_fin_vig").val(row.value2);
              }else {               
                $('#toogleReqVigencia').bootstrapToggle('off'); 
                $("#div_vigencia").slideUp();
                $("#imp_inicio_vig").val('');
                $("#imp_fin_vig").val('');
              }      
            }
  
            if (row.nombre === "actProductor") {
              if (row.visible === 'N') {
                $("#div_act_productor").slideUp();
                $('#toogleActProductor').bootstrapToggle('off');
              }else{
                $("#div_act_productor").slideDown();
                if (row.value === 'N') {
                  $('#toogleActProductor').bootstrapToggle('off');
                }else{
                  $('#toogleActProductor').bootstrapToggle('on');
                }
              }
            }
            /************************************/


          } 

        }


        if(item.idActividad == 31 || item.idActividad == 32 || item.idActividad == 33 || item.idActividad == 34 || item.idActividad == 35){

          $("#div_requiere_maquila").slideDown();
          $("#div_requiere_vigencia").slideDown();  

        } else{
          $("#div_requiere_maquila").slideUp();  
          $("#div_requiere_vigencia").slideUp();
        }




        $("#div_documentos_actuales").empty();
        var trDocumentos = '';
        var countRows = 1;
        _LISTA_DOCS_A_MODIFICAR = item.documentos;
        for (var d = 0; d < _LISTA_DOCS_A_MODIFICAR.length; d++) {
          var itemDocumentos = _LISTA_DOCS_A_MODIFICAR[d];
          if (_LISTA_DOCS_A_MODIFICAR[d].origen === 'UG') {
            trDocumentos += `<tr>
                              <td style="padding: 4px;">${countRows}</td>
                              <td style="padding: 4px;">${itemDocumentos.original}</td>
                              <td style="padding: 4px;text-align: center;"><div class="checkbox">
                                                          <label style="font-size: 12px;"><input type="checkbox" id="md_${_LISTA_DOCS_A_MODIFICAR[d].id}" name="md_${_LISTA_DOCS_A_MODIFICAR[d].id}" value=${_LISTA_DOCS_A_MODIFICAR[d].id} class="messageCheckbox" ${(_LISTA_DOCS_A_MODIFICAR[d].estatus === 'A'?'checked':'')}/></label>
                                                        </div>
                              </td>
                             </tr>
                           `;

          }else if(_LISTA_DOCS_A_MODIFICAR[d].origen === 'O'){
            var rutaDoc = _LISTA_DOCS_A_MODIFICAR[d].ruta;
            var slash = rutaDoc.lastIndexOf("/");
            var nombrePDF = rutaDoc.substring(slash + 1, rutaDoc.length);
            trDocumentos += `<tr>
                              <td style="padding: 4px;">${countRows}</td>
                              <td style="padding: 4px;">${nombrePDF}</td>
                              <td style="padding: 4px;text-align: center;"><div class="checkbox">
                                                          <label style="font-size: 12px;"><input type="checkbox" id="md_${_LISTA_DOCS_A_MODIFICAR[d].id}" name="md_${_LISTA_DOCS_A_MODIFICAR[d].id}" value=${_LISTA_DOCS_A_MODIFICAR[d].id} class="messageCheckbox" ${(_LISTA_DOCS_A_MODIFICAR[d].estatus === 'A'?'checked':'')}/></label>
                                                        </div>
                              </td>
                             </tr>
                           `;

          }
          countRows++;
        }


        var trDocumentosDiv = '';
        if (_LISTA_DOCS_A_MODIFICAR.length > 0) {
          trDocumentosDiv = `<div class="col-lg-12">
                              <label class="control-label col-sm-2" style="font-size: 12px;">DOCUMENTOS ADJUNTOS: </label>
                              <div class="col-lg-9" style="border: 3px double #337ab759;border-radius: 5px;padding: 5px;">
                                <div class="col-lg-12">
                                  <div class="table-responsive">
                                    <table class="table" style="margin-bottom: 0px;">
                                     <thead>
                                        <tr>
                                          <th style="padding: 4px;">#</th>
                                          <th style="padding: 4px;">DOCUMENTO</th>
                                          <th style="padding: 4px;text-align: center;">ELIMINAR</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        ${trDocumentos}
                                      </tbody>
                                    </table>
                                  </div>
                                </div>
                              </div>
                            </div>`;
          $("#div_documentos_actuales").append(trDocumentosDiv);

          $('.messageCheckbox').change(function(e){
            var idCBX     = $(this).attr("id");
            var idCBX_VAL = $(this).val();

            if($(this).is(":checked")) {
              for (var o = 0; o < _LISTA_DOCS_A_MODIFICAR.length; o++) {
                if (_LISTA_DOCS_A_MODIFICAR[o].id === idCBX_VAL) {
                  _LISTA_DOCS_A_MODIFICAR[o]['estatus'] = 'A';
                }
              }
            }else{
              $.confirm({
                title: 'Eliminar Documento',
                content: '¿Estas seguro(a) de que quieres aplicar estos cambios?',
                type: 'orange',
                buttons: {
                  aceptar: function () {
                    for (var o = 0; o < _LISTA_DOCS_A_MODIFICAR.length; o++) {
                      if (Number(_LISTA_DOCS_A_MODIFICAR[o].id) === Number(idCBX_VAL)) {
                        _LISTA_DOCS_A_MODIFICAR[o]['estatus'] = 'I';
                      }
                    }
                  },
                  cancelar: function () {
                    $(`input[type="checkbox"][id="${idCBX}"]`).prop("checked", true).change();
                  }
                }
              });
            }
          });
        }
      }
    },error: function(jqxhr, status, errorGenerado) {
      alert("Ha ocurrido un error al cargar las actividades: " + jqxhr.responseText);
    }
  });
}

/********************************************************************************************************************************
********************************************************************************************************************************/

/********************************************************************************************************************************
********************************************************************************************************************************/
function loadActividadesModal(){
  $(`#inp_actividad`).empty();
  $.ajax({
    type: "POST",
    url: "php/solicitudes_ocuv.php",
    contentType: "application/x-www-form-urlencoded;charset=UTF-8",
    data: {
      action:'listaActividadesModal'
    },
    dataType: "json",
    success: function(data, textStatus, jqXHR) {
      if (data.status == "correcto") {
        for (var i = 0; i < data.actividades.length; i++) {
          if (data.actividades[i].seMuestra === 'S') {
            $(`#inp_actividad`).append($("<option>", {
              value: data.actividades[i].id,
              text: data.actividades[i].actividad
            }));
          }else{
            $(`#inp_actividad`).append($("<option>", {
              value: data.actividades[i].id,
              text: data.actividades[i].actividad,
              props:"disabled",
              selected:true
            }).prop('disabled', true));
          }
        }
      }
      else {
        alert("Ha ocurrido un error al cargar las instalaciones del asociado: " + data.msj);
      }
    },error: function(jqxhr, status, errorGenerado) {
      alert("Ha ocurrido un error al cargar las instalaciones del asociado: " + jqxhr.responseText);
    }
  });
}
/********************************************************************************************************************************
********************************************************************************************************************************/
function getInfoOpcion() {
  $("#contenido").empty();
  var count=1;
  $.ajax({
    type: "POST",
    url: "php/solicitudes_ocuv.php",
    contentType: "application/x-www-form-urlencoded;charset=UTF-8",
    data: {
      action    : 'opcionesComboSolicitud',
      actividad : $("#inp_actividad").val()
    },
    datatype: 'json',
    success: function(response) {
      var result = $.parseJSON(response);
      if (result.status == "correcto") {
        var item = result.solicitud;
        var itemVariables = result.datos;

        $("#titleModalOP").text($("#inp_actividad option:selected").text());

        for (var i = 0; i < itemVariables.length; i++) {
          var rowItem = itemVariables[i];
          if (rowItem.visible === 'S') {
            $("#contenido").append(`<tr><td>${rowItem.nombreReal}</td><td><input type="checkbox" class="chboxOpciones" data-on="ACTIVO" data-off="INACTIVO" data-width="100" data-onstyle="success" id=chbox-${rowItem.nombre} checked data-toggle="toggle" data-size="mini"></td></tr>`);
          }else{
            $("#contenido").append(`<tr><td>${rowItem.nombreReal}</td><td><input type="checkbox" class="chboxOpciones" data-on="ACTIVO" data-off="INACTIVO" data-width="100" data-onstyle="success" id=chbox-${rowItem.nombre} data-toggle="toggle" data-size="mini"></td></tr>`);
          }
          $(`#chbox-${rowItem.nombre}`).bootstrapToggle();
        }
      }
      $('#modalOpAct').modal('show');
    },
    beforeSend: function() {
    }
  });
}
/********************************************************************************************************************************
********************************************************************************************************************************/
function modificarSolicitud(){
  var numCamposVacios   = 0;
  var ed_Actividad      = $("#inp_actividad").val();
  var ed_Solicitante    = $("#inp_solicitante").val().trim();
  var ed_Maquila        = $("#imp_maquila").val().trim();
  var ed_ClienteEnv     = $("#imp_cli_envia").val().trim();
  var ed_ResponUno      = $("#imp_responsable_uno").val().trim();

  var ed_DomUno         = $("#imp_domicilio_uno").val().trim();
  var ed_TelUno         = $("#imp_telefono_uno").val().trim();
  var ed_ClienteRec     = $("#imp_cli_recibe").val().trim();
  var ed_ResponDos      = $("#imp_responsable_dos").val().trim();
  var ed_DomDos         = $("#imp_domicilio_dos").val().trim();

  var ed_TelDos         = $("#imp_telefono_dos").val().trim();
  var ed_FechaProp      = $("#imp_fecha_prop").val().trim();
  var ed_IniVig         = $("#imp_inicio_vig").val().trim();
  var ed_FinVig         = $("#imp_fin_vig").val().trim();
  var ed_DescAct        = $("#imp_descripcion").val().trim();

  var ed_Productor      = $("#imp_productor").val();
  var ed_TipoTr         = ($('#toogleTR').is(':checked')) ? "S" : "N";
  var ed_TipoServicio   = ($('#txtprioridad').is(':checked')) ? "U" : "N";
  var reqConfirmacion   = ($('#txtReqConfirmacion').is(':checked')) ? "S" : "N";
  var reqHologramas     = ($('#toogleReqHolo').is(':checked')) ? "S" : "N";
  var hologramasEntrega = ($('#toogleHoloEntrega').is(':checked')) ? "S" : "N";
  var actProductor      = ($('#toogleActProductor').is(':checked')) ? "S" : "N";
  var tipoBaja          = $('input[name=optradio_baja]:checked').val();
      tipoBaja          = (tipoBaja === undefined)?'':tipoBaja;

  var ed_DomIns         = $('#tabla_ins tr');

  var misActividades      = new Array();
  $("#form-sol input:checked").each(function() {
    misActividades.push($(this).val());
  });

  var ed_DetActividad = misActividades;

  var datos             = $("#documentos_file").serializefiles();
  var files             = $('#documentos_file')[0].files;

  /*********/
  if ($('#inp_solicitante').is(':visible')) {
    $('#inp_solicitante').defaultBorder();
    if (ed_Solicitante.length === 0) {
      $('#inp_solicitante').redBorder();
      numCamposVacios++;
    }
  }
  /*********/
  if ($('#imp_maquila').is(':visible')) {
    $('#imp_maquila').defaultBorder();
    if (ed_Maquila.length === 0) {
      $('#imp_maquila').redBorder();
      numCamposVacios++;
    }
  }
  /*********/
  if ($('#imp_cli_envia').is(':visible')) {
    $('#imp_cli_envia').defaultBorder();
    if (ed_ClienteEnv.length === 0) {
      $('#imp_cli_envia').redBorder();
      numCamposVacios++;
    }
  }
  /*********/
  if ($('#imp_responsable_uno').is(':visible')) {
    $('#imp_responsable_uno').defaultBorder();
    if (ed_ResponUno.length === 0) {
      $('#imp_responsable_uno').redBorder();
      numCamposVacios++;
    }
  }
  /*********/
  if ($('#imp_domicilio_uno').is(':visible')) {
    $('#imp_domicilio_uno').defaultBorder();
    if (ed_DomUno.length === 0) {
      $('#imp_domicilio_uno').redBorder();
      numCamposVacios++;
    }
  }
  /*********/
  if ($('#imp_telefono_uno').is(':visible')) {
    $('#imp_telefono_uno').defaultBorder();
    if (ed_TelUno.length === 0) {
      $('#imp_telefono_uno').redBorder();
      numCamposVacios++;
    }
  }
  /*********/
  if ($('#imp_cli_recibe').is(':visible')) {
    $('#imp_cli_recibe').defaultBorder();
    if (ed_ClienteRec.length === 0) {
      $('#imp_cli_recibe').redBorder();
      numCamposVacios++;
    }
  }
  /*********/
  if ($('#imp_responsable_dos').is(':visible')) {
    $('#imp_responsable_dos').defaultBorder();
    if (ed_ResponDos.length === 0) {
      $('#imp_responsable_dos').redBorder();
      numCamposVacios++;
    }
  }
  /*********/
  if ($('#imp_domicilio_dos').is(':visible')) {
    $('#imp_domicilio_dos').defaultBorder();
    if (ed_DomDos.length === 0) {
      $('#imp_domicilio_dos').redBorder();
      numCamposVacios++;
    }
  }
  /*********/
  if ($('#imp_telefono_dos').is(':visible')) {
    $('#imp_telefono_dos').defaultBorder();
    if (ed_TelDos.length === 0) {
      $('#imp_telefono_dos').redBorder();
      numCamposVacios++;
    }
  }
  /*********/
  if ($('#imp_fecha_prop').is(':visible')) {
    $('#imp_fecha_prop').defaultBorder();
    if (ed_FechaProp.length === 0) {
      $('#imp_fecha_prop').redBorder();
      numCamposVacios++;
    }
  }
  /*********/
  if ($('#imp_inicio_vig').is(':visible')) {
    $('#imp_inicio_vig').defaultBorder();
    if (ed_IniVig.length === 0) {
      $('#imp_inicio_vig').redBorder();
      numCamposVacios++;
    }
  }
  /*********/
  if ($('#imp_fin_vig').is(':visible')) {
    $('#imp_fin_vig').defaultBorder();
    if (ed_FinVig.length === 0) {
      $('#imp_fin_vig').redBorder();
      numCamposVacios++;
    }
  }
  /*********/
  if ($('#imp_descripcion').is(':visible')) {
    $('#imp_descripcion').defaultBorder();
    if (ed_DescAct.length === 0) {
      $('#imp_descripcion').redBorder();
      numCamposVacios++;
    }
  }
  /*********/
  if ($('#imp_productor').is(':visible')) {
    $('#imp_productor').defaultBorder();
    if (ed_Productor.length === 0) {
      $('#imp_productor').redBorder();
      numCamposVacios++;
    }
  }
  /*********/
  if ($("input[name=optradio_baja]").is(':visible')) {
    if (tipoBaja === undefined) {
      $.confirm({
        title: 'No has definido el tipo de baja',
        content: 'Para continuar es necesario definir el tipo de baja para la instalación',
        type: 'red',
        typeAnimated: true,
        buttons: {
          tryAgain: {
          text: 'Entendido',
          btnClass: 'btn-red',
          action: function(){}
        }
      }});
      return ;
    }
  }

  /*********/
  if ($('#imp_domicilio_ins').is(':visible')) {
    $('#imp_domicilio_ins').defaultBorder();
    if (ed_DomIns.length === 0) {
      $('#imp_domicilio_ins').redBorder();
      numCamposVacios++;
    }
  }


  /*********/
  if ($('#det_actividades').is(':visible')) {
    $('#det_actividades').defaultBorder();
    if (ed_DetActividad.length === 0) {
      $('#det_actividades').redBorder();
      numCamposVacios++;
    }
  }


  if (numCamposVacios > 0) {
    $.confirm({
      title: 'Hay campos vacios',
      content: 'Para continuar es necesario llenar todos los campos',
      type: 'red',
      typeAnimated: true,
      buttons: {
        tryAgain: {
        text: 'Entendido',
        btnClass: 'btn-red',
        action: function(){}
      }
    }});
    return ;
  }

  if (files.length === 0) {
    var activos = 0;
    for (var i = 0; i < _LISTA_DOCS_A_MODIFICAR.length; i++) {
      if (_LISTA_DOCS_A_MODIFICAR[i].estatus === 'A') {
        activos++;
      }
    }
    if (activos === 0) {
      $.confirm({
        title: 'No has seleccionado ningun archivo',
        content: 'Para continuar es necesario que selecciones almenos un archivo',
        type: 'red',
        typeAnimated: true,
        buttons: {
          Cerrar: {
            text: 'Entendido',
            btnClass: 'btn-red',
            action: function(){

            }
          }
        }
      });
      return;
    }


  }

  var datos = $("#documentos_file").serializefiles();

  datos.append("actividad"      , ed_Actividad);
  datos.append("solicitante"    , ed_Solicitante);
  datos.append("maquila"        , ed_Maquila);
  datos.append("clienteEnv"     , ed_ClienteEnv);
  datos.append("responsableUno" , ed_ResponUno);

  datos.append("domicilioUno"   , ed_DomUno);
  datos.append("telefonoUno"    , ed_TelUno);
  datos.append("clienteRec"     , ed_ClienteRec);
  datos.append("responsableDos" , ed_ResponDos);
  datos.append("domicilioDos"   , ed_DomDos);

  datos.append("telefonoDos"    , ed_TelDos);
  datos.append("fechaProp"      , ed_FechaProp);
  datos.append("inicioVig"      , ed_IniVig);
  datos.append("finVig"         , ed_FinVig);
  datos.append("descripcion"    , ed_DescAct);

  datos.append("productor"      , ed_Productor);
  datos.append("tipoTr"         , ed_TipoTr);
  datos.append("prioridad"      , ed_TipoServicio);
  datos.append("reqConfirma"    , reqConfirmacion);
  datos.append("reqHologramas"  , reqHologramas);
  datos.append("holoEntrega"    , hologramasEntrega);


  datos.append("tipoBaja"       , tipoBaja);
  datos.append("personaNom"     , user);
  datos.append("personaID"      , id_usuario);
  datos.append("idSolicitud"    , SOLICITUD_SELECCIONADA);
  datos.append("documentos"     , JSON.stringify(_LISTA_DOCS_A_MODIFICAR));
  datos.append("numSolicitud"   , SOLICITUD_SELECCIONADA_NUM);
  datos.append("action"         , 'modificaSolicitud');

  datos.append("domicilioIns"   , JSON.stringify(getIdInstalaciones()));
  datos.append("misActividades"   , JSON.stringify(misActividades));
  datos.append("actProductor"   , actProductor);

  for (var i = 0; i < files.length; i++) {
    datos.append('file' + i, files[i]);
  }

  $.confirm({
    title: '¡Modificar Solicitud!',
    content: '¿Estas segura de querer modificar esta solicitud?',
    type: 'dark',
    typeAnimated: true,
    buttons: {
      tryAgain: {
        text: 'SI,MODIFICAR',
        btnClass: 'btn-dark',
        action: function(){

          $.ajax({
            type: "POST",
            url: "php/solicitudes_ocuv.php",
            contentType: false,
            processData: false,
            data: datos,
            datatype: 'json',
            success: function(response) {
              var result = $.parseJSON(response);
              if (result.status === 'correcto') {
                limpiaFormModifica();
                $("#modalRegistroModifica").modal("hide");
                getInformacionSolicitud();
              }
              $("#btnModificaSol").enable();
            },
            beforeSend: function() {
              $("#btnModificaSol").disabled();
            },
            error: function(jqxhr, status, errorGenerado) {

            }
          });
        }
      },
      cancelar: function () {

      }
    }
  });
}
/********************************************************************************************************************************
********************************************************************************************************************************/
function limpiaFormModifica(){
  $("#inp_actividad").val('');
  $("#inp_solicitante").val('');
  $("#imp_maquila").val('');
  $("#imp_cli_envia").val('');
  $("#imp_responsable_uno").val('');

  $("#imp_domicilio_uno").val('');
  $("#imp_telefono_uno").val('');
  $("#imp_cli_recibe").val('');
  $("#imp_responsable_dos").val('');
  $("#imp_domicilio_dos").val('');

  $("#imp_telefono_dos").val('');
  $("#imp_fecha_prop").val('');
  $("#imp_inicio_vig").val('');
  $("#imp_fin_vig").val('');
  $("#imp_descripcion").val('');
  $("#imp_productor").val('');
  $("#div_documentos_actuales").empty();
  $('#documentos_file').fileinput('clear');
  $('#txtprioridad').bootstrapToggle('off');
  $('#txtReqConfirmacion').bootstrapToggle('off');
  $('#toogleTR').bootstrapToggle('off');
  $("#labelMDClienteSol").text('');
  $("#labelEstadoClienteSol").text('');
  $("#labelMDClienteProd").text('');
  $("input[name=optradio_baja]").prop('checked', false);
  $('#toogleReqHolo').bootstrapToggle('off');
  $('#toogleHoloEntrega').bootstrapToggle('off');
  $("#tabla_ins").empty();
  $('#imp_domicilio_ins').empty();
  $( "#form-sol input:checked" ).prop( "checked", false );
  $('#imp_observaciones_ins').val('');
  $('#toogleActProductor').bootstrapToggle('off');


}
/********************************************************************************************************************************
********************************************************************************************************************************/
function confirmaPago(){
  $.confirm({
    title: '¡Confirmar Pago!',
    content: '¿Estas segura de querer confirmar el pago?',
    type: 'dark',
    typeAnimated: true,
    buttons: {
      tryAgain: {
        text: 'SI,CONFIRMAR',
        btnClass: 'btn-dark',
        action: function(){

          $.ajax({
            type: "POST",
            url: "php/solicitudes_ocuv.php",
            contentType: "application/x-www-form-urlencoded;charset=UTF-8",
            data:{
              action    : 'confirmarPago',
              user      : id_usuario,
              solicitud : SOLICITUD_SELECCIONADA,
              userNom   : user
            },
            dataType: "json",
            success: function(response) {
              if (response.status === 'correcto') {

                getInformacionSolicitud();
              }
            },
            beforeSend: function() {

            },
            error: function(jqxhr, status, errorGenerado) {

            }
          });
        }
      },
      cancelar: function () {

      }
    }
  });
}
/********************************************************************************************************************************
********************************************************************************************************************************/
function confirmaEntrgaHologramas(){
  $.confirm({
    title: '¡Confirmar Entrega!',
    content: '¿Estas segura de confirmar la entrega de hologramas?',
    type: 'dark',
    typeAnimated: true,
    buttons: {
      tryAgain: {
        text: 'SI,CONFIRMAR',
        btnClass: 'btn-dark',
        action: function(){

          $.ajax({
            type: "POST",
            url: "php/solicitudes_ocuv.php",
            contentType: "application/x-www-form-urlencoded;charset=UTF-8",
            data:{
              action    : 'confirmarHologramas',
              user      : id_usuario,
              solicitud : SOLICITUD_SELECCIONADA
            },
            dataType: "json",
            success: function(response) {
              if (response.status === 'correcto') {

                getInformacionSolicitud();
              }
            },
            beforeSend: function() {

            },
            error: function(jqxhr, status, errorGenerado) {

            }
          });
        }
      },
      cancelar: function () {

      }
    }
  });
}
/********************************************************************************************************************************
********************************************************************************************************************************/
function cancelarSolicitud(){
  $("#modalSolicitud").modal("hide");
  $.confirm({
    title: 'Cancelar solicitud',
    content: '' +
    '<form action="" class="formName">' +
    '<div class="form-group">' +
    '<label>Ingresa el motivo por el cual se cancela esta solicitud</label>' +
    '<textarea class="form-control" rows="5" id="prompCancela"></textarea>' +
    '</div>' +
    '</form>',
    buttons: {
      formSubmit: {
        text: 'Si,Cancelar',
        btnClass: 'btn-red',
        action: function () {
          var name = this.$content.find('#prompCancela').val();
          if(!name){
            $('#prompCancela').redBorder();
            return false;
          }

          $.ajax({
            type: "POST",
            url: "php/solicitudes_ocuv.php",
            contentType: "application/x-www-form-urlencoded;charset=UTF-8",
            data:{
              action    : 'cancelarSolicitud',
              user      : id_usuario,
              solicitud : SOLICITUD_SELECCIONADA,
              mensaje   : name,
              userNom   : user,
              cargo     : _PERMISOS_.area
            },
            dataType: "json",
            success: function(response) {
              if (response.status === 'correcto') {

                getInformacionSolicitud();
              }
            },
            beforeSend: function() {

            },
            error: function(jqxhr, status, errorGenerado) {

            }
          });

        }
      },
      cerrar: function () {

      },
    },
    onContentReady: function () {
      var jc = this;
      this.$content.find('form').on('submit', function (e) {
        e.preventDefault();
        jc.$$formSubmit.trigger('click');
      });
    }
  });
}
/********************************************************************************************************************************
********************************************************************************************************************************/
function rechazarSolicitud(){
  $("#modalSolicitud").modal("hide");
  $.confirm({
    title: 'Rechazar solicitud',
    content: '' +
    '<form action="" class="formName">' +
    '<div class="form-group">' +
    '<label>Ingresa el motivo por el cual se rechaza esta solicitud</label>' +
    '<textarea class="form-control" rows="5" id="prompRechaza"></textarea>' +
    '</div>' +
    '</form>',
    buttons: {
      formSubmit: {
        text: 'Si,Rechazar',
        btnClass: 'btn-red',
        action: function () {
          var name = this.$content.find('#prompRechaza').val();
          if(!name){
            $('#prompRechaza').redBorder();
            return false;
          }

          $.ajax({
            type: "POST",
            url: "php/solicitudes_ocuv.php",
            contentType: "application/x-www-form-urlencoded;charset=UTF-8",
            data:{
              action    : 'rechazarSolicitud',
              user      : id_usuario,
              solicitud : SOLICITUD_SELECCIONADA,
              mensaje   : name,
              userNom   : user,
              cargo     : _PERMISOS_.area
            },
            dataType: "json",
            success: function(response) {
              if (response.status === 'correcto') {

                getInformacionSolicitud();
              }
            },
            beforeSend: function() {

            },
            error: function(jqxhr, status, errorGenerado) {

            }
          });

        }
      },
      cerrar: function () {

      },
    },
    onContentReady: function () {
      var jc = this;
      this.$content.find('form').on('submit', function (e) {
        e.preventDefault();
        jc.$$formSubmit.trigger('click');
      });
    }
  });
}

/********************************************************************************************************************************
********************************************************************************************************************************/
function terminarSolicitud(){
  $("#modalSolicitud").modal("hide");
  $.confirm({
    title: 'Terminar solicitud',
    content: '¿Estas seguro(a) de que deseas terminar la solicitud?',
    type: 'orange',
    buttons: {
      aceptar: function () {
        $.ajax({
          type: "POST",
          url: "php/solicitudes_ocuv.php",
          contentType: "application/x-www-form-urlencoded;charset=UTF-8",
          data:{
            action    : 'terminarSolicitud',
            user      : id_usuario,
            solicitud : SOLICITUD_SELECCIONADA,
            userNom   : user,
            cargo     : _PERMISOS_.area
          },
          dataType: "json",
          success: function(response) {
            if (response.status === 'correcto') {

              getInformacionSolicitud();
            }
          },
          beforeSend: function() {

          },
          error: function(jqxhr, status, errorGenerado) {

          }
        });
      },
      cerrar: function () {

      }
    }
  });
}
/********************************************************************************************************************************
********************************************************************************************************************************/
function getComentarios(){
  var datos = {
    "action"    : "getComentarios",
    "solicitud" : SOLICITUD_SELECCIONADA
  };

  $.ajax({
    type: "POST",
    url: "php/solicitudes_ocuv.php",
    contentType: "application/x-www-form-urlencoded;charset=UTF-8",
    data: datos,
    dataType: "json",
    success: function(data, textStatus, jqXHR) {
      if (data.status === 'correcto') {

        contenido = '';
        var mensajes = data.mensajes;
        $("#chat").empty();
        for (var i = 0; i < mensajes.length; i++) {

          contenido += `<div class="message text-only">
                          <p class="text"><span style="font-size: 12px;font-weight: 600;color: #00796B;">${mensajes[i].usuario}</span>
                          <span style="font-size: 11px;font-weight: 500;color: #d40d0dc7;"> ${(mensajes[i].tipo === 1)?' (CANCELO SOLICITUD)':(mensajes[i].tipo === 2)?' (RECHAZO SOLICITUD)':''}</span>
                          <br> ${mensajes[i].mensaje}</p>
                        </div>
                        <p class="time">${mensajes[i].fecha}</p>
                        `;
        }
        $("#chat").html(contenido);
      }
    },
    beforeSend: function(){},
    error: function(jqxhr, status, errorGenerado) {}
  });
}
/********************************************************************************************************************************
********************************************************************************************************************************/
function addComentario(){
  $.ajax({
    type: "POST",
    url: "php/solicitudes_ocuv.php",
    contentType: "application/x-www-form-urlencoded;charset=UTF-8",
    data:{
      action    : 'agregarComentario',
      user      : id_usuario,
      solicitud : SOLICITUD_SELECCIONADA,
      mensaje   : $("#write-message").val()
    },
    dataType: "json",
    success: function(response) {
      if (response.status === 'correcto') {
        $("#write-message").val('');
        getComentarios();
      }
    },
    beforeSend: function() {

    },
    error: function(jqxhr, status, errorGenerado) {

    }
  });
}
/********************************************************************************************************************************
********************************************************************************************************************************/
function nuevaSolicitud(){
  $("#btnModalButtons").empty();
  _TIPO_MODAL = 'N';
  loadActividadesModal();
  $("#inp_solicitante").enable();
  $("#inp_actividad").enable();
  $("#btnOpcionesSol").enable();
  $("#btnNuevaSolicitud").disabled();
  $("#div_estado_cliente").hide();
  $("#div_estado_maquila").hide();


  $("#documentos_file").fileinput({
    browseClass: "btn btn-primary btn-block",
    allowedFileExtensions: ["jpg", "jpeg", "pdf", "png"],
    showCaption: false,
    showRemove: false,
    showUpload: false
  });
  limpiaFormModifica();
  $("#mdlTitle").text("NUEVA SOLICITUD");
  $("#btnModalButtons").append(`<button style="width: 150px;font-size: 12px;" id="btnModificaSol" onclick="registrarSolicitud();" type="button" class="btn btn-success">REGISTRAR</button>
                                <button style="width: 150px;font-size: 12px;" class="btn btn-danger" data-dismiss="modal">CANCELAR</button>`);
  setTimeout(function(){
    $("#inp_actividad").val(31).change();
    $("#btnNuevaSolicitud").enable();
    $("#modalRegistroModifica").modal("show");
  }, 500);
}
/********************************************************************************************************************************
********************************************************************************************************************************/
function registrarSolicitud(){
  var numCamposVacios   = 0;
  var ed_Actividad      = $("#inp_actividad").val();
  var ed_Solicitante    = $("#inp_solicitante").val().trim();
  var ed_Maquila        = $("#imp_maquila").val().trim();
  var ed_ClienteEnv     = $("#imp_cli_envia").val().trim();
  var ed_ResponUno      = $("#imp_responsable_uno").val().trim();

  var ed_DomUno         = $("#imp_domicilio_uno").val().trim();
  var ed_TelUno         = $("#imp_telefono_uno").val().trim();
  var ed_ClienteRec     = $("#imp_cli_recibe").val().trim();
  var ed_ResponDos      = $("#imp_responsable_dos").val().trim();
  var ed_DomDos         = $("#imp_domicilio_dos").val().trim();

  var ed_TelDos         = $("#imp_telefono_dos").val().trim();
  var ed_FechaProp      = $("#imp_fecha_prop").val().trim();
  var ed_IniVig         = $("#imp_inicio_vig").val().trim();
  var ed_FinVig         = $("#imp_fin_vig").val().trim();
  var ed_DescAct        = $("#imp_descripcion").val().trim();

  var ed_Productor      = $("#imp_productor").val();
  var ed_TipoTr         = ($('#toogleTR').is(':checked')) ? "S" : "N";
  var ed_TipoServicio   = ($('#txtprioridad').is(':checked')) ? "U" : "N";
  var reqConfirmacion   = ($('#txtReqConfirmacion').is(':checked')) ? "S" : "N";
  var reqHologramas     = ($('#toogleReqHolo').is(':checked')) ? "S" : "N";
  var hologramasEntrega = ($('#toogleHoloEntrega').is(':checked')) ? "S" : "N";
  var actProductor      = ($('#toogleActProductor').is(':checked')) ? "S" : "N";
  var tipoBaja          = $('input[name=optradio_baja]:checked').val();
      tipoBaja          = (tipoBaja === undefined)?'':tipoBaja;
  
  var ed_DomIns         = $('#tabla_ins tr');

  var misActividades      = new Array();
  $("#form-sol input:checked").each(function() {
    misActividades.push($(this).val());
  });

  var ed_DetActividad = misActividades;


  var datos             = $("#documentos_file").serializefiles();
  var files             = $('#documentos_file')[0].files;

  /*********/
  if ($('#inp_solicitante').is(':visible')) {
    $('#inp_solicitante').defaultBorder();
    if (ed_Solicitante.length === 0) {
      $('#inp_solicitante').redBorder();
      numCamposVacios++;
    }
  }
  /*********/
  if ($('#imp_maquila').is(':visible')) {
    $('#imp_maquila').defaultBorder();
    if (ed_Maquila.length === 0) {
      $('#imp_maquila').redBorder();
      numCamposVacios++;
    }
  }
  /*********/
  if ($('#imp_cli_envia').is(':visible')) {
    $('#imp_cli_envia').defaultBorder();
    if (ed_ClienteEnv.length === 0) {
      $('#imp_cli_envia').redBorder();
      numCamposVacios++;
    }
  }
  /*********/
  if ($('#imp_responsable_uno').is(':visible')) {
    $('#imp_responsable_uno').defaultBorder();
    if (ed_ResponUno.length === 0) {
      $('#imp_responsable_uno').redBorder();
      numCamposVacios++;
    }
  }
  /*********/
  if ($('#imp_domicilio_uno').is(':visible')) {
    $('#imp_domicilio_uno').defaultBorder();
    if (ed_DomUno.length === 0) {
      $('#imp_domicilio_uno').redBorder();
      numCamposVacios++;
    }
  }
  /*********/
  if ($('#imp_telefono_uno').is(':visible')) {
    $('#imp_telefono_uno').defaultBorder();
    if (ed_TelUno.length === 0) {
      $('#imp_telefono_uno').redBorder();
      numCamposVacios++;
    }
  }
  /*********/
  if ($('#imp_cli_recibe').is(':visible')) {
    $('#imp_cli_recibe').defaultBorder();
    if (ed_ClienteRec.length === 0) {
      $('#imp_cli_recibe').redBorder();
      numCamposVacios++;
    }
  }
  /*********/
  if ($('#imp_responsable_dos').is(':visible')) {
    $('#imp_responsable_dos').defaultBorder();
    if (ed_ResponDos.length === 0) {
      $('#imp_responsable_dos').redBorder();
      numCamposVacios++;
    }
  }
  /*********/
  if ($('#imp_domicilio_dos').is(':visible')) {
    $('#imp_domicilio_dos').defaultBorder();
    if (ed_DomDos.length === 0) {
      $('#imp_domicilio_dos').redBorder();
      numCamposVacios++;
    }
  }
  /*********/
  if ($('#imp_telefono_dos').is(':visible')) {
    $('#imp_telefono_dos').defaultBorder();
    if (ed_TelDos.length === 0) {
      $('#imp_telefono_dos').redBorder();
      numCamposVacios++;
    }
  }
  /*********/
  if ($('#imp_fecha_prop').is(':visible')) {
    $('#imp_fecha_prop').defaultBorder();
    if (ed_FechaProp.length === 0) {
      $('#imp_fecha_prop').redBorder();
      numCamposVacios++;
    }
  }
  /*********/
  if ($('#imp_inicio_vig').is(':visible')) {
    $('#imp_inicio_vig').defaultBorder();
    if (ed_IniVig.length === 0) {
      $('#imp_inicio_vig').redBorder();
      numCamposVacios++;
    }
  }
  /*********/
  if ($('#imp_fin_vig').is(':visible')) {
    $('#imp_fin_vig').defaultBorder();
    if (ed_FinVig.length === 0) {
      $('#imp_fin_vig').redBorder();
      numCamposVacios++;
    }
  }
  /*********/
  if ($('#imp_descripcion').is(':visible')) {
    $('#imp_descripcion').defaultBorder();
    if (ed_DescAct.length === 0) {
      $('#imp_descripcion').redBorder();
      numCamposVacios++;
    }
  }
  /*********/
  if ($('#imp_productor').is(':visible')) {
    $('#imp_productor').defaultBorder();
    if (ed_Productor.length === 0) {
      $('#imp_productor').redBorder();
      numCamposVacios++;
    }
  }
  /*********/

  /*********/
  if ($('#imp_domicilio_ins').is(':visible')) {
    $('#imp_domicilio_ins').defaultBorder();
    if (ed_DomIns.length === 0) {
      $('#imp_domicilio_ins').redBorder();
      numCamposVacios++;
    }
  }

  /*********/
  if ($('#det_actividades').is(':visible')) {
    $('#det_actividades').defaultBorder();
    if (ed_DetActividad.length === 0) {
      $('#det_actividades').redBorder();
      numCamposVacios++;
    }
  }

  /*********/
  if ($("input[name=optradio_baja]").is(':visible')) {
    if (tipoBaja === undefined) {
      $.confirm({
        title: 'No has definido el tipo de baja',
        content: 'Para continuar es necesario definir el tipo de baja para la instalación',
        type: 'red',
        typeAnimated: true,
        buttons: {
          tryAgain: {
          text: 'Entendido',
          btnClass: 'btn-red',
          action: function(){}
        }
      }});
      return ;
    }
  }


  if (numCamposVacios > 0) {
    $.confirm({
      title: 'Hay campos vacios',
      content: 'Para continuar es necesario llenar todos los campos',
      type: 'red',
      typeAnimated: true,
      buttons: {
        tryAgain: {
        text: 'Entendido',
        btnClass: 'btn-red',
        action: function(){}
      }
    }});
    return ;
  }

  if (files.length === 0) {
    $.confirm({
      title: 'No has seleccionado ningun archivo',
      content: 'Para continuar es necesario que selecciones almenos un archivo',
      type: 'red',
      typeAnimated: true,
      buttons: {
        Cerrar: {
          text: 'Entendido',
          btnClass: 'btn-red',
          action: function(){

          }
        }
      }
    });
    return;
  }

  var datos = $("#documentos_file").serializefiles();

  datos.append("actividad"      , ed_Actividad);
  datos.append("solicitante"    , ed_Solicitante);
  datos.append("maquila"        , ed_Maquila);
  datos.append("clienteEnv"     , ed_ClienteEnv);
  datos.append("responsableUno" , ed_ResponUno);

  datos.append("domicilioUno"   , ed_DomUno);
  datos.append("telefonoUno"    , ed_TelUno);
  datos.append("clienteRec"     , ed_ClienteRec);
  datos.append("responsableDos" , ed_ResponDos);
  datos.append("domicilioDos"   , ed_DomDos);

  datos.append("telefonoDos"    , ed_TelDos);
  datos.append("fechaProp"      , ed_FechaProp);
  datos.append("inicioVig"      , ed_IniVig);
  datos.append("finVig"         , ed_FinVig);
  datos.append("descripcion"    , ed_DescAct);

  datos.append("productor"      , ed_Productor);
  datos.append("tipoTr"         , ed_TipoTr);
  datos.append("prioridad"      , ed_TipoServicio);
  datos.append("reqConfirma"    , reqConfirmacion);
  datos.append("reqHologramas"  , reqHologramas);
  datos.append("holoEntrega"    , hologramasEntrega);



  datos.append("tipoBaja"       , tipoBaja);
  datos.append("personaNom"     , user);
  datos.append("personaID"      , id_usuario);
  datos.append("action"         , 'registrarSolicitud');

  datos.append("domicilioIns"   , JSON.stringify(getIdInstalaciones()));
  datos.append("misActividades"   , JSON.stringify(misActividades));
  datos.append("actProductor"   , actProductor);

  for (var i = 0; i < files.length; i++) {
    datos.append('file' + i, files[i]);
  }

  $.confirm({
    title: '¿Que tipo de solicitud es?',
    content: '¿Esta solicitud es ordinaria o solicitud se seguimiento?',
    type: 'dark',
    typeAnimated: true,
    buttons: {
      tryAgain: {
        text: 'ORDINARIA',
        btnClass: 'btn-dark',
        action: function(){
          datos.append("isSolicitud", 'O');
          _INSERT_      = datos;
          _INSERT_TIPO  = 'O';
          confirmaRegistro();
        }
      },
      info: {
        btnClass: 'btn-dark',
        text: 'SEGUIMIENTO',
        action: function(){
          datos.append("isSolicitud", 'S');
          _INSERT_      = datos;
          _INSERT_TIPO  = 'S';
          $("#confirmInsert").modal("show");
        }
      },
    }
  });
}
/*******************************************************************************************************************************************
*******************************************************************************************************************************************/
function confirmaRegistro(){
  if (_INSERT_TIPO === 'S') {
    var numIngresado = $("#in_solicitud").val().trim();
    var expresion = /^[SOL]{3}[0-9]{4}[/]{1}[0-9]{2}[-]{1}[0-9]{1,}$/.test(numIngresado);
    $("#in_solicitud").defaultBorder();
    if (!expresion) {
      $("#in_solicitud").redBorder();
      alert("Número de solicitud incorrecto");
      return $("#in_solicitud").focus();
    }
    _INSERT_.append("numSolicitud",numIngresado);
  }


  $.confirm({
    title: '¡Registrar solicitud!',
    content: '¿Estas segura de querer registrar esta solicitud?',
    type: 'dark',
    typeAnimated: true,
    buttons: {
      tryAgain: {
        text: 'SI,REGISTRAR',
        btnClass: 'btn-dark',
        action: function(){

          $.ajax({
            type: "POST",
            url: "php/solicitudes_ocuv.php",
            contentType: false,
            processData: false,
            data: _INSERT_,
            datatype: 'json',
            success: function(response) {
              var result = $.parseJSON(response);
              if (result.status === 'correcto') {
                if (result.codigo === 1) {
                  $.confirm({
                    title: 'Cancelada',
                    content: 'La solicitud ordinaria se encuentra cancelada',
                    type: 'red',
                    typeAnimated: true,
                    buttons: {
                      Cerrar: {
                        text: 'Entendido',
                        btnClass: 'btn-red',
                        action: function(){
                          $("#in_solicitud").redBorder();
                          return $("#in_solicitud").focus();
                        }
                      }
                    }
                  });

                }else if(result.codigo === 2){
                  $.confirm({
                    title: 'El número ya existe',
                    content: 'El número de solicitud ingresado ya existe',
                    type: 'red',
                    typeAnimated: true,
                    buttons: {
                      Cerrar: {
                        text: 'Entendido',
                        btnClass: 'btn-red',
                        action: function(){
                          $("#in_solicitud").redBorder();
                          return $("#in_solicitud").focus();
                        }
                      }
                    }
                  });
                }else if(result.codigo === 3){
                  $.confirm({
                    title: 'No corresponde a este asociado',
                    content: 'El número de solicitud no corresponde a este asociado',
                    type: 'red',
                    typeAnimated: true,
                    buttons: {
                      Cerrar: {
                        text: 'Entendido',
                        btnClass: 'btn-red',
                        action: function(){
                          $("#in_solicitud").redBorder();
                          return $("#in_solicitud").focus();
                        }
                      }
                    }
                  });
                }else if(result.codigo === 0){
                  $.confirm({
                    title: 'Solicitud registrada',
                    content: 'La solicitud fue registrada exitosamente',
                    type: 'green',
                    typeAnimated: true,
                    buttons: {
                      Cerrar: {
                        text: 'Entendido',
                        btnClass: 'btn-green',
                        action: function(){
                          $("#in_solicitud").val('');
                          $("#confirmInsert").modal("hide");
                          $("#modalRegistroModifica").modal("hide");
                          $('#tablaSolicitudes').bootstrapTable('refresh');
                          $('#documentos_file').fileinput('clear');
                        }
                      }
                    }
                  });
                }else if (result.codigo === 4) {
                  $.confirm({
                    title: 'Terminada',
                    content: 'La solicitud ordinaria se encuentra terminada',
                    type: 'red',
                    typeAnimated: true,
                    buttons: {
                      Cerrar: {
                        text: 'Entendido',
                        btnClass: 'btn-red',
                        action: function(){
                          $("#in_solicitud").redBorder();
                          return $("#in_solicitud").focus();
                        }
                      }
                    }
                  });

                }
              }
            },
            beforeSend: function() {

            },
            error: function(jqxhr, status, errorGenerado) {

            }
          });
        }
      },
      cancelar: function () {

      }
    }
  });
}
/*******************************************************************************************************************************************
*******************************************************************************************************************************************/
function ultimasSolicitudes(){
  var datos = {
    "action":'ultimasSol',
    "cliente":$("#inp_solicitante").val()
  };
  $.confirm({
    content: function () {
      var self = this;
      return $.ajax({
        url: "php/solicitudes_ocuv.php",
        dataType: 'json',
        data: datos,
        method: 'post'
      }).done(function (response) {
        var result = response.solicitud;
        if (result.length > 0) {
          var contenido = `<table class="table table-bordered"><thead style="background: #ffffff;color: #39485e;font-size: 12px;">
          <tr>
            <th style="text-align: center;">SOLICITUD</th>
            <th style="text-align: center;">SOLICITO</th>
            <th style="text-align: center;">MAQUILA</th>
            <th style="text-align: center;">NOMBRE (VIGILANCIA)</th>
            <th style="text-align: center;">TIPO DE SOLICITUD</th>
            <th style="text-align: center;">FECHA</th>
          </tr>
          </thead>
          <tbody>`;
          for (var i = 0; i < result.length; i++) {
            contenido += `<tr><td style="width: 110px;">${result[i].solicitud}</td>
                              <td>${result[i].cliente}  </td>
                              <td>${result[i].maquila}  </td>
                              <td>${result[i].nombre}   </td>
                              <td>${result[i].tipoSol}  </td>
                              <td>${result[i].fecha}  </td>
                          </tr>`;
          }
          contenido += '</tbody></table>';
          self.setContent(contenido);
          self.setTitle("ULTIMAS SOLICITUDES ASIGNADAS");

        }else{
          var contenido = `NO ENCONTRAMOS SOLICITUDES PARA ESTE CLIENTE/ASOCIADO`;
          self.setContent(contenido);
          self.setTitle("ULTIMAS SOLICITUDES ASIGNADAS");
        }
      }).fail(function(){
        self.setContent('Algo salio mal :(.');
      });
    },
    columnClass: 'col-md-12',
    theme: 'supervan',
    buttons: {
      Cerrar: function () {

      },
    }
  });
}
/*******************************************************************************************************************************************
*******************************************************************************************************************************************/
function consultaSeguimientos(solicitud){
  var datos = {
    "action" : "consultaSeguimientos",
    "solicitud":solicitud
  };
  $.confirm({
    content: function () {
      var self = this;
      return $.ajax({
        url: "php/solicitudes_ocuv.php",
        dataType: 'json',
        data: datos,
        method: 'post'
      }).done(function (response) {
        var result = response.solicitud;
        if (result.length > 0) {
          var contenido = `<table class="table table-bordered"><thead style="background: #ffffff;color: #39485e;font-size: 12px;">
          <tr>

            <th style="text-align: center;">SOLICITUD</th>
            <th style="text-align: center;">SOLICITO</th>
            <th style="text-align: center;">MAQUILA</th>
            <th style="text-align: center;">NOMBRE (VIGILANCIA)</th>
            <th style="text-align: center;">TIPO DE SOLICITUD</th>
            <th style="text-align: center;">FECHA</th>


          </tr>
          </thead>
          <tbody>`;
          for (var i = 0; i < result.length; i++) {
            contenido += `<tr><td style="width: 110px;">${result[i].solicitud}</td>
                          <td>${result[i].cliente}  </td>
                          <td>${result[i].maquila}  </td>
                          <td>${result[i].nombre}   </td>
                          <td>${result[i].tipoSol}  </td>
                          <td>${result[i].fecha}  </td>
                      </tr>`;
          }

          
          contenido += '</tbody></table>';
          self.setContent(contenido);
          self.setTitle("SEGUIMIENTO DE SOLICITUDES");

        }else{
          var contenido = `ESTE NÚMERO DE SOLICITUD NO SE HA UTILIZADO`;
          self.setContent(contenido);
          self.setTitle("SEGUIMIENTO DE SOLICITUDES");
        }
      }).fail(function(){
        self.setContent('Algo salio mal :(.');
      });
    },
    columnClass: 'col-md-12',
    theme: 'supervan',
    buttons: {
      Cerrar: function () {

      },
    }
  });
}
/*******************************************************************************************************************************************
*******************************************************************************************************************************************/
function putTablaNotificaciones(){
  $('#tablaNotificaciones').bootstrapTable('destroy');
  var tableTest = $('#tablaNotificaciones').bootstrapTable({
    url: "php/solicitudes_ocuv.php",
    method: "POST",
    contentType: "application/x-www-form-urlencoded;charset=UTF-8",
    queryParams:function(p){
      return {
        action  :'getNotificaciones',
        usuario : id_usuario,
        cargo   : _PERMISOS_.area,
        limit   : p.limit,
        offset  : p.offset
      };
    },
    showRefresh: false,
    columns:[
      {
        field: 'numSolicitud',
        width: '100px',
        formatter:"divNotificacion"
      }
    ],
    sortStable: true,
    sortOrder:"desc",
    pageNumber: 1,
    pageSize: 15,
    pageList: [15, 20, 50, 100],
    sidePagination: "server",
    paginationVAlign: "bottom",
    cache: false,
    maintainSelected: true,
    pagination: true,
  });
}
function divNotificacion(value, row){
  return `<div class="col-lg-12">
            <div class="col-lg-1">
              ${row.visto === 1?'<span style="font-size: 11px;color: #4fca89;font-weight: 500;"><img src="images/star-light.svg"  width="12"></span>':'<span style="font-size: 11px;color: #4fca89;font-weight: 500;"><img src="images/star-light2.svg"  width="12"></span>'}
            </div>
            <div class="col-lg-6" style="font-size: 12px;font-weight: 500;">
              ${row.nombre} ${row.descripcion } <span style="color: #c62828;">${row.num_solicitud}</span>
            </div>
            <div class="col-lg-3" style="font-size: 12px;font-weight: 500;text-align: center;">
              ${row.fecha}
            </div>
            <div class="col-lg-2" style="text-align: right;">
              ${row.visto === 1?'<span style="font-size: 11px;color: #4fca89;font-weight: 500;"><img src="images/tick.svg"  width="15"> VISTO</span>':'<span style="font-size: 11px;color: #b71c1c;font-weight: 500;">NO VISTO</span>'}
            </div>
          </div>`;

}
/*******************************************************************************************************************************************
*******************************************************************************************************************************************/
function dgasignacion(){

  var inspectorAcredita = $("#ui_inspector").val().trim();
  $("#ui_inspector").defaultBorder();
  $(`#p_ui_inspector`).text("");
  $(`#p_ui_inspector`).css('color', '#1f405d');
  if (inspectorAcredita.length === 0) {
    $("#ui_inspector").redBorder();
    $(`#p_ui_inspector`).text("ESCRIBE EL NOMBRE DEL UN INSPECTOR Y SELECCIONALO DE LA LISTA");
    $(`#p_ui_inspector`).css('color', 'red');
    return $("#ui_inspector").focus();
  }

  var inspectorCapacitacion = $("#ui_capacitacion").val().trim();
  // $("#ui_capacitacion").defaultBorder();
  // $(`#p_ui_capacitacion`).text("");
  // $(`#p_ui_capacitacion`).css('color', '#1f405d');
  // if (inspectorCapacitacion.length === 0) {
  //   $("#ui_capacitacion").redBorder();
  //   $(`#p_ui_capacitacion`).text("ESCRIBE EL NOMBRE DEL UN INSPECTOR O AUXILIAR Y SELECCIONALO DE LA LISTA");
  //   $(`#p_ui_capacitacion`).css('color', 'red');
  //   return $("#ui_capacitacion").focus();
  // }

  var fechaProgramada = $("#ui_fecha").val().trim();
  $("#ui_fecha").defaultBorder();
  if (fechaProgramada.length < 5) {
    $("#ui_fecha").redBorder();
    return $("#ui_fecha").focus();
  }

  var numActaDictamen = $("#ui_acta").val().trim();
  var observacionesUI = $("#ui_obervaciones").val().trim();




  $.confirm({
    title: '¡Confirmar asignación!',
    content: '¿Estas seguro(a) de realizar esta asignación?, recuerda que puedeas adjuntar hasta 3 documentos',
    type: 'dark',
    typeAnimated: true,
    buttons: {
      tryAgain: {
        text: 'SI,ASIGNAR',
        btnClass: 'btn-dark',
        action: function(){

          var datosUI = $("#documentos_file_ui").serializefiles();
          var filesUI = $('#documentos_file_ui')[0].files;

          datosUI.append("inspectorAcredita", inspectorAcredita);
          datosUI.append("inspectorCapacita", inspectorCapacitacion);
          datosUI.append("fechaPrograma",     fechaProgramada);
          datosUI.append("actaDictamen",      numActaDictamen);
          datosUI.append("observaciones",     observacionesUI);
          datosUI.append("personaNom",        user);
          datosUI.append("personaID",         id_usuario);
          datosUI.append("solicitud",         SOLICITUD_SELECCIONADA);
          datosUI.append("solicitudNum",      SOLICITUD_SELECCIONADA_NUM);
          datosUI.append("action",            'asignaInspeccion');

          for (var i = 0; i < filesUI.length; i++) {
            datosUI.append('file' + i, filesUI[i]);
          }

          $.ajax({
            type: "POST",
            url: "php/solicitudes_ocuv.php",
            contentType: false,
            processData: false,
            data: datosUI,
            datatype: 'json',
            success: function(response) {
              $("#btnUIAsigna").enable();
              var result = $.parseJSON(response);
              if (result.status === 'correcto') {
                $.confirm({
                  title: 'Correcto operación realizada',
                  content: 'La asignación fue realizada exitosamente',
                  type: 'green',
                  typeAnimated: true,
                  buttons: {
                    Cerrar: {
                      text: 'Entendido',
                      btnClass: 'btn-green',
                      action: function(){

                        $('#documentos_file_ui').fileinput('clear');
                        getInformacionSolicitud();
                        planEvaluacion(SOLICITUD_SELECCIONADA);
                        solicitudServicio(SOLICITUD_SELECCIONADA);
                      }
                    }
                  }
                });
              }
            },
            beforeSend: function() {
              $("#btnUIAsigna").disabled();
            },
            error: function(jqxhr, status, errorGenerado) {

            }
          });
        }
      },
      cancelar: function () {

      }
    }
  });
}
/*******************************************************************************************************************************************
*******************************************************************************************************************************************/
function mdAsignacion(){

  var inspectorAcredita = $("#ui_inspector").val().trim();
  $("#ui_inspector").defaultBorder();
  $(`#p_ui_inspector`).text("");
  $(`#p_ui_inspector`).css('color', '#1f405d');
  if (inspectorAcredita.length === 0) {
    $("#ui_inspector").redBorder();
    $(`#p_ui_inspector`).text("ESCRIBE EL NOMBRE DEL UN INSPECTOR Y SELECCIONALO DE LA LISTA");
    $(`#p_ui_inspector`).css('color', 'red');
    return $("#ui_inspector").focus();
  }

  var inspectorCapacitacion = $("#ui_capacitacion").val().trim();
  var fechaProgramada = $("#ui_fecha").val().trim();
  $("#ui_fecha").defaultBorder();
  if (fechaProgramada.length < 5) {
    $("#ui_fecha").redBorder();
    return $("#ui_fecha").focus();
  }

  var numActaDictamen = $("#ui_acta").val().trim();
  var observacionesUI = $("#ui_obervaciones").val().trim();




  $.confirm({
    title: '¡Confirmar modificación de asignación!',
    content: '¿Estas seguro(a) de realizar esta modificación?, recuerda que puedeas adjuntar hasta 3 documentos',
    type: 'dark',
    typeAnimated: true,
    buttons: {
      tryAgain: {
        text: 'SI,MODIFICAR',
        btnClass: 'btn-dark',
        action: function(){

          var datosUI = $("#documentos_file_ui").serializefiles();
          var filesUI = $('#documentos_file_ui')[0].files;

          datosUI.append("inspectorAcredita", inspectorAcredita);
          datosUI.append("inspectorCapacita", inspectorCapacitacion);
          datosUI.append("fechaPrograma",     fechaProgramada);
          datosUI.append("actaDictamen",      numActaDictamen);
          datosUI.append("observaciones",     observacionesUI);
          datosUI.append("personaNom",        user);
          datosUI.append("personaID",         id_usuario);
          datosUI.append("solicitud",         SOLICITUD_SELECCIONADA);
          datosUI.append("solicitudNum",      SOLICITUD_SELECCIONADA_NUM);
          datosUI.append("documentos",        JSON.stringify(_LISTA_DOCS_A_MODIFICAR_UI));
          datosUI.append("action",            'modificaInspeccion');

          for (var i = 0; i < filesUI.length; i++) {
            datosUI.append('file' + i, filesUI[i]);
          }

          $.ajax({
            type: "POST",
            url: "php/solicitudes_ocuv.php",
            contentType: false,
            processData: false,
            data: datosUI,
            datatype: 'json',
            success: function(response) {
              $("#btnUIAsignaMod").enable();
              var result = $.parseJSON(response);
              if (result.status === 'correcto') {
                $.confirm({
                  title: 'Correcto operación realizada',
                  content: 'La asignación fue realizada exitosamente',
                  type: 'green',
                  typeAnimated: true,
                  buttons: {
                    Cerrar: {
                      text: 'Entendido',
                      btnClass: 'btn-green',
                      action: function(){
                        $('#documentos_file_ui').fileinput('clear');
                        _LISTA_DOCS_A_MODIFICAR_UI = [];
                        getInformacionSolicitud();
                        planEvaluacion(SOLICITUD_SELECCIONADA);
                        solicitudServicio(SOLICITUD_SELECCIONADA);
                      }
                    }
                  }
                });
              }
            },
            beforeSend: function() {
              $("#btnUIAsignaMod").disabled();
            },
            error: function(jqxhr, status, errorGenerado) {

            }
          });
        }
      },
      cancelar: function () {

      }
    }
  });
}
/*******************************************************************************************************************************************
*******************************************************************************************************************************************/
function countChars(obj,label){
  var maxLength = 200;
  var strLength = obj.value.length;
  var charRemain = (maxLength - strLength);
  if(charRemain < 0){
    document.getElementById(`${label}`).innerHTML = '<span style="color: red;">Has superado el límite de '+maxLength+' caracteres.</span>';
  }else{
    document.getElementById(`${label}`).innerHTML = 'Quedan '+ charRemain+' caracteres.';
  }
}
/*******************************************************************************************************************************************
*******************************************************************************************************************************************/
function finalizaSolicitud(){
  var fechaSeleccionada   = '';
  var comentariosFinales  = '';
  $.confirm({
    title: '¡Finalizar Solicitud!',
    content: 'Indica si la solicitud sera finalizada como "Realizada" o "No realizada"',
    type: 'dark',
    typeAnimated: true,
    buttons: {
      si: {
        text: 'REALIZADA',
        btnClass: 'btn-green',
        action: function(){
          $("#modalSolicitud").modal("hide");
          $.confirm({
            title: 'Selecciona una fecha',
            content: '' +'<form action="" class="formName">' +
                         '<div class="form-group">' +
                         '<label>Selecciona la fecha real en que se llevo acabo la inspección</label>' +
                         '<input type="text" id="fechaPromp"  class="name form-control" required />' +
                         '</div>' +
                         '</form>',
            buttons: {
              formSubmit: {
                text: 'Aceptar',
                btnClass: 'btn-blue',
                action: function () {
                  fechaSeleccionada = this.$content.find('#fechaPromp').val();
                  $.confirm({
                    title: 'Ingresa un comentario final',
                    content: '' +'<form action="" class="formName">' +
                                 '<div class="form-group">' +
                                 '<label>Ingresa algún comentario sobre la inspección realizada</label>' +
                                 '<textarea class="form-control" rows="5" id="commentFinal"></textarea>' +
                                 '</div>' +
                                 '</form>',
                    buttons: {
                      formSubmit: {
                        text: 'Aceptar',
                        btnClass: 'btn-blue',
                        action: function () {
                          comentariosFinales= this.$content.find('#commentFinal').val();
                          if(!comentariosFinales){
                            $.alert('Ingresa un comentario para poder finalizar la solicitud');
                            return false;
                          }

                          var datos = {
                            "action"      : "finalizaRealizado",
                            "solicitud"   : SOLICITUD_SELECCIONADA,
                            "usuario"     : id_usuario,
                            "fecha"       : fechaSeleccionada,
                            "comentario"  : comentariosFinales,
                            "userNom"     : user
                          };

                          $.ajax({
                            type: "POST",
                            url: "php/solicitudes_ocuv.php",
                            contentType: "application/x-www-form-urlencoded;charset=UTF-8",
                            data: datos,
                            datatype: 'json',
                            success: function(data, textStatus, jqXHR) {
                              if (data != '') {
                                var result = JSON.parse(data);
                                if(result.status === "correcto"){
                                  $.alert({
                                    title: '¡Solicitud finalizada!',
                                    content: 'La solicitud fue finalizada correctamente',
                                    type: 'green',
                                    typeAnimated: true,
                                    icon: 'glyphicon glyphicon-ok',
                                  });
                                  getInformacionSolicitud();
                                }
                              }
                            },
                            beforeSend: function() {

                            },
                            error: function(jqxhr, status, errorGenerado) {
                              $.alert({
                                title: 'Ocurrio un error',
                                content: 'Ocurio un error al realizar esta operación',
                                type: 'orange',
                                typeAnimated: true,
                                icon: 'glyphicon glyphicon-warning-sign',
                              });
                            }
                          });
                        }
                      },
                      cancel: function () {
                        $("#modalSolicitud").modal("show");
                      },
                    },
                    onContentReady: function () {
                      var jc = this;
                      this.$content.find('form').on('submit', function (e) {
                        e.preventDefault();
                        jc.$$formSubmit.trigger('click');
                      });
                    }
                  });
                }
              },
              cancel: function () {
                $("#modalSolicitud").modal("show");
              },
            },
            onContentReady: function () {
              var jc = this;
              this.$content.find('form').on('submit', function (e) {
                e.preventDefault();
                jc.$$formSubmit.trigger('click');
              });
              $("#fechaPromp").val(getFechaActual(0));
              $("#fechaPromp").datepicker({
                changeMonth: true,
                changeYear: true,
                firstDay: 1,
                onClose: function(selectedDate) {

                }
              });
            }
          });

        }
      },no: {
        text: 'NO REALIZADA',
        btnClass: 'btn-danger',
        action: function(){
          $("#modalSolicitud").modal("hide");
          $.confirm({
            title: 'Ingresa un comentario final',
            content: '' +'<form action="" class="formName">' +
                         '<div class="form-group">' +
                         '<label>Ingresa algún comentario sobre la inspección realizada</label>' +
                         '<textarea class="form-control" rows="5" id="commentFinal"></textarea>' +
                         '</div>' +
                         '</form>',
            buttons: {
              formSubmit: {
                text: 'Aceptar',
                btnClass: 'btn-blue',
                action: function () {
                  comentariosFinales= this.$content.find('#commentFinal').val();
                  if(!comentariosFinales){
                    $.alert('Ingresa un comentario para poder finalizar la solicitud');
                    return false;
                  }

                  var datos = {
                    "action"      : "finalizaNoRealizado",
                    "solicitud"   : SOLICITUD_SELECCIONADA,
                    "usuario"     : id_usuario,
                    "comentario"  : comentariosFinales,
                    "userNom"     : user
                  };

                  $.ajax({
                    type: "POST",
                    url: "php/solicitudes_ocuv.php",
                    contentType: "application/x-www-form-urlencoded;charset=UTF-8",
                    data: datos,
                    datatype: 'json',
                    success: function(data, textStatus, jqXHR) {
                      if (data != '') {
                        var result = JSON.parse(data);
                        if(result.status === "correcto"){
                          $.alert({
                            title: '¡Solicitud finalizada!',
                            content: 'La solicitud fue finalizada correctamente',
                            type: 'green',
                            typeAnimated: true,
                            icon: 'glyphicon glyphicon-ok',
                          });
                          getInformacionSolicitud();
                        }
                      }
                    },
                    beforeSend: function() {

                    },
                    error: function(jqxhr, status, errorGenerado) {
                      $.alert({
                        title: 'Ocurrio un error',
                        content: 'Ocurio un error al realizar esta operación',
                        type: 'orange',
                        typeAnimated: true,
                        icon: 'glyphicon glyphicon-warning-sign',
                      });
                    }
                  });
                }
              },
              cancel: function () {
                $("#modalSolicitud").modal("show");
              },
            },
            onContentReady: function () {
              var jc = this;
              this.$content.find('form').on('submit', function (e) {
                e.preventDefault();
                jc.$$formSubmit.trigger('click');
              });
            }
          });
        }
      },
      cancelar: function () {

      }
    }
  });
}
/**************************************************************************************
**************************************************************************************/
function getFechaActual(dias) {
	var myDate = new Date();
	var dia = (myDate.getDate() + dias);
	var mes = (myDate.getMonth() + 1);
	var displayDate = myDate.getFullYear() + '-' + pad(mes, 2) + '-' + pad(dia, 2);
	return displayDate;
}
/**************************************************************************************
**************************************************************************************/
function pad(str, maxi) {
	"use strict";
	str = str.toString();
	return str.length < maxi ? pad("0" + str, maxi) : str;
}
/********************************************************************************************************************************
********************************************************************************************************************************/
function getComentariosReveca(){
  var datos = {
    "action"    : "getComentariosReveca",
    "solicitud" : SOLICITUD_SELECCIONADA
  };

  $.ajax({
    type: "POST",
    url: "php/solicitudes_ocuv.php",
    contentType: "application/x-www-form-urlencoded;charset=UTF-8",
    data: datos,
    dataType: "json",
    success: function(data, textStatus, jqXHR) {
      if (data.status === 'correcto') {

        contenido = '';
        var mensajes = data.mensajes;
        $("#chatReveca").empty();

        if (mensajes.length === 0) {
          contenido += `<div class="message text-only">
                          <p class="text" style="text-align: center;font-size: 15px;font-weight: 500;border: 1px solid whitesmoke;">
                            <span>SIN COMENTARIOS</span><br/>
                          </p>
                        </div>`;

        }
        for (var i = 0; i < mensajes.length; i++) {
          contenido += `<div class="message text-only">
                          <p class="text">
                          <span style="font-size: 12px;font-weight: 600;color: #1a237e;"> ${mensajes[i].verificador}</span><br/>
                          <span style="font-size: 11px;font-weight: 500;color: #3f51b5;"> ${mensajes[i].concepto} - ${mensajes[i].servicio}</span>
                          <br> ${mensajes[i].observaciones}
                          <br/>
                          <span><b>INSTALACIÓN:</b> ${mensajes[i].instalacion}</span><br/>
                          <span><b>ESTADO:</b> ${mensajes[i].area}</span><br/>
                          <span><b>HORA:</b> ${(mensajes[i].hora === null)?'---':mensajes[i].hora}</span><br/>
                          </p>

                        </div>
                        <p class="time">${mensajes[i].fecha}</p>

                        `;
        }
        $("#chatReveca").html(contenido);
      }
    },
    beforeSend: function(){},
    error: function(jqxhr, status, errorGenerado) {}
  });
}
/********************************************************************************************************************************
********************************************************************************************************************************/
function limpiarFiltros(){
  $("#filAsociado").val('');
  $("#filSolicitud").val('');
  $("#filPrioridad").val('0');
  $("#filEstatus").val('0');
  $("#filverificador").val('');
  $("#filActividad").val('0');
  $("#filFechaIni").val('2020-01-01');
  $("#filFechaFin").val(getFechaActual(0));
  $('#tablaSolicitudes').bootstrapTable('refresh');
}
/********************************************************************************************************************************
********************************************************************************************************************************/
function actualizaNotificacion(id){
  var datos = {
    "action"       : "notificacionVista",
    "notificacion" : id
  };
  $.ajax({
    type: "POST",
    url: "php/solicitudes_ocuv.php",
    contentType: "application/x-www-form-urlencoded;charset=UTF-8",
    data: datos,
    dataType: "json",
    success: function(data, textStatus, jqXHR) {
      app.loadNotificaciones();
      $('#tablaNotificaciones').bootstrapTable('refresh');
    },
    beforeSend: function(){

    },
    error: function(jqxhr, status, errorGenerado) {

    }
  });
}
function planEvaluacion(id){
  var GLOBAL_ALERT;
  const planEvaluacion = new Promise((resolve,reject) => {
    GLOBAL_ALERT = $.dialog({
      icon: 'fa fa-spinner fa-spin',
      title: 'Cargando....',
      content: 'Espere, generando plan de evaluación'
    });
    $.get(`../../../publicFolder/ocui/getDatosPlanE.php?s=${id}`,function() {
      resolve("Plan de evaluación generado");
    });
  });
  planEvaluacion.then((successMessage) => {
    GLOBAL_ALERT.close();
    $.alert({
      title: 'Plan de evaluación listo',
      content: 'El plan de evaluación fue generado exitosamente',
      type: 'green',
      typeAnimated: true,
      icon: 'glyphicon glyphicon-ok',
    });
  });
}

function solicitudServicio(id){

  var GLOBAL_ALERT;
  const planEvaluacion = new Promise((resolve,reject) => {
    GLOBAL_ALERT = $.dialog({
      icon: 'fa fa-spinner fa-spin',
      title: 'Cargando....',
      content: 'Espere, generando plan de evaluación'
    });
    $.get(`../../../publicFolder/ocui/getDatosSolicitudS.php?s=${id}`,function() {
      resolve("solicitud de servicio generado");
    });
  });
  planEvaluacion.then((successMessage) => {
    GLOBAL_ALERT.close();
    $.alert({
      title: 'Solicitud de servicio listo',
      content: 'La solicitud de servicio fue generado exitosamente',
      type: 'green',
      typeAnimated: true,
      icon: 'glyphicon glyphicon-ok',
    });
  });
}

function consultaHistorial(SOLICITUD){
  $("#lu_movs").empty();
  var datos = {
    "solicitud" : SOLICITUD
  };

  $.ajax({
    type: "GET",
    url: "php/bitacora.php",
    contentType: "application/x-www-form-urlencoded;charset=UTF-8",
    data: datos,
    dataType: "json",
    success: function(data, textStatus, jqXHR) {
      var movs = data.solicitud;
      var colores = ['red','green','blue','orange','yellow'];
      var colores2 = ['colorRed','colorGreen','colorBlue','colorOrange','colorYellow'];
      for (var i = 0; i < movs.length; i++) {
        $("#lu_movs").append(`<li class="one ${colores[i]}">
                                  <span class="task-title">${movs[i].movimiento}</span>
                                  <span class="task-time">${movs[i].fecha}</span>
                                  <span class="task-cat ${colores2[i]}">${movs[i].persona}</span>
                                 </li>`);
      }
    },
    beforeSend: function(){},
    error: function(jqxhr, status, errorGenerado) {}
  });
}

function loadInstalaciones(tipo_consulta, instalacionesSelect){

  var datos = new FormData();
  datos.append("action", "suggestInstalacionesTodas");
  datos.append("cliente",  $("#inp_solicitante").val());
  datos.append("maquila",  $("#imp_maquila").val());

  $.ajax({
    url: "php/solicitudes_ocuv.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (data) {

      if (data.status == "correcto") {
        
        var ins_select = $('#imp_domicilio_ins');
        ins_select.empty();
        ins_select.append("<option disabled selected value =''> SELECCIONA </option>");
        if (data.instalaciones.length > 0) {
          for (var i = 0; i < data.instalaciones.length; i++) {
            ins_select.append(`<option value="${data.instalaciones[i].id_tabla}">${data.instalaciones[i].domicilio}</option>`);
          }
        }

        if(tipo_consulta == 1){

          for (var x = 0; x < instalacionesSelect.length; x++) {
            var itemIns = instalacionesSelect[x];    
            $(`#imp_domicilio_ins option[value='${itemIns.id_tabla}']`).prop("disabled",true);
          }

          
        }

      }
    }
  });
}

$("#addIns").click(function (event) {
  event.preventDefault();
  var valor = $("#imp_domicilio_ins").val();
  var instalacion = $(`#imp_domicilio_ins option[value='${valor}']`).text();
  var observaciones = $(`#imp_observaciones_ins`).val();
  var rows = $('#tabla_ins tr').length;
  var count = 0;
  if (instalacion.trim() != '') {
      $("select#imp_domicilio_ins").prop('selectedIndex', 0);
      $(`#imp_observaciones_ins`).val('');
      $(`#imp_domicilio_ins option[value='${valor}']`).prop("disabled",true);
      $('#tabla_ins').append(`<tr id="ins-${$(`#imp_domicilio_ins option[value='${valor}']`).val()}" style="background-color: #fff"><td width="50%">${instalacion}</td><td width="20%" id="observaciones_ins">${observaciones}</td><td width="10%" align="center"><img class="delete" src="images/delete.svg"  height="15" width="15" style="cursor:pointer"></td></tr>`);
  } else {
    $("#imp_domicilio_ins").focus();
    $.confirm({
      title: 'Instalación',
      content: 'Para continuar es necesario seleccionar la instalación',
      type: 'red',
      typeAnimated: true,
      buttons: {
        tryAgain: {
        text: 'Entendido',
        btnClass: 'btn-red',
        action: function(){}
      }
    }});
  }
});

/*Funcion para remover algun registro de la tabla de analisis*/
$("#tabla_ins").on('click', '.delete', function () {
  var id_tabla = $(this).parents('tr').attr("id");
  id_tabla = id_tabla.split("ins-");
  $(`#imp_domicilio_ins option[value='${id_tabla[1]}']`).prop("disabled",false);
  $(this).parents('tr').remove();
});

function getIdInstalaciones(){
  
  var instalacionesArr = [];
  var instalacionesObj = {};

  $("#tabla_ins tbody tr").each(function (index) {

      var id_instalacion = $(this).closest('tr').attr('id');
      id_instalacion = id_instalacion.split("ins-");
      var observaciones =  $(this).children("td").eq(1).text();

      instalacionesObj = {
        id                : id_instalacion[1],
        observaciones     : observaciones,
      }
    
      instalacionesArr = [
        ...instalacionesArr,
        instalacionesObj
      ]
    
      instalacionesObj = {};
  });

  return instalacionesArr;
}

function addInstalacion(solicitante,maquila){
  loadInstalacionesOld(solicitante,maquila);
  $("#mdlInserInt").modal("show");
}

function loadInstalacionesOld(solicitante,maquila){

  var datos = new FormData();
  datos.append("action", "suggestInstalacionesTodas");
  datos.append("cliente",  solicitante);
  datos.append("maquila",  maquila );

  $.ajax({
    url: "php/solicitudes_ocuv.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (data) {

      if (data.status == "correcto") {

        $("#tabla_ins_add").empty();

        var contenido = "";

        contenido += ``;

        if (data.instalaciones.length > 0) {
          for (var i = 0; i < data.instalaciones.length; i++) {
            $('#tabla_ins_add').append(`<tr id="ins-${data.instalaciones[i].id_tabla}" style="background-color: #fff">
            <td width="70%">${data.instalaciones[i].domicilio}</td>
            <td width="10%">
              <div class="checkbox">
                <label style="font-size: 12px;"><input type="checkbox" id="chk_ins_${data.instalaciones[i].id_tabla}" name="chk_ins_${data.instalaciones[i].id_tabla}" value='${data.instalaciones[i].id_tabla}' class="instalacionesCheck" /></label>
              </div>
            </td></tr>`);
          }
        }
        
      }
    }
  });
}


1

function confirmaRegistroIns() {
  const instalacionesArr = Array.from(document.querySelectorAll(".instalacionesCheck:checked")).map((checkbox) => {
    return {
      id: checkbox.value,
    };
  });

  if (instalacionesArr.length === 0) {
    $.confirm({
      title: 'No has seleccionado ninguna instalación',
      content: 'Para continuar es necesario al menos seleccionar una instalación',
      type: 'red',
      typeAnimated: true,
      buttons: {
        tryAgain: {
          text: 'Entendido',
          btnClass: 'btn-red',
          action: function() {}
        }
      }
    });

    return;
  }

  var datos = new FormData();
  datos.append("action", "addInstalaciones");
  datos.append("instalaciones", JSON.stringify(instalacionesArr));
  datos.append("solicitud", SOLICITUD_SELECCIONADA);
  datos.append("user", id_usuario);
  
  

  $.confirm({
    title: '¡Agregar Instalaciones!',
    content: '¿Estás seguro de querer registrar las instalaciones en la solicitud?',
    type: 'dark',
    typeAnimated: true,
    buttons: {
      tryAgain: {
        text: 'SI,REGISTRAR',
        btnClass: 'btn-dark',
        action: function() {
          $.ajax({
            type: "POST",
            url: "php/solicitudes_ocuv.php",
            contentType: false,
            processData: false,
            data: datos,
            datatype: 'json',
            success: function(response) {
              var result = $.parseJSON(response);
              if (result.status === 'correcto') {
                    
                $("#mdlInserInt").modal("hide");
                getInformacionSolicitud();    

              } else {
                  $.confirm({
                    title: 'Ocurrió un error',
                    content: result.msj,
                    type: 'red',
                    typeAnimated: true,
                    buttons: {
                      Cerrar: {
                        text: 'Entendido',
                        btnClass: 'btn-red',
                        action: function(){
                        }
                      }
                    }
                  });
              }
            },
            beforeSend: function() {

            },
            error: function(jqxhr, status, errorGenerado) {

            }
          });
        }
      },
      cancelar: function() {

      }
    }
  });
}

function reporteVigencias() {

  $.confirm({
    title: '¡Reporte de vigencias!',
    content: '' +
    '<form action="" class="formName">' +
    '<div class="form-group">' +
    '<label>Fecha de Inicio</label>' +
    '<input type="date" placeholder="Fecha de Inicio" class="fechaIni form-control" required />' +
    '</div>' +
    '<div class="form-group">' +
    '<label>Fecha de Fin</label>' +
    '<input type="date" placeholder="Fecha de Fin" class="fechaFin form-control" required />' +
    '</div>' +
    '</form>',
    type: 'dark',
    typeAnimated: true,
    buttons: {
        tryAgain: {
            text: 'SI, GENERAR',
            btnClass: 'btn-dark',
            action: function() {
                var fechaIni = this.$content.find('.fechaIni').val();
                var fechaFin = this.$content.find('.fechaFin').val();
                
                if (!fechaIni || !fechaFin) {
                    $.alert('Debe ingresar ambas fechas');
                    return false;
                }
                
                if (new Date(fechaIni) > new Date(fechaFin)) {
                    $.alert('La fecha de inicio debe ser menor o igual a la fecha de fin');
                    return false;
                }

                generarReporte(fechaIni, fechaFin);
            }
        },
        cancelar: function() {
            // Acción al cancelar
        }
    }
});

}

function generarReporte(inicio, final) {
  var d_s = getParamUrl("d_s");
  var datos = `php/reporteVigencias.php?ini=${inicio}&fin=${final}&d_s=${d_s}`;
  var win = window.open(datos, '_blank');
  win.focus();

}

function getParamUrl(param) {
  var searchParams = new URLSearchParams(window.location.search);
  if (searchParams.has(param)) {
      return searchParams.get(param)
  } else {
      return '';
  }
}
