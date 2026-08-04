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




 	$(document).on("dblclick", "#tblProd tr:has(td)", function(e) {
     var id = $(this).attr('id');
	 var str = id;
     var res = str.split("rowRel");
	 get_observacion(res[1]);
 	});
 	$(document).on("dblclick", "#tblEnv tr:has(td)", function(e) {
     var id = $(this).attr('id');
	 var str = id;
     var res = str.split("rowRel");
	 get_observacion(res[1]);
 	});

	$(document).on("dblclick", "#tblProv tr:has(td)", function(e) {
     var id = $(this).attr('id');
	 var str = id;
     var res = str.split("rowRel");
	 get_observacion(res[1]);
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
	$('#txtObsMaq').val('');
	$('#dvVig').hide();
    $('#dvVigFec').hide();
	$('#tipo_vigencia_1').prop('checked', false);
	$('#tipo_vigencia_2').prop('checked', false);
	$('#txtVigIni').val(''); 
	$('#txtVigFin').val(''); 
	
	
}
function validaDatos()
{
	$('#formMaquila').valid();
	var contM=Object.keys(listaMarcas).length;
	var tipo_vigencia=$('input:radio[name=tipo_vigencia]:checked').val();
	if($('#formMaquila').valid())
	{
		if($('#cboTipoRel option:selected').val()!=='0'){
			switch($('#cboTipoRel option:selected').val()){
				case 'P':
				{

						if($('input:radio[name=tipo_vigencia]:checked').val()==1 || $('input:radio[name=tipo_vigencia]:checked').val()==2) {
						if(tipo_vigencia==1 && ($('#txtVigIni').val()=="" || $('#txtVigFin').val()=="")){
					    	alert('Para este tipo de vigencia debe seleccionar una fecha de inicio y una de fin');

						}

						else if(tipo_vigencia==2 && $('#txtVigIni').val()==""){		
							alert('Para este tipo de vigencia debe seleccionar una fecha de inicio');
						}

						else{
							registrarRelacion('P');	
						}

					}

					else{
						alert('Debe selecciona un tipo de vigencia');
					}

					
					break;
				}
				case 'E':
				{
					if(contM>0){

					if($('input:radio[name=tipo_vigencia]:checked').val()==1 || $('input:radio[name=tipo_vigencia]:checked').val()==2) {
						if(tipo_vigencia==1 && ($('#txtVigIni').val()=="" || $('#txtVigFin').val()=="")){
					    	alert('Para este tipo de vigencia debe seleccionar una fecha de inicio y una de fin');

						}

						else if(tipo_vigencia==2 && $('#txtVigIni').val()==""){		
							alert('Para este tipo de vigencia debe seleccionar una fecha de inicio');
						}

						else{
							registrarRelacion('E');		
						}

					}

					else{
						alert('Debe selecciona un tipo de vigencia');
					}



						
					}else{
						alert('Para este tipo de relacion debe agregar al menos una marca');
					}

					break;
				}
				case 'PE':
				{
					if(contM>0){

					if($('input:radio[name=tipo_vigencia]:checked').val()==1 || $('input:radio[name=tipo_vigencia]:checked').val()==2) {
						if(tipo_vigencia==1 && ($('#txtVigIni').val()=="" || $('#txtVigFin').val()=="")){
					    	alert('Para este tipo de vigencia debe seleccionar una fecha de inicio y una de fin');

						}

						else if(tipo_vigencia==2 && $('#txtVigIni').val()==""){		
							alert('Para este tipo de vigencia debe seleccionar una fecha de inicio');
						}

						else{
							registrarRelacion('PE');		
						}

					}

					else{
						alert('Debe selecciona un tipo de vigencia');
					}

						
					}else{
						alert('Para este tipo de relacion debe agregar al menos una marca');
					}
					break;
				}

				case 'V':
				{
					if($('input:radio[name=tipo_vigencia]:checked').val()==1 || $('input:radio[name=tipo_vigencia]:checked').val()==2) {
						if(tipo_vigencia==1 && ($('#txtVigIni').val()=="" || $('#txtVigFin').val()=="")){
					    	alert('Para este tipo de vigencia debe seleccionar una fecha de inicio y una de fin');

						}

						else if(tipo_vigencia==2 && $('#txtVigIni').val()==""){		
							alert('Para este tipo de vigencia debe seleccionar una fecha de inicio');
						}

						else{
							registrarRelacion('V');			
						}

					}

					else{
						alert('Debe selecciona un tipo de vigencia');
					}

					break;
				}
			}
		}else{
			alert('Debe seleccionar un tipo de relacion');
		}
		
	}
}

function validaDatosMod(id)
{

	var tipo_vigencia=$('input:radio[name=tipo_vigencia_mod]:checked').val();

	if($('input:radio[name=tipo_vigencia_mod]:checked').val()==1 || $('input:radio[name=tipo_vigencia_mod]:checked').val()==2) {
						if(tipo_vigencia==1 && ($('#txtVigIniMod').val()=="" || $('#txtVigFinMod').val()=="")){
					    	alert('Para este tipo de vigencia debe seleccionar una fecha de inicio y una de fin');

						}

						else if(tipo_vigencia==2 && $('#txtVigIniMod').val()==""){		
							alert('Para este tipo de vigencia debe seleccionar una fecha de inicio');
						}


							
						else{
							modificarRelacion(id);
							//console.log("modificar");		
						}	


					}

					else{
						alert('Debe selecciona un tipo de vigencia');
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

		var tipo_vigencia=$('input:radio[name=tipo_vigencia]:checked').val();
		datosMaquila+="&tipo_vigencia="+tipo_vigencia;
		datosMaquila+="&vigencia_ini="+$('#txtVigIni').val();
		datosMaquila+="&vigencia_fin="+$('#txtVigFin').val();
	
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
	        $('#dvVig').show();
			$('#dvVigFec').hide();
			$('#tipo_vigencia_1').prop('checked', false);
	        $('#tipo_vigencia_2').prop('checked', false);
			$('#txtVigIni').val(''); 
			$('#txtVigFin').val(''); 
			break;
		}
		case 'P':
		{
			$('#dvMarcasArr').hide();
			$('#dvMarcasCbo').hide();
			listaMarcas={};
	        $('#tblMarcas tbody').empty();
	        $('#dvVig').show();
			$('#dvVigFec').hide();
			$('#tipo_vigencia_1').prop('checked', false);
	        $('#tipo_vigencia_2').prop('checked', false);
			$('#txtVigIni').val(''); 
			$('#txtVigFin').val(''); 
			break;
		}
		case 'E':
		{
			get_marcas('cboMarcaRec');
			$('#dvMarcasArr').show();
			$('#dvMarcasCbo').show();
			$('#dvVig').show();
			$('#dvVigFec').hide();
			$('#tipo_vigencia_1').prop('checked', false);
	        $('#tipo_vigencia_2').prop('checked', false);
			$('#txtVigIni').val(''); 
			$('#txtVigFin').val('');  
			break;
		}
		case 'PE':
		{
			get_marcas('cboMarcaRec');
			$('#dvMarcasArr').show();
			$('#dvMarcasCbo').show();
		    $('#dvVig').show();
			$('#dvVigFec').hide();
			$('#tipo_vigencia_1').prop('checked', false);
	        $('#tipo_vigencia_2').prop('checked', false); 
			$('#txtVigIni').val(''); 
			$('#txtVigFin').val(''); 
			break;
		}

		case 'V':
		{
			$('#dvMarcasArr').hide();
			$('#dvMarcasCbo').hide();
			listaMarcas={};
	        $('#tblMarcas tbody').empty();
	        $('#dvVig').show();
			$('#dvVigFec').hide();
			$('#tipo_vigencia_1').prop('checked', false);
	        $('#tipo_vigencia_2').prop('checked', false);
			$('#txtVigIni').val(''); 
			$('#txtVigFin').val(''); 
			break;
		}

	}
}


