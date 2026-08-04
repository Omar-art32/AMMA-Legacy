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

//Variables globales
var solicitudActual;
var cliente;
var pedirPass;
var contrato = 0;
var faltaFactura = 0;
var hologramas;
var moral = 0;
var casoDocumento = 0;
var tipo_asignacion = 0;

var _INSCRIPCIONES = 0;
var _TRASLADOS = 0;
var _MADURAION = 0;
var _VARIOS = 0;
var _AJUSTE = 0;
var _CERTIFICADOS = 0;
var _ENVASADO = 0;
var _CAMBIO = 0;
var numero = ""; //Moises
var correoMensaje = "";
var documento;
var lista_documentos;
var persona_documento;
var marcas_lista;
var documento_indice;
var solicitante;
var status_prospecto;
var status_solicitud;
var prospecto;
var listaInstalaciones;
var listaMarcas;
var lista_telefonos;
var instalaciones_lista; //Moises
var estado;
var target = '#inscripciones';
var tipoDocumento = 0;
var solicitud_ejecutiva = 0;
var id_asignacion = 0;
var mostrar_opciones = 0;

$(document).ready(function() {

    if (clvuser == 1 || clvuser == 21) {
        $("#btnAsignarSolocitudInscripcion").removeClass("collapse");
    }

    $("#dtmFechaInicioRelacion").datetimepicker({
        viewMode: "years",
        format: "DD/MM/YYYY"
    });

    $("#dtmFechaFinRelacion").datetimepicker({
        viewMode: "years",
        format: "DD/MM/YYYY"
    });

    $("#dtmFechaInicio").datetimepicker({
        viewMode: "years",
        format: "DD/MM/YYYY"
    });

    $("#dtmFechaFin").datetimepicker({
        viewMode: "years",
        format: "DD/MM/YYYY"
    });

    $("#dtmProrroga").datetimepicker({
        viewMode: "years",
        format: "DD/MM/YYYY"
    });

    $('#btnProrroga').click(function() {
        $('#divProrroga').removeClass('collapse');
    });

    $('#btnAceptarProrroga').click(function() {
        if ($("#txtProrroga").val().trim() != "") {
            AsignarProrroga();
        } else {
            alert("No se capturo la fecha!!!");
        }
    });

    $("#txtCteProv").typeahead({
        source: function(query, process) {
            var datos = {
                "strBusqueda": query
            };
            datos = $.param(datos);
            $.ajax({
                type: "POST",
                url: "php/typeheadCte.php",
                contentType: "application/x-www-form-urlencoded;charset=UTF-8",
                data: datos,
                dataType: "json",
                success: function(data, textStatus, jqXHR) {
                    //alert(JSON.stringify(data));
                    if (data.status === "OK") {
                        process(data.suggest);
                    } else {
                        alert("Ha ocurrido un error al cargar el catalogo de asentamientos: " + data.msj);
                    }
                },
                error: function(jqxhr, status, errorGenerado) {
                    alert("Ha ocurrido un error al cargar el catalogo de asentamientos: " + jqxhr.responseText + errorGenerado);
                }
            });
        },
        updater: function(item) {
            obtenerMarcasRel(item);
            return item;
        }
    });

    function obtenerMarcasRel(item) {
        $('#divAgregarRe').removeClass('collapse');
        /*$.ajax({
        	url: "php/solicitudes.php",
        	method: "POST",
        	data: {
        		action: "obtenerMarcasRel",
        		cliente: item,
        		"id": id_s,
        	},
        	dataType: "json",
        	success: function (data) {
        		if (data.status) {
        			if (data.marcas.length==0) {
        				alert("El asociado "+item+" no tiene marcas")
        				$("#txtCteProv").val('');
        				$('#divAgregarRe').addClass('collapse');
        			}else{
        				$('#divAgregarRe').removeClass('collapse');
        			}
        		}
        	}
        });*/
    }

    //notificaciones(0); //moises

    //***********************Atender Solicitud****************//Moises
    $('#btnPermisoAtender').click(function() {
        $.ajax({
            url: "php/estados_solicitudes.php",
            method: "POST",
            data: {
                caso: "atendida",
                status: "",
                solicitud: solicitudActual,
                "id": id_s,
            },
            dataType: "json",
            success: function(data) {
                if (data.status) {
                    $("#modalAtendida").modal('hide');
                    $("#modalStatus").modal('show');
                    notificaciones(0);
                    $('#abrirModalMarcar').addClass('collapse');
                }
            }
        });
    });

    //***********************Atender Solicitud****************//
    $('#btnPermisoAtender').click(function() {
        $.ajax({
            url: "php/estados_solicitudes.php",
            method: "POST",
            data: {
                caso: "atendida",
                status: "",
                solicitud: solicitudActual,
                "id": id_s,
            },
            dataType: "json",
            success: function(data) {
                if (data.status) {
                    $("#modalAtendida").modal('hide');
                    $("#modalStatus").modal('show');
                    notificaciones(0);
                    $('#abrirModalMarcar').addClass('collapse');
                }
            }
        });
    });

    //***********************comentario interno Solicitud****************//
    $('#btnAgregarComentarioUAA').click(function() {
        $.ajax({
            url: "php/solicitudes.php",
            method: "POST",
            data: {
                action: "mensajeInscripcion",
                solicitud: solicitudActual,
                "id": id_s,
                comentario: $('#txtComentarioInscripcion').val()
            },
            dataType: "json",
            success: function(data) {
                if (data.status) {
                    alert("Comentario Guardado");
                }
            }
        });
    });

    //***********************Permitir edicion de datos prospectos****************//Moises
    $('#btnBloquearEdicion').click(function() {
        $.ajax({
            url: "php/edicion_prospectos.php",
            method: "POST",
            data: {
                caso: "1",
                folio: numero
            },
            dataType: "json",
            success: function(data) {
                if (data.status) {
                    alert("Edicion Activada");
                    $('#btnBloquearEdicion').addClass("disabled");
                } else {
                    alert("Error");
                }
            }
        });
    });

    //***********************Asignar revisor****************
    $('#btnAgregarEjecutiva').click(function() {
        $.ajax({
            url: "php/solicitudes.php",
            method: "POST",
            data: {
                "action": "asignarEjecutiva",
                "user": clvuser,
                "ejecutiva": $("#seEjecutiva").val(),
                "solicitud": solicitud_ejecutiva,
                tipo_asignacion: tipo_asignacion,
                id_asignacion: id_asignacion
            },
            dataType: "json",
            success: function(data) {
                if (data.status) {
                    alert("Solicitud Asignada");
                    $('#ContenidoAsignarSolicitud').load('php/tablas/tabla_sin_asignar.php');
                    $('#ContenidoSinAceptar').load('php/tablas/tabla_sin_aceptar.php');
                    $('#ContenidoAceptadas').load('php/tablas/tabla_en_revision.php');
                    $('#ContenidoFinalizadas').load('php/tablas/tabla_finalizadas.php');
                    $("#modalAsignarEjecutiva").modal('hide');
                } else {
                    alert("Error");
                }
            }
        });
    });

    //***********************Actualizar al ver solicitudes incompletas****************//Moises
    $('.actualizar').click(function() {
        cargarSolicitudes();
        $("#modalCargarSol").modal('show');
    });

    //***********************continuar sin relacion****************//Moises
    $('#btnContinuarEjecucion').click(function() {
        $("#modalContinuarRelacion").modal("hide");
        actualizarEstadoSolicitud2();
    });

    //***********************Ver notificaciones****************
    $('#verNotificaciones').click(function() {
        switch (target) {
            case '#edicion':
                notificaciones_Edicion(1);
                break;
            case '#inscripciones':
                notificaciones(1);
                break;
            default:
                break;
        }
    });

    //***********************Aceptar Solicitud****************
    $('#btnConfirmarRevision').click(function() {
        $.ajax({
            url: "php/solicitudes.php",
            method: "POST",
            data: {
                action: "aceptar_solicitud",
                user: clvuser,
                solicitud: solicitud_ejecutiva,
                id_asignacion: id_asignacion
            },
            dataType: "json",
            success: function(data) {
                if (data.status) {
                    alert("Solicitud aceptada");
                    contenidoMisSolicitudes();
                    $("#modalConfirmarRevision").modal('hide');
                } else {
                    alert("Error");
                }
            }
        });
    });

    //***********************Ver Solicitudes sin Asignar****************
    $('#btnAsignarSolocitudInscripcion').click(function() {
        notificaciones_sin_asignar();
    });

    //***********************Ver mis Solicitudes****************
    $('#btnMisSolicitudes').click(function() {
        notificaciones_mis_solicitudes();
    });

    $('#verHistorialNotificaciones').click(function() {
        switch (target) {
            case '#edicion':
                $("#modalHistorialNotificaciones").modal('show');
                $('#divHistorialNotificaciones').load('php/tabla_historial_notificaciones.php?c=2');
                break;
            case '#inscripciones':
                $("#modalHistorialNotificaciones").modal('show');
                $('#divHistorialNotificaciones').load('php/tabla_historial_notificaciones.php?c=1');
                break;
            default:
                break;
        }
    });

    $('#tipo_vigencia_1').click(function() {
        $('#dvVigFec').removeClass('collapse');
        $('#divFinRelac').removeClass('collapse');
    });
    $('#tipo_vigencia_2').click(function() {
        $('#dvVigFec').removeClass('collapse');
        $('#divFinRelac').addClass('collapse');
    });
    //***********************Abrir documento del prospecto***************//Moises
    $('#btnVerDocumentoProspecto').click(function() {
        var win = window.open('php/descargar_documento.php?id=' + documento, '_blank');
        win.focus();
    });

    //***********************Aceptar documento del prospecto***************//Moises
    $('#btnAceptarDocumentoProspecto').click(function() {
        $("#divDatosVigencia").removeClass('collapse');
        $('#divRechazo').addClass('collapse');
        if (tipoDocumento == 19 || tipoDocumento == 18) {
            $("#divRelacion").removeClass('collapse');
            $("#labelVigencia").removeClass('collapse');
        }
    });

    //***********************Aceptar documento del prospecto***************//Moises
    $('#btnAceptarDocumentoContinuar').click(function() {
        switch (casoDocumento) {
            case '1':
                verificacionDocumento(3);
                break;
            case '2':
                aceptarDocumentoCambio(3);
                break;
            default:
                alert("error");
                break;
        }
    });

    //***********************Rechazar documento del prospecto***************//Moises
    $('#btnRechazarDocumento').click(function() {
        $('#divRechazo').removeClass('collapse');
        $("#divDatosVigencia").addClass('collapse');
    });

    //***********************Rechazar documento del prospecto***************//Moises
    $('#btnRechazarDocumentoProspecto').click(function() {
        switch (casoDocumento) {
            case '1':
                verificacionDocumento(4);
                break;
            case '2':
                aceptarDocumentoCambio(4);
                break;
            default:
                alert("error");
                break;
        }
    });

    //***********************validar datos generales***************//Moises
    $('#btnValidarGenerales').click(function() {
        status_prospecto = (status_prospecto == 0) ? "1" : "0";
        $.ajax({
            url: "php/estados_solicitudes.php",
            method: "POST",
            data: {
                caso: "generales",
                status: status_prospecto,
                prospecto: prospecto
            },
            dataType: "json",
            success: function(data) {
                if (data.status) {
                    if (status_prospecto == 1) {
                        $('#btnValidarGenerales').html("<span class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\"></span> &nbspPermitir editar datos");
                        $('#btnValidarGenerales').removeClass("btn-success");
                        $('#btnValidarGenerales').addClass("btn-info");
                    } else {
                        $('#btnValidarGenerales').html("<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span> &nbspValidar datos generales");
                        $('#btnValidarGenerales').removeClass("btn-info");
                        $('#btnValidarGenerales').addClass("btn-success");
                    }
                    //$("#lblStatus").text("Cambio realizado");
                    $("#modalStatus").modal('show');
                } else {
                    alert("error");
                }
            }
        });
    });


    $('#btnAsignarNueva').click(function() {
        mostrarAsignarSolicitud(solicitudActual, '1', '0');
    });

    //***********************Abandonar expediente***************//Moises
    $('#btnAbandonado').click(function() {
        if (status_solicitud == 0) {
            $('#tituloAbandonada').text("La solicitud se cambiara a Abandonada, ¿Desea continuar?");
        } else {
            $('#tituloAbandonada').text("La solicitud se reactivara, ¿Desea continuar?");
        }
        $("#modalAbandonada").modal('show');
    });


    $('#ckbDespues').click(function() {
        var tipo = $("#ckbDespues").is(":checked");
        if (tipo) {
            $('#divDespues').addClass("collapse");
        } else {
            $('#divDespues').removeClass("collapse");
        }
    });

    //***********************Abandonar expediente***************//Moises
    $('#btnAbandonar').click(function() {
        status_solicitud = (status_solicitud == 1) ? 0 : 1;
        $.ajax({
            url: "php/estados_solicitudes.php",
            method: "POST",
            data: {
                caso: "abandonado",
                status: status_solicitud,
                solicitud: solicitudActual
            },
            dataType: "json",
            success: function(data) {
                if (data.status) {
                    cargarSolicitudes();
                    if (status_solicitud == 1) {
                        $('#btnAbandonado').html("<span class=\"glyphicon glyphicon-ok-circle\" aria-hidden=\"true\"></span> &nbspReactivar Expediente");
                        $('#btnAbandonado').removeClass("btn-danger");
                        $('#btnAbandonado').addClass("btn-success");
                        $('#expAbandonado').removeClass("collapse");
                        $('#edoSolicitud').html("Estado actual de la Solicitud: <b> EXPEDIENTE ABANDONADO");
                    } else {
                        $('#btnAbandonado').html("<span class=\"glyphicon glyphicon-remove-circle\" aria-hidden=\"true\" id=\"icoAbandonado\"></span> &nbspExpediente Abandonado");
                        $('#btnAbandonado').removeClass("btn-success");
                        $('#btnAbandonado').addClass("btn-danger");
                        $('#expAbandonado').addClass("collapse");
                        var estadoL = ""
                        switch (estado) {
                            case 1:
                                estadoL = "EN REVISION";
                                break;
                            case 2:
                                estadoL = "PENDIENTE DE PAGO";
                                break;
                            case 3:
                                estadoL = "EN AUTORIZACION";
                                break;
                            case 4:
                                estadoL = "EN EJECUCION";
                                break;
                            case 5:
                                estadoL = "TERMINADA";
                                break;
                            case 6:
                                estadoL = "RECHAZADA";
                                break;
                            case 7:
                                estadoL = "RECIBIDA";
                                break;
                            case 8:
                                estadoL = "PROGRAMANDO VISITA";
                                break;
                            case 9:
                                estadoL = "VERIFICANDO";
                                break;
                            case 10:
                                estadoL = "VALIDANDO";
                                break;
                            case 11:
                                estadoL = "VISITA AGENDADA";
                                break;
                            case 12:
                                estadoL = "REVISI&OacuteN POR EL CLIENTE";
                                break;
                            case 13:
                                estadoL = "IMPRIMIENDO";
                                break;
                            case 14:
                                estadoL = "RESPUESTA DEL CLIENTE";
                                break;
                            case 15:
                                estadoL = "AUTORIZADA";
                                break;
                            case 16:
                                estadoL = "CANCELADA";
                                break;
                            case 17:
                                estadoL = "ACTIVIDAD REALIZADA";
                                break;
                            case 18:
                                estadoL = "ACTIVIDAD NO REALIZADA";
                                break;
                            case 19:
                                estadoL = "REPROGRAMANDO VISITA";
                                break;
                            case 20:
                                estadoL = "TRASLADO REALIZADO";
                                break;
                            case 21:
                                estadoL = "TRASLADO NO REALIZADO";
                                break;
                            case 23:
                                estadoL = "SEGUIMIENTO CON OTRO FOLIO";
                                break;
                            case 24:
                                estadoL = "REGISTRO INCOMPLETO";
                                break; //Cuando el prospecto no termino su registro//Moises
                        }
                        //console.log(status_solicitud);
                        $('#edoSolicitud').html("Estado actual de la Solicitud: <b>" + estadoL);
                    }
                    enviarMensajeAbandono(status_solicitud);
                    //$("#lblStatus").text("Cambio realizado");
                    $("#modalAbandonada").modal('hide');
                    $("#modalStatus").modal('show');
                    //status_solicitud=(status_solicitud==1)?"0":"1";
                } else {
                    alert("error");
                }
            }
        });
    });

    //***********************Cambiar estado de documento***************//Moises
    function verificacionDocumento(status) {
        var mensaje = $('#txtObservacionesRechazo').val().trim();
        var despues = $("#ckbDespues").is(":checked");
        var tipo_vigencia_1 = $("#tipo_vigencia_1").is(":checked");
        var tipo_vigencia_2 = $("#tipo_vigencia_2").is(":checked");

        despues = (despues) ? "1" : "0";
        tipo_vigencia_1 = (tipo_vigencia_1) ? "1" : "0";
        tipo_vigencia_2 = (tipo_vigencia_2) ? "1" : "0";
        if ((tipoDocumento == 18 || tipoDocumento == 19) && $("#txtCteProv").val().trim() == "" && despues == 0 && status == 3) {
            alert("Debes asignar un cliente para relación");
        } else {
            $.ajax({
                url: "php/estados_solicitudes.php",
                method: "POST",
                data: {
                    "id": id_s,
                    caso: "documentos",
                    documento: documento,
                    status: status,
                    prospecto: prospecto,
                    mensaje: mensaje,
                    inicio: $("#txtInicio").val(),
                    fin: $("#txtFin").val(),
                    tipoDocumento: tipoDocumento,
                    despues: despues,
                    txtCteProv: $("#txtCteProv").val().trim(),
                    tipo_vigencia_1: tipo_vigencia_1,
                    tipo_vigencia_2: tipo_vigencia_2,
                    txtVigIni: $("#txtInicio").val().trim(),
                    txtVigFin: $("#txtFin").val().trim(),
                    txtObsMaq: $("#txtObsMaq").val().trim()
                },
                dataType: "json",
                success: function(data) {
                    if (data.status) {
                        if (status == 3 && (tipoDocumento == 18 || tipoDocumento == 19)) {
                            lista_documentos[documento_indice].nom_maq = "<b>Relacion con: " + $("#txtCteProv").val().trim() + "</b>";
                        }
                        lista_documentos[documento_indice].status = status;
                        $("#modalStatus").modal('show');
                        $("#modalAccionDocs").modal('hide');
                        $("#divDatosVigencia").addClass('collapse');
                        Listar_Documentos(lista_documentos, persona_documento, marcas_lista, instalaciones_lista);
                    } else {
                        alert("error");
                    }
                }
            });
        }
    }

    function AsignarProrroga() {
        $("#divCargandoCambio").show();
        $.ajax({
            url: "php/estados_solicitudes.php",
            method: "POST",
            data: {
                id: id_s,
                caso: "documentosProrroga",
                documento: documento,
                status: 5,
                prorroga: $("#txtProrroga").val(),
                descripcion: $("#txtObservacionesProrroga").val()
            },
            dataType: "json",
            success: function(data) {
                if (data.status) {
                    lista_documentos[documento_indice].status = 5;
                    $("#modalStatus").modal('show');
                    $("#modalAccionDocs").modal('hide');
                    $("#divDatosVigencia").addClass('collapse');
                    $("#txtObservacionesProrroga").val("");
                    Listar_Documentos(lista_documentos, persona_documento, marcas_lista, instalaciones_lista);
                } else {
                    alert("Error");
                    $("#divCargandoCambio").hide();
                }
            }
        });
    }

    // **********************************EVENTO CHANGE PARA LAS SOLICITUDES ***************************************************
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        target = $(e.target).attr("href");
        switch (target) {
            case '#traslados':
                if (_TRASLADOS == 0) {
                    _TRASLADOS = 1;
                    cargarSolicitudesTr();
                }
                $('#lblNumNot').text("0");
                break;
            case '#maduracion':
                if (_MADURAION == 0) {
                    _MADURAION = 1;
                    cargarSolicitudesMad();
                }
                $('#lblNumNot').text("0");
                break;
            case '#ajuste':
                if (_AJUSTE == 0) {
                    _AJUSTE = 1;
                    cargarSolicitudesAMM();
                }
                $('#lblNumNot').text("0");
                break;
            case '#varios':
                if (_VARIOS == 0) {
                    _VARIOS = 1;
                    cargarSolicitudesVAR();
                }
                $('#lblNumNot').text("0");
                break;
            case '#exportacion':
                //console.log('estoy aqui');
                if (_CERTIFICADOS == 0) {
                    _CERTIFICADOS = 1;
                    cargarSolicitudesExp();
                }
                $('#lblNumNot').text("0");
                break;
            case '#envasado':
                if (_ENVASADO == 0) {
                    _ENVASADO = 1;
                    cargarSolicitudesENV();
                }
                $('#lblNumNot').text("0");
                break;
            case '#edicion':
                notificaciones_Edicion(0);
                if (_CAMBIO == 0) {
                    _CAMBIO = 1;
                    cargarSolicitudesCambio();
                }
                break;
            case '#inscripciones':
                cargarSolicitudes();
                notificaciones(0);
                break;
            default:

        }
    });
    // **********************************EVENTO CHANGE PARA LAS SOLICITUDES ***************************************************

    $("#tblSolicitudes tfoot th").each(function() {
        var title = $(this).text();
        $(this).html("<input type=\"text\" style=\"width:100%\"/>");
    });


    $("#tblSolicitudes").DataTable({
        pageLength: 10,
        ordering: false,
        language: {
            url: "data_tables/Spanish.json"
        },

        columns: [
            { width: "10%", data: "id", visible: false },
            { width: "10%", data: "folio" },
            { width: "10%", data: "fecha" },
            { width: "10%", data: "ultimaModificacion" },
            { width: "10%", data: "tipoLetra" },
            { width: "10%", data: "estadoLetra" },
            { width: "14%", data: "no_cliente" },
            { width: "50%", data: "cliente" },
            { width: "50%", data: "actividades" },
            { width: "10%", data: "marcas", visible: false },
        ],
    });
    // $("#tblSolicitudes").on("draw.dt", function() {
    $("#tblSolicitudes tbody").on('click', 'tr', function() {
        //table = $('#tblSolicitudes').DataTable();
        var data = $('#tblSolicitudes').DataTable().row(this).data();
        var datos = {
            "action": "obtenerSolicitud",
            "idSolicitud": idSolicitud
        };
        var idSolicitud = data['id'];
        var tipo = data['tipo'];
        estado = data['estado'];
        $(document).data("idSolicitud", idSolicitud); //Guarda el id de la solicitud actual

        switch (tipo) {
            case 1:
                mostrarSolicitudAsociado(idSolicitud, tipo, estado, datos);
                break;
            case 5:
                cliente = data['no_cliente'];
                mostrarSolicitudCertificado(idSolicitud, tipo, estado, datos, cliente);
                break;
            case 6:
                cliente = data['no_cliente'];
                mostrarSolicitudCertificado(idSolicitud, tipo, estado, datos, cliente);
                break;
            case 10:
                $("#idSol").val(idSolicitud);
                var cargo = getCargo();
                getUVRegistro(idSolicitud, cargo);
                mostrarSolicitudAMM(idSolicitud);
                break;
            case 11:
                mostrarSolicitudTraslado(idSolicitud);
                break;
            case 12:
                $("#idSol").val(idSolicitud);
                var cargo = getCargo();
                getUVRegistro(idSolicitud, cargo);
                mostrarSolicitudVarios(idSolicitud);
                break;
            case 13:
                mostrarSolicitudILPM(idSolicitud);
                break;

        }

        /*/mostrarSolicitudAsociado(idSolicitud, tipo, estado, datos);
			if (tipo==1)
				mostrarSolicitudAsociado(idSolicitud, tipo, estado, datos);
		  /*MOSTRAR SOLICITUD DE AJUSTE DE GRADO Y MUESTREO
		   else
		   	if (tipo==10){
		   		$("#idSol").val(idSolicitud);
			    var cargo = getCargo();
			    getUVRegistro(idSolicitud,cargo);
				mostrarSolicitudAMM(idSolicitud);

		   	}
		   	/*FIN MOSTRAR SOLICITUD DE AJUSTE DE GRADO Y MUESTREO
			else if(tipo==11){
				mostrarSolicitudTraslado(idSolicitud);
			}
			/*SOLICITUD VARIOS
			else if (tipo==12){
		   		$("#idSol").val(idSolicitud);
			    var cargo = getCargo();
			    getUVRegistro(idSolicitud,cargo);
				mostrarSolicitudVarios(idSolicitud);

		   	}
			/*FIN VARIOS

			else if(tipo==13){
				mostrarSolicitudILPM(idSolicitud);

			}else{
				cliente = data['no_cliente'];
				mostrarSolicitudCertificado(idSolicitud, tipo, estado, datos,cliente);
			}*/

    });
    //});


    $("#modalAsociado").on("shown.bs.modal", function(e) {

        $(".modal .modal-body").css("overflow-y", "auto");
        $(".modal .modal-body").css("max-height", $(window).height() * 0.75);

    });

    $("#modalNotificaciones").on("shown.bs.modal", function(e) {

        $(".modal .modal-body").css("overflow-y", "auto");
        $(".modal .modal-body").css("max-height", $(window).height() * 0.75);

    });

    $("#modalHistorialNotificaciones").on("shown.bs.modal", function(e) {

        $(".modal .modal-body").css("overflow-y", "auto");
        $(".modal .modal-body").css("max-height", $(window).height() * 0.75);

    });

    $("#modalAccionDocs").on("shown.bs.modal", function(e) {

        $(".modal .modal-body").css("overflow-y", "auto");
        $(".modal .modal-body").css("max-height", $(window).height() * 0.75);

    });

    $("#modalListaSolicitudSinAsignar").on("shown.bs.modal", function(e) {

        $(".modal .modal-body").css("overflow-y", "auto");
        $(".modal .modal-body").css("max-height", $(window).height() * 0.75);

    });

    $("#modalMisSolicitudes").on("shown.bs.modal", function(e) {

        $(".modal .modal-body").css("overflow-y", "auto");
        $(".modal .modal-body").css("max-height", $(window).height() * 0.75);

    });

    /*$("#modalCertificado").on("shown.bs.modal", function (e) {

    	$(".modal .modal-body").css("overflow-y", "auto");
    	$(".modal .modal-body").css("max-height", $(window).height() * 0.75);

    });*/

    //btnAceptarA
    $("#btnAceptarA").click(function() {
        //console.log(status_solicitud);
        if (status_solicitud) {
            $("#modalStatusAbadono").modal("show")
        } else {
            if ($("#txtNumSocio").is(":visible") && $("#txtNumSocio").val().length < 5) {
                alert("Debe capturar los 5 digitos del numero de Asociado. Ejemplo: C0001");
                $("#txtNumSocio").focus();
            } else {
                if ($("#txtPass").is(":visible") && $("#txtPass").val().length < 4) {
                    alert("Debe capturar la contrase&ntilde;a.");
                    $("#txtPass").focus();
                } else {
                    //Verificar si es comercializador/envasador o productor
                    if ($("#txtNumIdenti").is(":visible") && $("#txtNumIdenti").val().length < 1) {
                        alert('Debe capturar el n&uacute;ro de identificacion');
                        $('#txtNumIdenti').focus();
                    } else {
                        $("#confirmacion").modal("show");
                        /*if ($("#cbxEstado").val() == "4")
                        {
                        	if (confirm("Esta operacion no se podra deshacer, �Desea continuar?"))
                        	{
                        		actualizarEstadoSolicitud();
                        	}
                        }
                        else actualizarEstadoSolicitud();*/
                    }
                }
            }

            return false;
        }
    });

    //Bot�n Aceptar para Certificado
    /*$("#btnAceptarC").click(function()
    {
    	if ($("#txtNumSolicitud").is(":visible") && $("#txtNumSolicitud").val().length < 4)
    	{
    		alert("Debe capturar el número de solicitud.");
    		$("#txtNumSolicitud").focus();
    	}
    	else
    		//actualizarEstadoSolicitudC();
    		$("#confirmacion2").modal("show");

    	return false;
    });*/

    $("#btnReporte").click(function() {
        exportarExcel();
    });


    $("#btnPermiso").click(function() {
        $("#confirmacion").modal('hide');
        actualizarEstadoSolicitud();
    });

    /*$("#btnPermiso2").click(function()
    {
    	$("#confirmacion2").modal('hide');
    	actualizarEstadoSolicitudC(); 
    });*/


    //cargarSolicitudes();
    //setInterval(cargarSolicitudes, 300000);


    /********************************************************************************************************************************************************************
     ********************************************FUNCIONES PARA SOLICITUDES TRASLADO,VARIOS,MADURACIO,AJUSTE**********************************************************/
    jQuery.fn.extend({
        getVal: function() { return $(this.selector).val(); },
        setVal: function(value) { $(this.selector).val(value); },
        isEmpty: function() { if ($(this.selector).val() == "") { return true; } else { return false; } },
        redBorder: function() { $(this.selector).css('border-color', 'red'); },
        defaultBorder: function() { $(this.selector).css('border-color', '#ccc'); },
        clear: function() {
            $(this.selector).val("");
            $(this.selector).text("");
        },
        enable: function() { $(this.selector).prop('disabled', false); },
        disabled: function() { $(this.selector).prop('disabled', true); },
        message: function(message) {
            if ($(`${this.selector}_span`).length == 0) { $(this.selector).after(`<span id="${this.selector.substring(1, this.selector.length)}_span" class="error-message">${message}</span>`); }
            $(this.selector).addClass('has-error').next('span').addClass('is-visible');
        },
        hideMessage: function() { $(this.selector).removeClass('has-error').next('span').removeClass('is-visible'); },
        getTextCmb: function(value) { return $(`${this.selector} option[value='${value}']`).text(); }
    });
    /********************************************************************************************************************************************************************
     ********************************************FIN FUNCIONES PARA SOLICITUDES TRASLADO,VARIOS,MADURACIO,AJUSTE**********************************************************/

    if (nivel != 1 && nivel != 2) { $("#li-inscripciones").hide() }
});

