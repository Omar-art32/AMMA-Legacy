var o=new Array("diez", "once", "doce", "trece", "catorce", "quince", "dieciséis", "diecisiete", "dieciocho", "diecinueve", "veinte", "veintiuno", "veintidós", "veintitrés", "veinticuatro", "veinticinco", "veintiséis", "veintisiete", "veintiocho", "veintinueve");
var u=new Array("cero", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve");
var d=new Array("", "", "", "treinta", "cuarenta", "cincuenta", "sesenta", "setenta", "ochenta", "noventa");
var c=new Array("", "ciento", "doscientos", "trescientos", "cuatrocientos", "quinientos", "seiscientos", "setecientos", "ochocientos", "novecientos");
var existe_req=0;
var existe_temp=0;
var mto_inv_req=0;
//folios existencias
var f_ext_in_req="";
var f_ext_fn_req="";
//folios de requisicon
var numf_in_req="";
var numf_fn_req="";
var in_req="";
var fn_req="";
var cte_req="";
var marca_req="";
var serie_req="";
//tipo de holograma
var tipo_h_req="";
//obtener el folio final
var last_ffn_req="";
//numero de pedido
var no_pedido=0;
var tot_req="";
//variables para el carrito de requisicion
var req_list  = { };
var count_list=0;
var consec_list=0;
//funcion suggest
function show_exists()
{
	$("#lista_exist_req" ).toggle( "blind" ,options, 1000 );
	if($('#btn_ver_req').hasClass('glyphicon-chevron-down'))
	{
      $('#btnVerLista_req').removeClass('btn-info');
	  $('#btnVerLista_req').addClass('btn-warning');
	  $('#btnVerLista_req').html("<span id='btn_ver' class='glyphicon glyphicon-chevron-up'></span>&nbsp;Ocultar");
	}
	else if($('#btn_ver_req').hasClass('glyphicon-chevron-up'))
	{
	  $('#btnVerLista_req').removeClass('btn-warning');
	  $('#btnVerLista_req').addClass('btn-info');
	  $('#btnVerLista_req').html("<span id='btn_ver' class='glyphicon glyphicon-chevron-down'></span>&nbsp;Ver Existencias");
	}
}
function sug_req() {
	//autocomplete
	$("#cliente_req").autocomplete({
		source: "php/search_marcas.php",//MISMA FUNCION QUE LOS RECIBOS
		minLength: 1,
		maxRows: 15,
		select: function(e,ui){ getTipo_req(ui.item.value);}
	}).keypress(function(e) {

	  if (e.keyCode === 13)
	  {
	  return false;
	  }

	});
	 $('#requeridos').on('keyup', function (event) {
	 if(event.which === 13){
	   calc_ffin_req();
	 }
   });
}
function getNoPedido(tipo_peticion)
{
 $.ajax({

	  type: "POST",
	  url: "php/inventario/get_NoPedido.php",//MISMA FUNCION QUE LOS RECIBOS
	  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
	  datatype: 'json',
	  success: function(response){
		   js_pedido=JSON.parse(response);
		  if(js_pedido.status=='correcto')
		  {
				//alert(tipo_peticion);
			no_pedido=js_pedido.no_pedido;
			$('#no_pedido').val(no_pedido);
			if(tipo_peticion==0)
			{
			  if(js_pedido.tmp=='si')
			  {
				  if(existe_temp==0)
				  {
				  carga_tmp();
				  existe_temp=1;
				  }
			  }
			}
		  }
		  else
		  {
			   alert('No se pudo cargar el numero de pedido');
		  }
	  },
	  beforeSend:function()
	  {

		   $("#add_err").html("Loading...")
	  }
  });
}
//fin funcion suggest
function getTipo_req(val_c)
{
  cte_req=val_c.substr(0,5);
  //var selected = $("input[type='radio'][name='tipo']:checked");
  var cargo = $("#cargo").val();
  if ($('#tipo_hol_req').is(":checked"))
	{
	  tipo_h_req='P';
	 carga_cbo_req();
	}
	else
	{
	   tipo_h_req='G';
	   getExists_req();//------->nueva funcion hay que agregarla
	}
}
//cargar los temporales
function carga_tmp()
{
	//alert('cargando tmp...');
  $.ajax({
	  type: "POST",
	  url: "php/inventario/get_temps.php",
	  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
	  data: "no_pedido="+no_pedido,
	  datatype: 'json',
	  success: function(response){
		res_tmp=JSON.parse(response);
		if(res_tmp.status=='OK')
		{
		   for (key in res_tmp.lista)
		   {
			  carrito_temp(res_tmp.lista[key]);
			  //alert(JSON.stringify(res_tmp.lista[key]))
		   }
		}
		else
		{

		}
	  },
	  beforeSend:function()
	  {
		$("#add_err").html("Loading...")
	  }
  });
}
function addCarritoReq()
{
  if(tipo_h_req=='P')
  {
	 nom_marca=$('#cbo_marcas_req option:selected').text();
	 edo_ind=get_edo($('#cbo_edos').val());
	 index= 'F_'+cte_req+marca_req+'_'+edo_ind;
  }
  else
  {
	 marca_req="--";
     index= 'F_GEN';
	 nom_marca='GENERICOS';
  }
  //revisar que no se halla agregado al carrito

	if(req_list[index])
	{
	  alert('Ya has usado esta marca');
	  limpiar_parcial_req();
	}
	else
	{
	   req_list[index] = { };
	   req_list[index].cte = cte_req;
	   req_list[index].marca = marca_req;
	   req_list[index].nom_marca = nom_marca;
	   req_list[index].edo = $('#cbo_edos').val();
	   req_list[index].serie = serie_req;
	   req_list[index].tipo =  $('#cbo_tipo').val();
	   req_list[index].cantidad = tot_req;
	   req_list[index].pagado = $('#cbo_pago').val();
	   var es_pagado=0;
	   var prioridad="";
	   var clase_pag="";
	   var clase_urg="";
	   var  urgente=0;
	   var tipo_mez="";
	   if($('#cbo_pago').val()=='1')
	   {
		   es_pagado='SI';
		   clase_pag=" pagado'>";
	   }
	   else
	   {
		   es_pagado="NO <button type='button'  name='btn_editar' id='btn_editar' class='btn btn-xs btn-success' onClick='confirma_pago(\""+index+"\")'><i class='fa fa-dollar fa-lg'></i> </button>";
		   clase_pag=" pendiente'>";
	   }
	    if($('#urge').is(":checked"))
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
		switch($('#cbo_tipo').val())
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
	   req_list[index].fini = numf_in_req;
	   req_list[index].ffin = numf_fn_req;
	   count_list++;
	   cadena = "<tr id='"+index+"' align='center'>";
	   cadena = cadena + "<td class='td_req'>" +cte_req + "</td>";
	   cadena = cadena + "<td class='td_req'>" +nom_marca + "</td>";
	   cadena = cadena + "<td class='td_req'>" +tipo_mez+ "</td>";
	   cadena = cadena + "<td class='td_req'>" +$('#cbo_edos').val() + "</td>";
	   cadena = cadena + "<td class='td_req'>" + in_req + "</td>";
	   cadena = cadena + "<td class='td_req'>" + fn_req+ "</td>";
	   cadena = cadena + "<td class='td_req'>" + tot_req + "</td>";
	   cadena = cadena + "<td class='td_req"+clase_pag + es_pagado+ "</td>";
		cadena = cadena + "<td class='td_req"+clase_urg+ prioridad+ "</td>";
	   cadena = cadena + "<td><button type='button'  name='btn_eliminar' id='btn_eliminar' class='btn btn-xs btn-danger' onClick='elimin_fil_req(\""+index+"\")'><i class='fa fa-minus fa-lg'></i></button</td>";
	   //agregamos la fila creada a la tabla
	   // $('#btnRecibo').prop('disabled',true)
	   //alert(JSON.stringify(req_list[index]));
	   add_list(JSON.stringify(req_list[index]),cadena);
	   limpiar_parcial_req();
	   //ver_var();
   }
}
function carrito_temp(arr_temp)
{
  if(arr_temp.serie!='')
  {
	 nom_marca=arr_temp.marca;
	  //edo_ind=get_edo($('#cbo_edos').val());
	 index= 'F_'+arr_temp.no_cliente+arr_temp.cve+'_'+get_edo(arr_temp.edo);
  }
  else
  {
	 marca_req="--";
     index= 'F_GEN';
	 nom_marca='GENERICOS';
  }
  //revisar que no se halla agregado al carrito

	if(req_list[index])
	{
	  alert('Ya has usado esta marca CARRITO');
	  //limpia_car();
	}
	else
	{
	   req_list[index] = { };
	   req_list[index].cte = arr_temp.no_cliente;
	   req_list[index].edo = arr_temp.edo;
	   req_list[index].marca = arr_temp.cve;
	   req_list[index].nom_marca = nom_marca;
	   req_list[index].serie = arr_temp.serie;
	   req_list[index].tipo = arr_temp.tipo;
	   req_list[index].cantidad = arr_temp.cantidad;
	   req_list[index].pagado = arr_temp.pagado;
	   req_list[index].urgente = arr_temp.urgente;
	   req_list[index].fini = arr_temp.fi;
	   req_list[index].ffin = arr_temp.ff;
	   req_list[index].id_row = arr_temp.id_row;
	   count_list++;
	   var es_pagado="";
	   var prioridad="";
	   var clase_pag="";
	   var clase_urg="";
	   if(arr_temp.pagado==1)
	   {
		   es_pagado='SI';
		   clase_pag=" pagado'>";
	   }
	   else
	   {
		   es_pagado="NO <button type='button'  name='btn_editar' id='btn_editar' class='btn btn-xs btn-success' onClick='confirma_pago(\""+index+"\")'><i class='fa fa-dollar fa-lg'></i> </button>";
		   clase_pag=" pendiente'>";
	   }

	   if(arr_temp.urgente==1)
	   {
		   prioridad='URGENTE';
		   clase_urg=" urgente'>";
	   }
	   else
	   {
		   prioridad="NORMAL";
		   clase_urg=" normal'>";
	   }

	   var tipo_mez="";
	   // alert(arr_temp.tipo);
	   switch(arr_temp.tipo)
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
	   var activaN = "";
	   var activaG = "";
	   if(arr_temp.holograma == '0')
	   		activaG = "selected";
	   else if(arr_temp.holograma == '1')
	   		activaN = "selected";
	   cadena = "<tr id='"+index+"' align='center'>";
	   //arr_temp.no_cliente+arr_temp.cve+(arr_temp.fi,7)+arr_temp.serie
	   cadena = cadena + "<td class='td_req'>" +arr_temp.no_cliente + "</td>";
	   cadena = cadena + "<td class='td_req'>" +nom_marca+ "</td>";
	   cadena = cadena + "<td class='td_req'>" +tipo_mez+ "</td>";
	   cadena = cadena + "<td class='td_req'>" +arr_temp.edo+ "</td>";
	   cadena = cadena + "<td class='td_req'>" + arr_temp.no_cliente+arr_temp.cve+pad(arr_temp.fi,7)+arr_temp.serie + "</td>";
	   cadena = cadena + "<td class='td_req'>" + arr_temp.no_cliente+arr_temp.cve+pad(arr_temp.ff,7)+arr_temp.serie+ "</td>";
	   cadena = cadena + "<td class='td_req'>" + arr_temp.cantidad+ "</td>";
	   cadena = cadena + "<td class='td_req"+clase_pag + es_pagado+ "</td>";
	   cadena = cadena + "<td class='td_req" + clase_urg +'<select class="form-control" id="cboholograma" name="cboholograma" onchange="cambiaTipoHolograma('+arr_temp.id_row+',this.value)" ><option value="1" '+activaN+'>Nuevo</option><option value="0" '+activaG+'>Genérico</option></select>'+ "</td>";
	   cadena = cadena + "<td class='td_req"+clase_urg+ prioridad+ "</td>";
	   cadena = cadena +"<td class='td_req'><button type='button'  name='btn_eliminar' id='btn_eliminar' class='btn btn-xs btn-danger' onClick='elimin_fil_req(\""+index+"\")'><i class='fa fa-minus fa-lg'></i></button></td>";
	   //agregamos la fila creada a la tabla
	   // $('#btnRecibo').prop('disabled',true)
	   //alert(JSON.stringify(req_list[index]));
	   add_temp(cadena);
	   consec_list++;
	   limpiar_parcial_req();
	   //ver_var();
	}
}

function add_list(datos,cad,tipo_pet) {
	if(tipo_pet=="ONLINE")
	{
	   if(existe_temp==0)
		{
		  carga_tmp();
		  existe_temp=1;
		}
	}
	user=$('#usr').val();
	$.ajax({
		type: "POST",
		url: "php/inventario/add_temp.php",
		contentType: "application/x-www-form-urlencoded;charset=UTF-8",
		data: 'datos='+datos+'&no_pedido='+no_pedido+'&user='+user,
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
				$("#tbl_carrito tbody").append(cad);
				$('#tabla_req').css('display','block');
				$('#btns_req').css('display','block');
			}
			else
				alert(data.msj);
		},
		beforeSend:function()
		{
		}
  	});
}

