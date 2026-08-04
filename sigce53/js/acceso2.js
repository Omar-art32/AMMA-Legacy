(function ($) {
	$.getUrl = function (key) {
		key = key.replace(/[\[]/, '\\[');
		key = key.replace(/[\]]/, '\\]');
		var pattern = "[\\?&]" + key + "=([^&#]*)";
		var regex = new RegExp(pattern);
		var url = unescape(window.location.href);
		var results = regex.exec(url);
		if (results === null) {
			return null;
		} else {
			return results[1];
		}
	}
})(jQuery);

! function (n) {
	"use strict";
	n.fn.idle = function (e) {
		var t, i, o = {
			idle: 6e4,
			events: "mousemove keydown mousedown touchstart",
			onIdle: function () { },
			onActive: function () { },
			onHide: function () { },
			onShow: function () { },
			keepTracking: !0,
			startAtIdle: !1,
			recurIdleCall: !1
		},
			c = e.startAtIdle || !1,
			d = !e.startAtIdle || !0,
			l = n.extend({}, o, e),
			u = null;
		return n(this).on("idle:stop", {}, function () {
			n(this).off(l.events), l.keepTracking = !1, t(u, l)
		}), t = function (n, e) {
			return c && (e.onActive.call(), c = !1), clearTimeout(n), e.keepTracking ? i(e) : void 0
		}, i = function (n) {
			var e, t = n.recurIdleCall ? setInterval : setTimeout;
			return e = t(function () {
				c = !0, n.onIdle.call()
			}, n.idle)
		}, this.each(function () {
			u = i(l), n(this).on(l.events, function () {
				u = t(u, l)
			}), (l.onShow || l.onHide) && n(document).on("visibilitychange webkitvisibilitychange mozvisibilitychange msvisibilitychange", function () {
				document.hidden || document.webkitHidden || document.mozHidden || document.msHidden ? d && (d = !1, l.onHide.call()) : d || (d = !0, l.onShow.call())
			})
		})
	}
}(jQuery);


var d_s = $.getUrl("d_s");
var url = "../../acceso/cerrar.php?d_s=" + d_s;


$(document).ready(function () {


	verificarAcceso();
	registrarAcceso(clvuser, moduloAcceso, seccionAcceso);
	setInterval(verificarAcceso, 900000);


});

function verificarAcceso() {
	var datos = {
		"action": "verificarAcceso",
		"clvuser": clvuser
	};
	datos = $.param(datos);
	$.ajax({
		type: "POST",
		url: "../../php/acceso.php",
		contentType: "application/x-www-form-urlencoded;charset= UTF-8",
		//contentType: "application/json; charset=utf-8",
		data: datos,
		dataType: "json",
		success: function (data, textStatus, jqXHR) {
			if (data.status == "dentro") {

				var horas = parseInt(data.horas);
				var minutos = parseInt(data.minutos);

				if (horas == 0 && minutos < 30) {
					alert("Le quedan " + data.minutos + " minutos para su cierre de sesión");

				}

			}
			else if (data.status == "fuera") {
				location.href = url;

			}

			else {
				alert("Ha ocurrido un error al verificar el acceso: " + data.msj);
			}
		},
		error: function (jqxhr, status, errorGenerado) {
			alert("Ha ocurrido un error al verificar el acceso: " + jqxhr.errorGenerado);
		}
	});
}

function registrarAcceso(clvuser, modulo, seccion) {
	var datos = {
		"action": "registrarAcceso",
		"clvuser": clvuser,
		"modulo": modulo,
		"seccion": seccion
	};
	datos = $.param(datos);
	$.ajax({
		type: "POST",
		url: "../../php/acceso.php",
		contentType: "application/x-www-form-urlencoded;charset= UTF-8",
		//contentType: "application/json; charset=utf-8",
		data: datos,
		dataType: "json",
		success: function (data, textStatus, jqXHR) {
			if (data.status == "correcto") {


			}
			else {
				alert("Ha ocurrido un error al registrar el acceso: " + data.msj);
			}
		},
		error: function (jqxhr, status, errorGenerado) {
			alert("Ha ocurrido un error al registrar el acceso: " + jqxhr.responseText);
		}
	});
}


$(document).idle({
	onIdle: function () {
		location.href = url

	},
	idle: 2400000
})

//1000 = 1 s
//40 minutos