var existe_reg=0;
var mto_inv=0;
var f_in="";
//existencias
var ext_ini="";
var ext_fin="";

//datos del cliente
var cte="";
var marca="";
var serie="";
//tipo de holograma
var tipo_h="";
//obtener el folio final
var last_ffn="";
//Datos de la entrada 
var numf_in=""
var numf_fn="";
var in_entrada="";
var fn_entrada="";
var tot_entrada="";
var options = { direction: 'up' };
 function runEffect() 
 {
	$( "#lista_exist" ).toggle( "blind" ,options, 1000 );
	if($('#btn_ver').hasClass('glyphicon-chevron-down'))
	{
      $('#btnVerLista').removeClass('btn-info');
	  $('#btnVerLista').addClass('btn-warning');
	  $('#btnVerLista').html("<span id='btn_ver' class='glyphicon glyphicon-chevron-up'></span>&nbsp;Ocultar");
	}
	else if($('#btn_ver').hasClass('glyphicon-chevron-up'))
	{
	  $('#btnVerLista').removeClass('btn-warning');
	  $('#btnVerLista').addClass('btn-info');
	  $('#btnVerLista').html("<span id='btn_ver' class='glyphicon glyphicon-chevron-down'></span>&nbsp;Ver Existencias");
	}
   
 }
$(document).ready(function() {
     /*$("#tabs").tabs();	 
	 $("#tabs").tabs({
       activate: function(event,ui) 
	   {
		  if ( ui.newPanel.selector=="#tabs-2" )
		  {
			  sug_req();
			  getNoPedido(0);
		  }
		  if ( ui.newPanel.selector=="#tabs-3" )
		  {
			  fill_grid();
			  sinc_list();
		  }
		  if ( ui.newPanel.selector=="#tabs-4" )
		  {
			  getNoPedido(1);
			  fill_online();
			  //sinc_online();
		  }			  
	   }//activate	
	 });
	 $( "#tabs" ).tabs({
	  active: 0
	});*/
	
	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {	 
	  var tab_target=String(e.target);
	  var tab_activate = tab_target.split("#");
	  switch(tab_activate[1])
	  {
		   case "tabReq":
		  {
			  sug_req();
			  getNoPedido(0);
			  break;
		  }
		  case "tabOnline":
		  {
		  	  getNoPedido(0);
			  getNoPedido(1);
			  fill_online();
			  break;
		  }
		  case "tabSolicitados":
		  {
			  fill_grid();
			  sinc_list();
			  break;
		  }
	  }
	});
	
});
//funcion suggest
$(function() {
		//autocomplete
		$("#cliente").autocomplete({
			source: "php/search.php",//MISMA FUNCION QUE LOS RECIBOS
			minLength: 1,
			maxRows: 15,
			//select: function(e,ui){ carga_cbo(ui.item.value);}
			select: function(e,ui){ getTipo(ui.item.value);}
		}).keypress(function(e) {

          if (e.keyCode === 13) 
          {
          return false;
          }

        });	
	    $('#entrada').on('keyup', function (event) {
         if(event.which === 13){
           calc_ffin();
         }
       });			
});
//fin funcion suggest
function addMarca()
{
	dialog = $( "#dialog-form1" ).dialog({
		autoOpen: false,
		height: 'auto',
		maxWidth: 500,
		width: 'auto',
		modal: true,
		buttons: {
		 Cerrar: function() {
			dialog.dialog( "close" );
		  }
		},
		close: function() {
		  
		}
	  });
		dialog.dialog( "open" );
	$('#nc_marca').val($('#cliente').val());
	nextMarca();
}
//funcion para obtener la siguiente marca
function nextMarca()
{ 
 
  var cte_nmarca=$('#nc_marca').val();
  $.ajax({
	  
	  type: "POST",
	  url: "php/get_marca.php",
	  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
	  data: "cliente="+cte_nmarca,
	  datatype: 'json',
	  success: function(response){
		  jmarcas=JSON.parse(response);
		  if(jmarcas.status=='correcto')
		  {
		    $('#marcas_d').html(jmarcas.lista);
			$('#letra').val(jmarcas.next);
			$('#marca_new').focus();
		  }
		  else
		  {
			$('#marcas_d').html(jmarcas.list);
		  }
	  },
	  beforeSend:function()
	  {
		  
		   $("#add_err").html("Loading...")
	  }
  });
}