function add_temp(cad)
{
		 $("#tbl_carrito tbody").append(cad);
		 $('#tabla_req').css('display','block');
		 $('#btns_req').css('display','block');
		 //alert('temp agregado');
}

//Funcion al seleccionar una de las opciones de tipo
function tipo_holograma_req()
{
   limpiar();
   if ($('#tipo_hol_req').is(":checked"))
	{
	  tipo_h_req='P';
	  $('#h_per_req').css('display','block');
	  $('#txt_cliente_req').css('display','block');
	}
	else
	{
	   $('#h_per_req').css('display','none');
	   $('#txt_cliente_req').css('display','none');
	   getTipo_req('--');
	}
}
function limpiar_req()
{
  $('#cliente_req').val('');
  cte_req="";
  marca_req="";
  serie_req="";
  mto_inv_req=0;
  //$('#cbo_m').html('-----');
  //$('#cbo_marcas_req').prop('selectedIndex',0);
  $('#cbo_marcas_req option[value=0]').prop('selected', true);
  $('#cbo_edos option[value=NS]').prop('selected', true);
  $("#cbo_pago").val('NS').change();
  $("#cbo_tipo").val('0').change();
  toggle_chk('on');
  //$('#cliente').focus();
  $('#serie_req').val('');
  //$('#d_existe_req').css('display','none');
   if($('#lista_exist_req').css('display')=='block')
  {
	show_exists();
  }
  $('#requeridos').val('');
  $('#requeridos').attr('readonly',true);
  $('#fi_req').val('');
  $('#ff_req').val('');
  $('#existencias_req').html('');
  $('#folExt_req').html('');
  $('#folExt2_req').html('');//msj_last
  $('#msj_last').html('');
  $('#fol_last_i').html('');
  $('#fol_last_f').html('');
  $('#tabla_last_ep').css('display','none');
  numf_in_req=""
  numf_fn_req="";
  f_ext_in_req=""
  f_ext_fn_req=""
  in_req="";
  fn_req="";
  tot_req="";
  existe_req=0;
  $('#addReq').css('display','none');
}

