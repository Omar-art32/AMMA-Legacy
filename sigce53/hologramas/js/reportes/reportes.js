$.jgrid.defaults.width = 900;
$.jgrid.defaults.responsive = false;
$.jgrid.defaults.styleUI = 'Bootstrap';
var tot_ini=0;

var cliente_f="";
var marca_f="";
var fecha_1="";
var fecha_2="";
var tipo_m="";
var tipo_f="";
var edo_f="";
var cat_f="";
var idSalida = 0;
var CLIENTESEL = "";

$( document ).ready(function() {
  $('#frm_buttons_recibo').on('keyup change', function(e) {
      $('#tablaRecibos').bootstrapTable('refresh');
  });

  $('#frm_buttons_pedidos').on('keyup change', function(e) {
      $('#tablaPedidos').bootstrapTable('refresh');
  });

  loadRecibos();
  loadPedidos();
  //Obtener Recibo
  $( "#tabs" ).tabs();
  var anioAct = (new Date).getFullYear();
  $( "#fecha_ini" ).datepicker({
	changeMonth: true,
    changeYear: true,
	yearRange: '2021:'+anioAct,
	buttonText: "Seleccionar Fecha",
	onClose: function(){
        busc_fecha();
     }
  });
  $( "#fecha_fin" ).datepicker({
	changeMonth: true,
    changeYear: true,
	yearRange: '2021:'+anioAct,
	buttonText: "Seleccionar Fecha",
	onClose: function(){
        busc_fecha();
     }
  });
  //PARA EL REPORTE CONCENTRADO
    $( "#fecha_ini_con" ).datepicker({
	changeMonth: true,
    changeYear: true,
	yearRange: '2021:'+anioAct,
	buttonText: "Seleccionar Fecha",
	onClose: function(){

     }
  });
  $( "#fecha_fin_con" ).datepicker({
	changeMonth: true,
    changeYear: true,
	yearRange: '2010:'+anioAct,
	buttonText: "Seleccionar Fecha",
	onClose: function(){

     }
  });

  llena_rep();
  $('form').bind("keypress", function(e) {
  if (e.keyCode === 13) {
    e.preventDefault();
    return false;
  }
});
});
$(function() {
		//autocomplete
		$("#txtbusca").autocomplete({
			source: "php/reportes/socios_rpt.php",
			minLength: 1,
			maxRows: 15,
			//select: function(e,ui){ carga_cbo(ui.item.value);}
			select: function(e,ui){ filtra_rep(ui.item.value,1,'','','','','T','T',''); limpiar();}
		}).keypress(function(e) {

          if (e.keyCode === 13)
          {
          return false;
          }

      });

      //autocomplete
  		$("#txtnocontrol").autocomplete({
    			source: "php/reportes/socios_pedidos.php",
    			minLength: 1,
    			maxRows: 15,
    			select: function(e,ui) {
            		console.log(ui.item.value);
            		CLIENTESEL = ui.item.value;
            		$('#tablaPedidos').bootstrapTable('refresh');
          		}
          //change: function( event, ui ) { $('#tablaPedidos').bootstrapTable('refresh');}
    	}).keypress(function(e) {
            if (e.keyCode === 13)
              return false;
      });
});
function limpiar()
{
	if($('#filtros').css('display')==='none')
	{
		$('#filtros').css('display','block');
	}
	if($('#by_fechas').css('display')==='none')
	{
		$('#by_fechas').css('display','block');
	}
	if($('#by_estado').css('display')==='none')
	{
		$('#by_estado').css('display','block');
	}
	if($('#by_categoria').css('display')==='none')
	{
		$('#by_categoria').css('display','block');
	}
	if($('#by_orden').css('display')==='none')
	{
		$('#by_orden').css('display','block');
	}
	$("#fecha_ini").val('');
	$("#fecha_fin").val('');


	//$('#muestra_por').css('display','none');
	  $('#lbl_mixto').css('visibility','hidden');
	  $('#div_mixto').css('visibility','hidden');
	reset_mixto();
}
function reset_mixto()
{
	 var tipo_sel=($('input:radio[name=radio_marca_tipo]:checked').val());
	  if(tipo_sel!=='T')
	  {
		$("#tipo_t").click();
	  }
}
function valida_fechas(f_fin)
{
	var startDate=$('#fecha_ini').val();
	if( Date.parse(startDate) >= Date.parse(f_fin))
	{
		$('#fecha_fin').val('');
		alert('La fecha final debe ser mayor que la fecha inicial');
		$('#fecha_fin').focus();
		$("#fecha_fin").datepicker("show");
	}
}
function busc_xmar()
{
	var cliente_f=$("#txtbusca").val();
	var marca_f=$("#cbo_marcas").val();
	var fecha_1=$("#fecha_ini").val();
	var fecha_2=$("#fecha_fin").val();
	var tipo_m="";
	var tipo_f="";

	var edo_f=$("#cbo_edo").val();
	var cat_f=$("#cbo_cat").val();
	$orden = $("#cboOrden").val();

	if(marca_f==='TODOS')
	{
	  tipo_f=1;
	  marca_f="";
	  //filtra_rep(cliente_f,tipo_f,marca_f,tipo_m,fecha_1,fecha_2,edo_f,cat_f);
	  //$('#muestra_por').css('display','none');
	  $('#lbl_mixto').css('visibility','hidden');
	  $('#div_mixto').css('visibility','hidden');
	}
	else if(marca_f==='GENERICOS')
	{
	  tipo_f=2;
	  marca_f="";
	  //filtra_rep(cliente_f,tipo_f,marca_f,tipo_m,fecha_1,fecha_2,edo_f,cat_f);
	  //$('#muestra_por').css('display','none');
	  $('#lbl_mixto').css('visibility','hidden');
	  $('#div_mixto').css('visibility','hidden');
	}
	else if(marca_f==='PERSON')
	{
	  tipo_f=3;
	  marca_f="";
	  //filtra_rep(cliente_f,tipo_f,marca_f,tipo_m,fecha_1,fecha_2,edo_f,cat_f);
	  //$('#muestra_por').css('display','none');
	}
	else
	{
	  tipo_f=4;
	  tipo_m='T';
	  //filtra_rep(cliente_f,tipo_f,marca_f,tipo_m,fecha_1,fecha_2,edo_f,cat_f);
	  //$('#muestra_por').css('display','block');
	  $('#lbl_mixto').css('visibility','visible');
	  $('#div_mixto').css('visibility','visible');
	  //$("#tipo_t").click();
	  //$('input:radio[name=radio_marca_tipo][value=T]').attr('checked', true);
	  reset_mixto();
	}
	filtra_rep(cliente_f,tipo_f,marca_f,tipo_m,fecha_1,fecha_2,edo_f,cat_f,$orden);
}

