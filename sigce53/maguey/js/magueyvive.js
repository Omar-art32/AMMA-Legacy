  function fn_cantidad(){
    cantidad = $("#grillas tbody").find("tr").length;
    $("#span_cantidad").html(cantidad);
  };

  function fn_agregare(){
    cadena = "<tr>";
    cadena = cadena + "<td>" + $("#registros").val() + "</td>";
    cadena = cadena + "<td>" + $("#origens").val() + "</td>";
    // cadena = cadena + "<td>" + $("#sm").val() + "</td>";
    cadena = cadena + "<td>" + $("#especies").val() + "</td>";
    cadena = cadena + "<td>" + $("#plantass").val() + "</td>";
    cadena = cadena + "<td>" + $("#fechavives").val() + "</td>";
    //cadena = cadena + "<td><a class='elimina'><img src='images/delete.png' /></a></td>";
    //cadena = cadena + "<td><a class='text-danger elimina'><span class='glyphicon glyphicon-minus-sign'></span></a></td>";
    cadena = cadena + "<td><a class='text-danger elimina'><span class='glyphicon glyphicon-trash'></span></a></td>";
    $("#grillas tbody").append(cadena);
    /*
    aqui puedes enviar un conunto de tados ajax para agregar al usuairo
    $.post("agregar.php", {ide_usu: $("#valor_ide").val(), nom_usu: $("#valor_uno").val()});
    */
    Limpiare();
    fn_dar_eliminar();
    fn_cantidad();
    alert("Registro Agregado");
  };

  function Limpiare(){
    document.magueys.registros.value="";
    document.magueys.origens.value="";
    document.magueys.especies.value="";
    document.magueys.plantass.value="";
    document.magueys.fechavives.value="";
    //document.magueys.local.focus();
  }

  function fn_dar_eliminar(){
    $("a.elimina").click(function(){
        id = $(this).parents("tr").find("td").eq(0).html();
        respuesta = confirm("Desea Eliminar el Registro: " + id);
        if (respuesta){
            $(this).parents("tr").fadeOut("normal", function(){
                $(this).remove();
                alert("Registro " + id + " Eliminado")
                /*
                aqui puedes enviar un conjunto de datos por ajax
                $.post("eliminar.php", {ide_usu: id})
                */
            })
        }
    });
  };


