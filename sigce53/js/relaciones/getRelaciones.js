"use strict";
$(function() {

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
function get_relaciones()
{	
    //var cteBus='0194';
	//alert(clienteActual);
	$.ajax({
		type: "POST",
		url: "../php/relaciones/get_relaciones.php",
		contentType: "application/x-www-form-urlencoded;charset=UTF-8",
		data: "cliente="+clienteActual+"&tipoConsulta='C'",
		dataType: "json",
		success: function(data) {	
			if (data.status === "OK") {				
				  if(parseInt(data.n_prod)>0){
					 $("#tblProd tbody").empty();
					 $("#tblProd tbody").append(data.prod);
					 $('#pnlVac').hide(); 
					 $('#pnlProd').show();
				  }else{
					 $('#pnlProd').hide();				  
				  }
				  if(parseInt(data.n_env)>0){
					 $("#tblEnv tbody").empty();
					 $("#tblEnv tbody").append(data.env); 					 
					 $('#pnlVac').hide();
					 $('#pnlEnv').show();
				  }else{
					 $('#pnlEnv').hide();				  
				  }
				  if(parseInt(data.n_prod)+parseInt(data.n_prod)===0)
				  {
					   $('#pnlVac').show();
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
				alert("Ha ocurrido un error al cargar las marcas del asociado: " + data.msj);
			}	
		},
		error: function(jqxhr) {
			alert("Ha ocurrido un error: " + jqxhr.responseText);
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