function busc_xedo()
{
	var cliente_f=$("#txtbusca").val();
	var marca_f=$("#cbo_marcas").val();
	var fecha_1=$("#fecha_ini").val();
	var fecha_2=$("#fecha_fin").val();
	var tipo_m="";
	var tipo_f="";

	var edo_f=$("#cbo_edo").val();
	var cat_f=$("#cbo_cat").val();

	$orden = $("#cboOrden").val();


	  if(marca_f==='TODOS')
	  {
		tipo_f=1;
		marca_f="";
		//filtra_rep(cliente_f,tipo_f,marca_f,tipo_m,fecha_1,fecha_2,edo_f,cat_f);
	  }
	  else if(marca_f==='GENERICOS')
	  {
		tipo_f=2;
		marca_f="";
		//filtra_rep(cliente_f,tipo_f,marca_f,tipo_m,fecha_1,fecha_2,edo_f,cat_f);
	  }
	  else if(marca_f==='PERSON')
	  {
		tipo_f=3;
		marca_f="";
		//filtra_rep(cliente_f,tipo_f,marca_f,tipo_m,fecha_1,fecha_2,edo_f),cat_f;
	  }
	  else
	  {
		tipo_f=4;
		tipo_m=($('input:radio[name=radio_marca_tipo]:checked').val());
	  }
	  filtra_rep(cliente_f,tipo_f,marca_f,tipo_m,fecha_1,fecha_2,edo_f,cat_f,$orden);
}

function ordenarTabla() {
	var cliente_f=$("#txtbusca").val();
	var marca_f=$("#cbo_marcas").val();
	var fecha_1=$("#fecha_ini").val();
	var fecha_2=$("#fecha_fin").val();
	var tipo_m="";
	var tipo_f="";
	
	var edo_f=$("#cbo_edo").val();
	var cat_f=$("#cbo_cat").val();

	$orden = $("#cboOrden").val();

	if(marca_f==='TODOS') {
		tipo_f=1;
		marca_f="";
	} else if(marca_f==='GENERICOS') {
		tipo_f=2;
		marca_f="";
	} else if(marca_f==='PERSON') {
		tipo_f=3;
		marca_f="";
	} else {
		tipo_f=4;
		tipo_m=($('input:radio[name=radio_marca_tipo]:checked').val());
	}
	filtra_rep(cliente_f,tipo_f,marca_f,tipo_m,fecha_1,fecha_2,edo_f,cat_f,$orden);
}

function busc_xcat()
{
	var cliente_f=$("#txtbusca").val();
	var marca_f=$("#cbo_marcas").val();
	var fecha_1=$("#fecha_ini").val();
	var fecha_2=$("#fecha_fin").val();
	var tipo_m="";
	var tipo_f="";

	var edo_f=$("#cbo_edo").val();
	var cat_f=$("#cbo_cat").val();

	$orden = $("#cboOrden").val();


	  if(marca_f==='TODOS')
	  {
		tipo_f=1;
		marca_f="";
		//filtra_rep(cliente_f,tipo_f,marca_f,tipo_m,fecha_1,fecha_2,edo_f,cat_f);
	  }
	  else if(marca_f==='GENERICOS')
	  {
		tipo_f=2;
		marca_f="";
		//filtra_rep(cliente_f,tipo_f,marca_f,tipo_m,fecha_1,fecha_2,edo_f,cat_f);
	  }
	  else if(marca_f==='PERSON')
	  {
		tipo_f=3;
		marca_f="";
		//filtra_rep(cliente_f,tipo_f,marca_f,tipo_m,fecha_1,fecha_2,edo_f,cat_f);
	  }
	  else
	  {
		tipo_f=4;
		tipo_m=($('input:radio[name=radio_marca_tipo]:checked').val());
		//filtra_rep(cliente_f,tipo_f,marca_f,tipo_m,fecha_1,fecha_2,edo_f,cat_f);
	  }
	  filtra_rep(cliente_f,tipo_f,marca_f,tipo_m,fecha_1,fecha_2,edo_f,cat_f,$orden);

}

