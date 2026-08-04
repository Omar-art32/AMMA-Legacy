"use strict";
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