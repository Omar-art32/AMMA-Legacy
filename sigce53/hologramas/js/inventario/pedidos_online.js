"use strict";
$.jgrid.defaults.responsive = false;
$.jgrid.defaults.styleUI = 'Bootstrap';
$(document).ready(function(e) {
	 $( "#frmEditPO" ).submit(function( event ) {
     event.preventDefault();
	 //valida_form();
	 });
    $("#by_asoc_online").autocomplete({
		source: "php/inventario/suggest_no_cliente_online.php",//MISMA FUNCION QUE LOS RECIBOS
		minLength: 1,
		maxRows: 15,
		select: function(e,ui){ filtra_online('no_cliente',ui.item.value);}
	}).keypress(function(e) {

	  if (e.keyCode === 13)
	  {
	  return false;
	  }
	});
	$("#by_pedido_online").autocomplete({
		source: "php/inventario/suggest_no_pedido_online.php",//MISMA FUNCION QUE LOS RECIBOS
		minLength: 1,
		maxRows: 15,
		select: function(e,ui){ filtra_online('id_solicitud',ui.item.value);}
	}).keypress(function(e) {

	  if (e.keyCode === 13)
	  {
	  return false;
	  }
	});
	$(function() {
		//autocomplete
		$("#by_marca_online").autocomplete({
			source: "php/inventario/suggest_marca_online.php",
			minLength: 1,
			maxRows: 15,
			//select: function(e,ui){ carga_cbo(ui.item.value);}
			select: function(e,ui1){

			     filtra_online('marca',ui1.item.value);

			}
		}).keypress(function(e) {

		  if (e.keyCode === 13)
		  {
		  return false;
		  }
	  });

            $('#by_estatus').on('change', function() {
                filtra_online("estatus",this.value);
            });

        });
	//ACTIVAR VALIDADOR DEL FORMULARIO
       jQuery.validator.addMethod("cbo", function(value, element, arg){
      return arg !== value; }, "Debes seleccionar una opción");
	$('#frmEditPO select').tooltipster({
        trigger: 'custom',
        onlyOne: false,
        position: 'right',
		timer:3000
    });
    $('#frmEditPO').validate({
        errorPlacement: function (error, element) {
			$(element).tooltipster('update', $(error).text());
            $(element).tooltipster('show');
        },
        success: function (label, element) {
            $(element).tooltipster('hide');
        },
        rules: {
			cbo_edosEPO: {
                cbo: '0',
            },
			cbo_tipoEPO: {
                cbo: '0',
            }
        },
            submitHandler: function (form) {
			guardaEditarPO();
            return false;
        }
    });


});
function fill_online()
{
	if($("#jqGridPager_online").hasClass('ui-jqgrid-pager'))
	{
		$("#jqGrid_online").trigger("reloadGrid");
	}
	else
	{
	  $.jgrid.styleUI.Bootstrap.base.rowTable = "table table-bordered";
	  $("#jqGrid_online").jqGrid({
		  url: 'php/inventario/listado_online.php',
		  mtype: "POST",
		  datatype: "json",
		  colModel: [
			  { label: '#', name: 'id', width: 30, align:'center', sortable:true },
			  { label: 'Fecha/Hora', name: 'fechahora', width: 75, sortable:true, align:'center' },
			  { label: '# de Control', name: 'no_cliente', width: 50, align:'center'},
			  { label: 'Marca', name: 'marca', width: 130 },
			  { label: 'Categoria', name: 'tipo', width: 70, align:'center'},
			  { label: 'Estado', name: 'estado', width: 75, align:'center'},
			  { label: 'Cantidad', name: 'cantidad', width: 70, align:'center' },
			  { label: 'Importe', name: 'importe', width: 70, align:'right' },
			  { label: 'Prioridad', name: 'prioridad', width: 80, align:'center'},
			  { label: 'Estatus', name: 'status', width: 80, align:'center',cellattr: bgnd_cell_online},
			  { label: '--', name: 'link', width: 110, align:'center'},
			  { label: '--', name: 'fpago', width: 30, align:'center'}
			  //{ label: '--', name: 'pago_opcion' }
		  ],
		  loadComplete : function() {
		   	  bgnd_row_online();
			  //console.log(row);
             //$('#jqGridPager_right').css('text-align','left');
			 //$('#jqGrid_online').jqGrid('hideCol',["pago_opcion"]);
          },
		  postData: {
		   'depto':id_depto,'cargo':usr_cargo, 'clvuser':clvuser, 'nivel': nivel
		  },
		  sortname : 'id',
		  sortorder: 'desc',
		  autowidth: true,
		  height: 'auto',
		  rowheight: 300,
		  rowNum: 10,
		  rowList:[10,20,30,50,100],
		  viewrecords: true,
		  grouping: true,
		  groupingView: {
			  groupField: ["id"],
			  groupColumnShow: [false],
			  groupText: ["<b>&nbsp;&nbsp;{0}</b>"],

			  groupOrder: ["desc"],
			  groupSummary: [false],
			  groupCollapse: false
		  },
		  pager: "#jqGridPager_online",
		  loadError: function (jqXHR, textStatus, errorThrown) {
			  alert('HTTP status code: ' + jqXHR.status + '\n' +
					'textStatus: ' + textStatus + '\n' +
					'errorThrown: ' + errorThrown);
			  alert('HTTP message body (jqXHR.responseText): ' + '\n' + jqXHR.responseText);
		  },

		 ondblClickRow: function(rowId) {
		 	get_observacion_pedido(rowId);
        },
		  caption: "Resultados de la busqueda"
	  });

	}
}
function getEditarPO(idEditarPO)
{
	limpiarEditPO();
	$("#modalEditPO").modal("show");
	$('#txtIdEditarPO').val(idEditarPO);
	$('#noCtePO').html($('#jqGrid_online').jqGrid('getCell',idEditarPO,'no_cliente'));
	$('#marcaPO').html($('#jqGrid_online').jqGrid('getCell',idEditarPO,'marca'));
	switch($('#jqGrid_online').jqGrid('getCell',idEditarPO,'tipo'))
	    {
		   case 'MEZCAL':
		   {
			   $('#cbo_tipoEPO').val('1').change();
			   break;
		   }
		   case 'ARTESANAL':
		   {
			    $('#cbo_tipoEPO').val('2').change();
			   break;
		   }
		   case 'ANCESTRAL':
		   {
			    $('#cbo_tipoEPO').val('3').change();
			   break;
		   }
	    }
	$('#cbo_edosEPO').val($('#jqGrid_online').jqGrid('getCell',idEditarPO,'estado')).change();
	$('#cantidadPO').html($('#jqGrid_online').jqGrid('getCell',idEditarPO,'cantidad'));
}
function limpiarEditPO()
{
	$('#txtIdEditarPO').val('');
	$('#noCtePO').html('');
	$('#marcaPO').html('');
	$('#cbo_tipoEPO').val('0').change();
	$('#cbo_edosEPO').val('0').change();
	$('#cantidadPO').html('');
	$('#txtObsPO').val('');
}
function guardaEditarPO()
{
	var datosPO=$('#frmEditPO').serialize();
	$.ajax({
	  type: "POST",
	  url: "php/inventario/guardaEditarPO.php",
	  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
	  data: datosPO+"&usr_up="+user,
	  datatype: 'json',
	  success: function(data_r){
		  var datos_r=JSON.parse(data_r);
		  if(datos_r.status==='OK')
		  {
		   limpiarEditPO();
	       $("#modalEditPO").modal("hide");
		   reload_online();
		  }
		  else
		  {
			alert(datos_r.msj);
		  }
	  },
	  beforeSend:function()
	  {

		   //$("#add_err").html("Loading...")
	  }
  });
}