function busc_fecha()
{
	var cliente_f=$("#txtbusca").val();
	var marca_f=$("#cbo_marcas").val();
	var fecha_1=$("#fecha_ini").val();
	var fecha_2=$("#fecha_fin").val();
	var tipo_m="";
	var tipo_f="";

	var edo_f=$("#cbo_edo").val();
	var cat_f=$("#cbo_cat").val();

	$orden = $("#cboOrden").val();

	if(fecha_1!=='')
	{
	  if(marca_f==='TODOS')
	  {
		tipo_f=1;
		marca_f="";
		//filtra_rep(cliente_f,tipo_f,marca_f,tipo_m,fecha_1,fecha_2,edo_f,cat_f);
	  }
	  else if(marca_f==='GENERICOS')
	  {
		tipo_f=2;
		marca_f="";
		//filtra_rep(cliente_f,tipo_f,marca_f,tipo_m,fecha_1,fecha_2,edo_f,cat_f);
	  }
	  else if(marca_f==='PERSON')
	  {
		tipo_f=3;
		marca_f="";
		//filtra_rep(cliente_f,tipo_f,marca_f,tipo_m,fecha_1,fecha_2,edo_f,cat_f);
	  }
	  else
	  {
		tipo_f=4;
		tipo_m=($('input:radio[name=radio_marca_tipo]:checked').val());
		//filtra_rep(cliente_f,tipo_f,marca_f,tipo_m,fecha_1,fecha_2,edo_f,cat_f);
	  }
	  filtra_rep(cliente_f,tipo_f,marca_f,tipo_m,fecha_1,fecha_2,edo_f,cat_f,$orden);
	}
}
function busc_multi()
{
	cliente_f=$("#txtbusca").val();
	marca_f=$("#cbo_marcas").val();
	fecha_1=$("#fecha_ini").val();
	fecha_2=$("#fecha_fin").val();
	tipo_m=($('input:radio[name=radio_marca_tipo]:checked').val());
	tipo_f=4;
	var edo_f=$("#cbo_edo").val();
	var cat_f=$("#cbo_cat").val();
	$orden = $("#cboOrden").val();	
	filtra_rep(cliente_f,4,marca_f,tipo_m,fecha_1,fecha_2,edo_f,cat_f,$orden);
}

function filtra_rep(ncliente, tipo_f, nmarca,tipom,fech_1,fech_2,nedo,ncat,orden)
{
  $("#jqGrid").clearGridData();
  jQuery("#jqGrid").setGridParam({postData:{'cliente':ncliente, 'tipo':tipo_f, 'marca':nmarca, 'tipo_m':tipom, 'fecha1':fech_1, 'fecha2':fech_2,'estado':nedo,'categoria':ncat,'orden':orden},page:1}).trigger("reloadGrid");
  //getHeader(valor_cons,campo);
  if((tipo_f===1)||(tipo_f===1&&nmarca===''&&tipom===''&&fech_1===''&&fech_2===''&&nedo===''))
  {
  carga_cbo(ncliente);
  carga_edo(ncliente);
  carga_cat(ncliente);
  //carga_cat(ncliente);
  }
}

function carga_cbo(num_cliente)
{
  ///cte=$("#txtbusca").val();
  //var cte=num_cliente.substring(0,4);
  var cte=num_cliente;
  $.ajax({

	  type: "POST",
	  url: "php/cbo_marca_rep.php",//MISMA FUNCION QUE LOS RECIBOS
	  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
	  data: "cliente="+cte,
	  datatype: 'json',
	  success: function(response){

		  var jcbo=JSON.parse(response);
		  if(jcbo.status==='correcto')
		  {
		  $('#cbo_m').html(jcbo.cbo);
		  }
		  else
		  {
			   $('#cbo_m').html(jcbo.msj);
		  }
	  },
	  beforeSend:function()
	  {


	  }
  });
}

function carga_edo(num_cliente)
{
  ///cte=$("#txtbusca").val();
  //var cte=num_cliente.substring(0,4);
  var cte=num_cliente;
  $.ajax({

	  type: "POST",
	  url: "php/cbo_estado_rep.php",//MISMA FUNCION QUE LOS RECIBOS
	  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
	  data: "cliente="+cte,
	  datatype: 'json',
	  success: function(response){

		  var jcbo=JSON.parse(response);
		  if(jcbo.status==='correcto')
		  {
		  $('#cbo_e').html(jcbo.cbo);
		  }
		  else
		  {
			   $('#cbo_e').html(jcbo.msj);
		  }
	  },
	  beforeSend:function()
	  {


	  }
  });
}

function carga_cat(num_cliente)
{
  ///cte=$("#txtbusca").val();
  var cte=num_cliente.substring(0,5);
  $.ajax({

	  type: "POST",
	  url: "php/cbo_categoria_rep.php",//MISMA FUNCION QUE LOS RECIBOS
	  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
	  data: "cliente="+cte,
	  datatype: 'json',
	  success: function(response){

		  var jcbo=JSON.parse(response);
		  if(jcbo.status==='correcto')
		  {
		  $('#cbo_c').html(jcbo.cbo);
		  }
		  else
		  {
			   $('#cbo_c').html(jcbo.msj);
		  }
	  },
	  beforeSend:function()
	  {


	  }
  });
}

