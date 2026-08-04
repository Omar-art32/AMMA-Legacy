    function fn_agregar(){
        cadena = "<tr>";
        cadena = cadena + "<td>" + $("#registro").val() + "</td>";
        cadena = cadena + "<td>" + $("#sc").val() + "</td>";
        cadena = cadena + "<td>" + $("#sm").val() + "</td>";
        cadena = cadena + "<td>" + $("#especie").val() + "</td>";
        cadena = cadena + "<td>" + $("#plantas").val() + "</td>";
        cadena = cadena + "<td>" + $("#edad").val() + "</td>";
        //cadena = cadena + "<td><a class='elimina'><img src='images/delete.png' /></a></td>";
        cadena = cadena + "<td><a class='text-danger elimina'><span class='glyphicon glyphicon-minus-sign'></span></a></td>";
        $("#grilla tbody").append(cadena);
        /*
        aqui puedes enviar un conunto de tados ajax para agregar al usuairo
        $.post("agregar.php", {ide_usu: $("#valor_ide").val(), nom_usu: $("#valor_uno").val()});
        */
        Limpiar();
        fn_dar_eliminar();
        fn_cantidad();
        alert("Registro Agregado");
    };

    function fn_cantidad(){
        cantidad = $("#grilla tbody").find("tr").length;
        $("#span_cantidad").html(cantidad);
    };

    function Limpiar(){
        document.maguey.registro.value="";
        document.maguey.sc.value="";
        document.maguey.sm.value="";
        document.maguey.especie.value="";
        document.maguey.plantas.value="";
        document.maguey.edad.value="";
        document.maguey.local.focus();
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

    function ArregloMaguey() {
        var myTableArray = [];
        $("table#grilla").each(function() { 
            var arrayOfThisRow = [];
            var tableData = $(this).find('td');
            if (tableData.length > 0) {
                tableData.each(function() { arrayOfThisRow.push($(this).text()); });
                myTableArray.push(arrayOfThisRow);
            }
        });
        alert(myTableArray); // alerts the entire array
    }

    function insertar(){
        datos = $("#maguey").serialize();
        datos.append("action", "inserta");
        datos.append("tMaguey", JSON.stringify(ArregloMaguey()));
        $.ajax({
            type: "POST",
            url: "../guardar.php",
            data: datos,
            contentType: false,
            processData: false,
            dataType: "json",
            success:function(server){
                alert(server);//cuando reciva la respuesta lo imprimo
            }
        });
    }