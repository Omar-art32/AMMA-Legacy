"use strict";
$.jgrid.defaults.responsive = false;
$.jgrid.defaults.styleUI = 'Bootstrap';


$(document).ready(function(e) {
    $('#sp_estatus').on('change', function() {
        if($('#sp_estatus').val() > 0)
            consulta_list($('#sp_estatus').val(),"status");
    });
});

function fill_grid()
{
	if($("#jqGridPager_list").hasClass('ui-jqgrid-pager'))
	{
		$("#jqGrid_list").trigger("reloadGrid");
	}
	else
	{
	  $.jgrid.styleUI.Bootstrap.base.rowTable = "table table-bordered table-striped";
	  $("#jqGrid_list").jqGrid({
		  url: 'php/inventario/listado.php',
		  mtype: "POST",
		  datatype: "json",
		  colModel: [
			  { label: '#', name: 'no_pedido', width: 40, align:'center', sortable:true },
			  { label: 'Folio', name: 'folio', width: 65, align:'center'},
			  { label: 'Fecha de Envio', name: 'fecha', width: 70, sortable:true },
			  { label: '# de Control', name: 'no_cliente', width: 65, align:'center'},
			  { label: 'Marca', name: 'marca', width: 100 },
			  { label: 'Estado', name: 'marca', width: 90 },
			  { label: 'Categoria', name: 'marca', width: 80 },
			  { label: 'Fol Ini', name: 'fi', width: 100, align:'center'},
			  { label: 'Fol Fin', name: 'ff', width: 100, align:'center'},
			  { label: 'Cantidad', name: 'cantidad', width: 70 },
			  { label: 'Versión', name: 'holograma', width: 70 },
			  { label: 'Prioridad', name: 'prioridad', width: 70, align:'center'},
			  { label: 'Estatus', name: 'status', width: 80, align:'center',cellattr: bgnd_cell},
			  //{ label: 'Comprobante', name: 'fpago', width: 60, align:'center',cellattr: bgnd_cell},
			  { label: '--', name: 'link', width: 90, align:'center'},
			  { label: '--', name: 'fpago', width: 30, align:'center'}
		  ], 
		   postData: {
		   'depto':id_depto,'cargo':usr_cargo, 'clvuser':clvuser
		  },
		  loadComplete : function() { 
		   	  bgnd_row(); 			  
             //$('#jqGridPager_right').css('text-align','left');
          },   
		  sortname : 'no_pedido',
		  sortorder: 'desc',
		  autowidth: true,
		  height: 'auto',
		  rowheight: 300,
		  rowNum: 30,
		  rowList:[30,50,100], 
		  viewrecords: true,
		  grouping: true,
                groupingView: {
                    groupField: ["no_pedido"],
                    groupColumnShow: [false],
                    groupText: ["<b>&nbsp;&nbsp;Pedido:{0}</b>"],
                    groupOrder: ["desc"],
                    groupSummary: [false],
                    groupCollapse: false                    
                },
		  pager: "#jqGridPager_list",
		  loadError: function (jqXHR, textStatus, errorThrown) {
			  alert('HTTP status code: ' + jqXHR.status + '\n' +
					'textStatus: ' + textStatus + '\n' +
					'errorThrown: ' + errorThrown);
			  alert('HTTP message body (jqXHR.responseText): ' + '\n' + jqXHR.responseText);
		  },

		 ondblClickRow: function(rowId) {
		 	get_observacion(rowId);
        },

		  caption: "Resultados de la busqueda"
	  });
	  
	}
}
function bgnd_cell(rowId, val, rawObject,cm,rdata)
{	
  switch(val)
  {
	case "SIN SOLICITAR":
	{
		return 'class="bg-sin"';
	}
	case "SOLICITADO":
	{
		return 'class="bg-env"';
	}
	case "RECIBIDO":
	{
		return 'class="bg-rec"';
	}
	case "PROCESANDO":
	{
		return 'class="bg-proc"';
	}
	case "IMPRESO":
	{
		return 'class="bg-imp"';
	}
	case "ENTREGADO":
	{
		return 'class="bg-ent"';
	}
	case "EN INVENTARIO":
	{
		return 'class="bg-inv"';
	}
  }	
}
function re_enviar(n_pdo)
{
	$.ajax({	  
	  type: "POST",
	  url: "php/inventario/re_enviar.php",//MISMA FUNCION QUE LOS RECIBOS
	  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
      data: "no_pedido="+n_pdo,
	  datatype: 'json',
	  success: function(response){
		   var js_pedido=JSON.parse(response);
		  if(js_pedido.status==='OK')
		  {
			alert(js_pedido.msj);
			$("#jqGrid_list").clearGridData();
			$("#jqGrid_list").trigger("reloadGrid");
		  }
		  else
		  {
			   alert(js_pedido.msj);
		  }
	  },
	  beforeSend:function()
	  {
		  
		   $("#add_err").html("Loading...");
	  }
  });		
}
function ocultar_msj()
{
  $("#lbl_resp_list").html(''); 
}