function llena_rep()
{
	if($("#jqGridPager").hasClass('ui-jqgrid-pager'))
	{
		jQuery("#tblclientes").trigger("reloadGrid");
	}
	else
	{
	  $.jgrid.styleUI.Bootstrap.base.rowTable = "table table-bordered table-striped";
	  $("#jqGrid").jqGrid({
		  url: 'php/reportes/filtra_rpt.php',
		  mtype: "POST",
		  datatype: "json",
		  colModel: [
			  { label: 'ID Recibo', name: 'recibo', width: 55, align:'center' },
			  { label: 'Marca', name: 'marca', width: 100, formatter: formatColors},
			  { label: 'Serie', name: 'serie', width: 32, align:'center', formatter: formatColors },
			  { label: 'Estado', name: 'edo', width: 60, align:'center', formatter: formatColors },
			  { label: 'Categoria', name: 'tipo', width: 65, align:'center', formatter: formatColors },
			  { label: 'Solicitud', name: 'solicitud', width: 60, align:'center', formatter: formatColors2},
			  { label: 'F. Entrega', name: 'solicitud', width: 60, align:'center'},
			  { label: 'F. Inicial', name: 'solicitud', width: 75 },
			  { label: 'F. Final', name: 'solicitud', width: 75 },
			  { label: 'Cantidad', name: 'tipo', width: 46}
		  ],
		  sortname : 'id_cliente',
		  autowidth: true,
		  postData: {
		  'tipo':1,'cliente':'0','orden':''
		  },
		  loadError: function (jqXHR, textStatus, errorThrown) {
        alert('HTTP status code: ' + jqXHR.status + '\n' +
              'textStatus: ' + textStatus + '\n' +
              'errorThrown: ' + errorThrown);
        alert('HTTP message body (jqXHR.responseText): ' + '\n' + jqXHR.responseText);
          },

         ondblClickRow: function(rowId) {
		 	get_observacion(rowId);
        },

		  height: 'auto',
		  rowheight: 300,
		  rowNum: 20,
		  viewrecords: true,
		  pager: "#jqGridPager",
		  caption: "Resultados de la busqueda"
	  });
	}
}

function formatColors(cellValue, options, rowObject) {
	var color = (cellValue==='N/A') ? "red" : "black";
	var cellHtml = "<span style='color:" + color + "' originalValue='" +
						 cellValue + "'>" + cellValue + "</span>";

	return cellHtml;
}

function formatColors2(cellValue, options, rowObject)
{
	var color = (cellValue==='S/N') ? "red" : "black";
	var cellHtml = "<span style='color:" + color + "' originalValue='" +
						 cellValue + "'>" + cellValue + "</span>";
	return cellHtml;
}

function rep_pdf()
{
	if(clvuser == 1 || cargo == 12 || cargo == 13) {
		  var  c_resumen;
		  var total_rows=jQuery("#jqGrid").jqGrid('getGridParam', 'records');
		  if(total_rows>0)
		  {
		    if ($('#resumen').is(":checked"))
			{
			  c_resumen='SI';
			}
			else
			{
			  c_resumen='NO';
			}
			var cliente_f=$("#txtbusca").val();
			var marca_f=$("#cbo_marcas").val();
			var fecha_1=$("#fecha_ini").val();
			var fecha_2=$("#fecha_fin").val();
		    var edo_f=$("#cbo_edo").val();
			var cat_f=$("#cbo_cat").val();

			if(marca_f==='TODOS')
			{
			  tipo_f=1;
			  marca_f="";
			}
			else if(marca_f==='GENERICOS')
			{
			  tipo_f=2;
			  marca_f="";
			}
			else if(marca_f==='PERSON')
			{
			  tipo_f=3;
			  marca_f="";
			}
			else
			{
			  tipo_f=4;
			  tipo_m=($('input:radio[name=radio_marca_tipo]:checked').val());
			}

			  $.ajax({
				type: "POST",
				url: "php/reportes/rpt_pdf.php",
				contentType: "application/x-www-form-urlencoded;charset=UTF-8",
				data: "cliente="+cliente_f+"&tipo="+tipo_f+"&marca="+marca_f+"&tipo_m="+tipo_m+"&fecha1="+fecha_1+"&fecha2="+fecha_2+"&estado="+edo_f+"&categoria="+cat_f+"&resumen="+c_resumen,
				datatype: 'json',
				success: function(response){
					//alert(response);
					destroy_progress(1);
					var j_res=JSON.parse(response);
					if(j_res.status==='correcto')
					{
					   var dir=j_res.msj;
					   window.open(dir, '_blank');
					}
					else
					{
					   $('#cbo_m').html(j_res.msj);
					}
				},
				beforeSend:function()
				{
					progres_b(1);
				}
			});

		  }
		  else
		  {
			  alert('No hay registros para exportar');
		  }

		}
}

