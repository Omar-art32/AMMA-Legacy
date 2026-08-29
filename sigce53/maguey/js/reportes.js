var CLIENTESEL = "";
var CLIENTESEL2 = "";
var CLIENTESEL3 = "";

var BUSCA1 = "";
var BUSCA2 = "";
var BUSCA3 = "";

$( document ).ready(function() {

    

    $("#noControl").autocomplete({
        source: "bus_clientes.php",
        select: function(event, ui) {
            CLIENTESEL = ui.item.value;
            $('#tablaPredios').bootstrapTable('refresh');
            $("#lblCliente").html(ui.item.abbrev);
        },
        change: function (event, ui) { 
            CLIENTESEL = (ui.item == null) ? 0: CLIENTESEL;
        }
    }).keypress(function(e) {
        if (e.keyCode === 13)
            return false;
    });
    
    $("#noControl2").autocomplete({
        source: "bus_clientes.php",
        select: function(event, ui) {
            CLIENTESEL2 = ui.item.value;
            $('#tablaPlantas').bootstrapTable('refresh');
            $("#lblCliente2").html(ui.item.abbrev);
        },
        change: function (event, ui) { 
            CLIENTESEL2 = (ui.item == null) ? 0: CLIENTESEL2;
        }
    }).keypress(function(e) {
        if (e.keyCode === 13)
            return false;
    });

    $("#noControl3").autocomplete({
        source: "bus_clientes.php",
        select: function(event, ui) {
            CLIENTESEL3 = ui.item.value;
            $("#lblCliente3").html(ui.item.abbrev);
            $('#tablaGuias').bootstrapTable('refresh');
        },
        change: function (event, ui) { 
            CLIENTESEL3 = (ui.item == null) ? 0: CLIENTESEL3;
        }
    }).keypress(function(e) {
        if (e.keyCode === 13)
            return false;
    });


    $('#frm_maguey_reporte').on('keyup change', function(e) {
        $('#tablaPredios').bootstrapTable('refresh');
    });

    $('#frm_plantas_reporte').on('keyup change', function(e) {
        $('#tablaPlantas').bootstrapTable('refresh');
    });

    $('#frm_guias_reporte').on('keyup change', function(e) {
        $('#tablaGuias').bootstrapTable('refresh');
    });
    
    loadPredios();
    loadPlantas();
    loadGuias();
  });

  

  function loadPredios(){
    var tableTest=$('#tablaPredios').bootstrapTable({
            onDblClickRow: function (row) {
            //    cargarCertificado(row.id);
            },
            url: "php/loadPredios.php",
            queryParams:function(p){
                datos={
                    limit:      p.limit,
                    offset:     p.offset,
                    sort:       p.sort,
                    order:      p.order,
                    search:     p.search,
                    clientesel: (CLIENTESEL != "") ? CLIENTESEL : 0,
                    fechaini:   ($('#bFechaIni').val() != "") ? $('#bFechaIni').val(): '',
                    fechafin:   ($('#bFechaFin').val() != "") ? $('#bFechaFin').val(): '',
                    idus:       clvuser
                };
                BUSCA1 = p.search;
                return datos;
            },
            showRefresh: true,
            search: true,
            showToggle: false, //
            showColumns: true, // menu muestra columnas
            checkboxHeader: true,
            toolbar: '#toolbar', // hace referencia al dom que tiene el toolbar
            columns: [
                {
                                field: 'fecharegistro',
                                title: 'FECHA',
                                sortable:true
                },{
                                field: 'id_paraje',
                                title: 'PREDIO',
                                sortable:true
                },{
                                field: 'paraje',
                                title: 'NOMBRE PREDIO',
                                sortable:true
                },{
                                field: 'id_cliente',
                                title: '# CLIENTE',
                                sortable:true
                },{
                                field: 'nombrecli',
                                title: 'NOMBRE CLIENTE',
                                sortable:true
                },{
                                field: 'nombrep',
                                title: 'NOMBRE PRODUCTOR',
                                sortable:true
                },{
                                field: 'rcampo',
                                title: 'REPRESENTANTE EN CAMPO',
                                sortable:true
                },{
                                field: 'superficie',
                                title: 'SUPERFICIE(ha)',
                                sortable:true
                },
                {
                                field: 'lat',
                                title: 'LATITUD',
                                sortable:true
                },
                {
                                field: 'lng',
                                title: 'LONGITUD',
                                sortable:true
                },{
                                field: 'localidad',
                                title: 'LOCALIDAD',
                                sortable:true
                },{
                                field: 'municipio',
                                title: 'MUNICIPIO',
                                sortable:true
                },{
                                field: 'nomestado',
                                title: 'ESTADO',
                                sortable:true
                },{
                                field: 'guias',
                                title: 'GUÍAS',
                                sortable:true
                },{
                                field: 'registro',
                                title: 'REGISTRO',
                                sortable:true
                },{
                                field: 'origen',
                                title: 'ORIGEN',
                                sortable:true
                },/*{
                                field: 'lng',
                                title: 'LONGITUD',
                                sortable:true
                },*/{
                                field: '',
                                title: 'COMPROBANTE',
                                sortable:false,
                                formatter: operateFormatter,
                                visible: false
                              //  events: window.operateEvents,
                },{
                                field: '',
                                title: 'ACCIÓN',
                                sortable:false,
                                visible: true,
                width: 150,
                                formatter: operateFormatter2,
                                visible: false
                }
             ],
            pagination: true ,
            sortStable: true,
            pageNumber: 1, // pagina q se muestra por default
            pageSize: 10,
            //Total de resultados q se muestran debe ser menor y igual al numero q comience pageList para q sea necesario el pagaList y se  pueda mostrar
            pageList: [10, 25, 50, 100],//
            smartDisplay: true,
            sidePagination: "server",
            // showPaginationSwitch: true,
            paginationVAlign: "bottom",//formato de botones en paginacion
            cache: false,
            rowStyle: "rowStyle",
            showColumns: true,
            maintainSelected: true,
            rowStyle: "pintaDictamenes"
        });
  }

    function rep_excel_predios() {
          var c_resumen;
          var total_rows = $('#tablaPredios').bootstrapTable('getOptions').totalRows;
          if(total_rows>0) {
              var id_ses=getUrlParameter('d_s');
              $.ajax({
                  type: "POST",
                  url: "php/r_excel_predios.php",
                  contentType: "application/x-www-form-urlencoded;charset=UTF-8",
                  data: "cliente="+CLIENTESEL+"&fecha1="+$("#bFechaIni").val()+"&fecha2="+$("#bFechaFin").val()+"&id_s="+id_ses+"&busca="+BUSCA1,
                  dataType: 'json',
                  success: function(response){
                      destroy_progress(1);
                      $('#divIcoRep1').html('<center><img src="../images/excell.png" onclick="rep_excel_predios()" alt="Generar Excel" style="cursor:pointer;" width="50px;"></center>');
                      var j_res=response;
                      if(j_res.status==='OK')
                      {
                         var dir=j_res.msj;
                         window.open(dir, '_blank');
                      }
                      else
                      {
                         $('#cbo_m').html(j_res.msj);
                      }
                  },
                  beforeSend:function()
                  {
                    $('#divIcoRep1').html('<img src="images/AjaxLoader.gif" alt="COCODRILOOOOOOOOO" />');
                    progres_b(1);
                  }
              });
            }
          else
          {
              alert('No hay registros para exportar');
          }
    }

    function rep_excel_plantas() {
        var c_resumen;
        var total_rows = $('#tablaPlantas').bootstrapTable('getOptions').totalRows;//jQuery("#jqGrid").jqGrid('getGridParam', 'records');
        //console.log(total_rows);
        if(total_rows>0) {
            var id_ses=getUrlParameter('d_s');
            $.ajax({
                type: "POST",
                url: "php/r_excel_plantas.php",
                contentType: "application/x-www-form-urlencoded;charset=UTF-8",
                data: "cliente="+CLIENTESEL2+"&fecha1="+$("#bFechaIni2").val()+"&fecha2="+$("#bFechaFin2").val()+"&id_s="+id_ses+"&busca="+BUSCA2,
                dataType: 'json',
                success: function(response){
                    destroy_progress(2);
                    $('#divIcoRep2').html('<center><img src="../images/excell.png" onclick="rep_excel_plantas()" alt="Generar Excel" style="cursor:pointer;" width="50px;"></center>');
                    var j_res=response;
                    if(j_res.status==='OK') {
                       var dir=j_res.msj;
                       window.open(dir, '_blank');
                    } else
                       $('#cbo_m').html(j_res.msj);
                },
                beforeSend:function()
                {                    
                  $('#divIcoRep2').html('<img src="images/AjaxLoader.gif" alt="COCODRILOOOOOOOOO" />');
                  progres_b(2);
                }
            });
          }
        else
        {
            alert('No hay registros para exportar');
        }
  }

    function rep_excel_guias() {
        var c_resumen;
        var total_rows = $('#tablaGuias').bootstrapTable('getOptions').totalRows;//jQuery("#jqGrid").jqGrid('getGridParam', 'records');
        //console.log(total_rows);
        if(total_rows>0) {
            var id_ses=getUrlParameter('d_s');
            $.ajax({
                type: "POST",
                url: "php/r_excel_guias.php",
                contentType: "application/x-www-form-urlencoded;charset=UTF-8",
                data: "cliente="+CLIENTESEL3+"&fecha1="+$("#bFechaIni3").val()+"&fecha2="+$("#bFechaFin3").val()+"&id_s="+id_ses+"&busca="+BUSCA3,
                dataType: 'json',
                success: function(response){
                    destroy_progress(3);
                    $('#divIcoRep3').html('<center><img src="../images/excell.png" onclick="rep_excel_guias()" alt="Generar Excel" style="cursor:pointer;" width="50px;"></center>');
                    var j_res=response;
                    if(j_res.status==='OK') {
                    var dir=j_res.msj;
                    window.open(dir, '_blank');
                    } else
                    $('#cbo_m').html(j_res.msj);
                },
                beforeSend:function()
                {                    
                $('#divIcoRep3').html('<img src="images/AjaxLoader.gif" alt="COCODRILOOOOOOOOO" />');
                progres_b(3);
                }
            });
        }
        else
        {
            alert('No hay registros para exportar');
        }
    }

    var getUrlParameter = function getUrlParameter(sParam) {
        var sPageURL = decodeURIComponent(window.location.search.substring(1)),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;
    
        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');
    
            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }
    };

    function progres_b(id)
    {
        var progressbar = $( "#progressbar"+id ),
        progressLabel = $( "#lbl_pb_"+id );
        progressLabel.text( "Generando informe..." );
        progressbar.progressbar({
        value: false,
        change: function() {
            progressLabel.text( progressbar.progressbar( "value" ) + "%" );
        },
        complete: function() {
            progressLabel.text( "Completo" );
        }
        });
    var progressbarValue = progressbar.find( ".ui-progressbar-value" );
    progressbarValue.css({
            "background": '#1189B0'
            });
    }
    function destroy_progress(id)
    {
        var progressLabel = $( "#lbl_pb_"+id );
        progressLabel.text( "" );
    $( "#progressbar"+id ).progressbar( "destroy" );
    }


    function actualizaTPlantas () {
        console.log("Holiiis");
        $('#tablaPlantas').bootstrapTable("refresh");
    }

  function loadPlantas(){
    var tableTest=$('#tablaPlantas').bootstrapTable({
            onDblClickRow: function (row) {
            //    cargarCertificado(row.id);
            },
            url: "php/loadPlantas.php",
            queryParams:function(p){
                BUSCA2 = p.search;
                return {
                    limit: p.limit,
                    offset: p.offset,
                    sort:p.sort,
                    order:p.order,
                    search: p.search,
                    clientesel: (CLIENTESEL2 != "") ? CLIENTESEL2 : 0,
                    fechaini:   ($('#bFechaIni2').val() != "") ? $('#bFechaIni2').val(): '',
                    fechafin:   ($('#bFechaFin2').val() != "") ? $('#bFechaFin2').val(): '',
                    idus:       clvuser
                };
            },
            showRefresh: true,
            search: true,
            showToggle: true, //
            showColumns: true, // menu muestra columnas
            checkboxHeader: true,
            toolbar: '#toolbar', // hace referencia al dom que tiene el toolbar
            columns: [
                {
                                field: 'fecharegistro',
                                title: 'FECHA',
                                sortable:true
                },{
                                field: 'id_paraje',
                                title: 'ID PREDIO',
                                sortable:true
                },{
                                field: 'id_plantas',
                                title: 'ID PLANTAS',
                                sortable:true
                },{
                                field: 'paraje',
                                title: 'PREDIO',
                                sortable:true
                },{
                                field: 'id_cliente',
                                title: '# CLIENTE',
                                sortable:true
                },{
                                field: 'comun',
                                title: 'COMÚN',
                                sortable:true
                },{
                                field: 'edad',
                                title: 'EDAD(AÑOS)',
                                sortable:true
                },{
                                field: 'cantidadini',
                                title: 'CANTIDAD INICIAL',
                                sortable:true
                },{
                                field: 'existenciaplantas',
                                title: 'EXISTENCIA',
                                sortable:true
                },{
                                field: 'regmaguey',
                                title: 'REGISTRO DE MAGUEY',
                                sortable:true
                },{
                                field: 'local',
                                title: 'LOCALIDAD',
                                sortable:true
                },{
                                field: 'municipio',
                                title: 'MUNICIPIO',
                                sortable:true
                },{
                                field: 'mestado',
                                title: 'ESTADO',
                                sortable:true
                },{
                                field: '',
                                title: 'ACCIÓN',
                                sortable:false,
                                visible: true,
                width: 150,
                                formatter: operateFormatter2,
                                visible: false
                }
             ],
            pagination: true ,
            sortStable: true,
            pageNumber: 1, // pagina q se muestra por default
            pageSize: 10,
            //Total de resultados q se muestran debe ser menor y igual al numero q comience pageList para q sea necesario el pagaList y se  pueda mostrar
            pageList: [10, 25, 50, 100],//
            smartDisplay: true,
            sidePagination: "server",
            // showPaginationSwitch: true,
            paginationVAlign: "bottom",//formato de botones en paginacion
            cache: false,
            rowStyle: "rowStyle",
            showColumns: true,
            maintainSelected: true,
            rowStyle: "pintaDictamenes"
        });
  }