function reiniciar_online() {
	limpiar_buscar('clear_all');
	$("#jqGrid_online").clearGridData();
	$("#jqGrid_online").setGridParam({postData: null});
	$("#jqGrid_online").setGridParam({postData:{'depto':id_depto,'depto':id_depto,'cargo':usr_cargo, 'clvuser':clvuser, 'nivel': nivel },page:1}).trigger("reloadGrid");
}

function reload_online() {
	$("#jqGrid_online").clearGridData();
	$("#jqGrid_online").setGridParam({postData: null});
	// by_pedido_online
	// by_marca_online
	// by_estatus
	if($("#by_asoc_online").val() != "") {
		$("#jqGrid_online").setGridParam({postData:{'depto':id_depto, 'cargo':usr_cargo, 'clvuser':clvuser ,'valor':$("#by_asoc_online").val(), 'campo':'no_cliente', 'nivel': nivel},page:1}).trigger("reloadGrid");
	} else
		$("#jqGrid_online").setGridParam({postData:{'depto':id_depto,'cargo':usr_cargo,'clvuser':clvuser, 'nivel': nivel },page:1}).trigger("reloadGrid");


	/*$("#jqGrid_online").setGridParam({postData:{'depto':id_depto, 'cargo':usr_cargo, 'clvuser':clvuser ,'campo':'todos', 'valor':'',
		'valor1':$("#by_pedido_online").val(),
		'valor2':$("#by_marca_online").val(),
		'valor3':$("#by_estatus").val(),
		'valor4':$("#by_asoc_online").val(),
	},page:1}).trigger("reloadGrid");*/


}
function filtra_online(campo_filtra, valor, no_cliente)
{
	 $("#jqGrid_online").clearGridData();
	 if(campo_filtra==='marca') {
		$("#jqGrid_online").setGridParam({postData:{'depto':id_depto,'valor':valor, 'campo':campo_filtra, 'nivel': nivel},page:1}).trigger("reloadGrid");
	 } else {
	    $("#jqGrid_online").setGridParam({postData:{'depto':id_depto,'valor':valor, 'no_cliente':no_cliente, 'campo':campo_filtra, 'nivel': nivel},page:1}).trigger("reloadGrid");
	 }
	 limpiar_buscar(campo_filtra);
}
function limpiar_buscar(campo_buscado) {
	switch(campo_buscado)
	{
		case 'no_cliente':
		{
			$('#by_pedido_online').val('');
			$('#by_marca_online').val('');
			$('#by_estatus').val(0);
			break;
		}
		case 'id_solicitud':
		{
			$('#by_asoc_online').val('');
			$('#by_marca_online').val('');
			$('#by_estatus').val(0);
			break;
		}
		case 'marca':
		{
			$('#by_asoc_online').val('');
			$('#by_pedido_online').val('');
			$('#by_estatus').val(0);
			break;
		}
		case 'estatus':
		{
			$('#by_asoc_online').val('');
			$('#by_pedido_online').val('');
			$('#by_marca_online').val('');
			break;
		}
		case 'clear_all':
		{
			$('#by_asoc_online').val('');
			$('#by_pedido_online').val('');
			$('#by_marca_online').val('');
			$('#by_estatus').val(0);
			break;
		}
	}

}
function bgnd_row_online()
{
    var rows = $("#jqGrid_online").getDataIDs();
	for (var i = 0; i < rows.length; i++)
	{
		var status = $("#jqGrid_online").getCell(rows[i],"prioridad");
		var  folio = $("#jqGrid_online").getCell(rows[i],"id");

		folio=parseInt(folio);
		$("#jqGrid_online").jqGrid("setCell", rows[i], "id", folio);
		if(status ==="URGENTE")
		{
			$("#jqGrid_online").jqGrid('setRowData',rows[i],false, {background:'#FFECCB'});
			//$("#jqGrid_list").jqGrid('setRowData',rows[i],false, {background:'#3071a9'});
		}

	}
}
function bgnd_cell_online(rowId, val, rawObject,cm,rdata) {
	if (rawObject[13] == 4 || rawObject[13] == 5 || rawObject[13] == 6) {
		return 'class="bg-proc"';
	} else {
		switch(rawObject[13]) {
			case "1": //1 REVISIÓN
			{
				return 'class="bg-aut"';
			}
			case "2": //2 - AUTORIZADO
			{
				return 'class="bg-env"';
			}
			case "3": //3 - EN LISTA
			{
				return 'class="bg-rec"';
			}
			/*case "4": //4 - SOLICITADO A PROVEEDOR
			{
				return 'class="bg-proc"';
			}
			case "5": //5 - EN PROCESO
			{
				return 'class="bg-imp"';
			}
			case "6": //6 - EN INVENTARIO
			{
				return 'class="bg-ent"';
			}*/
			case "7": //7 - CANCELADO
			{
				return 'class="bg-sin"';
			}
		}
	}
	  
}
function sinc_online()
{
  $.ajax({
	type: "POST",
	url: "php/inventario/sinc_online.php",//MISMA FUNCION QUE LOS RECIBOS
	contentType: "application/x-www-form-urlencoded;charset=UTF-8",
	data: "",
	datatype: 'json',
	success: function(response){
		//alert(response);
		var js_pedido=JSON.parse(response);
		if(js_pedido.status==='OK')
		{
		  alert(js_pedido.msj);

		}
		else
		{
			 alert(js_pedido.msj);
		}
	},
	beforeSend:function()
	{

		 $("#add_err").html("Cargando...");
	}
});
}
function confirma_pago_online(id_s,folio,tipo_pago) {
	//console.log(tipo_pago + "::" + id_s + "::" + folio);
	if(clvuser == "1") {
		$('#modif_tipop').show();
	}
	if(tipo_pago == "1") {
		//console.log(tipo_pago + "::" + id_s + "::" + folio + "::" + clvuser);
		$('#upload_file').show();
	}
	var dialog = $("#dialog_pago_online").dialog({
		autoOpen: false,
		height: 'auto',
		width: 'auto',
		title: 'Confirmar Pago',
		buttons:{
			'SI': function(){
				 marcar_pago_online(id_s,folio,tipo_pago);
			},
			'NO': function(){
				 $( this ).dialog( "close" );
			},
		},
		modal: true,
		close: function() {
		 //limpiar();
		}
	  });
		dialog.dialog( "open" );
	    //get_last_id();
}