function limpiar_parcial_req()
{
  marca_req="";
  serie_req="";
  mto_inv_req=0;
  //$('#cbo_m').html('-----');
  //$('#cbo_marcas_req').prop('selectedIndex',0);
  $('#cbo_marcas_req option[value=0]').prop('selected', true);
  $('#cbo_edos option[value=NS]').prop('selected', true);
  $("#cbo_pago").val('NS').change();
  $("#cbo_tipo").val('0').change();
  toggle_chk('on');
  //$('#cliente').focus();
  $('#serie_req').val('');
  //$('#d_existe_req').css('display','none');
  if($('#lista_exist_req').css('display')=='block')
  {
	show_exists();
  }
  $('#requeridos').val('');
  $('#requeridos').attr('readonly',true);
  $('#fi_req').val('');
  $('#ff_req').val('');
  numf_in_req=""
  numf_fn_req="";
  f_ext_in_req=""
  f_ext_fn_req=""
  in_req="";
  fn_req="";
  tot_req="";
  existe_req=0;
  $('#c_letras').val('');
  $('#cbo_marcas_req').focus();
  $('#addReq').css('display','none');
}
function limpia_exists_req()
{
  $('#existencias_req').html('');
  $('#folExt_req').html('');
  $('#folExt2_req').html('');
  //$('#d_existe_req').css('display','none');
}