function validaVig()
{
	//console.log($('input:radio[name=tipo_vigencia]:checked').val());
	switch($('input:radio[name=tipo_vigencia]:checked').val())
	{
		case '1':
		{
			$('#dvVigFec').show();
		    $('#labVigFecIni').show();
			$('#divVigFecIni').show();
			$('#labVigFecFin').show();
			$('#divVigFecFin').show();
			$('#txtVigIni').val(''); 
			$('#txtVigFin').val('');
			break;
		}

		case '2':
		{
			$('#dvVigFec').show();
		    $('#labVigFecIni').show();
			$('#divVigFecIni').show();
			$('#labVigFecFin').hide();
			$('#divVigFecFin').hide();
			$('#txtVigIni').val(''); 
			$('#txtVigFin').val('');
			break;
		}


	}
}

function validaVigMod()
{
	//console.log($('input:radio[name=tipo_vigencia]:checked').val());
	switch($('input:radio[name=tipo_vigencia_mod]:checked').val())
	{
		case '1':
		{
			$('#dvVigFecMod').show();
		    $('#labVigFecIniMod').show();
			$('#divVigFecIniMod').show();
			$('#labVigFecFinMod').show();
			$('#divVigFecFinMod').show();
		    $('#txtVigIniMod').val(''); 
			$('#txtVigFinMod').val('');
			break;
		}

		case '2':
		{
			$('#dvVigFecMod').show();
		    $('#labVigFecIniMod').show();
			$('#divVigFecIniMod').show();
			$('#labVigFecFinMod').hide();
			$('#divVigFecFinMod').hide();
			$('#txtVigIniMod').val(''); 
			$('#txtVigFinMod').val('');
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
				var otherPanel2="";
				var otherPanel="";
				var panelAct="";
				$("#modalElim").modal("hide");
				var parentTag = $( '#rowRel'+idRelacion).closest('table').attr('id');
				if(parentTag==='tblProd'){
					panelAct='pnlProd';
					otherPanel='pnlEnv';
					otherPanel2='pnlProv';
				}else if(parentTag==='tblEnv') {
					panelAct='pnlEnv';
					otherPanel='pnlProd';
					otherPanel2='pnlProv';
				}
				else{
					panelAct='pnlProv';
					otherPanel='pnlProd';
					otherPanel2='pnlEnv';
				}
				
				var idTbody=parentTag+" tbody";
				
				$('#rowRel'+idRelacion).remove();				
				if($("#"+idTbody).children().length===0){
					$("#"+panelAct).hide("slow");
					if($("#"+otherPanel).css('display')==='none' && $("#"+otherPanel2).css('display')==='none'){
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

				  if(parseInt(data.n_prov)>0){
					 $("#tblProv tbody").empty();
					 $("#tblProv tbody").append(data.prov);
					 $('#pnlVac').hide("slow"); 
					 $('#pnlProv').show("slow");
				  }else{
					 $('#pnlProv').hide("slow");				  
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

function get_observacion(id)
{	    
	$.ajax({
		type: "POST",
		url: "../php/relaciones/get_observacion.php",
		contentType: "application/x-www-form-urlencoded;charset=UTF-8",
		data: "id="+id,
		dataType: "json",
		success: function(data) {	
			if (data.status === "OK") {
				 $("#cliente_obs").html('No Asociado: <b>'+ data.cliente_prov + '</b>'); 	
				 $('#mdlObservacionesCliente').html(data.msj);
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


function modificar(id)
{	    
	$.ajax({
		type: "POST",
		url: "../php/relaciones/get_observacion.php",
		contentType: "application/x-www-form-urlencoded;charset=UTF-8",
		data: "id="+id,
		dataType: "json",
		success: function(data) {	
			if (data.status === "OK") {	
				 //$('#mdlObservacionesCliente').html(data.msj);
				  $('#dvVigMod').show();
			      $('#dvVigFecMod').hide();
			      $('#tipo_vigencia_1_mod').prop('checked', false);
	              $('#tipo_vigencia_2_mod').prop('checked', false);
			      $('#txtVigIniMod').val(''); 
			      $('#txtVigFinMod').val('');
			      $("#txtObsMaqMod").val('');
			      $("#cliente_prov").val('');


			      $("#cliente_prov").html('No Asociado: <b>'+ data.cliente_prov + '</b>'); 

			      
			      if(data.tipo_vig==1){

			      	$('#tipo_vigencia_1_mod').prop('checked', true);
			      	$('#dvVigFecMod').show();
		            $('#labVigFecIniMod').show();
			        $('#divVigFecIniMod').show();
			        $('#labVigFecFinMod').show();
			        $('#divVigFecFinMod').show();
			        
			        $('#txtVigIniMod').val(data.fecha_ini);
			        $('#txtVigFinMod').val(data.fecha_fin);
			        
			      }

			      else if(data.tipo_vig==2){
			      	$('#tipo_vigencia_2_mod').prop('checked', true);
			      	$('#dvVigFecMod').show();
		            $('#labVigFecIniMod').show();
			        $('#divVigFecIniMod').show();
			        $('#labVigFecFinMod').hide();
			        $('#divVigFecFinMod').hide();
			        $('#txtVigIniMod').val(data.fecha_ini);


			      }

			      $("#txtObsMaqMod").val(data.obs);
	              $('#mdlModificar').modal('show');
	              $('#mdlFooModificar').html('<a href="#" class="btn btn-success" id="btnAddRel" onClick="javascript:validaDatosMod('+id+');">Guardar</a><button type="button" id="btnCancelar" class="btn btn-danger" data-dismiss="modal">Cancelar</button>');
	              					
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

function modificarRelacion(id)
{	    

	var datosVigilancia="";
	var obs=$("#txtObsMaqMod").val();
	datosVigilancia="id="+id+"&obs="+obs;


	var tipo_vigencia=$('input:radio[name=tipo_vigencia_mod]:checked').val();
	datosVigilancia+="&tipo_vigencia="+tipo_vigencia;
	datosVigilancia+="&vigencia_ini="+$('#txtVigIniMod').val();
	datosVigilancia+="&vigencia_fin="+$('#txtVigFinMod').val();


	$.ajax({
		type: "POST",
		url: "../php/relaciones/modRelacion.php",
		contentType: "application/x-www-form-urlencoded;charset=UTF-8",
		data: datosVigilancia,
		dataType: "json",

		success: function(data) {	
			if (data.status === "OK") {	

	              $('#mdlModificar').modal('hide');
	              alert(data.msj)				
			}
			else {
				alert("Ha ocurrido un error al realizar la actualización: " + data.msj);
			}	
		},
		error: function(jqxhr,error) {
			alert("Ha ocurrido un error: " + jqxhr.responseText+error);
		}
	});	
}