function rep_excel()
{
		if(clvuser == 1 || cargo == 12 || cargo == 13 || cargo == 20) {
		  var c_resumen;
		  var total_rows=jQuery("#jqGrid").jqGrid('getGridParam', 'records');
		  if(total_rows>0)
		  {
			  if ($('#resumen').is(":checked"))
			  {
				c_resumen='SI';
			  }
			  else
			  {
				c_resumen='NO';
			  }
			  var cliente_f=$("#txtbusca").val();
			  var marca_f=$("#cbo_marcas").val();
			  var fecha_1=$("#fecha_ini").val();
			  var  fecha_2=$("#fecha_fin").val();
			  var edo_f=$("#cbo_edo").val();
			  var cat_f=$("#cbo_cat").val();
			  if(marca_f==='TODOS')
			  {
				tipo_f=1;
				marca_f="";
			  }
			  else if(marca_f==='GENERICOS')
			  {
				tipo_f=2;
				marca_f="";
			  }
			  else if(marca_f==='PERSON')
			  {
				tipo_f=3;
				marca_f="";
			  }
			  else
			  {
				tipo_f=4;
				tipo_m=($('input:radio[name=radio_marca_tipo]:checked').val());
			  }
			  var id_ses=getUrlParameter('d_s');
			  $.ajax({
				  type: "POST",
				  url: "php/reportes/rpt_excel.php",
				  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
				  data: "cliente="+cliente_f+"&tipo="+tipo_f+"&marca="+marca_f+"&tipo_m="+tipo_m+"&fecha1="+fecha_1+"&fecha2="+fecha_2+"&estado="+edo_f+"&categoria="+cat_f+"&resumen="+c_resumen+"&id_s="+id_ses,
				  datatype: 'json',
				  success: function(response){
					  destroy_progress(1);
					 // alert(response);
					  var j_res=JSON.parse(response);
					  if(j_res.status==='OK')
					  {
						 var dir=j_res.msj;
						 window.open(dir, '_blank');
					  }
					  else
					  {
						 $('#cbo_m').html(j_res.msj);
					  }
				  },
				  beforeSend:function()
				  {
					progres_b(1);
				  }
			  });
			}
		  else
		  {
			  alert('No hay registros para exportar');
		  }
		}
}

function rep_pdf_con()
{
		if(clvuser == 1 || cargo == 12 || cargo == 13 || cargo == 20) {
			var tipo_con=($('input:radio[name=radio_tipo_con]:checked').val());
		    var fecha1_con=$("#fecha_ini_con").val();
			var fecha2_con=$("#fecha_fin_con").val();
			var res_concen;
			 if ($('#resumen_con').is(":checked"))
			  {
				res_concen='SI';
			  }
			  else
			  {
				res_concen='NO';
			  }
			 $.ajax({
				type: "POST",
				url: "php/reportes/r_pdf_con.php",
				contentType: "application/x-www-form-urlencoded;charset=UTF-8",
				data: "tipo_con="+tipo_con+"&fecha1="+fecha1_con+"&fecha2="+fecha2_con+"&resumen="+res_concen,
				datatype: 'json',
				success: function(response){
					//alert(response);
					destroy_progress(2);
					var j_res=JSON.parse(response);
					if(j_res.status==='correcto')
					{
					   var dir=j_res.msj;
					   window.open(dir, '_blank');
					}
					else
					{
					   $('#cbo_m').html(j_res.msj);
					}
				},
				beforeSend:function()
				{
				 progres_b(2);
				}
			});
		}
}
function rep_excel_con()
{
		if(clvuser == 1 || cargo == 12 || cargo == 13 || cargo == 20) {
			var tipo_con=($('input:radio[name=radio_tipo_con]:checked').val());
		    var fecha1_con=$("#fecha_ini_con").val();
			var fecha2_con=$("#fecha_fin_con").val();
			var res_concen;
			 if ($('#resumen_con').is(":checked"))
			  {
				res_concen='SI';
			  }
			  else
			  {
				res_concen='NO';
			  }
			  var id_ses=getUrlParameter('d_s');
			  $.ajax({
				  type: "POST",
				  url: "php/reportes/r_excel_con_n.php?"+new Date().toISOString(),
				  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
				  data: "tipo_con="+tipo_con+"&fecha1="+fecha1_con+"&fecha2="+fecha2_con+"&resumen="+res_concen+"&id_s="+id_ses,
				  datatype: 'json',
				  success: function(response){
					  //alert(response);
					 destroy_progress(2);
					  var j_res=JSON.parse(response);
					  if(j_res.status==='OK')
					  {
						 var dir=j_res.msj;
						 window.open(dir, '_blank');
					  }
					  else
					  {
						 $('#cbo_m').html(j_res.msj);
					  }
				  },
				  beforeSend:function()
				  {
				  progres_b(2);
				  }
			  });
			}
}

function rep_excel_recibos() {
	if(clvuser == 1 || cargo == 12 || cargo == 13 || cargo == 20) {
	    var fechaini = $("#bFechaIni").val();
		var fechafin = $("#bFechaFin").val();
		//var res_concen;
		  var id_ses=getUrlParameter('d_s');
		  $.ajax({
			  type: "POST",
			  url: "php/reportes/r_excel_recibos.php",
			  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
			  data: "fechaini="+fechaini+"&fechafin="+fechafin+"&id_s="+id_ses,
			  datatype: 'json',
			  success: function(response){
				 destroy_progress(3);
				  var j_res=JSON.parse(response);
				  if(j_res.status==='OK')
				  {
					 var dir=j_res.msj;
					 window.open(dir, '_blank');
				  }
				  else
				  {
					 $('#cbo_m').html(j_res.msj);
				  }
			  },
			  beforeSend:function() {
			  	progres_b(3);
			  }
		  });
	}
}

