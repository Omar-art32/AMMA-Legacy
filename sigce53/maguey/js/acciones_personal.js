// variable globales
id_pe = 0;

$(document).ready(function(){
	select_cg();
	select_cgb();
});
function select_cg(){
	var parametros = {"action":"ajax"};
	$("#loader").fadeIn('slow');
	$.ajax({
		url:'select_cg.php',
		data: parametros,
		success: function(data){
			$(".select_cg").html(data).fadeIn('slow');
		}
	});
}
function select_cgb(){
	var parametros = {"action":"ajax"};
	$("#loader").fadeIn('slow');
	$.ajax({
		url:'select_cgb.php',
		data: parametros,
		success: function(data){
			$(".select_cgb").html(data).fadeIn('slow');
		}
	});
}
function agregar_pe(){
	var txt_pe = document.getElementById("txt_p").value;
	var txt_cg = document.getElementById("txt_cg").value;
	var txt_tit = document.getElementById("txt_tit").value;
	var txt_nom = document.getElementById("txt_nom").value;
	var txt_curp = document.getElementById("txt_curp").value;
	var txt_rfc = document.getElementById("txt_rfc").value;
	var txt_cve = document.getElementById("txt_cve").value;
	var txt_domi = document.getElementById("txt_domi").value;
	var txt_tel = document.getElementById("txt_tel").value;
	var txt_email = document.getElementById("txt_email").value;
	var txt_cont = document.getElementById("txt_cont").value;
	var txt_telc = document.getElementById("txt_telc").value;
	var txt_no = document.getElementById("txt_no").value;
	var txt_sang = document.getElementById("txt_sang").value;
	var txt_aler = document.getElementById("txt_aler").value;
	var txt_sta = document.getElementById("txt_sta").value;
	var txt_acre = document.getElementById("txt_acre").value;
	var datos = {"operacion":"agregar","id_p":txt_pe,"id_c":txt_cg,"tit_p":txt_tit,"nom_p":txt_nom,"curp_p":txt_curp,"rfc_p":txt_rfc,"cve_p":txt_cve,"dom_p":txt_domi,"tel_p":txt_tel,"email_p":txt_email,"cont_p":txt_cont,"tel_c":txt_telc,"no_p":txt_no,"sang_p":txt_sang,"aler_p":txt_aler,"sta_p":txt_sta,"acre_p":txt_acre};
    $.ajax({
		type:'POST',
		cache:false,
		url:"operar_pe.php",
		data:{datos:datos},
		success:function(server){
			alert(server,$("#example_p").DataTable().ajax.reload(),limpiar_modalb());
        }
    });
}
function modificare_pe(txt_pe,txt_cgb,txt_tit,txt_nom,txt_curp,txt_rfc,txt_cve,txt_domi,txt_tel,txt_email,txt_cont,txt_telc,txt_no,txt_sang,txt_aler,txt_sta,txt_acre){	
	document.getElementById("txt_p2").value = txt_pe;
	document.getElementById("txt_cgb").value = txt_cgb;
	document.getElementById("txt_tit2").value = txt_tit;
	document.getElementById("txt_nom2").value = txt_nom;
	document.getElementById("txt_curp2").value = txt_curp;
	document.getElementById("txt_rfc2").value = txt_rfc;
	document.getElementById("txt_cve2").value = txt_cve;
	document.getElementById("txt_domi2").value = txt_domi;
	document.getElementById("txt_tel2").value = txt_tel;
	document.getElementById("txt_email2").value = txt_email;
	document.getElementById("txt_cont2").value = txt_cont;
	document.getElementById("txt_telc2").value = txt_telc;
	document.getElementById("txt_no2").value = txt_no;
	document.getElementById("txt_sang2").value = txt_sang;
	document.getElementById("txt_aler2").value = txt_aler;
	document.getElementById("txt_sta2").value = txt_sta;
	document.getElementById("txt_acre2").value = txt_acre;
}
function modificar_pe(){
	var txt_pe = document.getElementById("txt_p2").value;
	var txt_cgb = document.getElementById("txt_cgb").value;
	var txt_tit = document.getElementById("txt_tit2").value;
	var txt_nom = document.getElementById("txt_nom2").value;
	var txt_curp = document.getElementById("txt_curp2").value;
	var txt_rfc = document.getElementById("txt_rfc2").value;
	var txt_cve = document.getElementById("txt_cve2").value;
	var txt_domi = document.getElementById("txt_domi2").value;
	var txt_tel = document.getElementById("txt_tel2").value;
	var txt_email = document.getElementById("txt_email2").value;
	var txt_cont = document.getElementById("txt_cont2").value;
	var txt_telc = document.getElementById("txt_telc2").value;
	var txt_no = document.getElementById("txt_no2").value;
	var txt_sang = document.getElementById("txt_sang2").value;
	var txt_aler = document.getElementById("txt_aler2").value;
	var txt_sta = document.getElementById("txt_sta2").value;
	var txt_acre = document.getElementById("txt_acre2").value;
	var datos = {"operacion":"modificar","id_p":txt_pe,"id_c":txt_cgb,"tit_p":txt_tit,"nom_p":txt_nom,"curp_p":txt_curp,"rfc_p":txt_rfc,"cve_p":txt_cve,"dom_p":txt_domi,"tel_p":txt_tel,"email_p":txt_email,"cont_p":txt_cont,"tel_c":txt_telc,"no_p":txt_no,"sang_p":txt_sang,"aler_p":txt_aler,"sta_p":txt_sta,"acre_p":txt_acre};
    $.ajax({
		type:'POST',
		cache:false,
		url:"operar_pe.php",
		data:{datos:datos},
		success:function(server){
			alert(server,$("#example_p").DataTable().ajax.reload());
        }
    });
}
function eliminare_pe(id){
	id_pe = id;
}
function eliminar_pe(){
	var datos = {"operacion":"eliminar","id_p":id_pe};
    $.ajax({
		type:'POST',
		cache:false,
		url:"operar_pe.php",
		data:{datos:datos},
		success:function(server){
			alert(server,$("#example_p").DataTable().ajax.reload());
        }
    });
}
function limpiar_modalb(){
	document.getElementById("txt_p").value = "";
	select_cg();
	document.getElementById("txt_tit").value = "";
	document.getElementById("txt_nom").value = "";
	document.getElementById("txt_curp").value = "";
	document.getElementById("txt_rfc").value = "";
	document.getElementById("txt_cve").value = "";
	document.getElementById("txt_domi").value = "";
	document.getElementById("txt_tel").value = "";
	document.getElementById("txt_email").value = "";
	document.getElementById("txt_cont").value = "";
	document.getElementById("txt_telc").value = "";
	document.getElementById("txt_no").value = "";
	document.getElementById("txt_sang").value = "";
	document.getElementById("txt_aler").value = "";
	document.getElementById("txt_sta").value = "";
	document.getElementById("txt_acre").value = "";
}