function loadGuias(){
    var tableTest=$('#tablaGuias').bootstrapTable({
        onDblClickRow: function (row) {
        //    cargarCertificado(row.id);
        },
        url: "php/loadGuias.php",
        queryParams:function(p){
            BUSCA3 = p.search;
            return {
                limit: p.limit,
                offset: p.offset,
                sort:p.sort,
                order:p.order,
                search: p.search,
                clientesel: (CLIENTESEL3 != "") ? CLIENTESEL3 : 0,
                fechaini:   ($('#bFechaIni3').val() != "") ? $('#bFechaIni3').val(): '',
                fechafin:   ($('#bFechaFin3').val() != "") ? $('#bFechaFin3').val(): '',
                idus:       clvuser
            };
        },
        showRefresh: true,
        search: true,
        showToggle: true, //
        showColumns: true, // menu muestra columnas
        checkboxHeader: true,
        toolbar: '#toolbar', // hace referencia al dom que tiene el toolbar
        columns: [
            {
                            field: 'fecha',
                            title: 'FECHA',
                            sortable:true
            },{
                            field: 'id_extraccion',
                            title: 'GUÍA',
                            sortable:true
            },{
                            field: 'id_paraje',
                            title: '# PREDIO',
                            sortable:true
            },{
                            field: 'paraje',
                            title: 'PREDIO',
                            sortable:true
            },{
                            field: 'id_cliente',
                            title: '# CLIENTE',
                            sortable:true
            },{
                            field: 'estado',
                            title: 'ESTADO',
                            sortable:true,
                            cellStyle: function(value){
                                if(value == "DISPONIBLE")
                                    return {classes: 'success'};
                                else 
                                    return {classes: 'warning'};
                            }
            },{
                            field: 'tguia',
                            title: 'TIPO DE GUÍA',
                            sortable:true
            },{
                            field: 'no_cliente_recibe',
                            title: '# CLIENTE RECIBE',
                            sortable:true
            },{
                            field: 'tapada',
                            title: 'TAPADA',
                            sortable:true
            },{
                            field: 'extraccion',
                            title: 'EXTRACCIÓN',
                            sortable:true
            },{
                            field: 'lts_producidos',
                            title: 'LITROS PROD',
                            sortable:true
            },{
                            field: 'pe_fecha',
                            title: 'FECHA PRODUCCIÓN',
                            sortable:true
            },{
                            field: '',
                            title: 'ACCIÓN',
                            sortable:false,
                            visible: true,
            width: 150,
                            formatter: operateFormatter2,
                            visible: false
            }
            ],
        pagination: true ,
        sortStable: true,
        pageNumber: 1, // pagina q se muestra por default
        pageSize: 10,
        //Total de resultados q se muestran debe ser menor y igual al numero q comience pageList para q sea necesario el pagaList y se  pueda mostrar
        pageList: [10, 25, 50, 100],//
        smartDisplay: true,
        sidePagination: "server",
        // showPaginationSwitch: true,
        paginationVAlign: "bottom",//formato de botones en paginacion
        cache: false,
        //rowStyle: "rowStyle",
        showColumns: true,
        maintainSelected: true,
        
    });
}

  function rowStyle(row, index) {
    
    if(row.estado == "DISPONIBLE"){
        return {classes: 'success'};
        console.log(row.estado);
    } else
        return {classes: 'warning'};
    return {};
  }

  function operateFormatter(value, row, index) {
        if(row.docpro != "") {
            return [
              '<center><a class="Consulta" href="'+row.docpro+'" title="Abrir Documento" target="_blank">',
                '<span style="font-size: 1.8em; color: Green;"><i class="fa fa-file-text" aria-hidden="true"></i></span>',
                '</a></center>'
            ].join('');
        } else
            return[''].join('');

    }

    function operateFormatter2(value, row, index) {
        return [
          /*'<center><a class="editR" href="javascript:void(0)" title="Editar" onclick=editarPredio("'+row.id_paraje+'");>',
          '<span style="font-size: 2em; color: Orange;"><i class="fa fa-pencil-square" aria-hidden="true"></i></span>',
          '</a>&nbsp; &nbsp; ',*/
          '<a class="editR" href="javascript:void(0)" title="Agregar Guía de Maguey" onclick=AddGuia("'+row.id_paraje+'",'+row.guias+','+row.guiaso+');>',
          '<span style="font-size: 2em; color: Blue sky;"><i class="fas fa-clipboard-check"></i></span>',
          '</a>&nbsp; </center> '

        ].join('');
    }

    