function cargarSolicitudes() {
    /**********************se agrego la opcion de ocultar solicitudes***************moises*/
    var tipo = $("#chkSolIncompletas").is(":checked");
    var caso = (tipo) ? "1" : "0";
    var datos = {
        "action": "obtenerSolicitudes",
        "tipo": caso
    };
    datos = $.param(datos);
    $.ajax({
        type: "POST",
        url: "php/solicitudes.php",
        contentType: "application/x-www-form-urlencoded;charset= UTF-8",
        //contentType: "application/json; charset=utf-8",
        data: datos,
        dataType: "json",
        success: function(data, textStatus, jqXHR) {
            if (data.status == "correcto") {
                $("#modalCargarSol").modal('hide'); //moises
                $("#tblSolicitudes").DataTable().rows().remove();

                for (var i = 0; i < data.solicitudes.length; i++) {

                    var tipoL = '';
                    var estadoL = '';

                    switch (data.solicitudes[i].tipo) {
                        case 1:
                            tipoL = "INSCRIPCIÓN";
                            break;
                        case 2:
                            tipoL = "INSCRIPCIÓN";
                            break;
                        case 5:
                            tipoL = "CERTIFICADO EXPORTACI&Oacute;N ";
                            break;
                        case 6:
                            tipoL = "CERTIFICADO PROMOCI&Oacute;N ";
                            break;
                        case 10:
                            tipoL = "AJUSTE DE GRADO, MEZCLA Y MUESTREO";
                            break;
                        case 11:
                            tipoL = "TRASLADO DE PRODUCTO";
                            break;
                        case 12:
                            tipoL = "VARIOS";
                            break;
                        case 13:
                            tipoL = "INGRESO Y/O LIBERACIÓN DE PRODUCTO A MADURACI&Oacute;N";
                            break;
                    }

                    if (data.solicitudes[i].abandonada) {
                        estadoL = "EXPEDIENTE ABANDONADO"; //moises
                    } else {
                        switch (data.solicitudes[i].estado) {
                            case 1:
                                estadoL = "EN REVISION";
                                break;
                            case 2:
                                estadoL = "PENDIENTE DE PAGO";
                                break;
                            case 3:
                                estadoL = "EN AUTORIZACION";
                                break;
                            case 4:
                                estadoL = "EN EJECUCION";
                                break;
                            case 5:
                                estadoL = "TERMINADA";
                                break;
                            case 6:
                                estadoL = "RECHAZADA";
                                break;
                            case 7:
                                estadoL = "RECIBIDA";
                                break;
                            case 8:
                                estadoL = "PROGRAMANDO VISITA";
                                break;
                            case 9:
                                estadoL = "VERIFICANDO";
                                break;
                            case 10:
                                estadoL = "VALIDANDO";
                                break;
                            case 11:
                                estadoL = "VISITA AGENDADA";
                                break;
                            case 12:
                                estadoL = "REVISI&OacuteN POR EL CLIENTE";
                                break;
                            case 13:
                                estadoL = "IMPRIMIENDO";
                                break;
                            case 14:
                                estadoL = "RESPUESTA DEL CLIENTE";
                                break;
                            case 15:
                                estadoL = "AUTORIZADA";
                                break;
                            case 16:
                                estadoL = "CANCELADA";
                                break;
                            case 17:
                                estadoL = "ACTIVIDAD REALIZADA";
                                break;
                            case 18:
                                estadoL = "ACTIVIDAD NO REALIZADA";
                                break;
                            case 19:
                                estadoL = "REPROGRAMANDO VISITA";
                                break;
                            case 20:
                                estadoL = "TRASLADO REALIZADO";
                                break;
                            case 21:
                                estadoL = "TRASLADO NO REALIZADO";
                                break;
                            case 23:
                                estadoL = "SEGUIMIENTO CON OTRO FOLIO";
                                break;
                            case 24:
                                estadoL = "REGISTRO INCOMPLETO";
                                break;
                        }
                    }

                    if (data.solicitudes[i].prorroga != '0' && (data.solicitudes[i].estado == 4 || data.solicitudes[i].estado == 5)) {
                        estadoL += " (Prorroga";
                        if (data.solicitudes[i].prorrogaVencida != '0') {
                            estadoL += " vencida";
                        }
                        estadoL += ")";
                    }

                    data.solicitudes[i].tipoLetra = tipoL;
                    data.solicitudes[i].estadoLetra = estadoL;
                    //data.solicitudes[i].ultimaModificacion = data.solicitudes[i].ultimaModificacion;

                    //Marcas
                    var marcas = "";
                    for (var j = 0; j < data.solicitudes[i].marcas.length; j++) {
                        marcas += data.solicitudes[i].marcas[j] + ", ";
                    }

                    data.solicitudes[i].marcas = marcas;

                }

                $("#tblSolicitudes").DataTable().rows.add(data.solicitudes).draw();

                //Colorear celdas
                for (var i = 0; i < data.solicitudes.length; i++) {

                    var tr = $("#tblSolicitudes").DataTable().row(i).node(5);

                    if (data.solicitudes[i].servicio == 1) {
                        $('td', tr).css("background-color", "#FFECCB");
                    }

                    if (data.solicitudes[i].abandonada) {
                        $('td', tr).eq(4).css("background-color", "#f78c8c");
                    } else {
                        switch (data.solicitudes[i].estado) {
                            case 1:
                                $('td', tr).eq(4).css("background-color", "");
                                break;
                            case 2:
                                $('td', tr).eq(4).css("background-color", "#e7ffac");
                                break;
                            case 3:
                                $('td', tr).eq(4).css("background-color", "#F9F97D");
                                break;
                            case 4:
                                $('td', tr).eq(4).css("background-color", "#FCC99E");
                                break;
                            case 5:
                                $('td', tr).eq(4).css("background-color", "#B2F5AE");
                                break;
                            case 6:
                                $('td', tr).eq(4).css("background-color", "#f78c8c");
                                break;
                            case 7:
                                $('td', tr).eq(4).css("background-color", "#B5F2F6");
                                break;
                            case 8:
                                $('td', tr).eq(4).css("background-color", "#F3B60C");
                                break;
                            case 9:
                                $('td', tr).eq(4).css("background-color", "#B0BBF7");
                                break;
                            case 10:
                                $('td', tr).eq(4).css("background-color", "#FCBBF0");
                                break;
                            case 11:
                                $('td', tr).eq(4).css("background-color", "#84EFBD");
                                break;
                            case 12:
                                $('td', tr).eq(4).css("background-color", "#CBF77A");
                                break;
                            case 13:
                                $('td', tr).eq(4).css("background-color", "#CBFF5C");
                                break;
                            case 14:
                                $('td', tr).eq(4).css("background-color", "#ADC3AF");
                                break;
                            case 15:
                                $('td', tr).eq(4).css("background-color", "#E0E496");
                                break;
                            case 16:
                                $('td', tr).eq(4).css("background-color", "#FFABAB");
                                break;
                            case 17:
                                $('td', tr).eq(4).css("background-color", "#BFFCC6");
                                break;
                            case 18:
                                $('td', tr).eq(4).css("background-color", "#F5D7D6");
                                break;
                            case 19:
                                $('td', tr).eq(4).css("background-color", "#F3B60C");
                                break;
                            case 20:
                                $('td', tr).eq(4).css("background-color", "#BFFCC6");
                                break;
                            case 21:
                                $('td', tr).eq(4).css("background-color", "#F5D7D6");
                                break;
                        }
                    }
                }


                $("#tblSolicitudes").DataTable().columns().every(function() {
                    var that = this;
                    $("input", this.footer()).on("keyup change", function() {
                        if (that.search() !== this.value) {
                            //console.log(this.value);
                            that.search(this.value).draw();
                        }
                    });
                });
            } else {
                alert("Ha ocurrido un error al cargar las Solicitudes: " + data.msj);
            }
        },
        error: function(jqxhr, status, errorGenerado) {
            alert("Ha ocurrido un error al cargar las Solicitudes: " + jqxhr.responseText);
        }
    });
}

