function fn_agregar(){
  cadena = "<tr>";
  cadena = cadena + "<td>" + $("#registro").val() + "</td>";
  cadena = cadena + "<td>" + $("#origen").val() + "</td>";
  // cadena = cadena + "<td>" + $("#sm").val() + "</td>";
  cadena = cadena + "<td>" + $("#especie").val() + "</td>";
  cadena = cadena + "<td>" + $("#plantas").val() + "</td>";
  cadena = cadena + "<td>" + $("#fechavive").val() + "</td>";
  //cadena = cadena + "<td><a class='elimina'><img src='images/delete.png' /></a></td>";
  //cadena = cadena + "<td><a class='text-danger elimina'><span class='glyphicon glyphicon-minus-sign'></span></a></td>";
  cadena = cadena + "<td><a class='text-danger elimina'><span class='glyphicon glyphicon-trash'></span></a></td>";
  $("#grilla tbody").append(cadena);
  /*
  aqui puedes enviar un conunto de tados ajax para agregar al usuairo
  $.post("agregar.php", {ide_usu: $("#valor_ide").val(), nom_usu: $("#valor_uno").val()});
  */
  document.maguey.registro.value="";
  document.maguey.origen.value="";
  document.maguey.especie.value="";
  document.maguey.plantas.value="";
  document.maguey.fechavive.value="";
  fn_dar_eliminar();
  fn_cantidad();
  alert("Registro Agregado");
};

function fn_cantidad(){
  cantidad = $("#grilla tbody").find("tr").length;
  $("#span_cantidad").html(cantidad);
};

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

function upperCase() {
  var x=document.getElementById("referencia2").value
  document.getElementById("referencia2").value=x.toUpperCase()
  var x=document.getElementById("referenciau").value
  document.getElementById("referenciau").value=x.toUpperCase()
  var x=document.getElementById("campo").value
  document.getElementById("campo").value=x.toUpperCase()
}