function rep_excel_pedidos() {
	if(clvuser == 1 || cargo == 12 || cargo == 13 || cargo == 20) {
	    var fechaini = $("#bpFechaIni").val();
		var fechafin = $("#bpFechaFin").val();
		var nocliente = $("#txtnocontrol").val();
		//var res_concen;
		  var id_ses=getUrlParameter('d_s');
		  $.ajax({
			  type: "POST",
			  url: "php/reportes/r_excel_pedidos.php",
			  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
			  data: "fechaini="+fechaini+"&fechafin="+fechafin+"&id_s="+id_ses+"&nocliente="+nocliente,
			  datatype: 'json',
			  success: function(response){
				 destroy_progress(2);
				  var j_res=JSON.parse(response);
				  if(j_res.status==='OK')
				  {
					 var dir=j_res.msj;
					 window.open(dir, '_blank');
				  }
				  else
				  {
					 $('#cbo_m').html(j_res.msj);
				  }
			  },
			  beforeSend:function()
			  {
			  progres_b(2);
			  }
		  });
	}
}

function borra_fecha(f_b)
{
	if(f_b===1)
	{
      $("#fecha_ini").val('');
	  $("#fecha_fin").val('');
	}
	else
	{
	  $("#fecha_fin").val('');
	}
}
function borra_fecha_con(f_b)
{
	if(f_b===1)
	{
      $("#fecha_ini_con").val('');
	  $("#fecha_fin_con").val('');
	}
	else
	{
	  $("#fecha_fin_con").val('');
	}
}
var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};

function progres_b(id)
{
    var progressbar = $( "#progressbar"+id ),
      progressLabel = $( "#lbl_pb_"+id );
     progressLabel.text( "Generando informe..." );
    progressbar.progressbar({
      value: false,
      change: function() {
        progressLabel.text( progressbar.progressbar( "value" ) + "%" );
      },
      complete: function() {
        progressLabel.text( "Completo" );
      }
    });
   var progressbarValue = progressbar.find( ".ui-progressbar-value" );
   progressbarValue.css({
          "background": '#1189B0'
        });
}
function destroy_progress(id)
{
	var progressLabel = $( "#lbl_pb_"+id );
     progressLabel.text( "" );
 $( "#progressbar"+id ).progressbar( "destroy" );
}

