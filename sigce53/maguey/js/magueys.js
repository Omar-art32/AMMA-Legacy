
  function fn_agregare(){
    if($("#registros").val()!=""){
      if($("#scs").val()!=""){
        if($("#sms").val()!=""){
          if($("#especies").val()!=""){
            if($("#plantass").val()!=""){
              if($("#edads").val()!=""){
                cadena = "<tr>";
                cadena = cadena + "<td>" + $("#registros").val() + "</td>";
                cadena = cadena + "<td>" + $("#scs").val() + "</td>";
                cadena = cadena + "<td>" + $("#sms").val() + "</td>";
                cadena = cadena + "<td>" + $("#especies").val() + "</td>";
                cadena = cadena + "<td>" + $("#plantass").val() + "</td>";
                cadena = cadena + "<td>" + $("#edads").val() + "</td>";
                //cadena = cadena + "<td><a class='elimina'><img src='images/delete.png' /></a></td>";
                //cadena = cadena + "<td><a class='text-danger elimina'><span class='glyphicon glyphicon-minus-sign'></span></a></td>";
                cadena = cadena + "<td><a class='text-danger elimina'><span class='glyphicon glyphicon-trash'></span></a></td>";
                $("#grillas tbody").append(cadena);
                /*
                  aqui puedes enviar un conunto de tados ajax para agregar al usuairo
                  $.post("agregar.php", {ide_usu: $("#valor_ide").val(), nom_usu: $("#valor_uno").val()});
                */
                Limpiar();
                fn_dar_eliminar();
                fn_cantidad();
                alert("Registro Agregado");
              }else{
                alert("Ingrese la edad");
                return false;
              }
            }else{
              alert("Ingrese un numero de plantas");
              return false;
            }
          }else{
            alert("Seleccione una especie");
            return false;
          }
        }else{
          alert("Ingrese una distancia entre plantas");
          return false;
        }
      }else{
        alert("Ingrese una distancia entre surcos");
        return false;
      }
    }else{
      alert("Seleccione un registro de maguey");
      return false;
    }
  };

  function Limpiar(){
    document.magueys.registros.value="";
    document.magueys.scs.value="";
    document.magueys.sms.value="";
    document.magueys.especies.value="";
    document.magueys.plantass.value="";
    document.magueys.edads.value="";
    //document.magueys.local.focus();
  }

  function fn_dar_eliminare(){
    $("a.elimina").click(function(){
      id = $(this).parents("tr").find("td").eq(0).html();
        respuesta = confirm("Desea Eliminar el Registro: " + id);
          if (respuesta){
          $(this).parents("tr").fadeOut("normal", function(){
            $(this).remove();
            alert("Registro " + id + " Eliminado")
            /*
              aqui puedes enviar un conjunto de datos por ajax
              $.post("eliminar.php", {ide_usu: id})
            */
        })
      }
    });
  };

  function fn_cantidade(){
    cantidad = $("#grillas tbody").find("tr").length;
    $("#span_cantidad").html(cantidad);
  };


	function loadPredios(){
        var tableTest=$('#tablaPredios').bootstrapTable({
            onDblClickRow: function (row) {
            //    cargarCertificado(row.id);
            },
            url: "php/loadPredios.php",
            queryParams:function(p){
                //console.log("p");
                //console.log(p);

                return {
                    limit:    p.limit,
                    offset:   p.offset,
                    sort:     p.sort,
                    order:    p.order,
                    search:   p.search,
                    idus:     clvuser,
                    fechaini: '',
                    fechafin: '',
                };
            },
            showRefresh: true,
            search: true,
            showToggle: true, //
            showColumns: true, // menu muestra columnas
            checkboxHeader: true,
            toolbar: '#toolbar', // hace referencia al dom que tiene el toolbar
            columns: [
                {
                                field: 'id_paraje',
                                title: 'ID PARAJE',
                                sortable:true
                },{
                                field: 'paraje',
                                title: 'PARAJE',
                                sortable:true
                },{
                                field: 'id_cliente',
                                title: '# CLIENTE',
                                sortable:true
                },{
                                field: 'nombrep',
                                title: 'NOMBRE',
                                sortable:true
                },{
                                field: 'guias',
                                title: 'GUÍAS',
                                sortable:true
                },/*{
                                field: 'lng',
                                title: 'LONGITUD',
                                sortable:true
                },*/{
                                field: '',
                                title: 'COMPROBANTE',
                                sortable:false,
                                formatter: operateFormatter,
                              //  events: window.operateEvents,
                },{
                                field: '',
                                title: 'ACCIÓN',
                                sortable:false,
                                visible: true,
								width: 150,
                                formatter: operateFormatter2
                }
             ],
            pagination: true ,
            sortStable: true,
            pageNumber: 1, // pagina q se muestra por default
            pageSize: 10,
            //Total de resultados q se muestran debe ser menor y igual al numero q comience pageList para q sea necesario el pagaList y se  pueda mostrar
            pageList: [10, 25, 50, 100],//
            smartDisplay: true,
            sidePagination: "server",
            // showPaginationSwitch: true,
            paginationVAlign: "bottom",//formato de botones en paginacion
            cache: false,
            rowStyle: "rowStyle",
            showColumns: true,
            maintainSelected: true,
            rowStyle: "pintaDictamenes"
	});

    }

    function operateFormatter(value, row, index) {
        if(row.docpro != "") {
            return [
              '<center><a class="Consulta" href="'+row.docpro+'" title="Abrir Documento" target="_blank">',
                '<span style="font-size: 1.8em; color: Green;"><i class="fa fa-file-text" aria-hidden="true"></i></span>',
                '</a></center>'
            ].join('');
        } else
            return[''].join('');

    }

    function operateFormatter2(value, row, index) {
        return [
          /*'<center><a class="editR" href="javascript:void(0)" title="Editar" onclick=editarPredio("'+row.id_paraje+'");>',
          '<span style="font-size: 2em; color: Orange;"><i class="fa fa-pencil-square" aria-hidden="true"></i></span>',
          '</a>&nbsp; &nbsp; ',*/
          '<a class="editR" href="javascript:void(0)" title="Agregar Guía de Maguey" onclick=AddGuia("'+row.id_paraje+'",'+row.guias+','+row.guiaso+');>',
          '<span style="font-size: 2em; color: Blue sky;"><i class="fas fa-clipboard-check"></i></span>',
          '</a>&nbsp; </center> '

        ].join('');
    }

	function AddGuia(predio, guias, guiaso) {
		$("#mySumGuia").modal('toggle');
		$("#mParaje").val(predio);
		$("#mGgeneradas").val(guias);
		$("#mGocupadas").val(guiaso);
	}

	function agregarG() {
		ag = $("#mGgeneradas").val();
		oc = $("#mGocupadas").val();
		res = ag - oc;
		if(res >= 10)
			swal("Error","El predio seleccionado tiene Guías disponibles.", "error");
		else {
			$.ajax({
					type: "POST",
					url: "php/loadPredios.php",
					contentType: "application/x-www-form-urlencoded;charset=UTF-8",
					data: {
					  funcion: "agregaG",
					  idp: $("#mParaje").val(),
					  ga: $("#mGagregar").val(),
            idus: clvuser
					},
					datatype: 'json',
					success: function(response) {
					  response = JSON.parse(response);
					  console.log(response);
					  if(response.exito=="1"){
						swal("Procesado !", "Las Guías han sido agregadas", "success");
						$('#tablaPredios').bootstrapTable('refresh');
						$("#mySumGuia").modal('toggle');
					  }else{
						swal("Error","Ha ocurrido un error interno", "error");
					  }
					},
					beforeSend: function() {
					}
			});
		}
	}

	function ImportarPredio(predio) {
		$("#modalPredio").modal('toggle');

	}

	function TransferirPredio() {

		if($("#mnocliente").val() != "" && $("#mpredioa").val() > 0) {
			swal({
				title: "<h3>¿Está seguro de hacer la transferencia de Predio?</h3><br><p>Esta acción no se puede deshacer</p>",
				showCancelButton: true,
				confirmButtonColor: "#5cb85c",
				confirmButtonText: "¡Sí, Transferir!",
				cancelButtonText: "¡Cancelar!",
				content: "input",
				closeOnConfirm: false,
				closeOnCancel: true,
				//showLoaderOnConfirm: true,
				allowEscapeKey: false,
				html: true
			},
			function(isConfirm) {
				if (isConfirm) {

					$("#wrapper").LoadingOverlay("show");
					$.ajax({
							type: "POST",
							url: "php/loadPredios.php",
							contentType: "application/x-www-form-urlencoded;charset=UTF-8",
							data: {
							  funcion: "transferir",
							  mnocliente: $("#mnocliente").val(),
							  idus: clvuser,
									  mpredioa: $("#mpredioa").val()
							},
							datatype: 'json',
							success: function(response) {
							  response = JSON.parse(response);
							  console.log(response);
							  if(response.exito=="1"){
								  $("#wrapper").LoadingOverlay("hide");
								  swal("Procesado !", "Se ha terminado la transferencia del Predio", "success");
								  $('#tablaPredios').bootstrapTable('refresh');
										   $("#modalPredio").modal('toggle');
							  }else{
								$("#wrapper").LoadingOverlay("hide");
								swal("Error","Ha ocurrido un error interno", "error");
							  }
							},
							beforeSend: function() {
							}
					});
				}
			}
			);
		} else
			swal("Error","Debe seleccionar un No. de Control y elegir un Predio para proceder.", "error");
	}

    window.operateEvents = {
        'click .editR': function (e, value, row, index) {
            alert("Edit");
            $.ajax({
                type: "POST",
                url: "php/loadPredios.php",
                contentType: "application/x-www-form-urlencoded;charset=UTF-8",
                data: {
                  tipo: "buscarDPredio",
                  nc: row.id_paraje,
                },
                datatype: 'json',
                success: function(response) {
                  response = JSON.parse(response);
                  console.log(response);
                  if(response.exito=="1"){
                    swal("Procesado !", "El Registro ha sido eliminado", "success");
                    $('#tablaGeo').bootstrapTable('refresh');
                  }else{
                    swal("Error","Ha ocurrido un error interno", "error");
                  }
                },
                beforeSend: function() {
                }
            });
        }
    }