function confirma_pago_online_otro(id_s,folio,tipo_pago,prioridad) {
	//console.log("Abre ventana: " + tipo_pago);
	$('#tipo_pago_otro').val(tipo_pago);
	$('#input-file-otro').val('');
    document.getElementById('comprobante_section_otro').classList.remove('hidden');
	var dialog = $("#dialog_pago_online_otro").dialog({
		autoOpen: false,
		height: 'auto',
		width: 'auto',
		title: 'Confirmar Pago',
		buttons: [
			{
				text: "Cancelar",
				//"class": 'btn-cancel',
				//"id": 'btn-cancel',
				click: function() {
					// Cancel code here
					$( this ).dialog( "close" );
				}
			},
			{
				text: "Autorizar",
				//"class": 'btn-confirm',
				//"id": 'btn-confirm',
				click: function() {
					marcar_pago_online_otro(id_s,folio,tipo_pago);
				}
			}
		],
		modal: true,
		close: function() {
		 //limpiar();
		}
	  });
		dialog.dialog( "open" );
	    //get_last_id();
}

function modifica_pago_online(id_s,folio) {
	var dialog = $("#dialog_pago_online").dialog({
		autoOpen: false,
		height: 'auto',
		width: 'auto',
		title: 'Modificar Forma de Pago',
		buttons:{
			'SI': function(){
				 modificar_pago_online(id_s,folio);
			},
			'NO': function(){
				 $( this ).dialog( "close" );
			},
		},
		modal: true,
		close: function() {
		 //limpiar();
		}
	  });
		dialog.dialog( "open" );
	    //get_last_id();
}