function reiniciar_list() {
	clean_search("clear_all");
	$("#jqGrid_list").clearGridData();
	$("#jqGrid_list").trigger("reloadGrid");
}

function sinc_list() {
	/*$.ajax({	  
	  type: "POST",
	  url: "php/inventario/sinc_listado.php",//MISMA FUNCION QUE LOS RECIBOS
	  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
	  data: "",
	  datatype: 'json',
	  success: function(response){
		  //alert(response);
		  var js_pedido=JSON.parse(response);
		  if(js_pedido.status==='OK')
		  {
			  
			 clean_search('clear_all');
			 if(js_pedido.tipo_r==='1'){
				$('#lbl_resp_list').removeClass('lbl-empty');
				$('#lbl_resp_list').addClass('lbl-ok');
			 } else {
				$('#lbl_resp_list').removeClass('lbl-ok');
				$('#lbl_resp_list').addClass('lbl-empty');
			 }
				
			
			$("#lbl_resp_list").html(js_pedido.msj); 
			setTimeout(ocultar_msj, 5000);
			$("#jqGrid_list").clearGridData();				
			$("#jqGrid_list").setGridParam({postData: null});
			$("#jqGrid_list").setGridParam({postData:{'depto':id_depto,'cargo':usr_cargo}});
			$("#jqGrid_list").setGridParam({page:1}).trigger("reloadGrid");				
		  }
		  else
		  {
			   
			   $('#lbl_resp_list').removeClass('lbl-ok');			   
			   $('#lbl_resp_list').addClass('lbl-empty');
			   $("#lbl_resp_list").html(js_pedido.msj); 
			   setTimeout(ocultar_msj, 5000);
		  }
	  },
	  beforeSend:function()
	  {
		  
		   $("#add_err").html("Loading...")
	  }
  });*/	

  fill_grid();	
}	//sinc_silent();
   
function change_color()
{
	$("#jqGrid_list").jqGrid("setCell", 20, "marca", "New value","bg-sin");
	 alert('ok');
}


