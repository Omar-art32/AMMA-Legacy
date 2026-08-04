"use strict";
var listaMarcas={};
var consec_list=0;
var clienteActual="";
$(document).ready(function(e) {
    get_relaciones();
});
$(function() {
		$("#txtCteProv").typeahead({
		   source: function (query, process) {
			var datos = {
				"strBusqueda": query
			};
			datos = $.param(datos);
			$.ajax({
				type: "POST",
				url: "../php/relaciones/typeheadCte.php",
				contentType: "application/x-www-form-urlencoded;charset=UTF-8",
				data: datos,
				dataType: "json",
				success: function(data, textStatus, jqXHR) {
					//alert(JSON.stringify(data));
					if (data.status === "OK") {
						process(data.suggest);
					}
					else {
						alert("Ha ocurrido un error al cargar el catalogo de asentamientos: " + data.msj);
					}	
				},
				error: function(jqxhr, status, errorGenerado) {
					alert("Ha ocurrido un error al cargar el catalogo de asentamientos: " + jqxhr.responseText + errorGenerado);
				}
			});
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
	$('#txtObs').val('');
	
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
				case 'PE':
				{
					if(contM>0){
						registrarRelacion('PE');
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
	var cteProv=$('#txtCteProv').val();
	var obs=$("#txtObsMaq").val();
	datosMaquila="id_ses="+id_s+"&cteRec="+clienteActual+"&cteProv="+cteProv+"&obs="+obs+"&tipoR="+tipoR;
	var marcas="";
	if(tipoR==='E' || tipoR==='PE'){
		marcas=JSON.stringify(listaMarcas);
		datosMaquila+="&marcas="+marcas;
	}
	$.ajax({
		type: "POST",
		url: "../php/relaciones/addRelacion.php",
		contentType: "application/x-www-form-urlencoded;charset=UTF-8",
		data: datosMaquila,
		dataType: "json",
		success: function(data) {
			//alert(data);
			if (data.status === "OK") {
				limpiarMaquilas();
				$('#modalAddMaq').modal('hide');
				$('#pResp').html(data.msj);
				$('#modalRespuesta').modal('show');
				get_relaciones();								
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
  if($('#cboMarcaRec option:selected').val()!=='0'){  
	 var cve=$('#cboMarcaRec option:selected').val();
	 var mca=$('#cboMarcaRec option:selected').text();
	 if(!listaMarcas[cve]){
	  listaMarcas[cve]={};
	  listaMarcas[cve].cve=cve;
	  listaMarcas[cve].mca=mca;
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
  }else{
	  alert('Debes seleccionar una marca'); 	  
  }
}
function get_marcas(o_target)
{  
	$("#cboMarcaRec option").remove();	
	$.ajax({
		type: "POST",
		url: "../php/relaciones/get_marcas_cliente.php",
		contentType: "application/x-www-form-urlencoded;charset=UTF-8",
		data: "cliente="+clienteActual,
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
function confirm_elim(id_elim)
{
	 $('#btnDeleteRelacion').off('click');
	  $( "#btnDeleteRelacion" ).on( "click", function() {
          eliminarRelacion(id_elim);
	  });
    $("#modalElim").modal("show");
}
function eliminarRelacion(idRelacion)
{
	//alert(idRelacion);
	$.ajax({
		type: "POST",
		url: "../php/relaciones/delRelacion.php",
		contentType: "application/x-www-form-urlencoded;charset=UTF-8",
		data: "idElim="+idRelacion,
		dataType: "json",
		success: function(data) {	
			if (data.status === "OK") {				
				var otherPanel="";
				var panelAct="";
				$("#modalElim").modal("hide");
				var parentTag = $( '#rowRel'+idRelacion).closest('table').attr('id');
				if(parentTag==='tblProd'){
					panelAct='pnlProd';
					otherPanel='pnlEnv';
				}else{
					panelAct='pnlEnv';
					otherPanel='pnlProd';
				}
				
				var idTbody=parentTag+" tbody";
				
				$('#rowRel'+idRelacion).remove();				
				if($("#"+idTbody).children().length===0){
					$("#"+panelAct).hide("slow");
					if($("#"+otherPanel).css('display')==='none'){
					$("#pnlVac").show("slow");					
				}					
				}
			}
			else {
				alert("Ha ocurrido un error al eliminar la relacion: " + data.msj);
			}	
		},
		error: function(jqxhr,error) {
			alert("Ha ocurrido un error: " + jqxhr.responseText+error);
		}
	});
}
function get_relaciones()
{	    
	$.ajax({
		type: "POST",
		url: "../php/relaciones/get_relaciones.php",
		contentType: "application/x-www-form-urlencoded;charset=UTF-8",
		data: "cliente="+clienteActual+"&tipoConsulta=E",
		dataType: "json",
		success: function(data) {	
			if (data.status === "OK") {				
				  if(parseInt(data.n_prod)>0){
					 $("#tblProd tbody").empty();
					 $("#tblProd tbody").append(data.prod);
					 $('#pnlVac').hide("slow"); 
					 $('#pnlProd').show("slow");
				  }else{
					 $('#pnlProd').hide("slow");				  
				  }
				  if(parseInt(data.n_env)>0){
					 $("#tblEnv tbody").empty();
					 $("#tblEnv tbody").append(data.env); 					 
					 $('#pnlVac').hide("slow");
					 $('#pnlEnv').show("slow");
				  }else{
					 $('#pnlEnv').hide("slow");				  
				  }
				  if(parseInt(data.n_prod)+parseInt(data.n_env)===0)
				  {
					   $('#pnlVac').show("slow");
				  }
			}
			else {
				alert("Ha ocurrido un error al cargar las relaciones: " + data.msj);
			}	
		},
		error: function(jqxhr,error) {
			alert("Ha ocurrido un error: " + jqxhr.responseText+error);
		}
	});	
}