function modifica_pago_online_otro(id_s,folio,tipo_pago,prioridad) {
	//console.log("Abre ventana: " + id_s+"::"+folio+"::"+tipo_pago+"::"+pago_opcion);
	let pago_opcion = 5;
	$('#input-file-otro').val(''); 
	$('#tipo_pago_otro').val(tipo_pago);
	$('#pago_opcion_otro').val(pago_opcion);
	document.getElementById('comprobante_section_otro').classList.remove('hidden');
	var dialog = $("#dialog_pago_online_otro").dialog({
		autoOpen: false,
		height: 'auto',
		width: 'auto',
		title: 'Modificar Forma de Pago',
		buttons:{
			'SI': function(){
				 modificar_pago_online_otro(id_s,folio,tipo_pago);
			},
			'NO': function(){
				 $( this ).dialog( "close" );
			},
		},
		modal: true,
		close: function() {
		 //limpiar();
		}
	  });
		dialog.dialog( "open" );
	    //get_last_id();
}

function marcar_pago_online(folio_detalle,id_solicitud,tipo_pago) {
	let pago_opcion = $("#pago_opcion").val();
	var datos = new FormData();
	if(tipo_pago == "1") {
		var file = document.getElementById('input-id').files[0]; 
		datos.append('file', file);
	} else {
		datos.append('file', null);
	}
	datos.append('folio', folio_detalle);
	datos.append('id_s', id_solicitud);
	datos.append('user', user);
	datos.append('pago_opcion', pago_opcion);

	$.ajax({
		type: "POST",
		url: "php/inventario/conf_pago_online.php",
		contentType: "application/x-www-form-urlencoded;charset=UTF-8",
		dataType: 'json',
		data: datos,
		contentType: false, // 👈 muy importante
    	processData: false,
		success: function(data){
			//data=JSON.parse(data);
			if(data.status==='OK')
			{
				/*console.log("antes de condición");
				if(tipo_pago == "1" && clvuser == "1") {
					console.log("entrando a condición");
					//reiniciarInputFileCA(id_solicitud);
				}*/
				$("#dialog_pago_online").dialog( "close" );
				reload_online();
				$('#upload_file').hide();
			}
			else
			{
			alert(data.msj);
			}

		},
		beforeSend:function()
		{
		}
   	});
}
function marcar_pago_online_otro(folio_detalle,id_solicitud,tipo_pago) {
	let forma_pago = $("#tipo_pago_otro").val();
	let pago_opcion = $("#pago_opcion_otro").val();
	if(tipo_pago > 0 && pago_opcion > 0) {
		var datos = new FormData();
		//if(tipo_pago == "1") {
			var file = document.getElementById('input-file-otro').files[0]; 
			datos.append('file', file);
		/*} else {
			datos.append('file', null);
		}*/
		datos.append('folio', folio_detalle);
		datos.append('id_s', id_solicitud);
		datos.append('user', user);
		datos.append('pago_opcion', pago_opcion);
		datos.append('forma_pago', forma_pago);

		$.ajax({
			type: "POST",
			url: "php/inventario/conf_pago_online.php",
			contentType: "application/x-www-form-urlencoded;charset=UTF-8",
			dataType: 'json',
			data: datos,
			contentType: false, // 👈 muy importante
			processData: false,
			success: function(data){
				//data=JSON.parse(data);
				if(data.status==='OK')
				{
					/*console.log("antes de condición");
					if(tipo_pago == "1" && clvuser == "1") {
						console.log("entrando a condición");
						//reiniciarInputFileCA(id_solicitud);
					}*/
					$("#dialog_pago_online_otro").dialog( "close" );
					reload_online();
					$('#upload_file').hide();
				}
				else
				{
				alert(data.msj);
				}

			},
			beforeSend:function()
			{
			}
		});
	} else {
		alert("Seleccione una forma y tipo de pago");
	}
}
function modificar_pago_online(folio_detalle,id_solicitud)
{
	let pago_opcion = $("#pago_opcion").val();
	$.ajax({
	type: "POST",
	url: "php/inventario/conf_pago_online.php",
	contentType: "application/x-www-form-urlencoded;charset=UTF-8",
	datatype: 'json',
	data: "modificar=1&folio="+folio_detalle+"&id_s="+id_solicitud+"&user="+user+"&pago_opcion="+pago_opcion,
	success: function(data){
		//alert(data);
		data=JSON.parse(data);
		if(data.status==='OK')
		{
		   $("#dialog_pago_online").dialog( "close" );
		   reload_online();
		}
		else
		{
		  alert(data.msj);
		}

	},
	beforeSend:function()
	{
	}
   });
}
function modificar_pago_online_otro(folio_detalle,id_solicitud,tipo_pago) {
	let forma_pago = $("#tipo_pago_otro").val();
	let pago_opcion = $("#pago_opcion_otro").val();
	if(tipo_pago > 0 && pago_opcion > 0) {
		var datos = new FormData();
		//if(tipo_pago == "1") {
			var file = document.getElementById('input-file-otro').files[0]; 
			datos.append('file', file);
		/*} else {
			datos.append('file', null);
		}*/
		datos.append('folio', folio_detalle);
		datos.append('id_s', id_solicitud);
		datos.append('user', user);
		datos.append('pago_opcion', pago_opcion);
		datos.append('forma_pago', forma_pago);
		$.ajax({
			type: "POST",
			url: "php/inventario/conf_pago_online.php",
			contentType: "application/x-www-form-urlencoded;charset=UTF-8",
			dataType: 'json',
			data: datos,
			contentType: false, // 👈 muy importante
			processData: false,
			success: function(data){
				//data=JSON.parse(data);
				if(data.status==='OK')
				{
					/*console.log("antes de condición");
					if(tipo_pago == "1" && clvuser == "1") {
						console.log("entrando a condición");
						//reiniciarInputFileCA(id_solicitud);
					}*/
					$("#dialog_pago_online_otro").dialog( "close" );
					reload_online();
					$('#upload_file').hide();
				}
				else
				{
				alert(data.msj);
				}

			},
			beforeSend:function()
			{
			}
		});
	} else {
		alert("Seleccione una forma y tipo de pago");
	}

	/*let pago_opcion = $("#pago_opcion").val();
	$.ajax({
	type: "POST",
	url: "php/inventario/conf_pago_online.php",
	contentType: "application/x-www-form-urlencoded;charset=UTF-8",
	datatype: 'json',
	data: "modificar=1&folio="+folio_detalle+"&id_s="+id_solicitud+"&user="+user+"&pago_opcion="+pago_opcion,
	success: function(data){
		//alert(data);
		data=JSON.parse(data);
		if(data.status==='OK')
		{
		   $("#dialog_pago_online").dialog( "close" );
		   reload_online();
		}
		else
		{
		  alert(data.msj);
		}

	},
	beforeSend:function()
	{
	}
   });*/
}
function ver_comprobante(name_file)
{
	window.open(name_file, '_blank');
}
function get_data_online_tmp(folio_detalle,cant_sol)
{
    $.ajax({
	type: "POST",
	url: "php/inventario/get_data_detalle.php",
	contentType: "application/x-www-form-urlencoded;charset=UTF-8",
	datatype: 'json',
	data: "folio="+folio_detalle+"&cantidad="+cant_sol,
	success: function(data){
		//alert(data);
		data=JSON.parse(data);
		if(data.status==='OK')
		  addCarrito_online(data);
		else
		  alert(data.msj);

	},
	beforeSend:function()
	{
	}
   });
}
function addCarrito_online(data_detalle)
{
	console.log(data_detalle);
	var cte_det=data_detalle.cliente;
    var cve_mca_det=data_detalle.marca;
    var nom_mca_det=data_detalle.nom_marca;
    var edo_det=get_edo(data_detalle.edo);
	//var edo_det=data_detalle.edo;
    var index= 'F_'+cte_det+cve_mca_det+'_'+edo_det;
	if(req_list[index])
	{
	  alert('Ya has usado esta marca');
	  //limpiar_parcial_req();
	}
	else
	{
		var variables = "";
	   req_list[index] = { };
	   req_list[index].cte = cte_det;
	   req_list[index].folio_det = data_detalle.folio_det;
	   req_list[index].marca =cve_mca_det;
	   //req_list[index].nom_marca = nom_mca_det;
	   req_list[index].edo = data_detalle.edo;
	   req_list[index].serie = data_detalle.serie;
	   req_list[index].tipo =  data_detalle.tipo;
	   req_list[index].cantidad = data_detalle.cantidad;
	   req_list[index].pagado =1;
	   
	   var prioridad="";
	   var clase_urg="";
	   var  urgente=0;
	   var tipo_mez="";

	   var es_pagado='SI';
	   var clase_pag=" pagado'>";
	    if(data_detalle.urgente==='0')
		{
			prioridad="NORMAL";
			clase_urg=" normal'>";
			urgente=0;
		}
		else
		{
			prioridad="URGENTE";
			clase_urg=" urgente'>";
			urgente=1;
		}
		switch(data_detalle.tipo)
	    {
		    case '0':
		   {
			   tipo_mez="N/S";
			   break;
		   }
		   case '1':
		   {
			   tipo_mez="MEZCAL";
			   break;
		   }
		   case '2':
		   {
			   tipo_mez="ARTESANAL";
			   break;
		   }
		   case '3':
		   {
			   tipo_mez="ANCESTRAL";
			   break;
		   }
	    }
	   req_list[index].urgente = urgente;
	   req_list[index].fini = data_detalle.fini;
	   req_list[index].ffin = data_detalle.ffin;
	   //var ini_completo=cte_det+cve_mca_det+pad(data_detalle.fini,7)+data_detalle.serie;
	   //var fin_completo=cte_det+cve_mca_det+pad(data_detalle.ffin,7)+data_detalle.serie;
	   //variables += "cte="+cte_det+"&folio_det="+data_detalle.folio_det+"&marca="+cve_mca_det+"&edo="+data_detalle.edo+"&serie="+data_detalle.serie+"&tipo="+data_detalle.tipo+"&cantidad="+data_detalle.cantidad+"&pagado=1&urgente="+urgente+"&pagado=1&urgente="+urgente+"&fini="+data_detalle.fini+"&ffin="+data_detalle.ffin;
	   // "fini":1,"ffin":500}
	   var activaN = "";
	   var activaG = "";
	   if(data_detalle.holograma == '0')
	   		activaG = "selected";
	   else if(data_detalle.holograma == '1')
	   		activaN = "selected";
	   count_list++;
	   var cadena = "<tr id='"+index+"' align='center'>";
	   cadena = cadena + "<td class='td_req'>" +cte_det + "</td>";
	   cadena = cadena + "<td class='td_req'>" +nom_mca_det + "</td>";
	   cadena = cadena + "<td class='td_req'>" +tipo_mez+ "</td>";
	   cadena = cadena + "<td class='td_req'>" +data_detalle.edo+ "</td>";
	   cadena = cadena + "<td class='td_req'>" + cte_det+cve_mca_det+pad(data_detalle.fini,7)+data_detalle.serie + "</td>";
	   cadena = cadena + "<td class='td_req'>" + cte_det+cve_mca_det+pad(data_detalle.ffin,7)+data_detalle.serie+ "</td>";
	   cadena = cadena + "<td class='td_req'>" + data_detalle.cantidad + "</td>";
	   cadena = cadena + "<td class='td_req"+clase_pag + es_pagado+ "</td>";

	   // /*
	  	/*cadena = cadena + "<td class='td_req" + clase_urg +'<select class="form-control" id="cboholograma" name="cboholograma" onchange="cambiaTipoHolograma('+data_detalle.id_row+',this.value)" ><option value="0" '+activaG+'>Genérico</option><option value="1" '+activaN+'>Nuevo</option></select>'+ "</td>";
   		cadena = cadena + "<td class='td_req"+clase_urg+ prioridad+ "</td>";
   		cadena = cadena + "<td><button type='button'  name='btn_eliminar' id='btn_eliminar' class='btn btn-xs btn-danger' onClick='elimin_fil_req(\""+index+"\")'><i class='fa fa-minus fa-lg'></i></button</td>";
   		add_list(JSON.stringify(req_list[index]),cadena,"ONLINE");*/
	   //
	   
	    // {"cte":"C9998","folio_det":"34","marca":"A","edo":"OAXACA","serie":"A","tipo":"2","cantidad":"500","pagado":1,"urgente":0,"fini":1,"ffin":500}
	    var reqlist = JSON.stringify(req_list[index]);
	   	var tipo_pet = "ONLINE";
	  	user=$('#usr').val();
		$.ajax({
			type: "POST",
			url: "php/inventario/add_temp.php",
			contentType: "application/x-www-form-urlencoded;charset=UTF-8",
			data: 'datos='+reqlist+'&no_pedido='+no_pedido+'&user='+user,
			datatype: 'json',
			success: function(data){
				//alert(data);
				data=JSON.parse(data);
				if(data.status=="OK") {
					//alert(data.msj);
					if(tipo_pet=="ONLINE") {
						alert("Se agrego correctamente al temporal");
						$("#jqGrid_online").clearGridData();
					    $("#jqGrid_online").setGridParam({page:1}).trigger("reloadGrid");
				    }
				    // AGREGANDO FILAS FALTANTES
				    cadena = cadena + "<td class='td_req" + clase_urg +'<select class="form-control" id="cboholograma" name="cboholograma" onchange="cambiaTipoHolograma('+data.id_row+',this.value)" ><option value="1" '+activaN+'>Nuevo</option><option value="0" '+activaG+'>Genérico</option></select>'+ "</td>";
			   		cadena = cadena + "<td class='td_req"+clase_urg+ prioridad+ "</td>";
			   		cadena = cadena + "<td><button type='button'  name='btn_eliminar' id='btn_eliminar' class='btn btn-xs btn-danger' onClick='elimin_fil_req(\""+index+"\")'><i class='fa fa-minus fa-lg'></i></button</td>";
					add_temp(cadena);
					limpiar_parcial_req();
				}
				else
					alert(data.msj);
			},
			beforeSend:function()
			{
			}
		});
	 	 
   }
}