function mostrarSolicitudAsociado(idSolicitud, tipo, estado, data) {

    $("#modalNotificaciones").modal('hide');
    estado += "";
    solicitudActual = idSolicitud;
    notificaciones(0);
    pedirPass = 0;
    contrato = 0;
    moral = 0;
    $("#modalAsociado").modal("show");

    var datos = {
        "action": "obtenerSolicitud",
        "idSolicitud": idSolicitud,
        "user": clvuser
    };
    datos = $.param(datos);

    $("#tabDetalleA a:first").tab("show");
    $("#tabAsignacion a:first").tab("show");
    $("#lblCargando").text("Cargando Solicitud...");
    $("#divCargando").show();
    $.ajax({
        type: "POST",
        url: "php/solicitudes.php",
        contentType: "application/x-www-form-urlencoded;charset=UTF-8",
        data: datos,
        dataType: "json",
        success: function(data, textStatus, jqXHR) {
            if (data.status == "correcto") {
                $("#btnProrroga").removeClass('disabled');
                var contenido = "<div class=\"form-group\">N&uacutemero de Solicitud: <b>" + data.numero + "</b><br>";//Tipo de Solicitud: <b>";
                numero = idSolicitud;
                try {
                    solicitante = data.documentos[0].tipoSolicitante;
                } catch (error) {
                    solicitante = 1;
                }
                status_prospecto = data.status_prospecto;
                status_solicitud = data.abandonada;
                prospecto = data.prospecto;
                $("#expAbandonado").addClass('collapse');
                if (status_solicitud) {
                    $("#expAbandonado").removeClass('collapse');
                }

                $("#txtComentarioInscripcion").val(data.comentarios);

                if (solicitante == 1) {
                    $("#btnAbandonado").removeClass('collapse');
                    $("#btnValidarGenerales").removeClass('collapse');
                    if (status_prospecto == 1) {
                        $("#btnValidarGenerales").html("<span class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\"></span> &nbspPermitir editar datos");
                        $("#btnValidarGenerales").removeClass("btn-success");
                        $("#btnValidarGenerales").addClass("btn-info");
                    } else {
                        $("#btnValidarGenerales").html("<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span> &nbspValidar datos generales");
                        $("#btnValidarGenerales").removeClass("btn-info");
                        $("#btnValidarGenerales").addClass("btn-success");
                    }
                    if (status_solicitud) {
                        $('#btnAbandonado').html("<span class=\"glyphicon glyphicon-ok-circle\" aria-hidden=\"true\"></span> &nbspReactivar Expediente");
                        $('#btnAbandonado').removeClass("btn-danger");
                        $('#btnAbandonado').addClass("btn-success");
                    } else {
                        $('#btnAbandonado').html("<span class=\"glyphicon glyphicon-remove-circle\" aria-hidden=\"true\" id=\"icoAbandonado\"></span> &nbspExpediente Abandonado");
                        $('#btnAbandonado').removeClass("btn-success");
                        $('#btnAbandonado').addClass("btn-danger");
                    }
                } else {
                    $("#btnValidarGenerales").addClass('collapse');
                    $("#btnAbandonado").addClass('collapse');
                }

                if (data.atendida == 0) {
                    $('#abrirModalMarcar').removeClass('collapse');
                } else {
                    $('#abrirModalMarcar').addClass('collapse');
                }

                correoMensaje = data.correo //obtener correos//Moises
                verificarEdicion(numero); //verificar si se puede o no desactivar//Moises
                $('#contMensajes').load('php/historial_mensajes.php?folio=' + numero);
                tipo += "";
                /*switch (tipo) {
                    case "1":
                        contenido += (data.tramite == 1) ? " ACTUALIZACIÓN DE DATOS " : " INSCRIPCIÓN PARA SER ASOCIADO DEL CRM ";
                        break;
                    case "2":
                        contenido += "PARA SER CLIENTE DEL CRM";
                        break;
                }*/
               // if (data.referenciaNC != '' && data.referenciaNC != null) contenido += "</b><br>Referencia Número de Asociado: <b>" + data.referenciaNC;
                contenido += "</b><br>Num. de registro: <b>" + data.no_cliente + "</b><br>Tipo de Persona: <b>";

                switch (data.persona) {
                    case 1:
                        contenido += "FISICA</b><br>Nombre: <b>";
                        break;
                    case 2:
                        contenido += "MORAL</b><br>Raz&oacuten Social: <b>";
                        moral++;
                        break;
                }

                var descEslabon = "";
                var i;
                for (i = 0; i < data.actividades.length; i++) {
                    switch (data.actividades[i]) {
                        case "A":
                            descEslabon += "PRODUCTOR DE MAGUEY";
                            break;
                        case "M":
                            descEslabon += (descEslabon.length > 1 ? "" : "PRODUCTOR DE ") + "MEZCAL";
                            contrato++;
                            break;
                        case "E":
                            descEslabon += "ENVASADOR";
                            contrato++;
                            break;
                        case "C":
                            descEslabon += "COMERCIALIZADOR";
                            pedirPass++;
                            contrato++;
                            break;
                        case "V":
                            descEslabon += "VIVERISTA";
                            break;
                        case "B":
                            descEslabon += "COMERCIALIZADOR BEBIDAS CON MEZCAL";
                            pedirPass++;
                            contrato++;
                            break;

                    }

                    if (i == (data.actividades.length - 2)) descEslabon += " Y ";
                    else if (i <= (data.actividades.length - 3)) descEslabon += ", ";

                }

                var descEstado;
                if (status_solicitud) {
                    descEstado = "EXPEDIENTE ABANDONADO"; //Cuando no se ha terminado el registro//Moises
                } else {
                    switch (estado) {
                        case '24':
                            descEstado = "REGISTRO INCOMPLETO";
                            break; //Cuando no se ha terminado el registro//Moises
                        case '6':
                            descEstado = "RECHAZADA";
                            break;
                        case '1':
                            descEstado = "EN REVISION";
                            break;
                        case '2':
                            descEstado = "PENDIENTE DE PAGO";
                            break;
                        case '3':
                            descEstado = "EN AUTORIZACION";
                            break;
                        case '4':
                            descEstado = "EN EJECUCION";
                            break;
                        case '5':
                            descEstado = "TERMINADA";
                            break;
                        case '23':
                            descEstado = "SEGUIMIENTO CON OTRO FOLIO";
                            break;
                    }
                }

                estatusRP = (data.representante != '') ? data.estatusRP : '';
                estatusPA = (data.autorizada != '') ? data.estatusPA : '';
                contenido += data.nombre + "</b><br>RFC: <b>" + data.rfc + "</b><br>Representante Legal: <b>" + data.representante + ' </b>(' + estatusRP + ')' + "<br>Persona Autorizada: <b>" + data.autorizada + '</b> (' + estatusPA + ')' +
                    "<br>Domicilio: <b>" + data.domicilio + "</b>";
                $("#contenidoGenerales").html(contenido);
                contenido = "";
                if (data.telefonos.length > 0) {
                    //contenido += "<div id=\"ListaTelefono\"></div>"
                    ListarTelefonos(data.telefonos);
                } else {
                    $("#ListaTelefono").html("");
                    contenido += "Tel&eacutefonos: <b>" + data.telefono + "</b>";
                }

                contenido += "<br>Fax: <b>" + data.fax + "</b><br>Correo electr&oacutenico: <b>" + data.correo +
                    "</b><br>Actividades dentro de la cadena productiva: <b>" + descEslabon +
                    "</b><br><br><p id=\"edoSolicitud\">Estado actual de la Solicitud: <b>" + descEstado + "</b><p><br>";

                contenido += "<div class=\"form-group\" id=\"divEstado\"" + ((estado == '5' || estado == '23' || nivel != 1 || data.mostrar_opciones == 0) ? " hidden" : "") + "><label for=\"cbxEstado\" class=\"col-xs-4 control-label\">Cambiar estado a:</label>" +
                    "<div class=\"col-xs-8\"><select class=\"form-control\" name=\"cbxEstado\" id=\"cbxEstado\" onchange=\"mostrarOcultar(this)\">";

                switch (estado) {
                    case '6':
                        contenido += "<option value=\"1\">EN REVISION</option>";
                        contenido += "<option value=\"2\">PENDIENTE DE PAGO</option>";
                        contenido += "<option value=\"23\">SEGUIMIENTO CON OTRO FOLIO</option>";
                        break;
                    case '1':
                        contenido += "<option value=\"2\">PENDIENTE DE PAGO</option>";
                        contenido += "<option value=\"6\">RECHAZADA</option>";
                        contenido += "<option value=\"23\">SEGUIMIENTO CON OTRO FOLIO</option>";
                        break;
                    case '2':
                        contenido += "<option value=\"3\">EN AUTORIZACION</option>";
                        contenido += "<option value=\"1\">EN REVISION</option>";
                        contenido += "<option value=\"6\">RECHAZADA</option>";
                        contenido += "<option value=\"23\">SEGUIMIENTO CON OTRO FOLIO</option>";
                        break;
                    case '3':
                        contenido += "<option value=\"4\">EN EJECUCION</option>";
                        //contenido += "<option value=\"3\">EN AUTORIZACION</option>";
                        contenido += "<option value=\"6\">RECHAZADA</option>";
                        contenido += "<option value=\"23\">SEGUIMIENTO CON OTRO FOLIO</option>";
                        break;
                    case '4':
                        contenido += "<option value=\"5\">TERMINADA</option>";
                        break;
                    case '30':
                        contenido += "<option value=\"1\">EN REVISION</option>";
                        break;
                    case '24':
                        contenido += "<option value=\"23\">SEGUIMIENTO CON OTRO FOLIO</option>";
                        break;
                }

                /*contenido += "</select></div></div><div class=\"collapse\" id=\"divNumSolicitud\">" +
                			"<label for=\"txtNumSolicitud\" class=\"col-xs-4 control-label\">Numero de Solicitud:</label><div class=\"col-xs-6\">" +
                			"<input type=\"text\" name=\"txtNumSolicitud\" id=\"txtNumSolicitud\" value=\"\" class=\"form-control\" placeholder=\"Numero de Solicitud\" maxlength=\"8\">" +*/

                contenido += "</select></div></div><div class=\"collapse\" id=\"divNumCliente\">" +
                    "<label for=\"txtNumSocio\" class=\"col-xs-4 control-label\">N&uacutemero de Asociado:</label><div class=\"col-xs-8\">" +
                    "<input type=\"text\" name=\"txtNumSocio\" id=\"txtNumSocio\" value=\"\" class=\"form-control\" placeholder=\"Número de asociado comenzando por la letra C (Ejemplo: C0001)\" maxlength=\"8\">" +
                    "</div></div>" +
                    "<div class=\"collapse\" id=\"divCRM\">" +
                    "<label for=\"txtCRM\" class=\"col-xs-4 control-label\">Número CRM (Solo Si Aplica)</label>" +
                    "<div class=\"col-xs-8\">" +
                    "<input type=\"text\" name=\"txtCRM\" id=\"txtCRM\" value=\"\" class=\"form-control\" maxlength=\"5\">" +
                    "</div></div>" +
                    "<div class=\"collapse\" id=\"divTipoRegistro\"><label for=\"cbxTipoRegistro\" class=\"col-xs-4 control-label\">Tipo Registro</label>" +
                    "<div class=\"col-xs-8\"><select class=\"form-control\" name=\"cbxTipoRegistro\" id=\"cbxTipoRegistro\">" +
                    "<option value=1>Asociado</option><option value=2>Socio</option><option value=0>Cliente</option></select></div></div>" +
                    "<div class=\"collapse\" id=\"divPass\">" +
                    "<label for=\"txtPass\" class=\"col-xs-4 control-label\">Contrase&ntildea</label>" +
                    "<div class=\"col-xs-8\">" +
                    "<input type=\"text\" name=\"txtPass\" id=\"txtPass\" value=\"\" class=\"form-control\" maxlength=\"5\">" +
                    "</div></div>" +
                    "<div class=\"collapse\" id=\"divNumIdenti\">" +
                    "<label for=\"txtNumIdenti\" class=\"col-xs-4 control-label\">No. identificaci&oacuten</label>" +
                    "<div class=\"col-xs-8\">" +
                    "<input type=\"text\" name=\"txtNumIdenti\" id=\"txtNumIdenti\" value=\"\" class=\"form-control\" maxlength=\"20\">" +
                    "</div></div>" +
                    "<div class=\"collapse\" id=\"divTipoIdenti\"><label for=\"cbxTipoIdenti\" class=\"col-xs-4 control-label\">Tipo Identificaci&oacuten</label>" +
                    "<div class=\"col-xs-8\"><select class=\"form-control\" name=\"cbxTipoIdenti\" id=\"cbxTipoIdenti\">" +
                    "<option value=1>CEDULA PROFESIONAL</option><option value=2>IFE</option><option value=3>INE</option>" +
                    "<option value=4>PASAPORTE</option><option value=5>CARTILLA MILITAR</option><option value=6>FM3</option>" +
                    "<option value=7>PASAPORTE EMITIDO EN EL EXTRAJERO</option><option value=8>PASAPORTE EXTRAJERO</option>" +
                    "<option value=9>VISA DE RESIDENTE PERMANENTE</option></select></div></div>" +
                    "<div class=\"collapse\" id=\"divActa\">" +
                    "<label for=\"txtACP\" class=\"col-xs-4 control-label\">Acta Constitutiva/Poder: </label>" +
                    "<div class=\"col-xs-8\">" +
                    "<textarea name=\"txtACP\" id=\"txtACP\" value=\"\" class=\"form-control\"  placeholder=\"Datos del Acta Constitutiva y Poder\"></textarea>" +
                    "</div></div>" +
                    "<div class=\"collapse\" id=\"divObjeto\">" +
                    "<label for=\"txtObjeto\" class=\"col-xs-4 control-label\">Objeto Social: </label>" +
                    "<div class=\"col-xs-8\">" +
                    "<textarea name=\"txtObjeto\" id=\"txtObjeto\" value=\"\" class=\"form-control\"></textarea>" +
                    "</div></div>" +
                    "<div class=\"\" id=\"divEnviarMensajes\"><label for=\"txtObs\" class=\"col-xs-12 control-label\">Observaciones:</label>" +
                    "<div><label for=\"fileDocumentoAdjunto\">Adjuntar archivo</label>" +
                    "<input type=\"file\" class=\"form-control-file\" name=\"fileDocumentoAdjunto\" id=\"fileDocumentoAdjunto\"></div>" +
                    "<textarea class=\"form-control\" name=\"txtObs\" id=\"txtObs\" rows=\"15\"></textarea>" +
                    "<br><center><button type=\"button\" class=\"btn btn-primary\" class=\"form-control\" name=\"btnEnviarMensaje\" id=\"btnEnviarMensaje\"onclick=\"enviarMensaje()\">Enviar Mensaje</button></center></div>";
                //MOISES CREAR BOTON

                $("#contenidoGenerales2").html(contenido);

                if (nivel == 1 && usr_cargo == 14 && data.mostrar_opciones == 1) {
                    $("#btnAceptarA").show();
                    $("#btnBloquearEdicion").show();
                    $("#abrirModalMarcar").show();
                    $(".revisor").show();
                    $('#btnEnviarMensaje').removeClass("disabled");
                    console.log("Mostrar");
                } else {
                    $("#btnAceptarA").hide();
                    $("#btnBloquearEdicion").hide();
                    $("#abrirModalMarcar").hide();
                    $(".revisor").hide();
                    $('#btnEnviarMensaje').addClass("disabled");
                    console.log("No Mostrar");
                }
                if ((estado == 1 || estado == 2 || estado == 3) && (clvuser == 1 || clvuser == 21)) {
                    $("#btnAsignarNueva").removeClass("collapse");
                } else {
                    $("#btnAsignarNueva").addClass("collapse");
                }

                $("#lblRevisor").text(data.revisores);

                mostrar_opciones = data.mostrar_opciones

                Listar_Instalaciones(data.instalaciones);

                Listar_Marcas(data.marcas);

                Listar_Documentos(data.documentos, data.persona, data.marcas, data.instalaciones); //moises
                bitacora_consultas(2, 0, 3, 0);
                //$("#txtObs").val(data.observaciones);//moises
                //if (data.numero != null) $("#txtNumSolicitud").val(data.numero);
                mostrarOcultar(document.getElementById("cbxEstado"));

                $("#divCargando").hide();
            } else if (data.status == "restringido") {
                $("#modalAsociado").modal("hide");
                $('#modalRestriccion').modal('show');
            } else {
                $("#divCargando").hide();
                alert("Ha ocurrido un error al cargar la Solicitud: " + data.msj + jqXHR.responseText);
            }
        },
        error: function(jqxhr, status, errorGenerado) {
            $("#divCargando").hide();
            alert("Ha ocurrido un error al cargar la Solicitud: " + jqxhr.responseText);
        }
    });
}

