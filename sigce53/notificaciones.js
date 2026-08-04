var app = new Vue({
  el: '#notificaciones',
  data: {
    todos: '',
    count:0,
    active_notify:'noti_active'
  },
  methods: {
    loadData: function () {
      var that = this;
      $.ajax({
        type: "POST",
        url: "php/solicitudes_ocuv.php",
        contentType: "application/x-www-form-urlencoded;charset=UTF-8",
        data:{
          action:'loadPermisos',
          user  :id_usuario
        },
        dataType: "json",
        success: function(data, textStatus, jqXHR) {
          if (data.status === 'correcto') {
            if (data.codigo === 0) {
              $("#divSeccionNuevo").empty();
              $("#divSeccionReporteVigencias").empty();
              $("#divConfiguracionPermisosSolicitud").empty();
              _PERMISOS_ = data.permisos;
              if (_PERMISOS_.registra === 'S') {
                $("#divSeccionNuevo").append(`<button class="btn btn-success" onclick="nuevaSolicitud()" id="btnNuevaSolicitud" type="button" style="font-size: 11px;width: 150px;font-weight: 500;">REGISTRAR SOLICITUD</button>`);
              }
              if (_PERMISOS_.reporteVigencias === 'S') {
                $("#divSeccionReporteVigencias").append(`<button class="btn btn-info" onclick="reporteVigencias()" id="btnReporteVigencias" type="button" style="font-size: 11px;width: 150px;font-weight: 500;">REPORTE DE VIGENCIAS</button>`);
              }
              if (_PERMISOS_.configuracionPermisosSolicitud === 'S') {
                $("#divConfiguracionPermisosSolicitud").append(`<button type="button" id="btnOpcionesSol" onclick="getInfoOpcion();" class="btn btn-default"><span class="glyphicon glyphicon-cog"></span></button>`);
              }

              comboActividades();
              
              putTablaNotificaciones();
              that.loadNotificaciones();

              if (_PERMISOS_.cargo === 11) {
                $("#filverificador").val(_PERMISOS_.nombre);
                $("#filverificador").disabled();
              }else{
                $("#filverificador").val('');
                $("#filverificador").enable();
              }
            }
          }
        },error: function(jqxhr, status, errorGenerado) {

        }
      });
    },
    loadNotificaciones: function () {
      var usuario = id_usuario;
      var cargo = _PERMISOS_.area;
      var that = this;
      axios.get('php/getNotificaciones.php?usuario='+usuario+'&cargo='+cargo)
      .then(function (response) {
          var res = response.data;
          that.todos = res.datos;
          that.count = res.total;
          $('#tblNotificaciones_2').bootstrapTable('refresh');
      })
      .catch(function (error) {
        console.log(error);
      });
    }
  },
  created: function() {
    jQuery.fn.extend({
      getVal: function() {return $(this.selector).val();},
      setVal: function(value){$(this.selector).val(value);},
    	isEmpty: function(){ if ($(this.selector).val()==""){ return true;}else{ return false;}},
      redBorder: function(){$(this.selector).css('border-color', 'red');},
      defaultBorder: function(){$(this.selector).css('border-color', '#ccc');},
      clear: function(){$(this.selector).val("");$(this.selector).text("");},
      enable: function(){$(this.selector).prop('disabled', false);},
      disabled: function(){$(this.selector).prop('disabled', true);},
      message: function(message){if ($(`${this.selector}_span`).length==0) {$(this.selector).after(`<span id="${this.selector.substring(1, this.selector.length)}_span" class="error-message">${message}</span>`);} $(this.selector).addClass('has-error').next('span').addClass('is-visible'); },
      hideMessage: function(){ $(this.selector).removeClass('has-error').next('span').removeClass('is-visible'); },
      getTextCmb: function(value){return $(`${this.selector} option[value='${value}']`).text();}
    });

    this.loadData();

    setInterval(function () {
      this.loadNotificaciones();
      $('#tablaSolicitudes').bootstrapTable('refresh');
    }.bind(this), 600000);
  }
})

var app2 = new Vue({
  el: '#solicitudes_por',
  data: {
    total       : 0,
    pendientes  : 0,
    no_fin      : 0,
    finalizada  : 0
  },
  methods: {
    getEstadisticas: function(e) {
      var that = this;
      var value = 0;
      value = (e)?e.target.value:0;
      axios.get('php/loadEstadistica.php?nombre='+value)
      .then(function (response) {
        var res = response.data;
        that.total = res.total;
        that.pendientes = res.pendientes;
        that.no_fin = res.no_fin;
        that.finalizada = res.finalizadas;
        $('#tablaSolicitudes').bootstrapTable('refresh');
      })
      .catch(function (error) {

      });
    },
  },
  created: function() {
    var that = this;
    that.getEstadisticas();
  }
});