function GuardaMarca()
{
	if($('#marca_new').val()!='')
	{    
	  var datos_env=$('#formMarcas').serialize();
	  $.ajax({
		  type: "POST",
		  url: "php/add_marca.php",
		  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
		  data: datos_env,
		  datatype: 'json',
		  success: function(response){
			 jaddmca=JSON.parse(response);
			  if(jaddmca.status=='OK')
			  {
				  if(jaddmca.remoto=='OK')
				  {
					  alert('Marca agregada LOCAL y REMOTA');
					  $('#marca_new').val('');
					  carga_cbo();
					  dialog.dialog( "close" );
				  }
				  else
				  {
					  var msj_marca=jaddmca.msj;
					  alert(msj_marca);
					  $('#marca_new').val('');
					  carga_cbo();
					  dialog.dialog( "close" );
				  }
			  }
			  else
			  {
			  alert(jaddmca.msj);
			  }
		  },
		  beforeSend:function()
		  {
			  
			   $("#add_err").html("Loading...")
		  }
	  });
	}
	else
	{
		alert('Ingresa el nombre de la marca');
    }
}
function getTipo(val_c)
{
  cte=val_c.substr(0,5);
  //var selected = $("input[type='radio'][name='tipo']:checked");
  var cargo = $("#cargo").val();
  var nivel_i = $("#nivel_inventario").val();
  //alert(nivel_i);
  if ($('#tipo_hol').is(":checked"))
	{  
	  tipo_h='P';
	 if(nivel_i=='1')
	 {
      vista_exists();
	  carga_cbo();
	 }
	 else
	 {
	  vista_exists();
	 }
	}
	else
	{
	   tipo_h='G';
	   getExists();
	   if($('#tot_existe').css('display')=='block')
	   {
		 $('#tot_existe').css('display','none');  
	   }
	}
  
}
//Funcion al seleccionar una de las opciones de tipo
function tipos(num)
{
  limpiar();
  if (num==1) {
   $('#h_per').css('display','none'); 
   $('#txt_cliente').css('display','none'); 
   getTipo('--');
  }
  else if(num==2) {
	tipo_h='P';
	 $('#h_per').css('display','block'); 
	 $('#txt_cliente').css('display','block'); 
  }
  
}

function tipo_holograma()
{  
   limpiar();
   if ($('#tipo_hol').is(":checked"))
	{  
	  tipo_h='P';
	  $('#h_per').css('display','block'); 
	  $('#txt_cliente').css('display','block');   
	}
	else
	{
	   $('#h_per').css('display','none'); 
	   $('#txt_cliente').css('display','none'); 
	   getTipo('--');
	}

  
}


function limpiar()
{
  $('#cliente').val('');
  cte="";
  marca="";
  serie="";
  $('#cbo_m').html('-----');
  $('#cliente').focus();
  $('#serie').val('');		
  $('#d_existe').css('display','none');
  $('#entrada').val('');
  $('#fi_entrada').val('');
  $('#ff_entrada').val('');
  ext_ini="";
  ext_fin="";
  numf_in=""
  numf_fn="";
  in_entrada="";
  fn_entrada="";
  tot_entrada="";
  existe_reg=0;
  $('#addEntrada').css('display','none');
}
function limpiar_parcial()
{
  marca="";
  serie="";
  //$('#cbo_m').html('-----');
  $('#cbo_m').prop('selectedIndex',0);
  //$('#cliente').focus();
  $('#serie').val('');		
  $('#d_existe').css('display','none');
  $('#entrada').val('');
  $('#fi_entrada').val('');
  $('#ff_entrada').val('');
  ext_ini="";
  ext_fin="";
  numf_in=""
  numf_fn="";
  in_entrada="";
  fn_entrada="";
  tot_entrada="";
  existe_reg=0;
  $('#addEntrada').css('display','none');
}
function limpia_exists()
{
  $('#existencias').val('');
  $('#folExt').val('');
  $('#folExt2').val('');
  $('#d_existe').css('display','none');
}

