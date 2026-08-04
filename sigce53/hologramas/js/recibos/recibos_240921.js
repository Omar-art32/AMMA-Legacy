var cons_rcbo="";
var anio_rcbo="";
//--------------//
var cte="";
var empresa="";
var nombre_marc="";
var grales={};
var dialog, form;
var rec_list  = { };
//lista de marcas donde se almacenan para saber si alguna ya fue usada
var  mca_list = { };
//variables para los folio asignados
var pos_asign=0;
var folios_asign={};
//arreglo y contador de mermas
var list_mermas  = { };
var count_mermas=0;
//arreglo y contador del carrito
var folios_carrito={};
var count_list=0;
var consec_list=0;
////NUEVO CODIGO PARA ESTADOS Y TIPOS
var num_edos=0;
var num_tipos=0;
var arr_exist={};
var count_arr_exist=0;
var gran_total_exist=0;

$( document ).ready(function() {
  "use strict";
  //Obtener Recibo
  getReciboNum();
  //Presionar enter en cantidad
   $('#txtCantidad').on('keyup', function (event) {
	   if(event.which === 13){
		    event.preventDefault();
		 var val_c= $('#txtCantidad').val();
		 if(val_c<1)
		 {
			 alert('La cantidad a entregar debe ser mayor a 0');
			 $('#txtCantidad').focus();
		 }
		 else
		 {
		   $('#obs_entrega').focus();
		 }
	   }

   });
   $('#txtCantidad').on('blur', function () {

		 var val_c= $('#txtCantidad').val();
		 if(val_c>0)
		 {
			 verifica_ex();
		 }
		 else
		 {
		   if($('#destino').val()!=="0")
		   {
			   $('#txtCantidad').focus();
		   }
		 }
	   return false;
   });
   //solo numeros
   $('#txtCantidad').keypress(function(tecla) {
        if(tecla.charCode < 48 || tecla.charCode > 57)
		{
			return false;
		}
    });
   //.--solonumeros
   $('#n_solicitud').on('keyup', function (event) {
         if(event.which === 13){
		    $('#destino').focus();
            $('#cbo_m').focus();
         }
   });
   //calendario
   $( "#txtFechaEntrega" ).datepicker({
      showOn: "button",
      buttonImage: "../images/date.png",
      buttonImageOnly: true,
      buttonText: "Seleccionar Fecha"
    });
   var myDate = new Date();
   var dia=(myDate.getDate());
   var mes=(myDate.getMonth()+1);
   var displayDate = myDate.getFullYear()+ '-' + pad(mes,2)  + '-' +pad(dia,2) ;
   $('#txtFechaEntrega').val(displayDate);

   //funciones para los inputs de las mermas
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

//funcion suggest
$(function() {
	"use strict";
		//autocomplete
		$("#cliente").autocomplete({
			source: "php/recibos/suggestClientes.php",
			minLength: 1,
			maxRows: 15,
			//select: function(e,ui){ cargaMarcas(ui.item.value);}
			select: function(e,ui){
				cte=ui.item.value;
				empresa= ui.item.asociado;
				cargaMarcas();
			}
		}).keypress(function(e) {

          if (e.keyCode === 13)
          {
          return false;
          }

      });
});
//fin funcion suggest

function muestraHistorico() {

	//alert("hola");
	$("#myHistorico").modal('toggle');

}

function getReciboNum()
{
   "use strict";
   $.ajax({
	  type: "POST",
	  url: "php/recibos/getReciboNum.php",
	  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
	  datatype: 'json',
	  success: function(response){
		   var  data=JSON.parse(response);
		  if(data.status==='correcto')
		  {
			  var myYear = new Date();
			  var fullY=myYear.getFullYear();
			  fullY=fullY.toString();
			  anio_rcbo=fullY.substring(2,4);
			  //alert(anio_rcbo);
			  cons_rcbo=parseInt(data.n_rcbo)+1;
			  var fol_rcbo='AR'+pad(cons_rcbo,4)+"/"+anio_rcbo;
		  $('#id_recibo').val(fol_rcbo);
		  }
		  else if(data.status==='error')
		  {
			  $('#msjs').html('No se pudo cargar el Folio del Recibo');
		  }
	  },
	  beforeSend:function()
	  {

		   $("#add_err").html("Loading...");
	  }
  });
}
function cargaMarcas()
{
  "use strict";
  limpiar_datos('nivCategoria');
  $.ajax({
	  type: "POST",
	  url: "php/cbo_marca.php",
	  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
	  data: "cliente="+cte+'&id=cbo_marcas'+'&funcion=checa_estados',
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
		 $("#add_err").html("Loading...");
	  }
  });
}
function checa_estados()
{
   "use strict";
   //----------inicia limpiar la seleccion anterior-----//
   gran_total_exist=0;
   $('#txtCantidad').val('');
   $('#txtCantidad').prop('disabled',true);
   limpiarCbo("cboTipo");
   limpiarCbo("cboEdo");
   limpiar_datos('nivCategoria');
   $("#tblExistencias tbody").empty();
   $('#dvExistencias').hide();
   $('#destino option[value=0]').prop('selected', true);
   //-------------fin limpiar--------//
   var marc=$('#cbo_marcas option:selected').val();
   if(marc!==0)
   {
	  $.ajax({
		  type: "POST",
		  url: "php/checaEstados.php",
		  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
		  data: "cliente="+cte+"&marca="+marc,
		  datatype: 'json',
		  success: function(response){
		      //alert(response);
			  var data_r=JSON.parse(response);
			  if(data_r.num_res>0)
			  {
				   var firs_edo="";
				    $('#dvEdoCat').show();
			        $("#cboEdo").append($("<option>", {
						value: '0',
						text: "SELECCIONAR"
					}));
				    for (var i = 0; i < data_r.estados.length; i++) {
					  $("#cboEdo").append($("<option>", {
						  value: data_r.estados[i].nombre,
						  text: data_r.estados[i].nombre
					  }));
					  num_edos++;
				    }
					if(num_edos===1)
					{
					   $('#cboEdo').val(data_r.estados[0].nombre).change();
					}
			  }
			  else
			  {
				  num_edos=0;
				  $('#dvEdoCat').hide();
				  limpiarCbo("cboEdo");
				   //cargaExistencias(1,marc);
			  }

		  },
		  beforeSend:function()
		  {
			   $("#add_err").html("Loading...");
		  }
	  });
   }
}
function getCategorias()
{
   "use strict";
   limpiarCbo("cboTipo");
   limpiar_datos('nivCategoria');
   if($('#cboEdo option:selected').val()!=='0' && $('#cboEdo option:selected').val()!=='NA')
   {
	   $.ajax({
		  type: "POST",
		  url: "php/recibos/getCategorias.php",
		  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
		  data: "cliente="+cte+"&marca="+$('#cbo_marcas option:selected').val()+"&edo="+$('#cboEdo option:selected').val(),
		  datatype: 'json',
		  success: function(response){
		     //alert(response);
			 var data_r=JSON.parse(response);
			 if(data_r.num_res>0)
			  {
				  num_tipos=0;
				  $("#tblExistencias tbody").empty();
				   var firs_edo="";
			        $("#cboTipo").append($("<option>", {
						value: '0',
						text: "SELECCIONAR"
					}));
					var tipo_full="";
				    for (var i = 0; i < data_r.tipos.length; i++) {
					 	tipo_full=getTipoFull(data_r.tipos[i].tipo);
					  $("#cboTipo").append($("<option>", {
						  value: data_r.tipos[i].tipo,
						  text: tipo_full
					  }));
					  num_tipos++;
				    }
					if(num_tipos===1)
					{
					   $('#cboTipo').val(data_r.tipos[0].tipo).change();
					}
			  }
			  else
			  {
				  num_tipos=0;
				  limpiarCbo("cboTipo");
				   //cargaExistencias(1,marc);
			  }
		  },
		  beforeSend:function()
		  {
			   $("#add_err").html("Loading...");
		  }
	  });
   }
}
function cargaExistencias()
{
  "use strict";
  limpiar_datos('nivDestino');
  $("#tblEstatus tbody").empty();
  try
  {
    var datos_env="";
    var error_datos=0;
    //PRIMERO VERIFICAMOS QUE HAYA SELECCIONADO UN CLIENTE Y UN MARCA
    if(cte==="" || $('#cbo_marcas option:selected').val()==='0')
	{
	  throw "Primero debe seleccionar un cliente y marca";
	}
    //VERIFICAMOS SI EXISTEN VARIOS ESTADOS PARA ESA MARCA O SI EXISTEN EXISTENCIAS SIN ESTADO (option: NA)
	if(num_edos===0 || $('#cboEdo option:selected').val()==='NA')
	{
	  datos_env="cliente="+cte+"&marca="+$('#cbo_marcas option:selected').val()+"&num_edos=0";
	}//..if num_edos===0
	else
	{
	  datos_env="cliente="+cte+"&marca="+$('#cbo_marcas option:selected').val()+"&num_edos="+num_edos+"&edo="+$('#cboEdo option:selected').val()+"&tipo="+$('#cboTipo option:selected').val();
	  //COMO EXISTEN REGISTROS CON ESTADOS DEBEMOS VERIFICAR QUE SE HAYA SELECCIONADO UNO
	  if($('#cboEdo option:selected').val()==='0' || $('#cboTipo option:selected').val()==='0')
	  {
		throw "Debes seleccionar un estado y categoria";
	  }
	}//.else num_edos===0
	//alert(datos_env);
	 $.ajax({
		  type: "POST",
		  url: "php/recibos/cargaExistencias.php",
		  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
		  data: datos_env,
		  datatype: 'json',
		  success: function(response){
			 var data_r=JSON.parse(response);
			 if(data_r.status==="OK")
			 {
				 if(data_r.num_res>0)
				 {
					  for (var i = 0; i < data_r.arr_exs.length; i++) //RECORRER EL ARRAY arr_exs DEVUELTO Y ASIGNAR LOS ARREGLOS HIJOS A UN OBJETO SEGUN EL NUMERO DE FILAS
					  {
						 count_arr_exist=i+1;
						 arr_exist[i]=data_r.arr_exs[i];
						 var cadena = "<tr id='row_Ex_"+i+"' align='center'>";
						 cadena = cadena + "<td>" +cte + arr_exist[i].marca + pad(arr_exist[i].fol_ini,7) + arr_exist[i].serie +"</td>";
						 cadena = cadena + "<td>" +cte + arr_exist[i].marca + pad(arr_exist[i].fol_fin,7) + arr_exist[i].serie + "</td>";
						 cadena = cadena + "<td>" +arr_exist[i].existencias + "</td>";
						 cadena = cadena + "</tr>";
						 gran_total_exist+=parseInt(arr_exist[i].existencias);
						 $("#tblExistencias tbody").append(cadena);
					  }
					  if(count_arr_exist>1)//AGREGAR EL GENERAL DE TOTALES SI SON MAS DE UNA FILA
					  {
					     var cad_tot="<tr id='row_Ex_tot' align='center'><td>&nbsp;</td><td><b>Total Existencias:</b></td><td>" +gran_total_exist + "</td></tr>";
						 $("#tblExistencias tbody").append(cad_tot);
					  }
					  // COLOCAR ESTATUS
					  // color:#900;
					  //if(clvuser == '1') {
              //alert(data_r.txth);
              $("#pHistorico").html(data_r.txth);
						  var cadenam = "<tr id='estatusm' align='center'>";
							 cadenam += "<td>" + data_r.estatus +"</td>";
							 cadenam += "<td><button id='historicoh' type='button' class='btn btn-info btn-sm' onclick='muestraHistorico();'>...</button></td>";
							 cadenam += "</tr>";

						  $("#tblEstatus tbody").append(cadenam);
					  //}

					  $('#dvExistencias').show();
					  $('#txtCantidad').prop('disabled',false);
					  $('#txtCantidad').focus();
				  }
		     }
			 else
			 {
				alert(data_r.msj);
		     }
		  },
		  beforeSend:function()
		  {
			 $("#add_err").html("Loading...");
		  }
	  });//..$ajax
  }//..try
  catch(err)
  {
	  alert(err);
	  $('#destino option[value=0]').prop('selected', true);
  }//..catch
}

function limpiar_datos(nivel)
{
    "use strict";
	switch(nivel)
	{
	    case 'nivCliente':
		{
			limpiarNivCliente();
			limpiarNivCategoria();
			limpiarNivDestino();
			limpiarNivCantidad();
			limpiarNivCarrito();
			break;
	    }
	    case 'nivCarrito':
		{
			limpiarNivCategoria();
			limpiarNivDestino();
			limpiarNivCantidad();
			limpiarNivCarrito();
			break;
	    }
		case 'nivCategoria':
		{
			limpiarNivCategoria();
			limpiarNivDestino();
			limpiarNivCantidad();
			break;
	    }
		case 'nivDestino':
		{
			limpiarNivDestino();
			limpiarNivCantidad();
			break;
	    }
		case 'nivCantidad':
		{
			limpiarNivCantidad();
			break;
	    }
	}
}
function limpiarNivCliente()
{
	"use strict";
	$('#cliente').prop('disabled',false);
    $('#cliente').val('');
    cte="";
}
function limpiarNivCarrito()
{
	"use strict";
	$('#cbo_marcas').val('0').change();
	$("#n_solicitud").val('');
}
function limpiarNivCategoria()
{
	"use strict";
	$('#txtCantidad').val('');
	$('#txtCantidad').prop('disabled',true);
	$('#destino option:eq(0)').prop('selected', true);
}
function limpiarNivDestino()
{
	"use strict";
	//limpiamos existencias
	arr_exist={};
    count_arr_exist=0;
    gran_total_exist=0;
	//limpiamos asignados
	pos_asign=0;
    folios_asign={};
	$("#tblExistencias tbody").empty();
	$('#dvExistencias').css('display','none');
	$('#txtCantidad').val('');
}
function limpiarNivCantidad()
{
	"use strict";
	//se eliminan los folios asignados y se reinicia el puntero
	pos_asign=0;
	folios_asign={};
	//se limpia el arreglo de mermas y se reinicia el contador
	list_mermas  = { };
	count_mermas=0;
	$('#txtTotal').val('');
	$('#mermas').val('');
	$('#fol_mermas').val('');
	$('#div_mermas').css('display','none');
	//limpiar la tabla de asignados
	$("#tblAsignados tbody").empty();
	$('#dvAsignados').css('display','none');
	//$("#cbo_mtvo option:value")
	$('#cbo_mtvo option[value=0]').prop('selected', true);
	$('#cbo_mtvo').prop('disabled',true);
	$('#btnDelMermas').css('visibility','hidden');
}

/*
function limpiar()
{
  "use strict";
  $('#cliente').prop('disabled',false);
  $('#cliente').val('');
  cte="";
  $('#cbo_marcas option[value=0]').prop('selected', true);
  $('#cbo_m').html('------');
  $('#cliente').focus();
  $('#n_solicitud').val('');
  $('#destino option[value=0]').prop('selected', true);
  $('#d_existe').css('display','none');
  $('#txtCantidad').val('');
  $('#txtfini').val('');
  $('#txtffin').val('');
  $('#recibo').css('display','none');
  $('#obs_entrega').val('');
  $('#cliente').focus();
}*/
function getTipoFull(numTipo)
{
  "use strict";
  switch(numTipo)
  {
	  case "1":
	  {
		  return "MEZCAL";
	  }
	  case "2":
	  {
		  return "MEZCAL ARTESANAL";
	  }
	  case "3":
	  {
		  return "MEZCAL ANCESTRAL";
	  }
  }
}
function limpiarCbo(target)
{
	"use strict";
	$("#"+target).text('');
	$("#"+target+" option").remove();
}
//Verificar que hay existencias para cobrir el monto requerido
function verifica_ex()
{
	"use strict";
    var cantidad_sol=parseInt($('#txtCantidad').val());
	if(cantidad_sol>gran_total_exist)
	{

		$('#msjBody').html('Hologramas insuficientes para la cantidad requerida');
		$('#modalMsjs').modal('show');
		$("#btn_entendido").bind( "click", function( event ){
		   $('#modalMsjs').modal('hide');
           $('#txtCantidad').focus();
        });
		//limpia_asignados();
		//return false;
	}
	else
	{
		pos_asign=0;
		limpiar_datos('nivCantidad');
		asignar_folios(cantidad_sol);
		$('#btns_recibo').css('display','block');
		return false;
	}
}
function asignar_folios(cantidad)
{
	"use strict";
	var existencia_pos=arr_exist[pos_asign].existencias;
	folios_asign[pos_asign]={};
	folios_asign[pos_asign].id_exs=arr_exist[pos_asign].id;
	folios_asign[pos_asign].marca=arr_exist[pos_asign].marca;
	folios_asign[pos_asign].edo=arr_exist[pos_asign].edo;
	folios_asign[pos_asign].tipo=arr_exist[pos_asign].tipo;
	folios_asign[pos_asign].serie=arr_exist[pos_asign].serie;
	folios_asign[pos_asign].fol_ini=arr_exist[pos_asign].fol_ini;
	folios_asign[pos_asign].tot_mermas=0;
	folios_asign[pos_asign].motivo_merma="";
	folios_asign[pos_asign].mermas_nums="";
	folios_asign[pos_asign].mermas_folio="";

	if(cantidad>=existencia_pos)
	{

	    folios_asign[pos_asign].borrar=1;
		folios_asign[pos_asign].fol_fin=arr_exist[pos_asign].fol_fin;
		folios_asign[pos_asign].cant=existencia_pos;
		folios_asign[pos_asign].cant_real=existencia_pos;
	}
	else
	{
		folios_asign[pos_asign].borrar=0;
		var f_fin_asign=(parseInt(arr_exist[pos_asign].fol_ini)+cantidad)-1;
		folios_asign[pos_asign].fol_fin=f_fin_asign;
		folios_asign[pos_asign].cant=cantidad;
		folios_asign[pos_asign].cant_real=cantidad;
	}
	var cadena = "<tr id='row_Ex_"+pos_asign+"' align='center'>";
    cadena = cadena + "<td>" +cte + arr_exist[pos_asign].marca + pad(folios_asign[pos_asign].fol_ini,7) + arr_exist[pos_asign].serie +"</td>";
    cadena = cadena + "<td>" +cte + arr_exist[pos_asign].marca + pad(folios_asign[pos_asign].fol_fin,7) + arr_exist[pos_asign].serie + "</td>";
    cadena = cadena + "<td>" +folios_asign[pos_asign].cant + "</td>";
    cadena = cadena + "</tr>";
    //gran_total_exist+=arr_exist[i].existencias;
    $("#tblAsignados tbody").append(cadena);
	$('#dvAsignados').show();
	if(cantidad>existencia_pos)
	{
	    //alert(existencia_pos);
		var faltantes=cantidad-folios_asign[pos_asign].cant;
		pos_asign++;
		asignar_folios(faltantes);
	}
	else
	{
		 get_totalEntrega();
		 $('#cbo_mtvo').prop('disabled',false);
         $('#cbo_mtvo').focus();
	}
}
//AGREGAR CEROS A LA DERECHA
function pad (str, maxi)
{
  "use strict";
  str = str.toString();
  return str.length < maxi ? pad("0" + str, maxi) : str;
}
//GENERAR RECIBO
function reciboSimple()
{



	grales.anio_recibo=anio_rcbo;
	grales.no_recibo=cons_rcbo;
	grales.cte=cte;
	grales.empresa=encodeURIComponent(empresa);
	//grales.nombre_marc=$('#cbo_marcas option:selected').html();
	grales.marca=$('#cbo_marcas option:selected').val();
	grales.obs_entrega=$('#obs_entrega').val();
	grales.fecha_entrega=$('#txtFechaEntrega').val();
	grales.solicitud=$('#n_solicitud').val();
	grales.destino=$('#destino').val();
	grales.usuario=user;
	folios_carrito.data={};
	for(var x=0;x<=pos_asign;x++)
	{
	  //asignamos los datos de los folios al arreglo data
	  folios_carrito.data[x]=folios_asign[x];
	}
	//alert(JSON.stringify(grales));
	$.ajax({
		  type: "POST",
		  url: "php/recibos/add_recibo.php",
		  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
		  data: "grales="+JSON.stringify(grales)+"&arr_data="+JSON.stringify(folios_carrito),
		  datatype: 'json',
		  success: function(data){
			  //alert(data);
			  var data_r=JSON.parse(data);
			  if(data_r.status==='OK')
			  {
			    getReciboNum();
				limpiar_datos('nivCliente');
				var dir=data_r.msj;
			    window.open(dir, '_blank');
			  }
			  else
			  {
				//limpiar();
				//getReciboNum();
				alert(data.msj);

			  }


		  },
		  beforeSend:function()
		  {
		  		$( "#btnRecibo" ).prop( "disabled", true );
		  		$( "#btnAgrega" ).prop( "disabled", true );
		  		$( "#btncCancel" ).prop( "disabled", true );


		  },

		  complete:function()
		  {
		  	    $( "#btnRecibo" ).prop( "disabled", false );
		  		$( "#btnAgrega" ).prop( "disabled", false );
		  		$( "#btncCancel" ).prop( "disabled", false );
		  }
	  });
}
function addCarrito()
{
  "use strict";
  var key_arr="kc_"+folios_asign[0].marca;
  if(num_edos>0)
  {
	  if($('#cboEdo option:selected').val()!=='NA')
	  {
		  key_arr=key_arr+folios_asign[0].edo+folios_asign[0].tipo;
	  }
  }
  if(mca_list[key_arr])
  {
	alert('Ya has usado esta marca');
	//limpia_car();
  }
  else
  {
	  mca_list[key_arr]=1;
	  folios_carrito[key_arr]={};
	  folios_carrito[key_arr].data={};
	  //alert(JSON.stringify(folios_carrito));

	  count_list++;
	  consec_list++;
	  //AGREGAMOS LOS DATOS GENERALES POR CADA MARCA
	  //folios_carrito[key_arr].nombre_marc=$('#cbo_marcas option:selected').html();
	  nombre_marc=$('#cbo_marcas option:selected').html();
	  folios_carrito[key_arr].marca=$('#cbo_marcas option:selected').val();
	  folios_carrito[key_arr].solicitud=$('#n_solicitud').val();
	  folios_carrito[key_arr].destino=$('#destino').val();
	  folios_carrito[key_arr].subtotal=$('#txtCantidad').val();
	  folios_carrito[key_arr].total_sellos=$('#txtTotal').val();
	    //creamos la fila con el ide consecutivo, si se elimina uno el contador disminuye pero el id consecutivo continua en su posicion actual
	    for(var x=0;x<=pos_asign;x++)
		{
		 //asignamos los datos de los folios al arreglo data
		 folios_carrito[key_arr].data[x]=folios_asign[x];
		 //creamos la cadena con los folios completos
		 var list_fi=cte+folios_carrito[key_arr].data[x].marca+pad(folios_carrito[key_arr].data[x].fol_ini,7)+folios_carrito[key_arr].data[x].serie;
	     var list_ff=cte+folios_carrito[key_arr].data[x].marca+pad(folios_carrito[key_arr].data[x].fol_fin,7)+folios_carrito[key_arr].data[x].serie;
		 //creamos el id para la fila de la tabla
		 var id_fila="fila"+key_arr+"_"+x;
		 //creamos la fila y agregamos sus celdas
		 var  cadena = "<tr id='"+id_fila+"' align='center'>";
		 cadena = cadena + "<td>" +nombre_marc + "</td>";
		 cadena = cadena + "<td>" +folios_carrito[key_arr].data[x].edo + "</td>";
		 cadena = cadena + "<td>" +folios_carrito[key_arr].data[x].tipo + "</td>";
		 cadena = cadena + "<td style='font-weight:bold; letter-spacing:2px;'>" + list_fi + "</td>";
		 cadena = cadena + "<td style='font-weight:bold; letter-spacing:2px;'>" + list_ff+ "</td>";
		 cadena = cadena + "<td style='font-weight:bold; letter-spacing:2px;'>" + folios_carrito[key_arr].data[x].cant_real + "</td>";
		 cadena = cadena + "<td><a href='javascript:elimin_fil(\""+key_arr+"\","+pos_asign+")'><img src='../images/delete.png' width='20px'/></a></td></tr>";
	      //agregamos la fila creada a la tabla
	      $("#tblCarrito tbody").append(cadena);
	   }
	   $('#cliente').prop('disabled',true);
	   $('#btnRecibo').prop('disabled',true);
	   $('#dvCarrito').css('display','block');
	   if(count_list===2)
	   {
		 $('#btns_carrito').css('display','block');
	   }
	   if(count_list===3)
	   {
		 $('#btns_carrito').css('display','block');
		 $('#btns_carrito').css('display','block');
		 $('#cbo_marcas').prop('disabled',true);
		 $('#destino').prop('disabled',true);
		 $('#txtCantidad').prop('disabled',true);
	   }
	   //alert(JSON.stringify(folios_carrito));
	   limpiar_datos('nivCarrito');
	   //delMermas();
  }
}
function reciboMultiple()
{
  "use strict";
  $('#btnTerminar').prop('disabled',true);
  grales.anio_recibo=anio_rcbo;
  grales.no_recibo=cons_rcbo;
  grales.cte=cte;
  grales.empresa=empresa;
  grales.obs_entrega=$('#obs_entrega').val();
  grales.fecha_entrega=$('#txtFechaEntrega').val();
  grales.usuario=user;
  //alert(JSON.stringify(folios_carrito));
  $.ajax({
	  type: "POST",
	  url: "php/recibos/add_reciboCarrito.php",
	  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
	  data: "grales="+JSON.stringify(grales)+"&arr_data="+JSON.stringify(folios_carrito),
	  datatype: 'json',
	  success: function(data){
		  //alert(data);
		  var data_r=JSON.parse(data);
		  if(data_r.status==='OK')
		  {
			  borra_carrito();
			  getReciboNum();
			  var dir=data_r.msj;
			  window.open(dir, '_blank');
		  }
		  else
		  {
			  alert(data_r);
		  }
		  $('#btnTerminar').prop('disabled',false);
		  //borra_carrito();
	  },
	  beforeSend:function()
		  {
		  		$( "#btnRecibo" ).prop( "disabled", true );
		  		$( "#btnAgrega" ).prop( "disabled", true );
		  		$( "#btncCancel" ).prop( "disabled", true );
		  		$( "#btnTerminar" ).prop( "disabled", true );
		  		$( "#btnEsc_Carrito" ).prop( "disabled", true );






		  },

		  complete:function()
		  {
		  	    $( "#btnRecibo" ).prop( "disabled", false );
		  		$( "#btnAgrega" ).prop( "disabled", false );
		  		$( "#btncCancel" ).prop( "disabled", false );
		  		$( "#btnTerminar" ).prop( "disabled", false );
		  		$( "#btnEsc_Carrito" ).prop( "disabled", false);
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
function goBottom2()
{
	$('html, body').animate({
           scrollTop: $(document).height()-50
       },
       1500);
}
function elimin_fil(id,num_pos)
{
    "use strict";
	var fil_remove="";
	delete mca_list[id];
	delete folios_carrito[id];
    for(var p=0;p<num_pos;p++)
	{
		fil_remove='fila'+id+'_'+p;
		$('#'+fil_remove).remove();
	}
	count_list--;
	if(count_list===2)
	{
		 $('#cbo_marcas').prop('disabled',false);
		 $('#destino').prop('disabled',false);
		 $('#txtCantidad').prop('disabled',false);
	}
	if(count_list===1)
	{
		$('#btns_carrito').css('display','none');
	}
	alert(count_list);
	if(count_list===0)
	{
		consec_list=0;
		$('#cliente').prop('disabled',false);
		$('#btnRecibo').prop('disabled',false);
		$('#btns_recibo').css('display','none');
		$('#btns_carrito').css('display','none');
		$("#dvCarrito").css('display','none');
		$('#cbo_marcas').prop('disabled',false);
		$('#destino').prop('disabled',false);
		$('#txtCantidad').prop('disabled',false);
	}
	limpiar_datos('nivCategoria');
	limpiarCbo("cboEdo");
	//alert(JSON.stringify(folios_carrito));
}
function confirma_canc()
{
	$( "#dialog-confirm" ).dialog("open");
}
$(function() {
  $( "#dialog-confirm" ).dialog({
	autoOpen:false,
	resizable: false,
	height:180,
	minWidth:250,
	modal: true,
	buttons: {
	  "Si": function() {
		if(count_list>0)
		{
		  borra_carrito();
		}
		else
		{
		  limpiar_datos('nivCliente');
		}
		$( this ).dialog( "close" );
	  },
	  "No": function() {
		$( this ).dialog( "close" );
	  }
	}
  });
});
function canc_carrito()
{
	$("#conf_esc_carrito").dialog("open");
}
function borra_carrito()
{
   "use strict";
   $("#tblCarrito tbody").empty();
   folios_carrito={};
   folios_asign={};
   mca_list ={};
   consec_list=0;
   count_list=0;
   $('#cliente').prop('disabled',false);
   $('#btns_carrito').css('display','none');
   $('#btnRecibo').prop('disabled',false);
   $("#dvCarrito").css('display','none');
   $('#cbo_marcas').prop('disabled',false);
   $('#destino').prop('disabled',false);
   $('#txtCantidad').prop('disabled',false);
   limpiar_datos('nivCarrito');
}

$(function() {
  $( "#conf_esc_carrito" ).dialog({
	autoOpen:false,
	resizable: false,
	height:180,
	minWidth:250,
	modal: true,
	buttons: {
	  "Si": function() {
		borra_carrito();
		$( this ).dialog( "close" );
	  },
	  "No": function() {
		$( this ).dialog( "close" );
	  }
	}
  });
});
function ver_add()
{
	$('#div_mermas').css('display','block');
	$('#fol_ini_mermas').focus();
}

function addMerma(tipo)
{
	"use strict";
	if($('#btnDelMermas').css('visibility')==='hidden')
	{
		$('#btnDelMermas').css('visibility','visible');
	}
	//tot_mermas=parseInt($('#mermas').val());

	var new_val='';
	var sep='';
	var subT=0;
	var i=0;
	var fol_merma_completo="";
	if(tipo===1)//tipo 1 significa que solo se esta ingresando un numero de merma
	{

	  for(i=0;i<=pos_asign;i++)//recorrer el arreglo de folios asignados
	  {
		  var fol_m=$('#fol_ini_mermas').val();



		  if(fol_m<parseInt(folios_asign[i].fol_ini) || fol_m>parseInt(folios_asign[i].fol_fin))
		  {
			  if(i===pos_asign)
			  {
				alert('El folio de la merma no se encuentra dentro del intervalo de sellos a entregar, le sugerimos revisar la informacion');
			  }
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
					if(folios_asign[i].mermas_folio!=='')
					{
						sep=', ';
					}
					folios_asign[i].mermas_nums=folios_asign[i].mermas_nums+sep+fol_m;
					fol_merma_completo=cte+folios_asign[i].marca+pad(fol_m,7)+folios_asign[i].serie;
					folios_asign[i].mermas_folio = folios_asign[i].mermas_folio+sep+fol_merma_completo;
					folios_asign[i].motivo_merma=$('#cbo_mtvo option:selected').val();
					subT= folios_asign[i].cant_real;
					subT=subT-1;
					count_mermas++;
					folios_asign[i].cant_real=subT;
					folios_asign[i].tot_mermas=folios_asign[i].tot_mermas+1;
					$('#fol_mermas').val($('#fol_mermas').val()+sep+fol_merma_completo);
					$('#mermas').val(count_mermas);
					get_totalEntrega();
					$('#fol_ini_mermas').val('');
					$('#fol_ini_mermas').focus();
				}
		  }
	  }//loop for
	}
	else if(tipo===2)
	{
		var fol_m1=parseInt($('#fol_ini_mermas').val());
		var fol_m2=parseInt($('#fol_fin_mermas').val());
		var ct_mermas=fol_m2-fol_m1+1;
		if(fol_m1>=fol_m2)
		{
			alert("El folio final debe ser mayor al inicial");
			$('#fol_fin_mermas').val('');
			$('#fol_fin_mermas').focus();
		}
		else
		{
			  var folio_i=fol_m1;
			  for(i=0;i<=pos_asign;i++)//recorrer el arreglo de folios asignados
			  {
				for (folio_i=fol_m1;folio_i<=fol_m2;folio_i++)
				{
				   if(folio_i<parseInt(folios_asign[i].fol_ini) || folio_i>parseInt(folios_asign[i].fol_fin))
					{
					  if(i===pos_asign)
			          {
						alert('El folio de la merma no se encuentra dentro del intervalo de sellos a entregar, le sugerimos revisar la informacion'+folio_i+' '+folios_asign[i].fol_ini);
						//folio_i=fol_m2+1;
					  }
					}
					else
					{
						if(list_mermas[folio_i])
						{
						   alert('Este folio ya fue agregado');
						   $('#fol_ini_mermas').val('');
						   $('#fol_ini_mermas').focus();
						}
						else
						{
							list_mermas[folio_i] =folio_i;
							if(folios_asign[i].mermas_folio!=='')
							{
								sep=', ';
							}
							folios_asign[i].mermas_nums=folios_asign[i].mermas_nums+sep+folio_i;
							fol_merma_completo=cte+folios_asign[i].marca+pad(folio_i,7)+folios_asign[i].serie;
							folios_asign[i].mermas_folio = folios_asign[i].mermas_folio+sep+fol_merma_completo;
							$('#fol_mermas').val($('#fol_mermas').val()+sep+fol_merma_completo);
							subT = folios_asign[i].cant_real;
					        subT=subT-1;
					        folios_asign[i].cant_real=subT;
							folios_asign[i].tot_mermas=folios_asign[i].tot_mermas+1;
							count_mermas++;
							$('#mermas').val(count_mermas);
							get_totalEntrega();
							if(folio_i===fol_m2)
							{
								i=pos_asign;
								$('#fol_ini_mermas').val('');
							    $('#fol_ini_mermas').focus();
							}
						}
					}
				}//loop for asignados
			}//loop for folio
			$('#fol_ini_mermas').val('');
			$('#fol_fin_mermas').val('');
			//alert(folios_asign[0].mermas_folio+'////////'+folios_asign[1].mermas_nums);
			$('#fol_ini_mermas').focus();
		}//fin else fol_m1>=fol_m2
	}//fin if tipo2
}

function get_totalEntrega()
{
	"use strict";
	var cantidad_req=parseInt($('#txtCantidad').val());
	var total_entrega=cantidad_req-count_mermas;
	if(isNaN(cantidad_req))
	{
		total_entrega=0;
	}
	$('#txtTotal').val(total_entrega);
}
function delMermas()
{
    "use strict";
	for(var i=0;i<=pos_asign;i++)//recorrer el arreglo de folios asignados
	{
		folios_asign[i].cant_real=parseInt(folios_asign[i].cant_real)+parseInt(folios_asign[i].tot_mermas);
		folios_asign[i].tot_mermas=0;
		folios_asign[i].motivo_merma="";
		folios_asign[i].mermas_nums="";
		folios_asign[i].mermas_folio="";
	}
	list_mermas={};
	count_mermas=0;
	$('#cbo_mtvo option[value=0]').prop('selected', true);
	//$('#cbo_mtvo').prop('disabled',true);
	$('#btnDelMermas').css('visibility','hidden');
	$('#mermas').val(count_mermas);
	get_totalEntrega();
	$('#fol_ini_mermas').val('');
	$('#fol_fin_mermas').val('');
	$('#fol_mermas').val('');
	//$('txtTotal').val('');
	$('#div_mermas').css('display','none');
}
function limpiarMermas()
{
	"use strict";
	list_mermas={};
	count_mermas=0;
	$('#cbo_mtvo option[value=0]').prop('selected', true);
	$('#cbo_mtvo').prop('disabled',true);
	$('#btnDelMermas').css('visibility','hidden');
	$('#mermas').val('');
	$('#fol_ini_mermas').val('');
	$('#fol_fin_mermas').val('');
	$('#fol_mermas').val('');
	$('#txtTotal').val('');
	$('#div_mermas').css('display','none');
}


function addMarca()
{
	 "use strict";
	  var dialog = $( "#dialog-form1" ).dialog({
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
   "use strict";
   $('#marca_new').val('');
   var cte_nmarca=$('#nc_marca').val();
   $.ajax({

	  type: "POST",
	  url: "php/get_marca.php",
	  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
	  data: "cliente="+cte_nmarca,
	  datatype: 'json',
	  success: function(response){
		  var jmarcas=JSON.parse(response);
		  if(jmarcas.status==='correcto')
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
		  $("#add_err").html("Loading...");
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
					  cargaMarcas();
					  dialog.dialog( "close" );
				  }
				  else
				  {
					  var msj_marca=jaddmca.msj;
					  alert(msj_marca);
					  $('#marca_new').val('');
					  cargaMarcas();
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
