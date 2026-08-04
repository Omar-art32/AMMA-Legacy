// variable globales
id_us = 0;

$(document).ready(function(){
	select_pe();
});
function select_pe(){
	var parametros = {"action":"ajax"};
	$("#loader").fadeIn('slow');
	$.ajax({
		url:'select_pe.php',
		data: parametros,
		success: function(data){
			$(".select").html(data).fadeIn('slow');
		}
	});
}
function agregar(){
	var pers = document.getElementById("txt_pers").value;
	var log = document.getElementById("txt_logu").value;
	var passw = document.getElementById("txt_passwu").value;
	var cve = document.getElementById("txt_cveu").value;
	var datos = {"operacion":"agregar","id_ps":pers,"log_p":log,"pass_p":passw,"clav_p":cve};
     $.ajax({
		type:'POST',
		cache:false,
		url:"operar_us.php",
		data:{datos:datos},
		success:function(server){
			alert(server,$("#example").DataTable().ajax.reload(),select_pe(),limpiar_modal());
        }
    });
}
function modificare_us(id,nombre,login,password,clave,status){
	document.getElementById("txt_idus2").value = id;
	document.getElementById("txt_nomu2").value = nombre;
	document.getElementById("txt_logu2").value = login;
	document.getElementById("txt_passwu2").value = password;
	document.getElementById("txt_cveu2").value = clave;
	document.getElementById("txt_stau2").value = status;
}
function modificar_us(){
	var id_pus = document.getElementById("txt_idus2").value;
	var nom_p = document.getElementById("txt_nomu2").value;
	var log_p = document.getElementById("txt_logu2").value;
	var pass_p = document.getElementById("txt_passwu2").value;
	var clav_p = document.getElementById("txt_cveu2").value;
	var esta_p = document.getElementById("txt_stau2").value;
	var datos = {"operacion":"modificar","id_pus":id_pus,"nom_p":nom_p,"log_p":log_p,"pass_p":pass_p,"clav_p":clav_p,"esta_p":esta_p};
    $.ajax({
		type:'POST',
		cache:false,
		url:"operar_us.php",
		data:{datos:datos},
		success:function(server){
			alert(server,$("#example").DataTable().ajax.reload());
        }
    });
}
function eliminare_us(id){
	id_us = id;
}
function eliminar_us(){
	var datos = {"operacion":"eliminar","id_us":id_us};
    $.ajax({
		type:'POST',
		cache:false,
		url:"operar_us.php",
		data:{datos:datos},
		success:function(server){
			alert(server,$("#example").DataTable().ajax.reload(),select_pe());
        }
    });
}
function limpiar_modal(){
	select_pe();
	document.getElementById("txt_logu").value = "";
	document.getElementById("txt_passwu").value = "";
	document.getElementById("txt_cveu").value = "";
}