function carga_cbo()
{    
  $.ajax({
	  
	  type: "POST",
	  url: "php/cbo_marca.php",//MISMA FUNCION QUE LOS RECIBOS
	  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
	  data: "cliente="+cte+'&id=cbo_marcas'+'&funcion=getSerie',
	  datatype: 'json',
	  success: function(response){
		   jcbo=JSON.parse(response);
		  if(jcbo.status=='correcto')
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
		  
		   $("#add_err").html("Loading...")
	  }
  });
}
function vista_exists()
{    
  $.ajax({
	  
	  type: "POST",
	  url: "php/get_tot_exists.php",//MISMA FUNCION QUE LOS RECIBOS
	  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
	  data: "cliente="+cte,
	  datatype: 'json',
	  success: function(response){
		   jcbo=JSON.parse(response);
		   var nivel_i = $("#nivel_inventario").val();
		   cargo=$('#cargo').val();
		   if( nivel_i=='1')
		   {
		   $('#toggle_vista').css('display','block');
		   $('#lista_exist').html(jcbo.msj);
		   }
		   else
		   {
			$('#tot_existe').css('display','block');
		    $('#tot_existe').html(jcbo.msj);  
		   }
	  },
	  beforeSend:function()
	  {
		  
		   $("#add_err").html("Loading...")
	  }
  });
}