function ListarTelefonos(telefonos) {

    lista_telefonos = telefonos;

    var contenido = "";
    contenido += "</b><table class=\"table-bordered table-hover\" style=\"text-align:center;\"> <tr><th style=\"text-align:center;\" colspan=\"4\">Teléfonos</th></tr>" +
        "<tr style=\"margin-top: 15px; margin-bottom: 15px;\"><th width=\"120\" style=\"text-align:center;\">Teléfono</th><th width=\"90\" style=\"text-align:center;\">Tipo</th><th width=\"120\" style=\"text-align:center;\">Notificaciones</th><th width=\"200\" style=\"text-align:center;\">Validar/desactivar</th></tr>";
    for (var i = 0; i < telefonos.length; i++) {
        var tipo = (telefonos[i].tipo == 0) ? "Celular" : "Fijo";
        var notificacion = (telefonos[i].notificacion == 1) ? "SI" : "NO";
        if (telefonos[i].status == 1) {
            contenido += "<tr><td style=\"text-align:center;\">" + telefonos[i].numero + "</td><td style=\"text-align:center;\">" + tipo + "</td><td style=\"text-align:center;\">" + notificacion + "</td><td style=\"text-align:center;\"><button type=\"button\" class=\"btn btn-success\" style=\"margin-top: 5px; margin-bottom: 5px;\" onclick=\"Activar_Telefonos('" + telefonos[i].id + "','" + telefonos[i].status + "','" + i + "');\">" +
                "<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span> &nbspValidar Teléfono</button</td></tr>";
        } else {
            contenido += "<tr><td style=\"text-align:center; margin-top: 30px; margin-bottom: 30px;\">" + telefonos[i].numero + "</td><td style=\"text-align:center;\">" + tipo + "</td><td style=\"text-align:center;\">" + notificacion + "</td><td style=\"text-align:center;\"><button type=\"button\" class=\"btn btn-info\" style=\"margin-top: 5px; margin-bottom: 5px;\" onclick=\"Activar_Telefonos('" + telefonos[i].id + "','" + telefonos[i].status + "','" + i + "');\">" +
                "&nbsp&nbsp<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span> &nbsp Deshabilitar&nbsp&nbsp&nbsp</button</td></tr>";
        }
    }
    contenido += "</table>";
    $("#ListaTelefono").html(contenido);

}

function Activar_Telefonos(tel, status, indice) {
    status = (status == 1) ? "2" : "1";
    $.ajax({
        url: "php/estados_solicitudes.php",
        method: "POST",
        data: {
            caso: "telefono",
            tel: tel,
            status: status
        },
        dataType: "json",
        success: function(data) {
            if (data.status) {
                lista_telefonos[indice].status = status;
                ListarTelefonos(lista_telefonos);
                $("#modalStatus").modal('show');
            } else {
                alert("error");
            }
        }
    });
}

