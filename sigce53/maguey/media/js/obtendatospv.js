$(document).ready(function() {			   
	$('#example_pr').DataTable( {
		bDeferRender: true,		
		order: [[0, 'asc']],	
		sPaginationType: "full_numbers",
		ajax: {
			url: "funcionTraePrevio.php",
        	type: "POST",
        	data: {
        		idus: clvuser
        	}
		},
		columns: [
			{ data: "id", visible: false },
			{ data: "paraje"},
			{ data: "cliente" },
			{ data: "nombre" },
			{ data: "constancias" }
		],
		oLanguage: {
            sProcessing: "Procesando...",
		    sLengthMenu: 'Mostrar <select class="form-control input-sm">'+
		        '<option value="10">10</option>'+
		        '<option value="20">20</option>'+
		        '<option value="30">30</option>'+
		        '<option value="40">40</option>'+
		        '<option value="50">50</option>'+
		        '<option value="-1">All</option>'+
		        '</select> registros',    
		    sZeroRecords:    "No se encontraron resultados",
		    sEmptyTable:     "Ningún dato disponible en esta tabla",
		    sInfo:           "Mostrando del (_START_ al _END_) de un total de _TOTAL_ registros",
		    sInfoEmpty:      "Mostrando del 0 al 0 de un total de 0 registros",
		    sInfoFiltered:   "(filtrado de un total de _MAX_ registros)",
		    sInfoPostFix:    "",
		    sSearch:         "Buscar: ",
		    sUrl:            "",
		    sInfoThousands:  ",",
		    sLoadingRecords: "Por favor espere - cargando...",
		    oPaginate: {
		        sFirst:    "Primero",
		        sLast:     "Último",
		        sNext:     "Siguiente",
		        sPrevious: "Anterior"
		    },
		    oAria: {
		        "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
		        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
		    }
        }
	});

	$('#example_vi').DataTable( {
		bDeferRender: true,			
		sPaginationType: "full_numbers",
		ajax: {
			url: "funcionTraeVivero.php",
        	type: "POST",
        	data: {
        		idus: clvuser
        	}
		},
		columns: [
			{ data: "paraje" },
			{ data: "cliente" },
			{ data: "nombre" },
			{ data: "constancias" }
		],
		oLanguage: {
            sProcessing: "Procesando...",
		    sLengthMenu: 'Mostrar <select class="form-control input-sm">'+
		        '<option value="10">10</option>'+
		        '<option value="20">20</option>'+
		        '<option value="30">30</option>'+
		        '<option value="40">40</option>'+
		        '<option value="50">50</option>'+
		        '<option value="-1">All</option>'+
		        '</select> registros',    
		    sZeroRecords:    "No se encontraron resultados",
		    sEmptyTable:     "Ningún dato disponible en esta tabla",
		    sInfo:           "Mostrando del (_START_ al _END_) de un total de _TOTAL_ registros",
		    sInfoEmpty:      "Mostrando del 0 al 0 de un total de 0 registros",
		    sInfoFiltered:   "(filtrado de un total de _MAX_ registros)",
		    sInfoPostFix:    "",
		    sSearch:         "Buscar: ",
		    sUrl:            "",
		    sInfoThousands:  ",",
		    sLoadingRecords: "Por favor espere - cargando...",
		    oPaginate: {
		        sFirst:    "Primero",
		        sLast:     "Último",
		        sNext:     "Siguiente",
		        sPrevious: "Anterior"
		    },
		    oAria: {
		        "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
		        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
		    }
        }
	});

	$('#example_ex').DataTable( {
		bDeferRender: true,			
		sPaginationType: "full_numbers",
		ajax: {
			url: "funcionTraeExtraccion.php",
        	type: "POST",
        	data: {
        		idus: clvuser
        	}
		},
		columns: [
			{ data: "paraje" },
			{ data: "cliente" },
			{ data: "nombre" },
			{ data: "constancias" },
			//{ data: "opciones" }
		],
		oLanguage: {
            sProcessing: "Procesando...",
		    sLengthMenu: 'Mostrar <select class="form-control input-sm">'+
		        '<option value="10">10</option>'+
		        '<option value="20">20</option>'+
		        '<option value="30">30</option>'+
		        '<option value="40">40</option>'+
		        '<option value="50">50</option>'+
		        '<option value="-1">All</option>'+
		        '</select> registros',    
		    sZeroRecords:    "No se encontraron resultados",
		    sEmptyTable:     "Ningún dato disponible en esta tabla",
		    sInfo:           "Mostrando del (_START_ al _END_) de un total de _TOTAL_ registros",
		    sInfoEmpty:      "Mostrando del 0 al 0 de un total de 0 registros",
		    sInfoFiltered:   "(filtrado de un total de _MAX_ registros)",
		    sInfoPostFix:    "",
		    sSearch:         "Buscar: ",
		    sUrl:            "",
		    sInfoThousands:  ",",
		    sLoadingRecords: "Por favor espere - cargando...",
		    oPaginate: {
		        sFirst:    "Primero",
		        sLast:     "Último",
		        sNext:     "Siguiente",
		        sPrevious: "Anterior"
		    },
		    oAria: {
		        "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
		        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
		    }
        }
	});

	$('#example_viex').DataTable({
		bDeferRender: true,			
		sPaginationType: "full_numbers",
		ajax: {
			url: "funcionTraeExtraccionVivero.php",
        	type: "POST",
        	data: {
        		idus: clvuser
        	}
		},
		oLanguage: {
            sProcessing: "Procesando...",
		    sLengthMenu: 'Mostrar <select class="form-control input-sm">'+
		        '<option value="10">10</option>'+
		        '<option value="20">20</option>'+
		        '<option value="30">30</option>'+
		        '<option value="40">40</option>'+
		        '<option value="50">50</option>'+
		        '<option value="-1">All</option>'+
		        '</select> registros',    
		    sZeroRecords:    "No se encontraron resultados",
		    sEmptyTable:     "Ningún dato disponible en esta tabla",
		    sInfo:           "Mostrando del (_START_ al _END_) de un total de _TOTAL_ registros",
		    sInfoEmpty:      "Mostrando del 0 al 0 de un total de 0 registros",
		    sInfoFiltered:   "(filtrado de un total de _MAX_ registros)",
		    sInfoPostFix:    "",
		    sSearch:         "Buscar: ",
		    sUrl:            "",
		    sInfoThousands:  ",",
		    sLoadingRecords: "Por favor espere - cargando...",
		    oPaginate: {
		        sFirst:    "Primero",
		        sLast:     "Último",
		        sNext:     "Siguiente",
		        sPrevious: "Anterior"
		    },
		    oAria: {
		        "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
		        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
		    }
        },
		columns: [
			{ data: "vparaje" },
			{ data: "vcliente" },
			{ data: "vnombre" },
			{ data: "vconstancias" },
		]
	});
});