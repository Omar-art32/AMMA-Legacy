"use strict";
function mapa_relaciones()
{
  var marca = '';
	var productor = '';
	var envasador = '';
	var comercializador = '';
  var proveedor ='';
  var marcas_env = '';
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
        var cir_proveedores = ``;
        var cir_marcas_env = ``;
				var panel = '';

        panel += `<div class="col-lg-12">
                    <div class="panel panel-default col-lg-12">
                      <div class="panel-heading">
                        <div class="row">
                          <div class="col-lg-6">
                            <h4 class="panel-title"><b>Producción</b></h4>
                         </div>
                         <div class="col-lg-6" style="background: #ccc;  text-align:  center;">
                            <img src="images/circular-orange.svg" height="15" width="15"><small> El produce a  </small>
                            <img src="images/circular-blue.svg" height="15" width="15"><small> Le producen a el  </small>
                            <img src="images/circular-green.svg" height="15" width="15"><small> El asociado</small>
                        </div>
                    </div>
                </div>`;
        panel += `<div class="panel-body" style="font-size:16px; min-height: 100px;">`;

        cir_productores += `<div class="badges">`;
        if (data.productores.length > 0) {
          for(var k = 0; k < data.productores.length; k++){
            		productor = data.productores[k];
            	if (productor.asociado!=clienteActual) {
                if(productor.tipo === 0)
            		  cir_productores +=`<div class="badge-profile"><a tabindex="0" class="badge-nores" data-toggle="popover" data-trigger="focus"  data-content="${productor.empresa}">${productor.asociado}</a></div>`;
                else
                  cir_productores +=`<div class="badge-profile"><a tabindex="0" class="badge-exres" data-toggle="popover" data-trigger="focus"  data-content="${productor.empresa}">${productor.asociado}</a></div>`;
            	}else{
            		cir_productores +=`<div class="badge-profile"><a tabindex="0" class="badge-res" data-toggle="popover" data-trigger="focus"  data-content="${productor.empresa}">${productor.asociado}</a></div>`;
            	}
          }
        }else{
            cir_productores += `<div class="alert alert-warning"><strong>Sin resultados.</strong> No se encontraron productores relacionados a este asociado</div>`;
        }
        cir_productores += `</div>`;
        panel += cir_productores;
        panel += `</div></div>`;

        /***************************************************/

        panel += `<div class="col-lg-12">
                    <div class="panel panel-default col-lg-12">
                      <div class="panel-heading">
                        <div class="row">
                          <div class="col-lg-6">
                            <h4 class="panel-title"><b>Proveedores</b></h4>
                         </div>
                         <div class="col-lg-6" style="background: #ccc;  text-align:  center;">
                            <img src="images/circular-orange.svg" height="15" width="15"><small> Proveedor de  </small>
                            <img src="images/circular-blue.svg" height="15" width="15"><small> Le proveen  </small>
                        </div>
                    </div>
                </div>`;
        panel += `<div class="panel-body" style="font-size:16px; min-height: 100px;">`;

        cir_proveedores += `<div class="badges">`;
        if (data.proveedores.length > 0) {
          for(var a = 0; a < data.proveedores.length; a++){
            		proveedor = data.proveedores[a];
            	if (proveedor.asociado!=clienteActual) {
                if(proveedor.tipo === 0)
            		  cir_proveedores +=`<div class="badge-profile"><a tabindex="0" class="badge-nores" data-toggle="popover" data-trigger="focus"  data-content="${proveedor.empresa}">${proveedor.asociado}</a></div>`;
                else
                  cir_proveedores +=`<div class="badge-profile"><a tabindex="0" class="badge-exres" data-toggle="popover" data-trigger="focus"  data-content="${proveedor.empresa}">${proveedor.asociado}</a></div>`;
            	}else{
            		cir_proveedores +=`<div class="badge-profile"><a tabindex="0" class="badge-res" data-toggle="popover" data-trigger="focus"  data-content="${proveedor.empresa}">${proveedor.asociado}</a></div>`;
            	}
          }
        }else{
          cir_proveedores += `<div class="alert alert-warning"><strong>Sin resultados.</strong> No se encontraron proveedores autorizados relacionados a este asociado</div>`;
        }
        cir_proveedores += `</div>`;
        panel += cir_proveedores;
        panel += `</div></div>`;

        /***************************************************/

        panel += `<div class="col-lg-12">
                    <div class="panel panel-default col-lg-12">
                      <div class="panel-heading">
                        <div class="row">
                          <div class="col-lg-6">
                            <h4 class="panel-title"><b>Otras marcas envasadas</b></h4>
                         </div>
                         <div class="col-lg-6">
                        </div>
                    </div>
                </div>`;
        panel += `<div class="panel-body" style="font-size:16px; min-height: 100px;">`;

        cir_marcas_env += `<div class="badges">`;
        if (data.marcasEnv.length > 0) {
          for(var b = 0; b < data.marcasEnv.length; b++){
            		marcas_env = data.marcasEnv[b];
                cir_marcas_env +=`<div class="badge-profile-marcas"><a tabindex="0" class="badge-marcas" data-toggle="popover" data-trigger="focus"  data-content="(${marcas_env.asociado}) ${marcas_env.empresa}">${marcas_env.marca}</a></div>`;
          }
        }else{
          cir_marcas_env += `<div class="alert alert-warning"><strong>Sin resultados.</strong> No se encontraron marcas envasadas por este asociado</div>`;
        }
        cir_marcas_env += `</div>`;
        panel += cir_marcas_env;
        panel += `</div></div>`;
        /***********************************************/

        // panel += `<div class="col-lg-12"><h4 style="font-weight: bold;background: #31708f;padding: 10px;color:  white; text-align:  center;">MARCAS PROPIAS</h4></div>`;
        if (data.marcas.length>0) {
          panel += `<div class="col-lg-12">
                            <div class="col-lg-6" style="color:  white;text-align:  center;background: #555;">
                              <h4 style="font-weight:bold;">MARCAS PROPIAS</h4>
                           </div>
                           <div class="col-lg-6" style="background: #ccc;text-align:  center;height: 38px;display:  flex;justify-content:  center;align-items:  center;">
                              <img src="images/circular-blue.svg" height="15" width="15"><small> Le envasan/comercializan  </small>
                              <img src="images/circular-green.svg" height="15" width="15"><small> El asociado  </small>
                          </div>
                  </div>`;
        }

				for (var i = 0; i < data.marcas.length; i++) {
					marca = data.marcas[i].marca;
					panel += `<div class="col-lg-12"><h5 style="font-weight: bold;background: #607D8B;padding: 10px;color:  white;">Marca: ${marca}</h5>`;


					cir_envasadores += `<div class="badges">`;
					panel += `<div class="panel panel-default col-lg-6"><div class="panel-heading"><h4 class="panel-title"><b>Envasadores</b></h4></div>`;
					panel += `<div class="panel-body" style="font-size:16px; min-height: 100px;">`;
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
					panel += `<div class="panel panel-default col-lg-6"><div class="panel-heading"><h4 class="panel-title"><b>Comercializadores</b></h4></div>`;
					panel += `<div class="panel-body" style="font-size:16px; min-height: 100px;">`;
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
        $("#tabMapa").append(panel);
        // $("#seccion_envasadores").append(cir_envasadores);
			}
			else {
				alert("Ha ocurrido un error al cargar las marcas del asociado: " + data.msj);
			}
		},
		error: function(err) {
			// alert("Ha ocurrido un error ->: " + jqxhr.responseText);
      console.log(err);
		}
	});
  setTimeout(function() {
  $('[data-toggle="popover"]').popover();
  console.log("listo");
}, 1000);

}