function Listar_Marcas(marcas) {
    var contenido = "";
    listaMarcas = marcas;
    for (i = 0; i < marcas.length; i++) {
        //contenido += "<b>MARCA " + data.marcas[i].clave + ":</b> " + data.marcas[i].nombre + "<br>";
        if (solicitante == 1 && mostrar_opciones == 1) {
            if (marcas[i].status == 0) {
                contenido += "<button type=\"button\" class=\"btn btn-success\" id=\"btnValidarMarca\" onclick=\"Activar_Marcas('" + marcas[i].id + "','" + marcas[i].status + "','" + i + "');\">" +
                    "<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span> &nbspValidar marca</button><br>"
            } else {
                contenido += "<button type=\"button\" class=\"btn btn-info\" id=\"btnValidarMarca\" onclick=\"Activar_Marcas('" + marcas[i].id + "','" + marcas[i].status + "','" + i + "');\">" +
                    "<span class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\"></span> &nbspPermitir editar marca</button><br>"
            }
        }
        contenido += "<b>MARCA" + ":</b> " + marcas[i].nombre + "<br>"; //Sin Clave de marca//moises
        contenido += "Tipo: <b>" + (marcas[i].tipo == 1 ? "NOMINATIVA" : "MIXTA") + "</b><br>";
        contenido += "Para: <b>" + (marcas[i].tipoUso == 1 ? "MEZCAL" : "BEBIDAS QUE CONTIENEN MEZCAL") + "</b><br>";
        contenido += "N&uacutemero de Registro:  <b>" + marcas[i].registro + "</b><br>";
        contenido += "Expediente:  <b>" + marcas[i].expediente + "</b><br><br>"; //Mostrar Expediente Moises
    }

    $("#tabMarcasA").html(contenido);
}

function Activar_Marcas(marca, status, indice) {
    status = (status == 1) ? "0" : "1";
    $.ajax({
        url: "php/estados_solicitudes.php",
        method: "POST",
        data: {
            caso: "marcas",
            marca: marca,
            status: status
        },
        dataType: "json",
        success: function(data) {
            if (data.status) {
                listaMarcas[indice].status = status;
                Listar_Marcas(listaMarcas);
                $("#modalStatus").modal('show');
            } else {
                alert("error");
            }
        }
    });
}

function Listar_Instalaciones(instalaciones) {
    var contenido = "";
    listaInstalaciones = instalaciones;
    for (i = 0; i < instalaciones.length; i++) {

        if (instalaciones[i].edoInst == 5) {
            edoInst = "<span class=\"label label-warning\"><b>ANTERIOR A LA ACTUALIZACIÓN</b></span>"
        } else {
            edoInst = (instalaciones[i].edoInst == 1) ? "<span class=\"label label-success\"><b>ACTIVA</b></span>" : "<span class=\"label label-danger\"><b>INACTIVA</b></span>";
        }

        //if (contenido.length > 0) contenido += "";
        //contenido += "<b>" + data.instalaciones[i].tipo + " " + data.instalaciones[i].numero;
        if (solicitante == 1 && mostrar_opciones == 1) {
            if (instalaciones[i].edoInst != 1) {
                contenido += "<br><div class=\"col-xs-12\"><div class=\"panel panel-primary\"><div class=\"panel-body\"><div class=\"col-md-6\"><button type=\"button\" class=\"btn btn-info\" id=\"btnValidarInsta\" onclick=\"ActivarInstalacion('" + instalaciones[i].id + "','" + instalaciones[i].edoInst + "','" + i + "');\">" +
                    "<span class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\"></span> &nbspPermitir editar instalación</button><br>"
            } else {
                contenido += "<div class=\"col-xs-12\"><div class=\"panel panel-primary\"><div class=\"panel-body\"><div class=\"col-md-6\"><button type=\"button\" class=\"btn btn-success\" id=\"btnValidarInsta\" onclick=\"ActivarInstalacion('" + instalaciones[i].id + "','" + instalaciones[i].edoInst + "','" + i + "');\">" +
                    "<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span> &nbspValidar instalación</button><br>"
            }
        } else {
            contenido += "<br><div class=\"col-xs-12\"><div class=\"panel panel-primary\"><div class=\"panel-body\"><div class=\"col-md-6\">";
        }

        contenido += "<b>" + instalaciones[i].tipo; //Sin numero de instalacion//Moises

        if (instalaciones[i].alias.length > 0) contenido += ": " + instalaciones[i].alias;

        if (solicitante != 1) {
            contenido += "</b><br>Estatus: <b>" + edoInst;
        }
        contenido += "</b><br>Domicilio: <b>" + instalaciones[i].domicilio + "</b><br>Paraje: <b>" + instalaciones[i].paraje + "</b><br>Colonia/Localidad: <b>" + instalaciones[i].colonia +
            "</b><br>Municipio: <b>" + instalaciones[i].municipio + "</b><br>Estado: <b>" + instalaciones[i].estado + "</b><br>C&oacutedigo Postal: <b>" + instalaciones[i].cp;

        if (instalaciones[i].telefono.length > 0) contenido += "</b><br>Tel&eacutefono: <b>" + instalaciones[i].telefono;
        if (instalaciones[i].fax.length > 0) contenido += "</b><br>Tel&eacutefono: <b>" + instalaciones[i].fax;
        if (instalaciones[i].correo.length > 0) contenido += "</b><br>Correo electr&oacutenico: <b>" + instalaciones[i].correo;

        contenido += "</b><br>Responsable de las instalaciones: <b>" + instalaciones[i].responsable + "</b><br>"; //Almacen de: <b>" +  instalaciones[i].almacen + "</b><br>";
        contenido += "</div><div class=\"col-md-6\"><iframe src=\"https://maps.google.com/maps?q=" + instalaciones[i].latitud + "," + instalaciones[i].longitud + "&z=10&output=embed\" width=\"100%\" height=\"230\" frameborder=\"0\" style=\"border:0;\"  aria-hidden=\"false\" tabindex=\"0\"></iframe></div></div></div></div>";
    }
    contenido += "";

    $("#tabInstalacionesA").html(contenido);
}

function ActivarInstalacion(instalacion_id, status, indice) {
    //console.log(instalacion_id);
    status = (status == 1) ? "4" : "1";
    $.ajax({
        url: "php/estados_solicitudes.php",
        method: "POST",
        data: {
            caso: "instalacion",
            instalacion: instalacion_id,
            status: status
        },
        dataType: "json",
        success: function(data) {
            if (data.status) {
                listaInstalaciones[indice].edoInst = status;
                Listar_Instalaciones(listaInstalaciones);
                $("#modalStatus").modal('show');
            } else {
                alert("error");
            }
        }
    });
}

function Listar_Documentos(documentos, persona, marcas, instalaciones) {
    $("#txtObservacionesProrroga").val("");
    var contenido = "<div class=\"panel panel-default\"><div class=\"panel-heading\"><h6 class=\"panel-title\">Persona " + (persona === 1 ? "F&iacutesica" : "Moral") + "</h6></div>" +
        "<div class=\"panel-content\">" +
        "<div class=\"list-group\" style=\"margin-bottom:0px\">";

    var instalacionAnt = null;
    var marcaAnt = null;
    var usoAnt = null;
    var tipoUso;
    var instalacion;
    var marca;
    var titulo;
    var contador = 0;

    lista_documentos = documentos;
    persona_documento = persona;
    marcas_lista = marcas;
    instalaciones_lista = instalaciones;

    for (i = 0; i < documentos.length; i++) { //todo el for editado para listar//Moises

        /*if(data.documentos[i].crm==1 &&  data.documentos[i].instalacion==null && data.documentos[i].tipo==27)
        {
        contenido += "</div></div></div><div class=\"panel panel-default\"><div class=\"panel-heading\"><h6 class=\"panel-title\">Documentaci&oacute;n emitida por CRM</h6></div>" +
        	"<div class=\"panel-content\"><div class=\"list-group\" style=\"margin-bottom:0px\">";
        	contador++;
        }*/

        if (!(documentos[i].crm == 1)) {
            if (documentos[i].instalacion !== instalacionAnt && documentos[i].instalacion !== null) {
                instalacionAnt = documentos[i].instalacion;
                instalacion = buscarInstalacion(instalaciones, instalacionAnt);
                //console.log("que es instalacion "+instalacion+" instalacion anterior "+instalacionAnt+"--data-- "+data);
                //console.log(instalacion);
                //if(instalacion!=undefined)//Agrege if(instalacion!=undefined) kevin
                //{
                //titulo = "<b>" + instalacion.tipo + " " + instalacion.numero;

                titulo = "<b>" + instalacion.tipo; //sin numero//moises
                if (instalacion.alias.length > 0) titulo += ": " + instalacion.alias;

                titulo += "</b> UBICADA EN " + instalacion.domicilio + ", " + instalacion.colonia + ", " + instalacion.municipio + ", " + instalacion.estado + ", " + instalacion.cp;
                contenido += "</div></div></div><div class=\"panel panel-default\"><div class=\"panel-heading\"><h6 class=\"panel-title\">" + titulo + "</h6></div>" +
                    "<div class=\"panel-content\"><div class=\"list-group\" style=\"margin-bottom:0px\">";
                //}//termina if kevin
            } else if (documentos[i].marca !== marcaAnt && documentos[i].marca !== null) {
                marcaAnt = documentos[i].marca;
                usoAnt = documentos[i].tipoUso;
                marca = buscarMarca(marcas, marcaAnt);
                tipoUso = (documentos[i].tipoUso == 1) ? " MEZCAL" : 'BEBIDAS PREPARADAS CON MEZCAL';
                clave = (marca.clave != null) ? marca.clave : '';

                titulo = "<b>Marca " + clave + ":</b> " + marca.nombre + " <b>(" + tipoUso + ")</b>";

                //titulo = "<b>Marca" +":</b> " + marca.nombre+" <b>("+tipoUso+")</b>";//Sin clave///Moises

                contenido += "</div></div></div><div class=\"panel panel-default\"><div class=\"panel-heading\"><h6 class=\"panel-title\">" + titulo + "</h6></div>" +
                    "<div class=\"panel-content\"><div class=\"list-group\" style=\"margin-bottom:0px\">";
            }

            //contenido += "<a style=\"cursor: pointer; width: 100%;\" onclick=\"AccionDocumentos('"+ data.documentos[i].id + "');\" class=\"list-group-item pull-left btn btn-primary text-left\" target=\"_blank\"><img src=\"images/pdf-icon.png\" class=\"pull-left\"/>";

            var nomDoc = "";
            switch (documentos[i].tipo) {
                case 1:
                    nomDoc = "RFC";
                    break;
                case 2:
                    nomDoc = "Acta Constitutiva";
                    break;
                case 3:
                    nomDoc = "Identificación Oficial";
                    break;
                case 4:
                    nomDoc = "Identificación Oficial del Representante Legal";
                    break;
                case 5:
                    nomDoc = "Comprobante del Domicilio Fiscal";
                    break;
                case 6:
                    nomDoc = "Carta de designación de responsable para trámites";
                    break;
                case 7:
                    nomDoc = "Plano de distribución";
                    break;
                case 8:
                    nomDoc = "Identificación Oficial del Responsable";
                    break;
                case 9:
                    nomDoc = "Comprobante de posesión";
                    break;
                case 10:
                    nomDoc = "Contrato de arrendamiento o comodato";
                    break;
                case 12:
                    nomDoc = "Identificación Oficial del Arrendatario";
                    break;
                case 11:
                    nomDoc = "Identificación Oficial del Arrendador";
                    break;
                case 13:
                    nomDoc = "Juego de etiquetas";
                    break;
                case 14:
                    nomDoc = "Titulo de Marca";
                    break;
                case 15:
                    nomDoc = "Licencia de uso de Marca";
                    break;
                case 16:
                    nomDoc = "Comprobante de Marca en Trámite";
                    break;
                case 17:
                    nomDoc = "Carta Responsiva";
                    break;
                case 18:
                    nomDoc = "Convenio de Corresponsabilidad";
                    break;
                case 19:
                    nomDoc = "Contrato de Maquila";
                    break;
                case 20:
                    nomDoc = "Identificación Oficial de la Persona Autorizada";
                    break;
                case 21:
                    nomDoc = "Oficio de Número de Asociado";
                    break;
                case 22:
                    nomDoc = "Oficio de contraseña";
                    break;
                case 23:
                    nomDoc = "Anexo";
                    break;
                case 24:
                    nomDoc = "Registro COFEPRIS";
                    break;
                case 25:
                    nomDoc = "Constacia de inscripción al Padrón de Bebidas Alcoholicas";
                    break;
                case 26:
                    nomDoc = "Pre-Registro de Maguey";
                    break;
                case 27:
                    nomDoc = "Solicitud Uso de Marca";
                    break;
                case 28:
                    nomDoc = "Contrato Prestaci&oacuten de Servicios";
                    break;
                case 29:
                    nomDoc = "CURP";
                    break;
                case 30:
                    nomDoc = "Formato 32-D";
                    break;
                case 31:
                    nomDoc = "Constancia de alta o inscripción en el Padrón de Exportadores Sectorial del SAT";
                    break;
                case 32:
                    nomDoc = "Analisis de Laboratorio";
                    break;
                case 33:
                    nomDoc = "Juego de etiquetas para solicitud";
                    break;
                case 34:
                    nomDoc = "Muestra de embalaje";
                    break;
                case 35:
                    nomDoc = "Factura";
                    break;
                case 36:
                    nomDoc = "Invitación u orden de compra";
                    break;
                case 37:
                    nomDoc = "Autorización del Convenio de Corresponsabilidad";
                    break;
                case 38:
                    nomDoc = "Solicitud de Servicio";
                    break;
                case 39:
                    nomDoc = "Plan del Cliente";
                    break;
                case 40:
                    nomDoc = "Dictamen";
                    break;
                case 41:
                    nomDoc = "Certificados";
                    break;
                case 42:
                    nomDoc = "Informe de Incumplimiento";
                    break;
                case 43:
                    nomDoc = "Decisión de la Certificación";
                    break;
                case 44:
                    nomDoc = "Carta responsiva por incumplimiento";
                    break;
                case 45:
                    nomDoc = "Convenio de Distribución";
                    break;
                case 46:
                    nomDoc = "Carta poder notarial";
                    break;
                case 47:
                    nomDoc = "Constancias";
                    break;
                case 48:
                    nomDoc = "Acuse de movimiento por apertura de sucursal";
                    break;
                case 49:
                    nomDoc = "Acuse de Pre-registro";
                    break;
                case 50:
                    nomDoc = "Acta Asamblea";
                    break;
                case 51:
                    nomDoc = "Carta compromiso para la inscripción al padrón de bebidas alcohólicas";
                    break;
                case 52:
                    nomDoc = "Carta responsiva trámite de convenio de corresponsabilidad";
                    break;
                case 53:
                    nomDoc = "Dictamen de Cumplimiento";
                    break;
                case 54:
                    nomDoc = "Expediente de Transmisión";
                    break;
                case 55:
                    nomDoc = "Carta responsiva de trámite de uso de la DOM";
                    break;
                case 56:
                    nomDoc = "Autorización del uso de la DOM";
                    break;
                case 57:
                    nomDoc = "Acuse de recibo del IMPI";
                    break;
                case 58:
                    nomDoc = "Borrador certificado";
                    break;
                case 59:
                    nomDoc = "Carta compromiso para el aviso de funcionamiento COFEPRIS";
                    break;
                case 60:
                    nomDoc = "Carta compromiso para inscripción al padrón de exportadores sectorial del SAT";
                    break;
                case 61:
                    nomDoc = "Autorización de la Transmisión";
                    break;
                case 63:
                    nomDoc = "Solicitud Beca de Productor";
                    break;
                case 65:
                    nomDoc = "Poder Notarial";
                    break;
                case 66:
                    nomDoc = "Carta Baja Temporal";
                    break;
                case 67:
                    nomDoc = "Carta Baja Definitiva";
                    break;
                case 68:
                    nomDoc = "Suspención";
                    break;
                case 69:
                    nomDoc = "Adenda contrato prestación de servicios";
                    break;
                case 70:
                    nomDoc = "Baja representante legal";
                    break;
                case 71:
                    nomDoc = "Baja persona autorizada";
                    break;
                case 72:
                    nomDoc = "Otro";
                    break;
                default:
                    nomDoc = "Tipo desconocido: " + documentos[i].tipo;
            }

            var icono = "";

            if (solicitante == 1 && mostrar_opciones == 1) {
                //console.log(mostrar_opciones + " " + documentos[i].tipoSolicitante);
                switch (documentos[i].status) {
                    case 3:
                        icono = "<span class=\"glyphicon glyphicon-ok text-success\" aria-hidden=\"true\"></span>";
                        break;
                    case 4:
                        icono = "<span class=\"glyphicon glyphicon-remove text-danger\" aria-hidden=\"true\"></span>";
                        break;
                    case 5:
                        icono = "<span class=\"glyphicon glyphicon-time text-primary\" aria-hidden=\"true\"></span>&nbsp;&nbsp;" + documentos[i].fin;
                        //icono = "<span class=\"glyphicon glyphicon-ok text-success\" aria-hidden=\"true\"></span>";
                        break;
                    default:
                        break;
                }
                contenido += "<a style=\"cursor: pointer;\" class=\"list-group-item\" onclick=\"AccionDocumentos('" + documentos[i].id + "','" + nomDoc + "','" + i + "','" + documentos[i].fin + "','" + documentos[i].inicio + "','1','" + documentos[i].tipo + "',`" + documentos[i].observaciones + "`);\" ><img src=\"images/pdf-icon.png\" class=\"pull-left\"/>" + nomDoc + "&nbsp&nbsp" + icono + " " + documentos[i].nom_maq + "</a>";
            } else {
                if (documentos[i].status == 5) {
                    contenido += "<a href=\"php/descargar_documento.php?id=" + documentos[i].id + "&user=" + clvuser + "\" class=\"list-group-item\" target=\"_blank\"><img src=\"images/pdf-icon.png\" class=\"pull-left\"/>" + nomDoc + " &nbsp;&nbsp;- <span class=\"glyphicon glyphicon-time text-primary\" aria-hidden=\"true\"></span> " + documentos[i].fin + "</a>";
                    //contenido += "<a href=\"php/descargar_documento.php?id=" + documentos[i].id + "\" class=\"list-group-item\" target=\"_blank\"><img src=\"images/pdf-icon.png\" class=\"pull-left\"/>" + nomDoc + " &nbsp;&nbsp;- <span class=\"glyphicon glyphicon-time text-primary\" aria-hidden=\"true\"></span> </a>";
                } else {
                    contenido += "<a href=\"php/descargar_documento.php?id=" + documentos[i].id + "&user=" + clvuser + "\" class=\"list-group-item\" target=\"_blank\"><img src=\"images/pdf-icon.png\" class=\"pull-left\"/>" + nomDoc + "</a>";
                }
            }
        }

    }
    contador = 0
    for (i = 0; i < documentos.length; i++) { //For par comprobar solicitud de uso de marca//moises

        if (documentos[i].crm == 1 && contador == 0) {
            contenido += "</div></div></div><div class=\"panel panel-default\"><div class=\"panel-heading\"><h6 class=\"panel-title\">Documentaci&oacute;n emitida por AMMA</h6></div>" +
                "<div class=\"panel-content\"><div class=\"list-group\" style=\"margin-bottom:0px\">";
            contador++;
        }
        if (documentos[i].crm == 1) {
            contenido += "<a href=\"php/descargar_documento.php?id=" + documentos[i].id + "&user=" + clvuser + "\" class=\"list-group-item\" target=\"_blank\"><img src=\"images/pdf-icon.png\" class=\"pull-left\"/>";
            switch (documentos[i].tipo) {
                case 27:
                    contenido += "Solicitud Uso de Marca";
                    break;
                case 21:
                    contenido += "Oficio de N&uacutemero de Asociado";
                    break;
                case 22:
                    contenido += "Oficio de contrase&ntilde;a";
                    break;
                case 23:
                    contenido += "Anexo";
                    break;
                case 28:
                    contenido += "Contrato Prestaci&oacuten de Servicios";
                    break;
                default:
                    contenido += "Tipo desconocido: " + documentos[i].tipo;
            }

            contenido += "</a>";
        }


    }


    contenido += "</div></div></div>";

    $("#tabDocumentacionA").html(contenido);
}
//****************Modal accion documentos*******************//Moises
function AccionDocumentos(numero, documentoNombre, indice, fin, inicio, doc, tipo, observaciones) {
    casoDocumento = doc;
    tipoDocumento = tipo;
    $('#txtFin').val(fin);
    $('#txtInicio').val(inicio);
    //console.log(numero+documentoNombre);
    documento_indice = indice;
    documento = numero;
    $('#divProrroga').addClass('collapse');
    $('#divRelacion').addClass('collapse');
    $("#labelVigencia").addClass('collapse');
    $("#divRechazo").addClass("collapse");
    $('#txtObservacionesRechazo').val("");
    $("#tituloDocumento").text(documentoNombre);
    $("#divDatosVigencia").addClass('collapse');
    $('#divRechazo').addClass('collapse');
    $('#divAgregarRe').addClass('collapse');
    $('#tipo_vigencia_1').prop('checked', false);
    $('#tipo_vigencia_2').prop('checked', false);
    $('#divFinRelac').addClass('collapse');
    $('#dvVigFec').addClass('collapse');

    if (observaciones != "" && observaciones != "NULL" && observaciones != "undefined") {
        $('#lblObservaciones').html("<b>Observaciones:</b> " + observaciones);
        $("#lblObservaciones").removeClass('collapse');
        console.log(observaciones);
    } else {
        $('#lblObservaciones').html("");
    }
    $("#modalAccionDocs").modal('show');
    $("#txtCteProv").val('');
    $('#txtProrroga').val('');
    /*var win = window.open('php/descargar_documento.php?id='+numero, '_blank');
  	win.focus();*/
}
//****************modal accion documentos*******************//Moises