function get_observacion_pedido(id)
{
	$.ajax({
		type: "POST",
		url: "php/inventario/get_observacion_pedido.php",
		contentType: "application/x-www-form-urlencoded;charset=UTF-8",
		data: "id="+id,
		dataType: "json",
		success: function(data) {
			if (data.status === "OK") {
				 $('#mdlBodyObservacionesP').html(data.msj);
	             $('#mdlObsPedido').modal('show');
			}
			else {
				alert("Ha ocurrido un error al cargar las observaciones: " + data.msj);
			}
		},
		error: function(jqxhr,error) {
			alert("Ha ocurrido un error: " + jqxhr.responseText+error);
		}
	});
}
function cancelar_solicitud(id_s,folio)
{
	var dialog = $("#dialog_cancelar_solicitud").dialog({
		autoOpen: false,
		height: 'auto',
		width: 'auto',
		title: 'Cancelar Solicitud',
		buttons:{
			'SI': function(){
				 marcar_cancelar_solicitud(id_s,folio);

			},
			'NO': function(){
				 $( this ).dialog( "close" );
			},
		},
		modal: true,
		close: function() {
		 //limpiar();
		}
	  });
		dialog.dialog( "open" );
	    //get_last_id();
}

function marcar_cancelar_solicitud(folio_detalle,id_solicitud)
{
	$.ajax({
	type: "POST",
	url: "php/inventario/conf_cancelar_solicitud.php",
	contentType: "application/x-www-form-urlencoded;charset=UTF-8",
	datatype: 'json',
	data: "folio="+folio_detalle+"&id_s="+id_solicitud+"&user="+user,
	success: function(data){
		//alert(data);
		data=JSON.parse(data);
		if(data.status==='OK')
		{
		   $("#dialog_cancelar_solicitud").dialog( "close" );
		   reload_online();
		}
		else
		{
		  alert(data.msj);
		}

	},
	beforeSend:function()
	{
	}
   });
}

