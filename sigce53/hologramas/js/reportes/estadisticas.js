
let __DATA_SERVICIOS = [];
let __ULTIMO_SALDO_FAVOR = {};
let __ITEM_ACTUAL    = '';
let __ITEM_MANUAL_ACTUAL= {};
let __NO_CONTROL = '';
let __PREVIEW_PDF = '';
let __EDO_CUENTAS_EMITIDOS = [];
let _PERIODOSEL = "";
let _CLIENTEPUB = "";
let _PUBLICADO = false;
let _GETDATA = [];
let _GETDATA2 = [];
let _GETDATA3 = [];
let _GETDATA4 = [];
let _CLIENTES4 = [];

$(document).ready(function(){
  serviciosBarrasA();
  fetchDataA();
  moment.locale("es-mx");
});


function fetchDataA() {
  $.ajax({
    url: "php/reportes/index.php",  // URL del archivo PHP
    method: 'GET',
    dataType: 'json',
    data: {
        action:'estadisticas1',
				tipo: 'grafico'
    },
    success: function(data) {
      _GETDATA3 = data.grafica;
      google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChartA);
    },
    error: function(error) {
      console.error('Error al obtener los datos:', error);
    }
  });
}

function drawChartA() {
  data = _GETDATA3;
  var chartDataA = [['Mes', '2022', '2023', '2024', '2025', '2026', 'Promedio']];
  Object.entries(data).forEach(function(row) {
    chartDataA.push([row[1].nMes, parseInt(row[1].m2022), parseInt(row[1].m2023), parseInt(row[1].m2024), parseInt(row[1].m2025), parseInt(row[1].m2026), parseInt(row[1].promedio)]);
  });
  //console.log(chartDataA);
  var dataTable = google.visualization.arrayToDataTable(chartDataA);

  var options = {
    title : 'Pedidos por mes',
    vAxis: {title: 'Pedidos'},
    hAxis: {title: 'Mes'},
    seriesType: 'bars',
    series: {5: {type: 'line'}},
    width: 1200,
      height: 900,
  };

  var chart = new google.visualization.ComboChart(document.getElementById('piechart_barrasA'));
  //var chart = new google.visualization.AreaChart(document.getElementById('piechart_barrasA'));
  chart.draw(dataTable, options);  
}

function monedaFormatter(value) {
  let cantidad = parseFloat(value);
  if (isNaN(cantidad)) return '-'; // Validar si el valor no es un número
  return cantidad.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
}

function serviciosBarrasA(){
  __EDO_CUENTAS_EMITIDOS = [];
  
  $('#tablaAnios').bootstrapTable('destroy');
  var tableTest = $('#tablaAnios').bootstrapTable({
    url: "php/reportes/index.php",
    queryParams:function(p){
      return {
        action:'estadisticas1',
				limit: p.limit,
				offset: p.offset,
				sort:p.sort,
				order:p.order,
        tipo:"tabla"
      };
    },
    columns: [
      {
          field: 'nMes',
          title: 'MES',
          width: '70px',
          cellStyle: function(value){return { classes: 'bold' };},

      }, {
        field: 'anio',
        title: 'AÑO',
        width: '70px',
        cellStyle: function(value){return { classes: 'bold' };},
      },{
        field: 'monto',
        title: 'MONTO',
        width: '150px',
        cellStyle: function(value){return { classes: 'bold' };},
        formatter: monedaFormatter
      }
    ],
    sortOrder:"desc",
    pageNumber: 1, // pagina q se muestra por default
    pageSize: 5,
    pageList: [5,10,15,30,45,90,210],
    sidePagination: "server",
    paginationVAlign: "bottom", //formato de botones en paginacion
    cache: false,
    pagination: true,
    rowStyle:"rowStyle",
    maintainSelected: true,
  });
}