//****************Verificar si ya tiene permisos para editar*******************//Moises
function verificarEdicion(numero) {
    $.ajax({
        url: "php/edicion_prospectos.php",
        method: "POST",
        data: {
            caso: "2",
            folio: numero
        },
        dataType: "json",
        success: function(data) {
            if (data.status == 0) {
                $('#btnBloquearEdicion').addClass("disabled");
            } else {
                $('#btnBloquearEdicion').removeClass("disabled");
            }
        }
    });
}
//****************Verificar si ya tiene permisos para editar*******************//Moises

function actualizarEstadoSolicitud() {
    if ($("#cbxEstado").val() == 4) {
        $.ajax({
            url: "php/solicitudes.php",
            method: "POST",
            data: {
                action: "verificar_relaciones",
                solicitud: solicitudActual,
                prospecto: prospecto
            },
            dataType: "json",
            success: function(data) {
                if (data.status == "correcto") {
                    if (data.continuar == 0) {
                        actualizarEstadoSolicitud2();
                    } else {
                        $("#modalContinuarRelacion").modal("show");
                    }
                } else {
                    alert("error");
                }
            }
        });
    } else {
        actualizarEstadoSolicitud2();
    }
}

function actualizarEstadoSolicitud2() {
    var datos = {
        "action": "actualizarEstado",
        "idSolicitud": solicitudActual,
        "nuevoEstado": $("#cbxEstado").val(),
        "txtCRM": $("#txtCRM").val(),
        "cbxTipoRegistro": $("#cbxTipoRegistro").val(),
        "txtNumSocio": $("#txtNumSocio").val(),
        "txtPass": $("#txtPass").val(),
        "txtObs": $("#txtObs").val(),
        "txtNumIdenti": $("#txtNumIdenti").val(),
        "cbxTipoIdenti": $("#cbxTipoIdenti").val(),
        "txtActa": $("#txtACP").val(),
        "txtObjeto": $("#txtObjeto").val(),
        "id": id_s,
    };

    $("#btnAceptarA").prop("disabled", true);
    $("#lblCargando").text("Guardando cambios...");
    $("#divCargando").show();

    datos = $.param(datos);
    //alert(datos);
    $.ajax({
        type: "POST",
        url: "php/solicitudes.php",
        contentType: "application/x-www-form-urlencoded;charset=UTF-8",
        data: datos,
        dataType: "json",
        success: function(data, textStatus, jqXHR) {
            if (data.status != "user") {
                $("#divCargando").hide();
                $("#btnAceptarA").prop("disabled", false);
                var estado = $("#cbxEstado").val();
                if (estado == 4 && data.actualizado == '1') {
                    alert("Este usuario ya se encuentra actualizado");
                } else {
                    if (data.status == "correcto") {
                        cargarSolicitudes();
                        contenidoMisSolicitudes();
                        alert("Cambios guardados exitosamente.");
                        $("#modalAsociado").modal('hide');
                    } else {
                        alert("Ha ocurrido un error al actualizar el estado de la solicitud: " + data.msj);
                    }
                }
            } else {
                alert("Sesión Terminada, actualiza la página" + data.msj);
            }
        },
        error: function(jqxhr, status, errorGenerado) {
            $("#divCargando").hide();
            $("#btnAceptarA").prop("disabled", false);
            alert("Ha ocurrido un error al actualizar el estado de la solicitud: " + jqxhr.responseText);
        }
    });
}