function getSerie()
{ 
	 var marc=$('#cbo_marcas').val();
	 if(marc!=0)
	 {
	 $.ajax({		    
            type: "POST",
            url: "php/get_serie.php",//MISMA FUNCION QUE LOS RECIBOS
            contentType: "application/x-www-form-urlencoded;charset=UTF-8",
			data: "cliente="+cte+"&marca="+marc,
			datatype: 'json',
            success: function(response){
				$('#serie').val(response);
				getExists();
				
            },
            beforeSend:function()
			{
                 $("#add_err").html("Loading...")
            }
        });
	 }
	 else
	 {
		 $('#cbo_marcas').focus();
		 limpiar_parcial();
	 }	 
}
//OBTENER LA ULTIMA ENTRADA
function getLast()
{     
	 if(cte!='')//checar si hay cliente
	 {
	 marca=$('#cbo_marcas').val();
	 var ctdad=$('#cantidad').val();
	 serie=$('#serie').val();
	 $.ajax({
		    
            type: "POST",
            url: "php/inventario/get_last.php",//DE INVENTARIOS
            contentType: "application/x-www-form-urlencoded;charset=UTF-8",
			data: "tipo_h="+tipo_h+"&cliente="+cte+"&marca="+marca+"&serie="+serie,
			datatype: 'json',
            success: function(data){
				//alert(data);
				data=JSON.parse(data);
				if(data.status=='correcto')
				{
					 numf_in=parseInt(data.ffin)+1;
					if(tipo_h=='G')
					{
					  in_entrada=numf_in;
					}
					else if(tipo_h=='P')
					{
					  in_entrada=cte+marca+pad(numf_in,7)+serie;
					  
					}
					$('#fi_entrada').val(in_entrada);
					goBottom();
					$('#entrada').focus();
					
					
				}
				else if(data.status=='error')
				{
					if(data.ne=='0')
					{
						numf_in=1;
						if(tipo_h=='G')
						{
						  in_entrada=numf_in;
						}
						else if(tipo_h=='P')
						{
						  in_entrada=cte+marca+pad(numf_in,7)+serie;
						  
						}
					$('#fi_entrada').val(in_entrada);
					goBottom();
					$('#entrada').focus();
					}
					else
					{
					alert(data.msj);
					}
					limpia_exists();
				}
            },
            beforeSend:function()
			{
                 
            }
        });
	 }//checar si hay cliente
	 else
	 {
		 alert('Ingrese un numero de cliente Last');
	 }
}
//OBTENER EXISTENCIAS ACTUALES
function getExists()
{    
	 if(cte!='')//checar si hay cliente
	 {
	 marca=$('#cbo_marcas').val();
	 var ctdad=$('#cantidad').val();
	 serie=$('#serie').val();
	 $.ajax({		    
            type: "POST",
            url: "php/get_exists.php",//MISMA FUNCION QUE LOS RECIBOS
            contentType: "application/x-www-form-urlencoded;charset=UTF-8",
			data: "tipo_h="+tipo_h+"&cliente="+cte+"&marca="+marca+"&serie="+serie,
			datatype: 'json',
            success: function(data){
				data=JSON.parse(data);
				if(data.status=='correcto')
				{
					//Obtenemos los numeros de folios de las existencias
					ext_ini=data.fini;
					ext_fin=data.ffin;
					//revisar eltipo de hologramas y generar la codificacion de los folios iniciales y finales
					if(tipo_h=='G')
					{
					  f_in=ext_ini;
					  f_fn=ext_fin;
					}
					else if(tipo_h=='P')
					{
					  f_in=cte+marca+pad(ext_ini,7)+serie;
					  f_fn=cte+marca+pad(ext_fin,7)+serie;
					}
					//mostrar las existencias
					$('#d_existe').css('display','block');
					mto_inv=data.mto;
					$('#existencias').val(mto_inv);
					$('#folExt').val(f_in);
					$('#folExt2').val(f_fn);
					//obtener la ultima entrada
					existe_reg=1;
					getLast();
				}
				else if(data.status=='error')
				{
					if(data.msj=='Inventario vacio')
					{
					  alert(data.msj);
					  getLast();
					  ext_ini=0;
					  ext_fin=0;
					  existe_reg=1;
					}
					else
					{
				      existe_reg=0;
					  alert(data.msj);
					  getLast();
					}
				}
            },
            beforeSend:function()
			{
                 
            }
        });
	 }//checar si hay cliente
	 else
	 {
		 alert('Ingrese un numero de cliente Existe');
		 $('#destino option[value=NS]').attr('selected', 'selected');
	 }
}
//Verificar que hay existencias para cobrir el monto requerido
//AGREGAR CEROS A LA DERECHA
function pad (str, maxi) {
  str = str.toString();
  return str.length < maxi ? pad("0" + str, maxi) : str;
}

//CALCULAR EL FOLIO FINAL

function calc_ffin()
{
	tot_entrada=$('#entrada').val();
	numf_fn=numf_in+parseInt(tot_entrada)-1;
	if(tipo_h=='G')
	{
	  fn_entrada=numf_fn;
	  $('#ff_entrada').val(fn_entrada);
	  $('#addEntrada').css('display','block');
	}
	else if(tipo_h=='P')
	{
	  fn_entrada=cte+marca+pad(numf_fn,7)+serie;
	  $('#ff_entrada').val(fn_entrada);
	  $('#addEntrada').css('display','block');
	}  	
}
//GENERAR ENTRADA
function addEntrada()
{ 
   user=$('#usr').html();
   $.ajax({
    type: "POST",
		  url: "php/inventario/add_entrada.php",
		  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
		  data: "user="+user+"&tipo="+tipo_h+"&cliente="+cte+"&marca="+marca+"&serie="+serie+"&ext_ini="+ext_ini+"&ext_fin="+ext_fin+"&ini_ent="+numf_in+"&fin_ent="+numf_fn+"&total="+tot_entrada+"&existe_reg="+existe_reg,
		  datatype: 'json',
		  success: function(data){
			 //alert(data);
			  data=JSON.parse(data);
			 
			  if(data.status=='correcto')
			  {
			   alert(data.msj);
			   limpiar_parcial();
			   vista_exists();
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

function goBottom()
{
	$('html, body').animate({
           scrollTop: $(document).height()
	 },
	 1500);
}