$(document).ready(function(){


      $("#noparaje2").autocomplete({
        source: "bus_predio_ex.php",
        //minLength: 1,
        select: function(event, ui) {

        },

      change: function(event, ui) {
            if (!ui.item) {
                this.value = '';
            }
        }

      });


  $('body').on('click', '#items_en_uso_predio a', function(){

      var id= $(this).attr('id');
          id=id.split('_');
          id=id[1];


        swal({
          title: 'Advertencia',
          text: "¿Realmente desea generar Nuevamente el archivo?",
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#449d44',
          cancelButtonColor: '#DD6B55',
          cancelButtonText:"No",
          confirmButtonText: "Si",
          focusCancel: true,

        }).then(function(result) {
          // handle confirm, result is needed for modals with input
    
          $("#wrapper").LoadingOverlay("show");
          window.open('constancia/reporte_historial.php?id='+id);
          var table=$("#example_pr").DataTable();

         setInterval( function () {
         table.ajax.reload( null, false ); // user paging is not reset on reload

         $("#wrapper").LoadingOverlay("hide");
         }, 5000 );
            
        }, function(dismiss) {
          // dismiss can be "cancel" | "close" | "outside"    
        });
  
  });

    $('body').on('click', '#items_en_uso_extracciones a', function(){
    
        var id= $(this).attr('id');
            id=id.split('_');
            id=id[1];


        swal({
          title: 'Advertencia',
          text: "¿Realmente desea generar Nuevamente el archivo?",
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#449d44',
          cancelButtonColor: '#DD6B55',
          cancelButtonText:"No",
          confirmButtonText: "Si",
          focusCancel: true,

        }).then(function(result) {
          // handle confirm, result is needed for modals with input
    
            $("#wrapper").LoadingOverlay("show");
            window.open('constancia/reportesalida.php?id='+id);
            var table=$("#example_ex").DataTable();

            setInterval( function () {
            table.ajax.reload( null, false ); // user paging is not reset on reload

            $("#wrapper").LoadingOverlay("hide");
            }, 5000 );
            
        }, function(dismiss) {
          // dismiss can be "cancel" | "close" | "outside"    
        });



  });

    // FORMATO GUÍAS VIVEROS
    $('body').on('click', '#items_en_uso_extraccionesv a', function(){
    
        var id= $(this).attr('id');
            id=id.split('_');
            id=id[1];


        swal({
          title: 'Advertencia',
          text: "¿Realmente desea generar Nuevamente el archivo?",
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#449d44',
          cancelButtonColor: '#DD6B55',
          cancelButtonText:"No",
          confirmButtonText: "Si",
          focusCancel: true,

        }).then(function(result) {
          // handle confirm, result is needed for modals with input
    
            $("#wrapper").LoadingOverlay("show");
            window.open('constancia/reportesalida.php?id='+id+'&tipo=V');
            var table=$("#example_viex").DataTable();

            setInterval( function () {
            table.ajax.reload( null, false ); // user paging is not reset on reload

            $("#wrapper").LoadingOverlay("hide");
            }, 5000 );
            
        }, function(dismiss) {
          // dismiss can be "cancel" | "close" | "outside"    
        });

  });

    $('body').on('click', '#items_en_uso_vivero a', function(){
    
        var id= $(this).attr('id');
            id=id.split('_');
            id=id[1];


        swal({
          title: 'Advertencia',
          text: "¿Realmente desea generar Nuevamente el archivo?",
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#449d44',
          cancelButtonColor: '#DD6B55',
          cancelButtonText:"No",
          confirmButtonText: "Si",
          focusCancel: true,

        }).then(function(result) {
          // handle confirm, result is needed for modals with input
    
            $("#wrapper").LoadingOverlay("show");
            window.open('constancia/reporte_historial3.php?id='+id);
            var table=$("#example_vi").DataTable();

            setInterval( function () {
            table.ajax.reload( null, false ); // user paging is not reset on reload

            $("#wrapper").LoadingOverlay("hide");
            }, 5000 );
            
        }, function(dismiss) {
          // dismiss can be "cancel" | "close" | "outside"    
        });
  });


})
function constancias(id_paraje,no_cliente,nombre){
    document.getElementById("cant").value="";
    document.getElementById('noparaje').value = id_paraje;
    document.getElementById('nocliente').value = no_cliente;
    document.getElementById('nomcliente').value = nombre;
}


function historial(){
    var id_paraje = document.getElementById('noparaje').value;
    var no_cliente = document.getElementById('nocliente').value;
    var nombre = document.getElementById('nomcliente').value;
    var cantidad = document.getElementById('cant').value;

    if(cantidad!=""){
    var datos = {"id_paraje":id_paraje,"no_cliente":no_cliente,"nombre":nombre,"cantidad":cantidad};
     $.ajax({
    type:'POST',
    cache:false,
    url:"operar_historial.php",
    data:{datos:datos},
    success:function(server){
      alert(server);

            $("#exampleModalCenter").modal("hide");

        }
    });
    }

    else{
        alert("Ingrese una cantidad")
    }
}


function historial2(){
    var id_paraje = document.getElementById('noparaje2').value;
    var cantidad = document.getElementById('cant2').value;

    if(id_paraje!=""){

    if(cantidad!=""){
    var datos = {"id_paraje":id_paraje,"cantidad":cantidad};
     $.ajax({
    type:'POST',
    cache:false,
    url:"operar_historial2.php",
    data:{datos:datos},
    success:function(server){
      alert(server);
            
            $("#exampleModalCenter2").modal("hide");

            $("#wrapper").LoadingOverlay("show");

            var table=$("#example_ex").DataTable();

            setInterval( function () {
            table.ajax.reload( null, false ); // user paging is not reset on reload

            $("#wrapper").LoadingOverlay("hide");
            }, 5000 );
        }
    });
    }

    else{
        alert("Ingrese una cantidad");
    }
  }

  else{

    alert("Ingrese el numero de paraje");
  }
}
