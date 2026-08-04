"use strict";
$.jgrid.defaults.responsive = false;
$.jgrid.defaults.styleUI = 'Bootstrap';
var list_mermas  = { };
var fol_mermas_nums='';
var count_mermas=0;
var tipo_h="";
var cte="";
var marca="";
var serie="";
var tot_ini=0;
$( document ).ready(function() {	
  //Obtener Recibo
 fill_sincronizar();
  $('form').bind("keypress", function(e) {
  if (e.keyCode === 13) {               
    e.preventDefault();
    return false;
  }
  }); 
  //autocomplete
		$("#txtByRecibo").autocomplete({
			source: "php/sincronizar/suggest_recibo.php",
			minLength: 1,
			maxRows: 15,
			//select: function(e,ui){ carga_cbo(ui.item.value);}
			select: function(e,ui1){ 
			consulta_sinc(ui1.item.value,'id_recibo');
			//alert(ui1.item.value);
			}
		}).keypress(function(e) {

		  if (e.keyCode === 13) 
		  {
		  return false;
		  }
	  });	//END AUTOCOMPLETE
	  $("#txtByCliente").autocomplete({
			source: "php/sincronizar/suggest_no_cliente.php",
			minLength: 1,
			maxRows: 15,
			//select: function(e,ui){ carga_cbo(ui.item.value);}
			select: function(e,ui1){ 
			consulta_sinc(ui1.item.value,'no_cliente');
			//alert(ui1.item.value);
			}
		}).keypress(function(e) {

		  if (e.keyCode === 13) 
		  {
		  return false;
		  }
	  });	//END AUTOCOMPLETE
	  $("#txtByMarca").autocomplete({
			source: "php/sincronizar/suggest_by_marca.php",
			minLength: 1,
			maxRows: 15,
			//select: function(e,ui){ carga_cbo(ui.item.value);}
			select: function(e,ui1){ 
			consulta_sinc(ui1.item.value,'marca');
			//alert(ui1.item.value);
			}
		}).keypress(function(e) {

		  if (e.keyCode === 13) 
		  {
		  return false;
		  }
	  });	//END AUTOCOMPLETE
   $('#fol_ini_mermas').on('keyup', function (event) {
	   if(event.which === 13){		   
	   event.preventDefault();
		if($('#fol_ini_mermas').val()!=='')
		{
			$('#fol_fin_mermas').focus();
		}
		else
		{
			$('#fol_ini_mermas').focus();
		}
	   }
   });
   
   $('#fol_fin_mermas').on('keyup', function (event) {
	   if(event.which === 13){
		   event.preventDefault();
		  if($('#fol_fin_mermas').val()!=='')
		  {
			  addMerma(2);
		  }
		  else
		  {
			  addMerma(1);
		  }
	   }
   });  
});
function lista(tipo,clave)
{
	  $.ajax({
		  type: "POST",
		  url: "php/sincronizar/lista.php",
		  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
		  data: "tipo="+tipo+"&clave="+clave,
		  datatype: 'json',
		  success: function(response){
			  $('#table_body').html(response);
		  },
		  beforeSend:function()
		  {
			  
			   $("#add_err").html("Loading...");
		  }
	  });
	
}
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
			  { label: 'Fecha', name: 'fecha', width: 75, sortable:true, align:'center' },
			  { label: '# Cte', name: 'no_cliente', width: 50, align:'center'},
			  { label: 'Marca', name: 'marca', width: 130 },
			  { label: 'Tipo', name: 'tipo', width: 80, align:'center'},
			  { label: 'Estado', name: 'estado', width: 90, align:'center'},
			  { label: 'Cantidad', name: 'cantidad', width: 70, align:'center' },
			  { label: 'Importe', name: 'importe', width: 85, align:'right' },
			  { label: 'Prioridad', name: 'prioridad', width: 80, align:'center'},
			  { label: 'Estatus', name: 'status', width: 80, align:'center',cellattr: bgnd_cell_online},
			  { label: '--', name: 'link', width: 80, align:'center'}			                   
		  ],
		  loadComplete : function() { 
		   	  bgnd_row_online(); 			  
             //$('#jqGridPager_right').css('text-align','left');
          },  
		  postData: {
		   'depto':id_depto 
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
			  groupText: ["<b>&nbsp;&nbsp;FOL-{0}</b>"],
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
		  caption: "Resultados de la busqueda"
	  });
	  
	}
}
function fill_sincronizar()
{
	if($("#jqGridPager_sinc").hasClass('ui-jqgrid-pager'))
	{
		$("#jqGrid_sinc").trigger("reloadGrid");
	}
	else
	{
	  $.jgrid.styleUI.Bootstrap.base.rowTable = "table table-bordered table-striped";
	  $("#jqGrid_sinc").jqGrid({
		  url: 'php/sincronizar/listado_sinc.php',
		  mtype: "POST",
		  datatype: "json",
		  colModel: [
			  { label: 'Recibo', name: 'id_recibo', width: 60, align:'center', sortable:true },
			  { label: 'Cliente', name: 'cliente', width: 55, sortable:true, align:'center' },
			  { label: 'Marca', name: 'marca', width: 140 },
			  { label: 'Serie', name: 'serie', width: 40, align:'center'},
			  { label: 'Solicitud', name: 'solicitud', width: 90, align:'center'},
			  { label: 'Fol. Inicial', name: 'fol_ini', width: 85, align:'center' },
			  { label: 'Fol. Final', name: 'fol_fin', width: 85, align:'right' },
			  { label: 'Cantidad', name: 'cantidad', width: 70, align:'center'},
			  { label: 'Verificar', name: 'verificar', width: 80, align:'center'}                  
		  ],
		  loadComplete : function() { 
		   	 
          },  
		  postData: {
		   'depto':id_depto 
		  },
		  sortname : 'id_recibo',
		  sortorder: 'desc',
		  autowidth: true,
		  height: 'auto',
		  rowheight: 300,
		  rowNum: 30,
		  rowList:[30,50,100], 
		  viewrecords: true,
		  pager: "#jqGridPager_sinc",
		  loadError: function (jqXHR, textStatus, errorThrown) {
			  alert('HTTP status code: ' + jqXHR.status + '\n' +
					'textStatus: ' + textStatus + '\n' +
					'errorThrown: ' + errorThrown);
			  alert('HTTP message body (jqXHR.responseText): ' + '\n' + jqXHR.responseText);
		  },
		  caption: "Resultados de la busqueda"
	  });
	  
	}
}
function reload_sinc()
{
	 $("#jqGrid_sinc").clearGridData();
	 $("#jqGrid_sinc").setGridParam({postData: null});
	 $("#jqGrid_sinc").setGridParam({postData:{'depto':id_depto},page:1}).trigger("reloadGrid");				
}
function consulta_sinc(valor_cons,campo)
{
	//alert(valor_cons+ ' : '+ campo);
  $("#jqGrid_sinc").clearGridData();
  $("#jqGrid_sinc").setGridParam({postData:{'valor':valor_cons, 'campo':campo},page:1}).trigger("reloadGrid");
  clean_search(campo);
}
function clean_search(v_field)
{
	switch(v_field)
	{
		case 'txtByRecibo':
		{
			$('#txtByCliente').val('');
			break;
		}
		case 'no_cliente':
		{
			$('#by_pedido_espera').val('');
			break;
		}
		case 'marca':
		{
			$('#by_marca').val('');
			break;
		}
		case 'clear_all':
		{
			$('#by_asoc_espera').val('');
			$('#by_pedido_espera').val('');
			$('#by_marca').val('');
			break;
		}
	}
}