/*
//Mostrar Solicitud Certificado
function mostrarSolicitudCertificado(idSolicitud, tipo, estado, data,cliente) {

	solicitudActual = idSolicitud;
	faltaFactura=0;
	$("#modalCertificado").modal("show");

	var datos = {
		"action": "obtenerSolicitud",
		"idSolicitud": idSolicitud,
	};
	datos = $.param(datos);

	$("#tabDetalleC a:first").tab("show");
	$("#lblCargando2").text("Cargando Solicitud...");
	$("#divCargando2").show();
	$.ajax({
		type: "POST",
		url: "php/solicitudesCertificado.php",
		contentType: "application/x-www-form-urlencoded;charset=UTF-8",
		data: datos,
		dataType: "json",
		success: function(data, textStatus, jqXHR) {
			if (data.status == "correcto") {
				var contenido = "<div class=\"form-group\">N&uacutemero de Solicitud: <b>" + data.numero + "</b><br>Tipo de Solicitud: <b>";
				switch(tipo) {

					case 3: contenido += "CERTIFICADO NOM GRANEL"; break;
					case 4: contenido += "CERTIFICADO ENVASADO NACIONAL";break;
					case 5: contenido += "CERTIFICADO EXPORTACI&Oacute;N ";break;
					case 6: contenido += "CERTIFICADO PROMOCI&Oacute;N ";break;
					case 7: contenido += "VERIFICACI&Oacute;N EN ENVASADO";break;
					case 8: contenido += "ENTREGA DE HOLOGRAMAS ";break;
					case 9: contenido += "VERIFICACI&Oacute;N EN ENVASADO Y ENTREGA DE HOLOGRAMAS";break;
					case 8: contenido += "RENOVACI&Oacute;N ENVASADO NACIONAL ";break;
				}

				contenido += "</b><br>Num. de Asociado: <b>" + data.no_cliente;
				contenido += "</b><br>Tipo de Persona: <b>";

				switch(data.persona) {
					case 1: contenido += "FISICA</b><br>Nombre: <b>"; break;
					case 2: contenido += "MORAL</b><br>Raz&oacuten Social: <b>"; break;
				}

				var descEstado;
				switch(estado) {
					case 1: descEstado = "EN REVISION"; break;
					case 1: descEstado = "PENDIENTE DE PAGO"; break;
					case 5: descEstado = "TERMINADA"; break;
					case 6: descEstado = "RECHAZADA"; break;
					case 7: descEstado = "RECIBIDA"; break;
					case 8: descEstado = "PROGRAMANDO VISITA"; break;
					case 9: descEstado = "VERIFICANDO"; break;
					case 10: descEstado = "VALIDANDO"; break;
					case 11: descEstado = "VISITA AGENDADA"; break;
					case 12: descEstado = "REVISI&OacuteN POR EL CLIENTE"; break;
					case 13: descEstado = "IMPRIMIENDO"; break;
					case 14: descEstado = "RESPUESTA DEL CLIENTE"; break;
					case 16: descEstado = "CANCELADA"; break;
				}
				complemento='';
				complemento2='';
				if(data.hologramas==1) complemento='<br><br><b>Solicita Entrega de hologramas</b>';
				if(data.fechaVerificacion!='') complemento2='<br>Fecha de Verificaci&oacuten: <b>'+data.fechaVerificacion+'</b>, a cargo de <b>'+data.verificador+'.</b>';
				contenido += data.nombre  + "</b><br>RFC: <b>" + data.rfc + "</b><br>Representante Legal: <b>" + data.representante + "</b><br>Persona Autorizada: <b>" + data.autorizada +
							"</b><br>Tel&eacutefono: <b>" + data.telefono +
							"</b><br>Fax: <b>" + data.fax + "</b><br>Correo electr&oacutenico: <b>" + data.correo +
							"</b><br>--------------------------------------------------------------------------------------------------------------"+
							"<br>El producto se encuentra en: <b>" + data.domicilio + "</b>" +
							"<br>Responsable Instalaci&oacuten: <b>" + data.responsable + "</b><br>" +
							"Informaci&oacuten adicional: <b>"+data.infoAdicional+ "</b><br>"+
							complemento+complemento2+
							"<br><br>Estado actual de la Solicitud: <b>" + descEstado+ "</b><br>"+

							"Observaciones del Cliente: <b>" + data.observacionesCliente+ "</b><br>";

				contenido += "<div class=\"form-group\" id=\"divEstado\"" + (((estado==7)||(estado==11)||(estado==2))&& nivel==1 ? "" : "hidden") + "><label for=\"cbxEstado\" class=\"col-xs-4 control-label\">Cambiar estado a:</label>" +
							"<div class=\"col-xs-8\"><select class=\"form-control\" name=\"cbxEstadoC\" id=\"cbxEstadoC\" onchange=\"mostrarOcultarC(this)\">";


				switch(estado) {
					case 7:
						if (data.servicio==1)
							contenido += "<option value=\"2\">PENDIENTE DE PAGO</option>";
						else if(data.hologramas==1)
							contenido += "<option value=\"1\">EN REVISION</option>";
						else
							contenido += "<option value=\"8\">PROGRAMANDO VISITA</option>";

						contenido += "<option value=\"6\">RECHAZADA</option>";
					break;
					case 2:
						if(data.hologramas==1)
							contenido += "<option value=\"1\">EN REVISION</option>";
						else
							contenido += "<option value=\"8\">PROGRAMANDO VISITA</option>";

						contenido += "<option value=\"6\">RECHAZADA</option>";
					break;
					case 11:
						contenido += "<option value=\"9\">VERIFICAR</option>"; 
					break;
				}

				//contenido += "</select></div></div>";
				contenido +="</select></div></div><form id=\"datafiles\" enctype=\"multipart/form-data\">"+
							"<div class=\"collapse\" id=\"divComplemento\"><label for='chkComplemento' class='col-xs-4 control-label'>Complemento:</label>"+
							"<div class='col-xs-6'><input type='checkbox' name='chkComplemento' id='chkComplemento' onchange='mostrarComplemento(this)' checked>"+
							"</div></div>"+
							"<div class=\"collapse\" id=\"divNumSolicitud\">" +
							"<label for=\"txtNumSolicitud\" class=\"col-xs-4 control-label\">No. de Solicitud:</label><div class=\"col-xs-6\">" +
							"<input type=\"number\" name=\"txtNumSolicitud\" id=\"txtNumSolicitud\" value=\"\" class=\"form-control\"  placeholder=\"Solo los 4 digitos del N&uacute;mero de la solicitud\" maxlength=\"8\">" +
							"</div></div>"+
							"<div class=\"collapse\" id=\"divFactura\">"+
							"<div class=\"form-group\"><label for=\"fileFactura\" class=\"col-xs-4 control-label\">Factura o Proforma:</label><div class=\"col-xs-6\">"+
							"<input type=\"file\" name=\"fileFactura\" id=\"fileFactura\" required onchange=\"verificarArchivo(this)\" accept=\"application/pdf\"/></div></div></div>"+
							"<div class=\"collapse\" id=\"divObs\"><label for=\"txtObsC\" class=\"col-xs-12 control-label\">Observaciones:</label>" +
							"<textarea class=\"form-control\" name=\"txtObsC\" id=\"txtObsC\" rows=\"2\"></textarea></div></form>";

				$("#tabGeneralesC").html(contenido);

				$("#chkComplemento").bootstrapToggle({
						on: "No",
						off: "Si",
					});
				if(estado==11)
				{
					//$("#divNumSolicitud").collapse("show");
					//$("#chkComplemento").bootstrapToggle("on");
					$("#divComplemento").collapse("show");
				}


				$("#divObs").collapse("show");
				contenido = "";

				for(i = 0; i < data.productos.length; i++) {

					if (contenido.length > 0) contenido += "<br>";

					var categoria;
					switch(data.productos[i].categoria) {
						case 1: categoria = "MEZCAL"; break;
						case 2: categoria = "MEZCAL ARTESANAL"; break;
						case 3: categoria = "MEZCAL ANCESTRAL"; break;
					}

					var clase;
					switch(data.productos[i].clase) {
						case 1: clase = "JOVEN"; break;
						case 2: clase = "BLANCO"; break;
						case 3: clase = "MADURADO EN VIDRIO"; break;
						case 4: clase = "REPOSADO"; break;
						case 5: clase = "A&Ntilde;EJO"; break;
						case 6: clase = "ABOCADO CON"; break;
						case 7: clase = "DESTILADO CON"; break;
					}

					contenido += "<b> PRODUCTO " + data.productos[i].clave+ " MARCA "+ data.productos[i].marca;
					contenido += "</b><br>No. de Lote Granel: <b>" + data.productos[i].loteGranel + "</b><br>No. Lote Envasado: <b>" + data.productos[i].loteEnvasado +
								"</b><br>Categoria: <b>" + categoria + "</b><br>Clase: <b>" +clase+"</b><br>Cont. Alcoholico: <b>" + data.productos[i].alcVolETQ+
								"</b><br>Abocantes/Ingredientes: <b>" + data.productos[i].ingredientes + "</b><br>No. Analisis FQ: <b>"+ data.productos[i].analisisFQ;

					contenido +="</b><br><table class=\"table table-striped\" id="+data.productos[i].id+"><thead><tr><th>Cnt. Net. por Botella</th><th>Unidad</th><th>No. Botellas</th><th>Botellas por caja</th><th>Cajas</th></thead><tbody>";

					for(j = 0; j < data.productos[i].presentacion.length; j++)
					{
						contenido+="<tr><td>"+data.productos[i].presentacion[j].contenido +"</td><td>"+data.productos[i].presentacion[j].unidad+"</td><td>"+data.productos[i].presentacion[j].botellas+"</td><td>"+data.productos[i].presentacion[j].botellasxcaja+"</td><td>"+data.productos[i].presentacion[j].numCajas+"</td></tr>";
					}
					contenido+="</tbody></table>";
				}

				$("#tabProductos").html(contenido);

				//Datos Exportaci�n
				contenido='';
				for(i = 0; i < data.personas.length; i++) {

					if(data.personas[i].figura==1)
					{
						nombreImportador=data.personas[i].nombre;
						domImportador=data.personas[i].domicilio;
						contenido += " Nombre: <b>" + data.personas[i].nombre;
						contenido += "</b><br>Domicilio: <b>" + data.personas[i].domicilio + "</b><br>Pais destino: <b>" + data.pais +
						"</b><br>Aduana Salida: <b>" + data.aduana	+ "</b><br>Fraccion Arancelaria: <b>" + data.arancel+'</b><br><br>';
					}

					else if(data.personas[i].figura==2 || data.personas[i].figura==4 || data.personas[i].figura==3)
					{
						var titulo1=(data.personas[i].figura==2)?' CONSIGNATORIO':((data.personas[i].figura==4)?'POR ORDEN D&Eacute;':'PARA');
						contenido += "<b>"+titulo1+"</b><br> Nombre: <b>"+ data.personas[i].nombre;
						contenido += "</b><br>Domicilio: <b>" + data.personas[i].domicilio + "</b><br>RFC: <b>" + data.personas[i].rfc +"</b><br><br>";
					}

					else
					{

						contenido += "<b>PARA</b><br>Nombre: <b>"+ data.personas[i].nombre;
						contenido += "</b><br>Domicilio: <b>" + data.personas[i].domicilio + "</b><br>RFC: <b>" + data.personas[i].rfc +"</b>";
					}

				}
				$("#tabExportacion").html(contenido);

				/*if(data.ndestino!=null)
				{
					contenido='';

					contenido += "<b> NOMBRE: " + data.ndestino;
					contenido += "</b><br>Domicilio: <b>" + data.dom_dest + "</b><br>Pais destino: <b>" + data.pais +
					"</b><br>Aduana Salida: <b>" + data.aduana	+ "</b><br>Fraccion Arancelaria: <b>" + data.arancel;

					$("#tabExportacion").html(contenido);
				}**

				contenido='';
				contenido =  "<div class=\"panel panel-default\"><div class=\"panel-heading\"><h6 class=\"panel-title\">Datos Generales</h6></div>" +
							"<div class=\"panel-content\">" +
							"<div class=\"list-group\" style=\"margin-bottom:0px\">";
				var productoAnt = null;
				var producto;
				var titulo;

				for(i = 0; i <data.documentos.length; i++) {
					if (data.documentos[i].producto !== productoAnt && data.documentos[i].producto !== null) {
						productoAnt = data.documentos[i].producto;
						producto = buscarProductos(data.productos, productoAnt);
						var categoria;
						switch(producto.categoria) {
							case 1: categoria = "MEZCAL"; break;
							case 2: categoria = "MEZCAL ARTESANAL"; break;
							case 3: categoria = "MEZCAL ANCESTRAL"; break;
						}

						var clase;
						switch(producto.clase) {
							case 1: clase = "JOVEN"; break;
							case 2: clase = "BLANCO"; break;
							case 3: clase = "MADURADO EN VIDRIO"; break;
							case 4: clase = "REPOSADO"; break;
							case 5: clase = "A&Ntilde;EJO"; break;
							case 6: clase = "ABOCADO CON"; break;
							case 7: clase = "DESTILADO CON"; break;
						}

						titulo = "<b>" + producto.marca + "</b> " +categoria +" "+clase+" %ALC. VOL. "+producto.alcVolETQ;

						//if (instalacion.alias.length > 0) titulo += ": " + instalacion.alias;

						//titulo += "</b> UBICADA EN " + instalacion.domicilio + ", " + instalacion.colonia + ", " + instalacion.municipio + ", " + instalacion.estado + ", " + instalacion.cp;
						contenido += "</div></div></div><div class=\"panel panel-default\"><div class=\"panel-heading\"><h6 class=\"panel-title\">" + titulo + "</h6></div>" +
							"<div class=\"panel-content\"><div class=\"list-group\" style=\"margin-bottom:0px\">";
					}

					contenido += "<a href=\"php/descargar_documento.php?id=" + data.documentos[i].id + "\" class=\"list-group-item\" target=\"_blank\"><img src=\"images/pdf-icon.png\" class=\"pull-left\"/>";

					switch(data.documentos[i].tipo) {
						case 32: contenido += "An&aacutelisis de Laboratorio"; break;
						case 33: contenido += "Juego de Etiqueta"; break;
						case 34: contenido += "Muestra Embalaje"; break;
						case 35: contenido += "Factura"; faltaFactura=1; break;
						case 36: contenido += "Invitaci&oacuten u Orden de Compra"; break;
						case 38: contenido += "Solicitud de Servicio"; break;
						case 39: contenido += "Plan del cliente"; break;
						case 40: contenido += "Dictamen"; break;
						case 41: contenido += "Certificados"; break;
						case 42: contenido += "Informe de Incumplimiento"; break;
						case 43: contenido += "Decisi&oacuten Sobre la Certificaci&oacuten"; break;
						default: contenido += "Tipo desconocido: " + data.documentos[i].tipo;

					}

					contenido += "</a>";
				}
				contenido += "</div></div></div>";
				$("#tabDocumentacionC").html(contenido);
				$("#txtObsC").val(data.observaciones);
				//mostrarOcultarC(document.getElementById("cbxEstado"));
				if(faltaFactura==0 && nivel==1)$("#divFactura").collapse("show");

				if (((estado==7)||(estado==11)||(faltaFactura==0)||(estado==2))&& nivel==1) {
					$("#btnAceptarC").show();
				}
				else {
					$("#btnAceptarC").hide();

				}
				
				/*$("#chkComplemento").change(function() {
					if ($(this).prop("checked")) $("#divNumSolicitud").collapse("show");
					else $("#divNumSolicitud").collapse("hide");
				});**
				

				$("#divCargando2").hide();
				hologramas=data.hologramas;
			}
			else {
				$("#divCargando2").hide();
				alert("Ha ocurrido un error al cargar la Solicitud: " + data.msj + jqXHR.responseText);
			}
		},
		error: function(jqxhr, status, errorGenerado) {
			$("#divCargando2").hide();
			alert("Ha ocurrido un error al cargar la Solicitud: " + jqxhr.responseText);
		}
	});
}

function mostrarComplemento (e) {
	if (e.checked) {
		$("#divNumSolicitud").collapse("show");
	}
	else {
		$("#divNumSolicitud").collapse("hide");	
	}
}

function actualizarEstadoSolicitudC() {
	$("#btnAceptarC").prop("disabled", true);
	$("#lblCargando2").text("Guardando cambios...");
	$("#divCargando2").show();
	datos = $("#datafiles").serializefiles();
	datos.append("action", "actualizarEstadoC");
	datos.append("idSolicitud", solicitudActual);
	datos.append("no_cliente", cliente);
	datos.append("nuevoEstado", $("#cbxEstadoC").val());
	datos.append("id", id_s);
	$.ajax({
		type: "POST",
		url: "php/solicitudesCertificado.php",
		contentType: false,//"application/x-www-form-urlencoded;charset=UTF-8",
		processData: false,
		data: datos,
		dataType: "json",
		success: function(data, textStatus, jqXHR) {
			$("#divCargando2").hide();
			$("#btnAceptarC").prop("disabled", false);
			if (data.status == "correcto") {
				cargarSolicitudes();
				alert("Cambios guardados existosamente.");
				$("#modalCertificado").modal('hide');
			}
			else {
				alert("Ha ocurrido un error al actualizar el estado de la solicitud: "+ data.msj);
			}
		},
		error: function(jqxhr, status, errorGenerado) {
			$("#divCargando2").hide();
			$("#btnAceptarC").prop("disabled", false);
			alert("Ha ocurrido un error al actualizar el estado de la solicitud: " + jqxhr.responseText);
		}
	});
}*/


function mostrarOcultar(sel) {
    if (nivel == 1) {
        if (sel.value == "4") {
            $("#divNumCliente").collapse('show');
            $("#divCRM").collapse('show');
            $("#divTipoRegistro").collapse('show');

            if (contrato > 0) {
                $("#divNumIdenti").collapse('show');
                $("#divTipoIdenti").collapse('show');
            }

            /*if(pedirPass>0)

            	$("#divPass").collapse('show');*/
            if (moral > 0) {
                $("#divActa").collapse('show');
                $("#divObjeto").collapse('show');
            }
        } else {
            if ($("#divNumCliente").is(":visible")) $("#divNumCliente").collapse('hide');
            if ($("#divNumIdenti").is(":visible")) $("#divNumIdenti").collapse('hide');
            if ($("#divTipoIdenti").is(":visible")) $("#divTipoIdenti").collapse('hide');
            if ($("#divPass").is(":visible")) $("#divPass").collapse('hide');
            if ($("#divActa").is(":visible")) $("#divActa").collapse('hide');
            if ($("#divObjeto").is(":visible")) $("#divObjeto").collapse('hide');
            if ($("#divCRM").is(":visible")) $("#divCRM").collapse('hide');
        }
    }

}