$(function() {
		//autocomplete
		$("#by_pedido_espera").autocomplete({
			source: "php/inventario/suggest_no_pedido.php",
			minLength: 1,
			maxRows: 15,
			//select: function(e,ui){ carga_cbo(ui.item.value);}
			select: function(e,ui1){ consulta_list(ui1.item.value,'no_pedido');}
		}).keypress(function(e) {

		  if (e.keyCode === 13) 
		  {
		  return false;
		  }
	  });				
});
$(function() {
		//autocomplete
		$("#by_marca").autocomplete({
			source: "php/inventario/suggest_by_marca.php",
			minLength: 1,
			maxRows: 15,
			//select: function(e,ui){ carga_cbo(ui.item.value);}
			select: function(e,ui1){ 
			
			     consulta_list(ui1.item.value,'marca');
			
			}
		}).keypress(function(e) {

		  if (e.keyCode === 13) 
		  {
		  return false;
		  }
	  });				
});
$(function() {
		//autocomplete
		$("#by_asoc_espera").autocomplete({
			source: "php/inventario/suggest_no_cliente.php",
			minLength: 1,
			maxRows: 15,
			//select: function(e,ui){ carga_cbo(ui.item.value);}
			select: function(e,ui2){ consulta_list(ui2.item.value,'no_cliente');}
		}).keypress(function(e) {

		  if (e.keyCode === 13) 
		  {
		  return false;
		  }
	  });				
});
function consulta_list(valor_cons,campo)
{
	//alert(valor_cons+ ' : '+ campo);
  $("#jqGrid_list").clearGridData();
  $("#jqGrid_list").setGridParam({postData:{'clave':valor_cons, 'campo':campo},page:1}).trigger("reloadGrid");
  clean_search(campo);
}
function clean_search(v_field)
{
	switch(v_field)
	{
		case 'no_pedido':
		{
			$('#by_asoc_espera').val('');
			$('#by_marca').val('');
			$('#sp_estatus').val(0);
			break;
		}
		case 'no_cliente':
		{
			$('#by_marca').val('');
			$('#by_pedido_espera').val('');
			$('#sp_estatus').val(0);
			break;
		}
		case 'marca':
		{
			$('#by_asoc_espera').val('');
			$('#by_pedido_espera').val('');
			$('#sp_estatus').val(0);
			break;
		}
		case 'status':
		{
			$('#by_asoc_espera').val('');
			$('#by_marca').val('');
			$('#by_pedido_espera').val('');
			break;
		}
		case 'clear_all':
		{
			$('#by_asoc_espera').val('');
			$('#by_pedido_espera').val('');
			$('#by_marca').val('');
			$('#sp_estatus').val(0);
			break;
		}
	}
}
//PARA AGREGAR LAS ENTRADAS DESDE EL LISTADO DE PEDIDOS
function ingresarPedido(idFilaPedido)
{
	var fi_ing=$('#jqGrid_list').jqGrid('getCell',idFilaPedido,'fi');
	var ff_ing=$('#jqGrid_list').jqGrid('getCell',idFilaPedido,'ff');
	var largo = fi_ing.length;
	if(largo == 15) {
		fi_ing=fi_ing.substring(7, 14);
		ff_ing=ff_ing.substring(7, 14);
	} else {
		fi_ing=fi_ing.substring(6, 13);
		ff_ing=ff_ing.substring(6, 13);
	}

	$.ajax({	  
	  type: "POST",
	  url: "php/inventario/ingresarPedido.php",//MISMA FUNCION QUE LOS RECIBOS
	  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
      data: "fila_pedido="+idFilaPedido+"&fi_ing="+fi_ing+"&ff_ing="+ff_ing+"&usr="+user,
	  datatype: 'json',
	  success: function(response){	
	      //alert(response);
	      $(".class-botones-ingresar").prop('disabled', false);		  
		  var js_pedido=JSON.parse(response);
		  if(js_pedido.status=='OK')
		  {
			alert(js_pedido.msj);
			$("#jqGrid_list").trigger("reloadGrid");
		  }
		  else
		  {
			   alert(js_pedido.msj);
		  }
	  },
	  beforeSend:function()
	  {		  
		 $("#add_err").html("Loading...");
		 $(".class-botones-ingresar").prop('disabled', true);
	  }
  });			
}


function get_observacion(id)
{	    
	$.ajax({
		type: "POST",
		url: "php/inventario/get_observacion.php",
		contentType: "application/x-www-form-urlencoded;charset=UTF-8",
		data: "id="+id,
		dataType: "json",
		success: function(data) {	
			if (data.status === "OK") { 	
				 $('#mdlObservacionesPedido').html(data.msj);
	             $('#mdlObservaciones').modal('show');
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

function bgnd_row()
{
	
    var rows = $("#jqGrid_list").getDataIDs(); 
	for (var i = 0; i < rows.length; i++)
	{
		var status = $("#jqGrid_list").getCell(rows[i],"prioridad");
		var  folio = $("#jqGrid_list").getCell(rows[i],"id");
		folio=parseInt(folio);
		$("#jqGrid_list").jqGrid("setCell", rows[i], "id", folio);
		if(status ==="URGENTE")
		{
			$("#jqGrid_list").jqGrid('setRowData',rows[i],false, {background:'#FFECCB'});   
			//$("#jqGrid_list").jqGrid('setRowData',rows[i],false, {background:'#3071a9'});      
		}
		
	}	
}