function frm_detalle(id_recibo)
{
	
  $('#modalSalida').modal( "show" );
	carga_detalle(id_recibo);
}

function carga_detalle(recibo)
{ 
      limpiar();
	  $.ajax({
		  type: "POST",
		  url: "php/sincronizar/carga_detalle.php",
		  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
		  data: "clave="+recibo,
		  datatype: 'json',
		  success: function(response){
		  var data_salida=JSON.parse(response);
		  if(data_salida.status==='OK')
		   {
			   var fol_rcbo='AR'+pad(data_salida.id_recibo,4)+"/"+data_salida.anio_recibo;
			   cte=data_salida.no_cliente;
			   var fol_ini,fol_fin;
			   marca=data_salida.cve_marca;
			   serie=data_salida.serie;
			   if(data_salida.serie!="-"&&data_salida.serie!="")
			   {
			   $('#tipo').val('P');
			   tipo_h='P';
			   fol_ini=data_salida.no_cliente+data_salida.cve_marca+pad(data_salida.fi1,7)+data_salida.serie;
			   fol_fin=data_salida.no_cliente+data_salida.cve_marca+pad(data_salida.ff1,7)+data_salida.serie;
			   }
			   else
			   {
				   $('#tipo').val('G');
				   tipo_h='G';
				   fol_ini=data_salida.fi1;
				   fol_fin=data_salida.ff1;
			   }
			   //campos ocultos
			   $('#id_salidas').val(data_salida.id_salidas);
			   $('#cve_marca').val(data_salida.cve_marca);
			   $('#vfini').val(data_salida.fi1);
			   $('#vffin').val(data_salida.ff1);
			   //campos visibles
			   $('#recibo').val(fol_rcbo);
			   $('#cliente').val(data_salida.no_cliente);
			   if(data_salida.cve_marca=='N/A')
			   {
				   $('#marca').val('');
			   }
			   else
			   {
			   $('#marca').val(data_salida.cve_marca+'-'+data_salida.marca);
			   }
			   if(parseInt(data_salida.m1)>0)
			   {
				   $('#mer_ent').val(data_salida.m1);
				   $('#mer_ent_fol').val(data_salida.fol_m1);
			       $('#mer_ent_num').val(data_salida.fol_m1_num);
				   var arr_ent=data_salida.fol_m1_num.split(',');
				   for(var i=0; i<arr_ent.length;i++)
				   {
					    list_mermas[arr_ent[i]] =arr_ent[i];
				   }
				   $('#mermas_ent').css('display','block');
			   }
			   $('#serie').val(data_salida.serie);
			   $('#n_solicitud').val(data_salida.solicitud);
			   $('#fecha_e').val(data_salida.fecha_e);
			   $('#destino').val(data_salida.destino);
			   $('#fini').val(fol_ini);
			   $('#ffin').val(fol_fin);
			   $('#cantidad').val(data_salida.se1);
			   $('#mtvo_merma').focus();
		   }
		  },
		  beforeSend:function()
		  {
			  
			   $("#add_err").html("Loading...")
		  }
	  });
	
}
function limpiar()
{
   $('#id_recibo').val('');
   $('#cve_marca').val('');
   $('#vfini').val('');
   $('#vffin').val('');
   //campos visibles
   $('#recibo').val('');
   $('#cliente').val('');
   $('#marca').val('');
   $('#serie').val('');
   $('#n_solicitud').val('');
   $('#fecha_e').val('');
   $('#destino').val('');
   $('#fini').val('');
   $('#ffin').val('');
   $('#cantidad').val('');
   $('#observ').val(''); 
   $('#total').val('');
   limpiar_mermas();
}
function delMermas()
{	
	list_mermas={};
	count_mermas=0;
	fol_mermas_nums="";
	$('#mtvo_merma option[value=NS]').prop('selected', true);
	//$('#mtvo_merma').prop('disabled',true);
	$('#btnDelMermas').css('visibility','hidden');
	$('#mer_rep').val(count_mermas);
	get_totalEntrega();
	$('#fol_ini_mermas').val('');					   
	$('#fol_fin_mermas').val('');
	$('#mer_rep_fol').val('');			
	//$('#total_sellos').val('');	
	$('#div_mermas').css('display','none');
	$('#div_fols').css('display','none');
}
function limpiar_mermas()
{	
	list_mermas={};
	count_mermas=0;
	fol_mermas_nums="";
	$('#mtvo_merma option[value=NS]').prop('selected', true);
	//$('#mtvo_merma').prop('disabled',true);
	$('#mermas_ent').css('display','none');
	$('#mer_ent').val('');
	$('#btnDelMermas').css('visibility','hidden');
	$('#mer_rep').val(count_mermas);
	//get_totalEntrega();
	$('#fol_ini_mermas').val('');					   
	$('#fol_fin_mermas').val('');
	$('#mer_rep_fol').val('');			
	//$('#total_sellos').val('');	
	$('#div_mermas').css('display','none');
	$('#div_fols').css('display','none');
}