/*/Mostrar/Ocultar para certificado
function mostrarOcultarC(sel) {
	if(nivel==1)
	{
		if (sel.value !='6') {
			if(faltaFactura==0)
			{
				$("#divFactura").collapse('show');
			}
		}
		else {
			//if ($("#divNumSolicitud").is(":visible")) $("#divNumSolicitud").collapse('hide');
			if ($("#divFactura").is(":visible")) $("#divFactura").collapse('hide');
		}
	}

}*/

function buscarInstalacion(instalaciones, id) {
    for (var i = 0; i < instalaciones.length; i++) {
        if (instalaciones[i].id === id) return instalaciones[i];
    }

    return undefined;
}

function buscarMarca(marcas, clave) {
    for (var i = 0; i < marcas.length; i++) {
        if (marcas[i].id === clave) return marcas[i];
    }

    return undefined;
}
var pr = 0;
/*function buscarProductos (productos, id) {

	for (var i = 0; i < productos.length; i++) {
		if (productos[i].id === id) {
			return productos[i];
		}
	}

	return undefined;
}*/

function zeroPad(num, places) {
    var zero = places - num.toString().length + 1;
    return Array(+(zero > 0 && zero)).join("0") + num;
}


function exportarExcel() {
    var datos = {
        "action": "exportarSolicitudes",
    };
    $('#myPleaseWait').modal('show');
    $.ajax({
        type: "POST",
        url: "php/solicitudes.php",
        contentType: "application/x-www-form-urlencoded;charset=UTF-8",
        data: datos,
        dataType: "json",
        success: function(data, textStatus, jqXHR) {
            if (data.status == "correcto") {
                window.open('https://serviciosenlinea.amma.org.mx/reportesExcel/listaSolicitudes.xlsx', '_blank');
                //window.open("https://www.w3schools.com", "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=400,height=400");
                $('#myPleaseWait').modal('hide');
            } else {
                $('#myPleaseWait').modal('hide');
                alert("Ha ocurrido un error al descargar el archivo " + data.msj);

            }
        },
        error: function(jqxhr, status, errorGenerado) {

            alert("Ha ocurrido un error al descargar el archivo " + jqxhr.responseText);
        }
    });
}

/********************************************************************************************************************************************************************
 ********************************************FUNCIONES PARA SOLICITUDES TRASLADO,VARIOS,MADURACIO,AJUSTE**********************************************************/
function datePicker(identificador) {
    $(identificador).datepicker({
        showOn: "button",
        buttonImage: "../images/date.png",
        buttonImageOnly: true,
        buttonText: "Seleccionar Fecha",
        firstDay: 1,
    });
}

//***********************Enviar Mensaje****************//Moises
function enviarMensaje() {
    if (!($("#txtObs").val().trim() == "")) {
        $("#divCargando").show();
        var formData = new FormData();
        var files = $('#fileDocumentoAdjunto')[0].files[0];
        formData.append('file', files);
        formData.append('id', id_s);
        formData.append('caso', 1);
        formData.append('folio', numero);
        formData.append('mensaje', $("#txtObs").val().trim());
        formData.append('correoMensaje', correoMensaje);
        formData.append('status', '');
        $.ajax({
            url: 'php/enviar_mensaje.php',
            type: 'post',
            data: formData,
            contentType: false,
            processData: false,
            success: function(data) {
                if (!(data.status == "error")) {
                    $("#fileDocumentoAdjunto").val('');
                    $('#contMensajes').load('php/historial_mensajes.php?folio=' + numero);
                    alert("Mensaje enviado");
                    $('#btnEnviarMensaje').addClass("disabled");
                    $('#txtObs').val("");
                    $("#divCargando").hide();
                } else {
                    alert("Error al enviar el mensaje ");
                    $("#divCargando").hide();
                }
            }
        });
    } else {
        alert("Mensaje Vacio");
    }
}
//***********************Enviar Mensaje****************//Moises

//***********************Enviar Mensaje****************//Moises
function enviarMensajeAbandono(status) {
    $("#divCargando").show();
    $.ajax({
        url: "php/enviar_mensaje.php",
        method: "POST",
        data: {
            "id": id_s,
            caso: "2",
            folio: numero,
            mensaje: "", //mensaje
            correoMensaje: correoMensaje,
            status: status,
            file: ''
        },
        dataType: "json",
        success: function(data) {
            if (!(data.status == "error")) {
                alert("Mensaje enviado");
                $("#divCargando").hide();
            } else {
                alert("Error al enviar el mensaje ");
                $("#divCargando").hide();
            }
        }
    });
}
//***********************Enviar Mensaje****************//

//***********************Notificaciones****************//
function MostrarSolicitudes(notificaciones) {
    var contenido = "";
    /*contenido += ""+"<table width=\"100%\" class=\"table-bordered table-hover\" style=\"text-align:center;\" id=\"myTable\">" +
    	"<tr style=\"margin-top: 15px; margin-bottom: 15px;\"><th width=\"120\" style=\"text-align:center;margin-top: 15px; margin-bottom: 15px;\">Folio</th><th width=\"180\""+
    	"style=\"text-align:center;\">Fecha de envio</th><th width=\"200\" style=\"text-align:center;\">Tipo</th><th width=\"220\" style=\"text-align:center;\">Nombre</th><th style=\"text-align:center;\">Acción</th></tr>";*/
    for (var i = 0; i < notificaciones.length; i++) {
        var status;
        var clase = "";
        switch (notificaciones[i].estado) {
            case "1":
                status = "Nuevo registro";
                clase = "info";
                break;
            case "2":
                status = "Observaciones corregidas";
                clase = "success";
                break;
            case "3":
                status = "Prorroga vencida";
                clase = "danger";
                break;
            default:
                break;
        }
        contenido += "<tr><td style=\"text-align:center;\">" + notificaciones[i].folio + "</td><td style=\"text-align:center;\">" + notificaciones[i].fecha_envio + "</td>" +
            "<td style=\"text-align:center;\" class=\"" + clase + "\">" + status + "</td><td style=\"text-align:center;\">" + notificaciones[i].nombre + "</td>" +
            "<td style=\"text-align:center;\"><button type=\"button\" class=\"btn btn-info\" style=\"margin-top: 5px; margin-bottom: 5px;\" onclick=\"mostrarSolicitudAsociado('" + notificaciones[i].id_solicitud + "','" + notificaciones[i].tipo + "','" + notificaciones[i].status + "','');\">" +
            "<span class=\"glyphicon glyphicon-eye-open\" aria-hidden=\"true\"></span> &nbspVer solicitud</button></td></tr>";
    }
    //contenido += "</table>";
    $("#NotContenido").html(contenido);
    $("#divCargandoNotificaciones").addClass('collapse');
}

/*function MostrarAsignarSolicitudes(notificaciones) {
	var contenido="";
	for (var i = 0; i < notificaciones.length; i++) {
		var status;
		var clase="";
		switch (notificaciones[i].estado) {
			case "1":
				status="Nuevo registro";
				clase="info";
				break;
			default:
				status="Desconocido";
				clase="danger";
				break;
		}
		contenido += "<tr><td style=\"text-align:center;\">" + notificaciones[i].folio + "</td><td style=\"text-align:center;\">" + notificaciones[i].fecha_envio + "</td>"+
		"<td style=\"text-align:center;\" class=\""+ clase +"\">" + status + "</td><td style=\"text-align:center;\">" + notificaciones[i].nombre + "</td>"+
		"<td style=\"text-align:center;\"><button type=\"button\" class=\"btn btn-info\" style=\"margin-top: 5px; margin-bottom: 5px;\" onclick=\"mostrarAsignarSolicitud('"+notificaciones[i].id_solicitud + "');\">" +
		"<span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span> &nbspAsignar Solicitud</button></td></tr>";
	}
	//contenido += "</table>";
	$("#ContenidoAsignarSolicitud").html(contenido);

	$("#divCargandoNotificacionesAsignar").addClass('collapse');
}*/

function mostrarAsignarSolicitud(solicitud, tipo, id) {
    console.log(solicitud);
    solicitud_ejecutiva = solicitud;
    tipo_asignacion = tipo;
    id_asignacion = id;
    $.ajax({
        url: "php/estados_solicitudes.php",
        method: "POST",
        data: {
            caso: "obtenerEjecutivas",
            status: "",
            id: clvuser
        },
        dataType: "json",
        success: function(data) {
            var lista_ejecutivas = '<div class="form-group"><label for="formEntrada" class="control-label">Selecciona la ejecutiva que atendera la solicitud:</label>' +
                '<div class=""><select class="form-control" name="seEjecutiva" id="seEjecutiva" >';
            for (var i = 0; i < data.ejecutivas.length; i++) {
                lista_ejecutivas += '<option value=' + data.ejecutivas[i].id + '>' + data.ejecutivas[i].nombre + '</option>';
            }
            lista_ejecutivas += '</select></div></div>';
            $("#divListaEjecutivas").html(lista_ejecutivas);
            $("#modalAsignarEjecutiva").modal('show');
        }
    });
}

function notificaciones(caso) {
    $.ajax({
        url: "php/estados_solicitudes.php",
        method: "POST",
        data: {
            caso: "notificacion",
            status: "",
            "id": id_s
        },
        dataType: "json",
        success: function(data) {
            switch (caso) {
                case 1:
                    if (data.noti == 1) {
                        $('#lblNumNot').text(data.notificaciones.length);
                    }
                    $("#modalNotificaciones").modal('show');
                    $("#divCargandoNotificaciones").removeClass('collapse');
                    MostrarSolicitudes(data.notificaciones);
                    break;
                case 0:
                    if (data.noti == 1) {
                        $('#lblNumNot').text(data.notificaciones.length);
                    }
                    break;
                default:
                    break;
            }
        }
    });
}
//***********************Notificaciones****************


function notificaciones_sin_asignar() {
    $('#ContenidoAsignarSolicitud').load('php/tablas/tabla_sin_asignar.php');
    $('#ContenidoSinAceptar').load('php/tablas/tabla_sin_aceptar.php');
    $('#ContenidoAceptadas').load('php/tablas/tabla_en_revision.php');
    $('#ContenidoFinalizadas').load('php/tablas/tabla_finalizadas.php');
    $("#modalListaSolicitudSinAsignar").modal('show');
    $("#divCargandoNotificacionesAsignar").addClass('collapse');
}

function notificaciones_mis_solicitudes() {
    contenidoMisSolicitudes();
    /*$('#ContenidoMisRevisando').load('php/tablas/tabla_sin_aceptar.php');
    $('#ContenidoMisAceptadas').load('php/tablas/tabla_en_revision.php');
    $('#ContenidoMisNotificaciones').load('php/tablas/tabla_finalizadas.php');*/
    $("#modalMisSolicitudes").modal('show');
    $("#divCargandoMisSolicitudes").addClass('collapse');
}

function contenidoMisSolicitudes() {
    $('#ContenidoMisNuevas').load('php/tablas/tabla_nueva.php?eje=' + clvuser);
    $('#ContenidoMisRevisando').load('php/tablas/tabla_aceptadas.php?eje=' + clvuser);
    $('#ContenidoMisAceptadas').load('php/tablas/tabla_historial.php?eje=' + clvuser);
    $('#ContenidoMisNotificaciones').load('php/tablas/tabla_correcciones.php?eje=' + clvuser);
}

function regresarRevisor(solicitud, id) {
    $.ajax({
        url: "php/solicitudes.php",
        method: "POST",
        data: {
            action: "regresar_revisor",
            solicitud: solicitud,
            id: id,
            user: clvuser
        },
        dataType: "json",
        success: function(data) {
            if (data.status) {
                $('#ContenidoAsignarSolicitud').load('php/tablas/tabla_sin_asignar.php');
                $('#ContenidoSinAceptar').load('php/tablas/tabla_sin_aceptar.php');
                $('#ContenidoAceptadas').load('php/tablas/tabla_en_revision.php');
                $('#ContenidoFinalizadas').load('php/tablas/tabla_finalizadas.php');
            } else {
                alert("error");
            }
        }
    });
}

function aceptarRevision(solicitud, id) {
    solicitud_ejecutiva = solicitud;
    id_asignacion = id;
    $("#modalConfirmarRevision").modal('show');
}

function getFechaActual(dias) {
    var myDate = new Date();
    var dia = (myDate.getDate() + dias);
    var mes = (myDate.getMonth() + 1);
    var displayDate = myDate.getFullYear() + '-' + pad(mes, 2) + '-' + pad(dia, 2);
    return displayDate;
}

function pad(str, maxi) {
    "use strict";
    str = str.toString();
    return str.length < maxi ? pad("0" + str, maxi) : str;
}
/********************************************************************************************************************************************************************
 ********************************************FIN FUNCIONES PARA SOLICITUDES TRASLADO,VARIOS,MADURACIO,AJUSTE**********************************************************/
$('#tabHistorialNot').DataTable({
    order: [
        [0, 'desc']
    ],
    searching: true,
    paging: true,
    info: true,
    destroy: true,
    language: idioma_espanol
});

var idioma_espanol = {
    "sProcessing": "Procesando...",
    "sLengthMenu": "Mostrar _MENU_ registros",
    "sZeroRecords": "No se encontraron resultados",
    "sEmptyTable": "Ningún dato disponible en esta tabla",
    "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
    "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
    "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
    "sInfoPostFix": "",
    "sSearch": "Buscar:",
    "sUrl": "",
    "sInfoThousands": ",",
    "sLoadingRecords": "Cargando...",
    "oPaginate": {
        "sFirst": "Primero",
        "sLast": "Último",
        "sNext": "Siguiente",
        "sPrevious": "Anterior"
    },
    "oAria": {
        "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
    }
}

function bitacora_consultas(tipo, documento, origen, clientet) {
    var datos = {
        "action": "bitacora_consultas",
        "usuario": clvuser,
        "origen": origen,
        "tipo": tipo,
        "no_cliente": clientet,
        "documento": documento,
        "solicitud": solicitudActual
    };
    $.ajax({
        type: "POST",
        url: "../php/bitacora.php",
        contentType: "application/x-www-form-urlencoded;charset=UTF-8",
        data: datos,
        dataType: "json",
        success: function(data) {
            if (data.status == "correcto") {
                console.log("Guardado");
            }
        },
        error: function() {
            alert("Ha ocurrido un error ");
        }
    });
}