function carga_cbo_req()
{
  $.ajax({
	  type: "POST",
	  url: "php/cbo_marca_req.php",//MISMA FUNCION QUE LOS RECIBOS
	  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
	  data: "cliente="+cte_req,
	  datatype: 'json',
	  success: function(response){
		   jcbo=JSON.parse(response);
		  if(jcbo.status=='correcto')
		  {
			$('#cbo_m_req').html(jcbo.cbo);

		  }
		  else
		  {
			   $('#cbo_m_req').html(jcbo.msj);
		  }
	  },
	  beforeSend:function()
	  {

		   $("#add_err").html("Loading...")
	  }
  });
}
function getSerie_req()
{
	 var marc=$('#cbo_marcas_req').val();
	 if(marc!=0)
	 {
	 $.ajax({

            type: "POST",
            url: "php/get_serie.php",//MISMA FUNCION QUE LOS RECIBOS
            contentType: "application/x-www-form-urlencoded;charset=UTF-8",
			data: "cliente="+cte_req+"&marca="+marc,
			datatype: 'json',
            success: function(response){
				$('#serie_req').val(response);
				limpia_exists_req();
				getExists_req();

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
		 limpiar_parcial_req();
	 }
}
//OBTENER LA ULTIMA ENTRADA
function getLast_req()
{
	 if(cte_req!='')//checar si hay cliente
	 {
	 marca_req=$('#cbo_marcas_req').val();
	 var ctdad=$('#requeridos').val();
	 serie_req=$('#serie_req').val();
	 $.ajax({
            type: "POST",
            url: "php/inventario/get_last_req.php",//DE INVENTARIOS
            contentType: "application/x-www-form-urlencoded;charset=UTF-8",
			data: "tipo_h="+tipo_h_req+"&cliente="+cte_req+"&marca="+marca_req+"&serie="+serie_req,
			datatype: 'json',
            success: function(data){
				//alert(data);
				data=JSON.parse(data);
				if(data.status=='correcto')
				{
					numf_in_req=parseInt(data.ffin)+1;
					if(tipo_h_req=='G')
					{
					  in_req=numf_in_req;
					}
					else if(tipo_h_req=='P')
					{
					  in_req=cte_req+marca_req+pad(numf_in_req,7)+serie_req;
					}
					//alert(numf_in_req);
					$('#fi_req').val(in_req);
					$('#requeridos').val('');
					$('#ff_req').val('');
					//goBottom();
					$('#tabla_last_ep').css('display','inline-table');
					var last_entrada_ini=cte_req+marca_req+pad(parseInt(data.fini),7)+serie_req
					var last_entrada_fin=cte_req+marca_req+pad(parseInt(data.ffin),7)+serie_req
					$('#msj_last').html(data.msj);
					$('#fol_last_i').html(last_entrada_ini);
					$('#fol_last_f').html(last_entrada_fin);
					if($('#lista_exist_req').css('display')=='none')
					{
					  $('#tabla_ext').css('display','inline-table');
					  show_exists();
					}
					$('#cbo_edos').focus();
				}
				else if(data.status=='error')
				{
					if(data.ne=='0')
					{
						numf_in_req=1;
						if(tipo_h_req=='G')
						{
						  in_req=numf_in_req;
						}
						else if(tipo_h_req=='P')
						{
						  in_req=cte_req+marca_req+pad(numf_in_req,7)+serie_req;
						}
					$('#fi_req').val(in_req);
					$('#ff_req').val('');
					//goBottom();
					if($('#lista_exist_req').css('display')=='none')
					{
					  $('#tabla_ext').css('display','inline-table');
					  show_exists();
					}
					$('#tabla_last_ep').css('display','none');
					$('#requeridos').focus();
					}
					else
					{
					alert(data.msj);
					}
					limpia_exists_req();
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
function getExists_req()
{
	 if(cte_req!='')//checar si hay cliente
	 {
	 marca=$('#cbo_marcas_req').val();
	 var ctdad=$('#cantidad').val();
	 serie_req=$('#serie_req').val();
	 $.ajax({
            type: "POST",
            url: "php/inventario/get_exists.php",//MISMA FUNCION QUE LOS RECIBOS
            contentType: "application/x-www-form-urlencoded;charset=UTF-8",
			data: "tipo_h="+tipo_h_req+"&cliente="+cte_req+"&marca="+marca+"&serie="+serie_req,
			datatype: 'json',
            success: function(data){
				data=JSON.parse(data);
				if(data.status=='correcto')
				{
					if(tipo_h_req=='G')
					{
					  f_ext_in_req=data.fini;
					  f_ext_fn_req=data.ffin;
					}
					else if(tipo_h_req=='P')
					{
					  f_ext_in_req=cte_req+marca+pad(data.fini,7)+serie_req;
					  f_ext_fn_req=cte_req+marca+pad(data.ffin,7)+serie_req;
					}
					//mostrar las existencias
					//$('#d_existe_req').css('display','block');
					if($('#lista_exist_req').css('display')=='none')
					{
					  $('#tabla_ext').css('display','inline-table');
					  show_exists();
					}

					mto_inv_req=data.mto;
					$('#existencias_req').html(mto_inv_req);
					$('#folExt_req').html(f_ext_in_req);
					$('#folExt2_req').html(f_ext_fn_req);
					//obtener la ultima entrada
					existe_req=1;
					getLast_req();
				}
				else if(data.status=='error')
				{
					if(data.msj=='Inventario vacio')
					{
					  $('#tabla_ext').css('display','none');
					  alert(data.msj);
					  getLast_req();
					  ext_ini=0;
					  ext_fin=0;
					  existe_req=1;
					}
					else
					{
				      existe_req=0;
					  alert(data.msj);
					  getLast_req();
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

function calc_ffin_req()
{
	tot_req=$('#requeridos').val();
	var letras_cant=nn(tot_req);
	$('#c_letras').val(letras_cant);
	numf_fn_req=numf_in_req+parseInt(tot_req)-1;
	if(tipo_h_req=='G')
	{
	  fn_req=numf_fn_req;
	  $('#ff_req').val(fn_req);
	  $('#cbo_pago').focus();
	}
	else if(tipo_h_req=='P')
	{
	  fn_req=cte_req+marca_req+pad(numf_fn_req,7)+serie_req;
	  $('#ff_req').val(fn_req);
	  $('#cbo_pago').focus();
	}
}
function muestra_boton()
{
	if($.trim($('#ff_req').val())!='')
	{
	  if($('#cbo_pago').val()!='NS')
	  {
		$('#addReq').css('display','block');
	  }
	}
   else
   {
	   $('#requeridos').focus();
   }

}

function elimin_fil_req(id)
{


	d_cliente=id.substr(2, 4);
	d_marca=id.substr(6,1);
	d_edo=rev_edo(id.substr(8,3));
	$.ajax({
	type: "POST",
	url: "php/inventario/del_row_temp.php",
	contentType: "application/x-www-form-urlencoded;charset=UTF-8",
	data: 'no_pedido='+no_pedido+'&cliente='+d_cliente+'&marca='+d_marca+'&estado='+d_edo,
	datatype: 'json',
	success: function(data){
		//alert(data);
		data=JSON.parse(data);
		if(data.status=="OK")
		{
			id_gral=id.substr(0,11);
			$('#tbl_carrito tbody tr').each(function() {

				id_row=$(this).attr('id');
				id_act=id_row.substr(0,11);
				if(id_gral==id_act)
				{
					//alert(id_gral+' '+id_act);
				  delete req_list[id_row];
				  $('#'+id_row).remove();
				  count_list--;
				}
			 });

			if(count_list==0)
			{
				$('#tabla_req').css('display','none');
				$('#btns_req').css('display','none');
			}

			reload_online();
		}
		else
		{
			alert(data.status);
		}
		//borra_carrito();
	},
	beforeSend:function()
	{
	}
    });
	//remover de tabla html
}
function ver_var()
{
	var cad_var= 'Existe_req='+existe_req+'<br>mto_inv_req='+mto_inv_req+'<br>f_in_req='+f_in_req+'<br>f_ext_fn_req='+f_ext_fn_req+'<br>cte_req='+cte_req+'<br>marca_req='+marca_req+'<br>serie_req='+serie_req+'<br>tipo_h_req='+tipo_h_req+'<br>last_ffn_req='+last_ffn_req+'<br>numf_in_req='+numf_in_req+'<br>numf_fn_req='+numf_fn_req+'<br>in_req='+in_req+'<br>fn_req='+fn_req+'<br>tot_req='+tot_req;
	$('#variables').html(cad_var);
}

function cambiaTipoHolograma(idrow,valor){
	$.ajax({
		type: "POST",
		url: "php/inventario/upd_temps.php",
		contentType: "application/x-www-form-urlencoded;charset=UTF-8",
		data: 'idrow='+idrow+'&valor='+valor,
		datatype: 'json',
		success: function(data){
			//alert(data);
			data=JSON.parse(data);
			if(data.status !="OK") {
				if(valor == '1')
					valor = '0'
				else if(valor == '0')
					valor = '1'
				//alert(valor);
				$("#cboholograma").val(valor);
				alert(data.msj);
			}
		},
		beforeSend:function()
		{
		}
  	});
}

function genReq()
{
	user=$('#usr').val();
	$.ajax({
	type: "POST",
	url: "php/inventario/gen_req.php",
	contentType: "application/x-www-form-urlencoded;charset=UTF-8",
	data: 'user='+user+'&no_pedido='+no_pedido,
	datatype: 'json',
	success: function(data){
		//alert(data);
		data=JSON.parse(data);
		if(data.status=="OK")
		{
			alert(data.msj);
			limpiar_req();
			existe_temp=0;
			cte_req="";
			last_ffn_req="";
			no_pedido=0;
			req_list  = { };
			count_list=0;
			consec_list=0;
			$("#tbl_carrito").find("tr:gt(0)").remove();
			$('#tabla_req').css('display','none');
			$('#btns_req').css('display','none');
			getNoPedido(0);
			reload_online();
		}
		else
		{
			alert(data.msj);
		}
		//borra_carrito();
	},
	beforeSend:function()
	{
	}
  });
}
//GENERAR ENTRADA
function addEntrada2()
{
   user=$('#usr').html();
   $.ajax({
    type: "POST",
		  url: "php/inventario/add_entrada.php",
		  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
		  data: "user="+user+"&tipo="+tipo_h_req+"&cliente="+cte_req+"&marca="+marca+"&serie="+serie+"&ext_ini="+ext_ini+"&ext_fin="+ext_fin+"&ini_ent="+numf_in+"&fin_ent="+numf_fn+"&total="+tot_entrada+"&existe_reg="+existe_req,
		  datatype: 'json',
		  success: function(data){
			  data=JSON.parse(data);

			  if(data.status=='correcto')
			  {
			   alert(data.msj);
			   limpiar_parcial_req();
			   //vista_exists();
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
function canc_req()
{
	$.ajax({
    type: "POST",
		  url: "php/inventario/del_temps.php",
		  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
		  datatype: 'json',
		  success: function(data){
			  data=JSON.parse(data);
			  if(data.status=='OK')
			  {
			    //alert(data.msj);
			    limpiar_parcial_req();
			    existe_temp=0;
				cte_req="";
				last_ffn_req="";
				no_pedido=0;
				req_list  = { };
				count_list=0;
				consec_list=0;
				$("#tbl_carrito").find("tr:gt(0)").remove();
				$('#tabla_req').css('display','none');
				$('#btns_req').css('display','none');
				getNoPedido(0);
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
function conf_canc_req()
{
        dialog = $( "#conf_esc_req" ).dialog({
		autoOpen: false,
		height: 'auto',
		maxWidth: 500,
		width: 'auto',
		modal: true,
		buttons: {
		"Si": function() {
		  canc_req();
		  $( this ).dialog( "close" );
		},
		"No": function() {
		  $( this ).dialog( "close" );
		}
	  }
	  });
		dialog.dialog( "open" );

}
function check_estado()
{
	edo_sel=$('#cbo_edos').val();
	if(edo_sel=='NS')
	{
		$('#cbo_edos').focus();
	}
	else
	{
		$('#requeridos').attr('readonly',false)
		$('#cbo_tipo').focus();
	}

}
function ver_ids()
{
 $('#tbl_carrito tbody tr').each(function() {
  var customerId = $(this).find("td").eq(4).html();

  //alert(customerId);
 });
if($('#urge').is(":checked"))
		{
			alert('Normal');
		}
		else
		{
			alert('urgente');
		}

}
function get_edo(edo_s)
{
	switch(edo_s)
	{
		case 'OAXACA':
		{
			return 'OAX';
			break;
		}
		case 'GUERRERO':
		{
			return 'GRO';
			break;
		}
		case 'GUANAJUATO':
		{
			return 'GTO';
			break;
		}
		case 'MICHOACAN':
		{
			return 'MICH';
			break;
		}
		case 'ZACATECAS':
		{
			return 'ZAC';
			break;
		}
		case 'DURANGO':
		{
			return 'DGO';
			break;
		}
		case 'TAMAULIPAS':
		{
			return 'TAMS';
			break;
		}
		case 'SAN LUIS POTOSI':
		{
			return 'SLP';
			break;
		}
	}
}
function rev_edo(edo_s)
{
	switch(edo_s)
	{
		case 'OAX':
		{
			return 'OAXACA';
			break;
		}
		case 'GRO':
		{
			return 'GUERRERO';
			break;
		}
		case 'GTO':
		{
			return 'GUANAJUATO';
			break;
		}
		case 'MICH':
		{
			return 'MICHOACAN';
			break;
		}
		case 'ZAC':
		{
			return 'ZACATECAS';
			break;
		}
		case 'DGO':
		{
			return 'DURANGO';
			break;
		}
		case 'TAMS':
		{
			return 'TAMAULIPAS';
			break;
		}
		case 'SLP':
		{
			return 'SAN LUIS POTOSI';
			break;
		}
	}
}
function toggle_chk(state)
{
  $('#urge').bootstrapToggle(state)
}

function marcar_pago(folio_pedido)
{
	cliente_m=folio_pedido.substr(2,4);
	marca_m=folio_pedido.substr(6,1);
	estado_m=rev_edo(folio_pedido.substr(8));
	$.ajax({
	type: "POST",
	url: "php/inventario/conf_pago.php",
	contentType: "application/x-www-form-urlencoded;charset=UTF-8",
	datatype: 'json',
	data: "no_pedido="+no_pedido+"&no_cliente="+cliente_m+"&marca="+marca_m+"&estado="+estado_m,
	success: function(data){
		//alert(data);
		data=JSON.parse(data);
		if(data.status=='OK')
		{
		   $("#dialog_pago").dialog( "close" );
		   $('#'+folio_pedido).find("td").eq(7).html('SI');
		   $('#'+folio_pedido).find("td").eq(7).removeClass('pendiente');
		   $('#'+folio_pedido).find("td").eq(7).addClass('pagado');
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

function confirma_pago(folio)
{
	dialog = $("#dialog_pago").dialog({
		autoOpen: false,
		height: 'auto',
		width: 'auto',
		title: 'Confirmar Pago',
		buttons:{
			'SI': function(){
				 marcar_pago(folio)
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
function nn(n)
{
  var n=parseFloat(n).toFixed(2); /*se limita a dos decimales, no sabía que existía toFixed() :)*/
  var p=n.toString().substring(n.toString().indexOf(".")+1); /*decimales*/
  var m=n.toString().substring(0,n.toString().indexOf(".")); /*número sin decimales*/
  var m=parseFloat(m).toString().split("").reverse(); /*tampoco que reverse() existía :D*/
  var t="";

  /*Se analiza cada 3 dígitos*/
  for (var i=0; i<m.length; i+=3)
  {
    var x=t;
    /*formamos un número de 2 dígitos*/
    var b=m[i+1]!=undefined?parseFloat(m[i+1].toString()+m[i].toString()):parseFloat(m[i].toString());
    /*analizamos el 3 dígito*/
    t=m[i+2]!=undefined?(c[m[i+2]]+" "):"";
    t+=b<10?u[b]:(b<30?o[b-10]:(d[m[i+1]]+(m[i]=='0'?"":(" y "+u[m[i]]))));
    t=t=="ciento cero"?"cien":t;
    if (2<i&&i<6)
      t=t=="uno"?"mil ":(t.replace("uno","un")+" mil ");
    if (5<i&&i<9)
      t=t=="uno"?"un millón ":(t.replace("uno","un")+" millones ");
    t+=x;
    //t=i<3?t:(i<6?((t=="uno"?"mil ":(t+" mil "))+x):((t=="uno"?"un millón ":(t+" millones "))+x));
  }

  //t+=" con "+p+"/100";
  /*correcciones*/
  t=t.replace("  "," ");
  t=t.replace(" cero","");
  //t=t.replace("ciento y","cien y");
  //alert("Numero: "+n+"\nNº Dígitos: "+m.length+"\nDígitos: "+m+"\nDecimales: "+p+"\nt: "+t);
  //document.getElementById("esc").value=t;
  return t;
}