function enterK(ev,nop)
{
	if(ev.keyCode=='13')
	{
	 alert(nop);
	}
}

function pad (str, maxi) {
  str = str.toString();
  return str.length < maxi ? pad("0" + str, maxi) : str;
}
function acceptNum(e,field)
{
	if(e.keyCode=='13')
	{
	  calc_total()
	  return false;
	}
	else
	{
	var key = (document.all) ? e.keyCode : e.which;
	return (key < 13 || (key>= 48 && key <= 57));
	}
}

function calc_total()
{
      var merm=parseInt($('#mer_rep').val());
	  var ctd=parseInt($('#cantidad').val());
	  if(merm=='')
	  {
		$('#mer_rep').val('0'); 
		$('#total').val(ctd); 
		$('#observ').focus();
	  }
      else
	  {
		if(merm>=ctd)
		{
			alert('La merma deber ser menor a la cantidad entregada '+ctd+' '+merm);
			$('#total').val('');
			$('#mer_rep').focus();
		}
		else
		{
			var tot_fin=ctd-merm;
			$('#total').val(tot_fin); 
			 $('#observ').focus();
		}	
	  }
}

function sinc_salida()
{
	 calc_total(); 	
	 var id_salida_del=$('#id_salidas').val();  
	  var datos_env=$('#frmDetalle_salida').serialize();
	  //alert(datos_env);
	  $.ajax({
		  type: "POST",
		  url: "php/sincronizar/sinc_salida.php",
		  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
		  data: datos_env,
		  datatype: 'json',
		  success: function(response){
			  //alert(response);
			 var resp=JSON.parse(response);
			 if(resp.status==='OK')
			 {
			  alert(resp.msj);
			  limpiar();
			  $('#modalSalida').modal('hide');
			  $('#jqGrid_sinc').jqGrid('delRowData',id_salida_del);
			 }
			 else
			 {
				alert(resp.msj+' Detalle:'+resp.sql); 
			 }
		  },
		  beforeSend:function()
		  {
			  
			   $("#add_err").html("Cargando...");
		  }
	  });
	
}
//------INICIAN FUNCIONES PARA LAS MERMAS
function ver_add()
{
    if($('#mtvo_merma').val()!='NS')
	{
	  $('#div_mermas').css('display','block');
	  $('#div_fols').css('display','block');
	  $('#fol_ini_mermas').focus();
	}
	else
	{
	  $('#div_mermas').css('display','none');
	  $('#div_fols').css('display','none');
	}
}


