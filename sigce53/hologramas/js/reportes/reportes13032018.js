"use strict";
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
	
$( document ).ready(function() {
  //Obtener Recibo
  $( "#tabs" ).tabs();
  $( "#fecha_ini" ).datepicker({
	changeMonth: true,
    changeYear: true,	
	yearRange: '2010:2018',
	buttonText: "Seleccionar Fecha",
	onClose: function(){
        busc_fecha();
     }
  });
  $( "#fecha_fin" ).datepicker({
	changeMonth: true,
    changeYear: true,
	yearRange: '2010:2018',
	buttonText: "Seleccionar Fecha",
	onClose: function(){
        busc_fecha();
     }
  });
  //PARA EL REPORTE CONCENTRADO
    $( "#fecha_ini_con" ).datepicker({
	changeMonth: true,
    changeYear: true,	
	yearRange: '2010:2018',
	buttonText: "Seleccionar Fecha",
	onClose: function(){
        
     }
  });
  $( "#fecha_fin_con" ).datepicker({
	changeMonth: true,
    changeYear: true,
	yearRange: '2010:2018',
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
			select: function(e,ui){ filtra_rep(ui.item.value,1,'','','',''); limpiar();}
		}).keypress(function(e) {

          if (e.keyCode === 13) 
          {
          return false;
          }

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
	if(marca_f==='TODOS')
	{
	  tipo_f=1;
	  marca_f="";
	  filtra_rep(cliente_f,tipo_f,marca_f,tipo_m,fecha_1,fecha_2);
	  //$('#muestra_por').css('display','none');
	  $('#lbl_mixto').css('visibility','hidden');
	  $('#div_mixto').css('visibility','hidden');
	}
	else if(marca_f==='GENERICOS')
	{
	  tipo_f=2;
	  marca_f="";
	  filtra_rep(cliente_f,tipo_f,marca_f,tipo_m,fecha_1,fecha_2);	
	  //$('#muestra_por').css('display','none');
	  $('#lbl_mixto').css('visibility','hidden');
	  $('#div_mixto').css('visibility','hidden');
	}
	else if(marca_f==='PERSON')
	{
	  tipo_f=3;
	  marca_f="";
	  filtra_rep(cliente_f,tipo_f,marca_f,tipo_m,fecha_1,fecha_2);	
	  //$('#muestra_por').css('display','none');
	}
	else
	{
	  tipo_f=4;
	  tipo_m='T';
	  filtra_rep(cliente_f,tipo_f,marca_f,tipo_m,fecha_1,fecha_2);
	  //$('#muestra_por').css('display','block');	  
	  $('#lbl_mixto').css('visibility','visible');
	  $('#div_mixto').css('visibility','visible');
	  //$("#tipo_t").click();
	  //$('input:radio[name=radio_marca_tipo][value=T]').attr('checked', true);
	  reset_mixto();
	}
}

function busc_fecha()
{
	var cliente_f=$("#txtbusca").val();
	var marca_f=$("#cbo_marcas").val();
	var fecha_1=$("#fecha_ini").val();
	var fecha_2=$("#fecha_fin").val();
	var tipo_m="";
	var tipo_f="";
	if(fecha_1!=='')
	{
	  if(marca_f==='TODOS')
	  {
		tipo_f=1;
		marca_f="";
		filtra_rep(cliente_f,tipo_f,marca_f,tipo_m,fecha_1,fecha_2);
	  }
	  else if(marca_f==='GENERICOS')
	  {
		tipo_f=2;
		marca_f="";
		filtra_rep(cliente_f,tipo_f,marca_f,tipo_m,fecha_1,fecha_2);	
	  }
	  else if(marca_f==='PERSON')
	  {
		tipo_f=3;
		marca_f="";
		filtra_rep(cliente_f,tipo_f,marca_f,tipo_m,fecha_1,fecha_2);	
	  }
	  else
	  {
		tipo_f=4;
		tipo_m=($('input:radio[name=radio_marca_tipo]:checked').val());
		filtra_rep(cliente_f,tipo_f,marca_f,tipo_m,fecha_1,fecha_2);
	  }
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
	filtra_rep(cliente_f,4,marca_f,tipo_m,fecha_1,fecha_2);	
}

function filtra_rep(ncliente, tipo_f, nmarca,tipom,fech_1,fech_2)
{
  $("#jqGrid").clearGridData();
  jQuery("#jqGrid").setGridParam({postData:{'cliente':ncliente, 'tipo':tipo_f, 'marca':nmarca, 'tipo_m':tipom, 'fecha1':fech_1, 'fecha2':fech_2},page:1}).trigger("reloadGrid");
  //getHeader(valor_cons,campo);
  if((tipo_f===1&&$("#cbo_marcas").val()!=='TODOS')||(tipo_f===1&&nmarca===''&&tipom===''&&fech_1===''&&fech_2===''))
  {
  carga_cbo(ncliente);
  }
}
function carga_cbo(num_cliente)
{ 
  ///cte=$("#txtbusca").val();
  var cte=num_cliente.substring(0,4);   
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
			  { label: 'ID Recibo', name: 'recibo', width: 47, align:'center' },
			  { label: 'Marca', name: 'marca', width: 100, formatter: formatColors},
			  { label: 'Serie', name: 'serie', width: 32, align:'center', formatter: formatColors },
			  { label: 'Estado', name: 'edo', width: 60, align:'center'},
			  { label: 'Solicitud', name: 'solicitud', width: 50, align:'center', formatter: formatColors2},
			  { label: 'F. Entrega', name: 'solicitud', width: 50, align:'center'},
			  { label: 'F. Inicial', name: 'solicitud', width: 67 },
			  { label: 'F. Final', name: 'solicitud', width: 67 },
			  { label: 'Cantidad', name: 'tipo', width: 46}                   
		  ],
		  sortname : 'id_cliente',
		  autowidth: true,
		  postData: {
		  'tipo':1,'cliente':'0412'
		  },
		  loadError: function (jqXHR, textStatus, errorThrown) {
        alert('HTTP status code: ' + jqXHR.status + '\n' +
              'textStatus: ' + textStatus + '\n' +
              'errorThrown: ' + errorThrown);
        alert('HTTP message body (jqXHR.responseText): ' + '\n' + jqXHR.responseText);
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
		data: "cliente="+cliente_f+"&tipo="+tipo_f+"&marca="+marca_f+"&tipo_m="+tipo_m+"&fecha1="+fecha_1+"&fecha2="+fecha_2+"&resumen="+c_resumen,
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

function rep_excel()
{	
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
		  data: "cliente="+cliente_f+"&tipo="+tipo_f+"&marca="+marca_f+"&tipo_m="+tipo_m+"&fecha1="+fecha_1+"&fecha2="+fecha_2+"&resumen="+c_resumen+"&id_s="+id_ses,
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

function rep_pdf_con()
{
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
function rep_excel_con()
{
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
		  url: "php/reportes/r_excel_con.php",
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