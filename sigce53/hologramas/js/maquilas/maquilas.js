"use strict";
var listaMarcas={};
var consec_list=0;
var clienteActual="";
$(function() {
		//autocomplete
		$("#txtCteProv").autocomplete({
			source: "php/search.php",//MISMA FUNCION QUE LOS RECIBOS
			minLength: 1,
			maxRows: 15,
			//select: function(e,ui){ carga_cbo(ui.item.value);}
			select: function(e,ui){ //getTipo(ui.item.value);
			}
		}).keypress(function(e) {

          if (e.keyCode === 13) 
          {
          return false;
          }

        });		
		 $("#txtCteRec").autocomplete({
			source: "php/search.php",//MISMA FUNCION QUE LOS RECIBOS
			minLength: 1,
			maxRows: 15,
			//select: function(e,ui){ carga_cbo(ui.item.value);}
			select: function(e,ui){ //getTipo(ui.item.value);
			}
		}).keypress(function(e) {

		  if (e.keyCode === 13) 
		  {
		  return false;
		  }	  
		});
		$('#modalAddMaq').on('shown.bs.modal', function (e) {	 
			  //autocomplete
			 limpiarMaquilas();
			 $('#txtCteProv').focus();
	    });
		
	    		
});
function limpiarMaquilas()
{
	$('#txtCteProv').val('');
	listaMarcas={};
	$('#tblMarcas tbody').empty();
	$('#cboTipoRel option[value=0]').removeAttr('selected');	
    $('#cboTipoRel option[value=0]').attr('selected',true);
	$("#cbo_marca option").remove();
	$('#dvMarcasArr').hide();
	$('#dvMarcasCbo').hide();
}
function validaDatos()
{
	$('#formMaquila').valid();
	var contM=Object.keys(listaMarcas).length;
	if($('#formMaquila').valid())
	{
		if($('#cboTipoRel option:selected').val()!=='0'){
			switch($('#cboTipoRel option:selected').val()){
				case 'P':
				{
					registrarRelacion('P');
					break;
				}
				case 'E':
				{
					if(contM>0){
						registrarRelacion('E');
					}else{
						alert('Para este tipo de relacion debe agregar al menos una marca');
					}
					break;
				}
				case 'EP':
				{
					if(contM>0){
						registrarRelacion('EP');
					}else{
						alert('Para este tipo de relacion debe agregar al menos una marca');
					}
					break;
				}
			}
		}else{
			alert('Debe seleccionar un tipo de relacion');
		}
		
	}
}
function registrarRelacion(tipoR)
{
	var datosMaquila="";
	clienteActual=$('#txtCteRec').val();	
	var cteProv=$('#txtCteProv').val();
	var obs=$('#txtObs').val();
	datosMaquila="id_ses="+id_s+"&cteRec="+clienteActual+"&cteProv="+cteProv+"&obs="+obs+"&tipoR="+tipoR;
	var marcas="";
	if(tipoR==='E' || tipoR==='PE'){
		marcas=JSON.stringify(listaMarcas);
		datosMaquila+="&marcas="+marcas;
	}
	$.ajax({
		type: "POST",
		url: "php/maquilas/addMaquila.php",
		contentType: "application/x-www-form-urlencoded;charset=UTF-8",
		data: datosMaquila,
		dataType: "json",
		success: function(data) {
			alert(data);
			if (data.status === "correcto") {
				//alert(data.marcas);
								
			}
			else {
				alert(data.msj);
			}	
		},
		error: function(jqxhr) {
			alert("Ha ocurrido un error al registrar la relacion: " + jqxhr.responseText);
		}
	});	
	
}
function validaRel()
{
	switch($('#cboTipoRel option:selected').val())
	{
		case '0':
		{
			$('#dvMarcasArr').hide();
			$('#dvMarcasCbo').hide();
			listaMarcas={};
	        $('#tblMarcas tbody').empty();
			break;
		}
		case 'P':
		{
			$('#dvMarcasArr').hide();
			$('#dvMarcasCbo').hide();
			listaMarcas={};
	        $('#tblMarcas tbody').empty();
			break;
		}
		case 'E':
		{
			get_marcas('cboMarcaRec');
			$('#dvMarcasArr').show();
			$('#dvMarcasCbo').show();
			break;
		}
		case 'PE':
		{
			get_marcas('cboMarcaRec');
			$('#dvMarcasArr').show();
			$('#dvMarcasCbo').show();
			break;
		}
	}
}
function addMarca()
{   
   var cve=$('#cboMarcaRec option:selected').val();
   var mca=$('#cboMarcaRec option:selected').text();
   if(!listaMarcas[cve]){
	listaMarcas[cve]=cve;
	var cadena = "<tr id='row_Ex_"+consec_list+"' align='center'>";						
		  cadena = cadena + "<td>" +cve+"</td>";
		  cadena = cadena + "<td>" +mca+ "</td>";
		  cadena = cadena + "<td><button type='button' class='btn btn-sm btn-danger' onClick='eliminaMca("+consec_list+");'><i class='fa fa-md fa-minus' aria-hidden='true'></i></button></td>";
		  cadena = cadena + "</tr>";
		  $("#tblMarcas tbody").append(cadena);	
		  consec_list++;
	     $('#cboMarcaRec option[value=0]').removeAttr('selected');	
		 $('#cboMarcaRec option[value=0]').attr('selected',true);
   }else{
	   alert('Ya has usado esa marca');
   }
}
function get_marcas(o_target)
{	
    var cteBus=$('#txtCteRec').val();
	$("#cboMarcaRec option").remove();	
	$.ajax({
		type: "POST",
		url: "php/maquilas/get_marcas_cliente.php",
		contentType: "application/x-www-form-urlencoded;charset=UTF-8",
		data: "cliente="+cteBus,
		dataType: "json",
		success: function(data) {
			if (data.status === "correcto") {
				//alert(data.marcas);
				 $("#"+o_target).append($("<option>", {
						value: '0',
						text: "SELECCIONAR"
					}));
				    for (var i = 0; i < data.marcas.length; i++) {
					  $("#"+o_target).append($("<option>", {
						  value: data.marcas[i].cve_marca,
						  text: data.marcas[i].marca
					  }));
				    }
			}
			else {
				alert("Ha ocurrido un error al cargar las marcas del asociado: " + data.msj);
			}	
		},
		error: function(jqxhr) {
			alert("Ha ocurrido un error al cargar las marcas del asociado: " + jqxhr.responseText);
		}
	});	
}