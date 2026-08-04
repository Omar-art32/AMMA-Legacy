"use strict";
function mapa_relaciones()
{
  var marca = '';
	var productor = '';
	var envasador = '';
	var comercializador = '';
	$.ajax({
		type: "POST",
		url: "../php/relaciones/get_relacionesMapa.php",
		contentType: "application/x-www-form-urlencoded;charset=UTF-8",
		data: "cliente="+clienteActual+"&tipoConsulta='C'",
		dataType: "json",
		success: function(data) {
			if (data.status === "OK") {
        $("#tabMapa").empty();
				var cir_productores = ``;
        var cir_envasadores = ``;
        var cir_comercializadores = ``;
				var panel = '';
				for (var i = 0; i < data.marcas.length; i++) {
					marca = data.marcas[i].marca;
					panel += `<div class="col-lg-12"><h4 style="font-weight: bold;  background: #5cb85c94;  padding: 10px;">Marca: ${marca}</h4><div class="panel panel-default col-lg-4"><div class="panel-heading"><h4 class="panel-title"><b>Productores</b></h4></div>`;
					panel += `<div class="panel-body" style="font-size:16px; min-height: 200px;">`;

					cir_productores += `<div class="badges">`;
					for (var j = 0; j < data.marcas[i].productores.length; j++) {
						productor = data.marcas[i].productores[j];
						if (productor.asociado!=clienteActual) {
							cir_productores +=`<div class="badge-profile"><a tabindex="0" class="badge-nores" data-toggle="popover" data-trigger="focus"  data-content="${productor.empresa}">${productor.asociado}</a></div>`;
						}else{
							cir_productores +=`<div class="badge-profile"><a tabindex="0" class="badge-res" data-toggle="popover" data-trigger="focus"  data-content="${productor.empresa}">${productor.asociado}</a></div>`;
						}
					}
					cir_productores += `</div>`;
					panel += cir_productores;
					panel += `</div></div>`;

					cir_envasadores += `<div class="badges">`;
					panel += `<div class="panel panel-default col-lg-4"><div class="panel-heading"><h4 class="panel-title"><b>Envasadores</b></h4></div>`;
					panel += `<div class="panel-body" style="font-size:16px; min-height: 200px;">`;
					for (var k = 0; k < data.marcas[i].envasadores.length; k++) {
						envasador = data.marcas[i].envasadores[k];
						if (envasador.asociado!=clienteActual) {
							cir_envasadores +=`<div class="badge-profile"><a tabindex="0" class="badge-nores" data-toggle="popover" data-trigger="focus"  data-content="${envasador.empresa}">${envasador.asociado}</a></div>`;
						}else {
							cir_envasadores +=`<div class="badge-profile"><a tabindex="0" class="badge-res" data-toggle="popover" data-trigger="focus"  data-content="${envasador.empresa}">${envasador.asociado}</a></div>`;
						}
					}
					cir_envasadores += `</div>`;
					panel += cir_envasadores;
					panel += `</div></div>`;

					cir_comercializadores += `<div class="badges">`;
					panel += `<div class="panel panel-default col-lg-4"><div class="panel-heading"><h4 class="panel-title"><b>Comercializadores</b></h4></div>`;
					panel += `<div class="panel-body" style="font-size:16px; min-height: 200px;">`;
					for (var l = 0; l < data.marcas[i].comercializadores.length; l++) {
						comercializador = data.marcas[i].comercializadores[l];
						if (comercializador.asociado!=clienteActual) {
							cir_comercializadores +=`<div class="badge-profile"><a tabindex="0" class="badge-nores" data-toggle="popover" data-trigger="focus"  data-content="${comercializador.empresa}">${comercializador.asociado}</a></div>`;
						}else{
							cir_comercializadores +=`<div class="badge-profile"><a tabindex="0" class="badge-res" data-toggle="popover" data-trigger="focus"  data-content="${comercializador.empresa}">${comercializador.asociado}</a></div>`;
						}
					}
					cir_comercializadores += `</div>`;
					panel += cir_comercializadores;
					panel += `</div></div>`;

					panel += `</div>`;
					marca ='';
					cir_productores = '';
					cir_envasadores = '';
					cir_comercializadores = '';
				}
        // var cir_productores = `<div class="badges">`;
        // var cir_envasadores = `<div class="badges">`;
        // var cir_comercializadores = `<div class="badges">`;

        // if (data.esProductor) {
        //   cir_productores +=`<div class="badge-profile"><a class="badge-res">${clienteActual}</a></div>`;
        // }
        //
        // if (data.esEnvasador) {
        //   cir_envasadores +=`<div class="badge-profile"><a class="badge-res">${clienteActual}</a></div>`;
        // }
        //
        // if (data.esComercializador) {
        //   cir_comercializadores +=`<div class="badge-profile"><a class="badge-res">${clienteActual}</a></div>`;
        // }
        //
        // if (data.prod_obj.length>0) {
        //   for (var i = 0; i < data.prod_obj.length; i++) {
        //       cir_productores +=`<div class="badge-profile"><a tabindex="0" class="badge-nores" data-toggle="popover" data-trigger="focus"  data-content="${data.prod_obj[i].empresa}">${data.prod_obj[i].asociado}</a></div>`;
        //     }
        // }
        //
        // if (data.env_obj.length>0) {
        //   for (var i = 0; i < data.env_obj.length; i++) {
        //       cir_envasadores +=`<div class="badge-profile"><a tabindex="0" class="badge-nores" data-toggle="popover" data-trigger="focus"  data-content="${data.env_obj[i].empresa}">${data.env_obj[i].asociado}</a></div>`;
        //     }
        // }

        // if (data.prod_obj.length>0) {
        //   for (var i = 0; i < data.prod_obj.length; i++) {
        //       cir_productores +=`<div class="badge-profile"><a tabindex="0" class="badge-nores" data-toggle="popover" data-trigger="focus"  data-content="${data.prod_obj[i].empresa}">${data.prod_obj[i].asociado}</a></div>`;
        //     }
        // }
        // cir_productores += `</div>`;
        // cir_envasadores += `</div>`;
        $("#tabMapa").append(panel);
        // $("#seccion_envasadores").append(cir_envasadores);
			}
			else {
				alert("Ha ocurrido un error al cargar las marcas del asociado: " + data.msj);
			}
		},
		error: function(jqxhr) {
			alert("Ha ocurrido un error: " + jqxhr.responseText);
		}
	});
  setTimeout(function() {
  $('[data-toggle="popover"]').popover();
  console.log("listo");
}, 1000);

}