function get_observacion(id)
{
	$.ajax({
		type: "POST",
		url: "php/reportes/get_observacion.php",
		contentType: "application/x-www-form-urlencoded;charset=UTF-8",
		data: "id="+id,
		dataType: "json",
		success: function(data) {
			if (data.status === "OK") {
				 $('#mdlObservacionesReporte').html(data.msj);
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

function loadRecibos(){
	var tableTest=$('#tablaRecibos').bootstrapTable({
	    url: "php/reportes/informacion_recibos.php",
	    //esto para paginar
	    queryParams:function(p){
                var searchSend="";
                var datos=[];
                if(p.search){
                    datos={
                    limit: p.limit,
                    offset: p.offset,
                    sort:p.sort,
                    order:p.order,
                    search:p.search,
                    estatus: ""
                    };
                }else{
                    datos={
                    fechaini:$('#bFechaIni').val(),
                    fechafin:$('#bFechaFin').val(),
                    limit: p.limit,
                    offset: p.offset,
                    sort:p.sort,
                    order:p.order,
                    search:''
                    };
                }
	        return datos;

      	},
			showRefresh: true,
			search: true,
			showToggle: true, //
			showColumns: true, // menu muestra columnas
      //showExport: true,
			checkboxHeader: true,
			toolbar: '#toolbar', // hace referencia al dom que tiene el toolbar
			columns: [
	      {
	          field: 'id_recibo',
	          title: 'ID RECIBO',
	          sortable:true,
	          visible: true,
	      },{
					field: 'anio',
					title: 'AÑO',
					sortable:true,
			},{
					field: 'nocliente',
					title: '# DE CONTROL',
					sortable:true,
			},{
					field: 'marca',
					title: 'MARCA',
					sortable:true,
			},{
					field: 'serie',
					title: 'SERIE',
					sortable:true,
			}, {
					field: 'estado',
					title: 'ESTADO',
					sortable:true,
			},{
					field: 'categoria',
					title: 'CATEGORÍA',
					sortable:true,
			},{
					field: 'solicitud',
					title: 'SOLICITUD',
					sortable:true,
			},{
				field: 'persona_entrega',
				title: 'PERSONA ENTREGA',
				sortable:true,
			},{
				field: 'fecha_entrega',
				title: 'FECHA DE ENTREGA',
				sortable:true,
			},{
					field: 'folioi',
					title: 'FOLIO INICIAL',
					sortable:true,
			},{
					field: 'foliof',
					title: 'FOLIO FINAL',
					sortable:true,
			},{
					field: 'cantidad',
					title: 'CANTIDAD',
					sortable:true,
			},{
            title: 'ACCIÓN',
            sortable:true,
            align: 'center',
            events: window.operateEvents,
            formatter: operateFormatter
        }
	    ,
	     ],
	    pagination: true ,
		  sortStable: true,
	    pageNumber: 1, // pagina q se muestra por default
	    pageSize: 5,
	    //Total de resultados q se muestran debe ser menor y igual al numero q comience pageList para q sea necesario el pagaList y se  pueda mostrar
	    pageList: [10, 25, 50, 100],//
	    smartDisplay: true,
      sidePagination: "server",
	   // showPaginationSwitch: true,
	    paginationVAlign: "bottom",//formato de botones en paginacion
	    cache: false,
	    //rowStyle: "rowStyle",
      showColumns: true,
	    maintainSelected: true,
	   // rowStyle: "pintaDictamenes",
	});
}

function loadPedidos(){
	CLIENTESEL = (CLIENTESEL != "") ? CLIENTESEL: $('#txtnocontrol').val();
	var tableTest=$('#tablaPedidos').bootstrapTable({
	    url: "php/reportes/informacion_pedidos.php",
	    //esto para paginar
	    queryParams:function(p){
                var searchSend="";
                var datos=[];
                if(p.search){
                    datos={
                    limit: p.limit,
                    offset: p.offset,
                    sort:p.sort,
                    order:p.order,
                    search:p.search,
                    estatus: ""
                    };
                }else{
                    datos={
                    fechaini:$('#bpFechaIni').val(),
                    fechafin:$('#bpFechaFin').val(),
                    nocliente: CLIENTESEL,
                    limit: p.limit,
                    offset: p.offset,
                    sort:p.sort,
                    order:p.order,
                    search:''
                    };
                }
	        return datos;

      	},
			showRefresh: true,
			search: true,
			showToggle: true, //
			showColumns: true, // menu muestra columnas
      showExport: true,
			checkboxHeader: true,
			toolbar: '#toolbar', // hace referencia al dom que tiene el toolbar
			
			columns: [
	      {
	          field: 'id_row',
	          title: 'ID',
	          sortable:true,
	          visible: false,
	      },{
					field: 'no_pedido',
					title: '# PEDIDO',
					sortable:true,
			},{
					field: 'fecha',
					title: 'FECHA',
					sortable:true,
			},{
					field: 'no_cliente',
					title: '# DE CONTROL',
					sortable:true,
			},{
					field: 'cve_marca',
					title: 'CLAVE',
					sortable:true,
			},{
					field: 'marca',
					title: 'MARCA',
					sortable:true,
			},{
					field: 'estado',
					title: 'ESTADO',
					sortable:true,
			}, {
					field: 'folio',
					title: 'SOLICITUD',
					sortable:true,
			},{
					field: 'categoria',
					title: 'CATEGORÍA',
					sortable:true,
			},{
					field: 'holograma',
					title: 'VERSIÓN',
					sortable:true,
			},{
					field: 'tipo_pago',
					title: 'TIPO DE PAGO',
					sortable:true,
			},{
					field: 'urgente',
					title: 'PRIORIDAD',
					sortable:true,
			},{
					field: 'estatus',
					title: 'ESTATUS',
					sortable:true,
			},{
					field: 'folioi',
					title: 'FOLIO INICIAL',
					sortable:true,
			},{
					field: 'foliof',
					title: 'FOLIO FINAL',
					sortable:true,
			},{
					field: 'cantidad',
					title: 'CANTIDAD',
					sortable:true,
			}/*,{
            title: 'ACCIÓN',
            sortable:true,
            align: 'center',
            events: window.operateEvents,
            formatter: operateFormatter
        	},*/
	     ],
	    pagination: true ,
		  sortStable: true,
	    pageNumber: 1, // pagina q se muestra por default
	    pageSize: 5,
	    //Total de resultados q se muestran debe ser menor y igual al numero q comience pageList para q sea necesario el pagaList y se  pueda mostrar
	    pageList: [10, 25, 50, 100],//
	    smartDisplay: true,
      sidePagination: "server",
	   // showPaginationSwitch: true,
	    paginationVAlign: "bottom",//formato de botones en paginacion
	    cache: false,
	    //rowStyle: "rowStyle",
      showColumns: true,
	    maintainSelected: true,
	   // rowStyle: "pintaDictamenes",
	   /*onPostBody: function (row) {
		//var rowData = $('#tblAccountList').bootstrapTable('getRowByUniqueId', 70);
		//console.log("RowData: " + JSON.stringify(rowData));                                
		console.log(row);
		},*/
	});
}


$('#tablaPedidos').on('load-success.bs.table', function (event, field, value, row, td) {
  //console.log(field.totalH);
  $('#mAcumulado').html('&nbsp;&nbsp;'+field.totalH+'&nbsp;&nbsp;');
})

/*tableTest=$('#tablaPedidos');
$('#tablaPedidos').on('post-body.bs.table', function (event, rowArray) { // I prefer to call ``data`` as ``rowArray``.
  console.log("holiiissss"); 
})*/

function headerStyle(row, index) {
    return {classes: 'success'};
}

window.operateEvents = {
    'click .subeAcuse': function (e, value, row, index) {
        //$('#et_obsAAprob').val(row.obs_final);
        //$('#mdlObservaciones').modal('show');
        $("#modalSubir").modal({
            keyboard: false
        });
        idSalida = row.id_salidas;
        if(row.acuse == '1')
        	reiniciarInputFileCA(idSalida);
        else
        	reiniciarInputFileSA();

    }
}


function operateFormatter(value, row, index) {
	if(row.acuse == '1') {
		return [
	      '<a class="reciboPdf" href="php/reportes/descargar_documento.php?id='+row.nombreRecibo+'&tipo=R" title="Descargar Recibo" target="_blank">',
	      '<span style="color: Red;"><i class="fa fa-file-pdf-o fa-lg"></i></span>',
	      '</a>&nbsp;&nbsp;&nbsp;&nbsp;',
	      '<a class="acusePdf" href="php/reportes/descargar_documento.php?id='+row.nombreRecibo+'&tipo=AR" title="Descargar Acuse" target="_blank">',
	      '<span style="color: Blue;"><i class="fa fa-file-pdf-o fa-lg"></i></span>',
	      '</a>&nbsp;&nbsp;&nbsp;&nbsp;',
	      '<a class="subeAcuse" href="javascript:void(0)" title="Subir Acuse" >',
	      '<i class="fa fa-upload fa-lg"></i>',
	      '</a>&nbsp;&nbsp;'
	    ].join('')
	} else {
	    return [
	      '<a class="reciboPdf" href="php/reportes/descargar_documento.php?id='+row.nombreRecibo+'&tipo=R" title="Descargar Recibo" target="_blank">',
	      '<span style="color: Red;"><i class="fa fa-file-pdf-o fa-lg" aria-hidden="true"></i></span>',
	      '</a>&nbsp;&nbsp;&nbsp;&nbsp;',
	      '<a class="subeAcuse" href="javascript:void(0)" title="Subir Acuse" >',
	      '<i class="fa fa-upload fa-lg"></i>',
	      '</a>&nbsp;&nbsp;'
	    ].join('')
	}
}

function exportTable() {
      $ ("# tablaRecibos"). tableExport ({fileName: 'nombre de archivo', tipo: "xls", escape: "false"});
    }


$('#input-id').on('fileuploaded', function(event, data, previewId, index) {
    $('#tablaRecibos').bootstrapTable('refresh');
    // CONSULTAR PRODUCTO PARA VALIDAR EL INITIALPREVIEW
    /*$.ajax({
        type: "GET",
        url: "php/loadProductos.php",
        contentType: "application/x-www-form-urlencoded;charset=UTF-8",
        data: {
            numero_informe: $("#et_numInforme").val(),
            num_com_informe: numComInforme,
            numCliente: data.extra.numCliente,
            idProducto: data.extra.idProducto,
            limit: 1,
            offset: 0
        },
        datatype: 'json',
        success: function(response) {
            response = JSON.parse(response);
            nrow = response.rows[0];
            // RECARGAR fileinput
            reiniciarInputFileCA(data.extra.numCliente,nrow.nombreArchivo);
        },
        beforeSend: function() {
        }
    });*/
    //console.log(data);
    //reiniciarInputFileCA(data.extra.numCliente,nrow.nombreArchivo);
    reiniciarInputFileCA(data.extra.id_salida);
});

$('#input-id').on('filedeleted', function(event, key, jqXHR, data) {
    $('#tablaRecibos').bootstrapTable('refresh');
    reiniciarInputFileSA();
});

function reiniciarInputFileI() {
    // DESTRUYENDO VALORES DE input file
    $("#input-id").fileinput('destroy');
    $('#input-id').attr('disabled', true);
    //$('#input-id').attr('disabled', false);
    // INICIALIZANDO input file
    $("#input-id").fileinput({
        showCaption: false,
        //dropZoneEnabled: false,
        showUpload: false,
        showRemove: false,
        allowedFileExtensions: ["pdf", "PDF"],
        language: 'es',
        maxFileSize: 51200, // NO MÁS DE 50 MB POR ARCHIVO
        validateInitialCount: true,
        overwriteInitial: false,
        previewFileType:'any',
        uploadAsync: false,
        maxFileCount: 1,
    });
}

function reiniciarInputFileCA(idSalida) {
    // RECARGAR fileinput
    $("#input-id").fileinput('destroy');
    $('#input-id').attr('disabled', false);
    $("#input-id").fileinput({
        showCaption: false,
        //dropZoneEnabled: false,
        showUpload: false,
        showRemove: false,
        allowedFileExtensions: ["pdf", "PDF"],
        language: 'es',
        maxFileSize: 51200, // NO MÁS DE 50 MB POR ARCHIVO
        validateInitialCount: true,
        overwriteInitial: false,
        previewFileType:'any',
        uploadAsync: false,
        maxFileCount: 1,
        uploadUrl: "php/reportes/upload.php", // your upload server url
        uploadExtraData: function() {
            return {
                id_salida: idSalida,
                userid: clvuser
            };
        },
        initialPreviewAsData: true,
        initialPreviewFileType: 'pdf',
        //initialPreview: ["../../../../documentos/"+carpetaUnica+"/"+nombreArchivo],
        initialPreview: ['php/reportes/descargar_documento.php?id_salida='+idSalida+'&tipo=A'],
        initialPreviewConfig:
        [{type: 'pdf', downloadUrl: false, caption: 'recibo.pdf',
            url: 'php/reportes/quitarArchivo.php',
        key: idSalida }]
    }); //*/
}

function reiniciarInputFileSA() {
    // RECARGAR fileinput
    // RECARGAR fileinput
    $("#input-id").fileinput('destroy');
    $("#input-id").fileinput({
        showCaption: false,
        //dropZoneEnabled: false,
        showUpload: false,
        showRemove: false,
        allowedFileExtensions: ["pdf", "PDF"],
        language: 'es',
        maxFileSize: 51200, // NO MÁS DE 50 MB POR ARCHIVO
        validateInitialCount: true,
        overwriteInitial: false,
        previewFileType:'any',
        uploadAsync: false,
        maxFileCount: 1,
        uploadUrl: "php/reportes/upload.php", // your upload server url
        uploadExtraData: function() {
            return {
                id_salida: idSalida,
                userid: clvuser
            };
        }
    });
}