function addMerma(tipo)
{
	if($('#btnDelMermas').css('visibility')==='hidden')
	{
		$('#btnDelMermas').css('visibility','visible');
	}
	var num_ini=parseInt($('#vfini').val());
	var num_fin=parseInt($('#vffin').val());
	var tot_mermas=parseInt($('#mer_rep').val());
	var new_val='';
	if(tipo===1)
	{
	 var fol_m=parseInt($('#fol_ini_mermas').val());
	  if(fol_m<num_ini || fol_m>num_fin)
	  {
		  alert('El folio de la merma no se encuentra dentro del intervalo de sellos a entregar, le sugerimos revisar la informacion');		
	  }
	  else
	  {		
			if(list_mermas[fol_m])
			{
			   alert('Este folio ya fue agregado');
		       $('#fol_ini_mermas').val('');
		       $('#fol_ini_mermas').focus();
			}
			else
			{
				list_mermas[fol_m] =fol_m;
				if($('#mer_rep_fol').val()!=='')
				 {
					new_val=$('#mer_rep_fol').val()+',';
					fol_mermas_nums=fol_mermas_nums+',';
				 }
				 fol_mermas_nums=fol_mermas_nums+fol_m;
				 if(tipo_h==='P')
				 {
					fol_m=cte+marca+pad(fol_m,7)+serie;
				 }				
				new_val=new_val+fol_m;				
				$('#mer_rep_fol').val(new_val);
				$('#mer_rep_num').val(fol_mermas_nums);
				count_mermas++;
			    $('#mer_rep').val(count_mermas);	
				calc_total(); 	
				$('#fol_ini_mermas').val('');			
		        $('#fol_ini_mermas').focus();		
			}
	  }
	}
	else if(tipo===2)
	{
		var error_rango=0;
		var fol_m1=parseInt($('#fol_ini_mermas').val());
		var fol_m2=parseInt($('#fol_fin_mermas').val());
		if(fol_m1>=fol_m2)
		{
			alert("El folio final debe ser mayor al inicial");
			$('#fol_fin_mermas').val('');
			$('#fol_fin_mermas').focus();			
		}
		else
		{
			
			var folio_i=fol_m1;
			for (folio_i=fol_m1;folio_i<=fol_m2;folio_i++)
			{
			   if(folio_i<num_ini || folio_i>num_fin)
				{
				  alert('El folio de la merma no se encuentra dentro del intervalo de sellos a entregar, le sugerimos revisar la informacion');
				  error_rango=1;
				  folio_i=fol_m2+1;
				}
				else
				{
				  if(!list_mermas[folio_i])
				  {				
				  list_mermas[folio_i] =folio_i;
				  if(fol_mermas_nums!=='')
				   {
					   fol_mermas_nums=fol_mermas_nums+',';					   
				   }
				  fol_mermas_nums=fol_mermas_nums+folio_i;					  
				  count_mermas++;
				  $('#mer_rep').val(count_mermas);
				  calc_total(); 
					   $('#fol_ini_mermas').val('');					   
					   $('#fol_fin_mermas').val('');
					   $('#fol_ini_mermas').focus();
				  }
				}
			}
			if(error_rango===0)
			{
				if(tipo_h==='P')
				 {
				   fol_m1=cte+marca+pad(fol_m1,7)+serie;
				   fol_m2=cte+marca+pad(fol_m2,7)+serie;
				 }	
				
				if($('#mer_rep_fol').val()!=='')
				{
					new_val=$('#mer_rep_fol').val()+',';
				}
				new_val=new_val+'('+fol_m1+'--'+fol_m2+')';
				$('#mer_rep_fol').val(new_val);					
				$('#mer_rep_num').val(fol_mermas_nums);
			}
			else
			{
				$('#fol_ini_mermas').val('');					   
				$('#fol_fin_mermas').val('');
				$('#fol_ini_mermas').focus();
			}
		}//fin else fol_m1>=fol_m2
		
	}
    //alert($('#mer_rep_num').val());
}
