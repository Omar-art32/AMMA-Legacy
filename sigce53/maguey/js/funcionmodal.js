// $(document).ready(function(){
            // $(".btnmodal").click(function(){
            //     var valor1="";
            //     var valor2="";
            //     var valor3="";

            //     $(this).parents("tr").find("#first-col").each(function(){
            //         valor1 += $(this).html();
            //         $('#noparaje').val(valor1);
            //     });

            //     $(this).parents("tr").find("#second-col").each(function(){
            //         valor2 += $(this).html();
            //         $('#nocliente').val(valor2);
            //     });

            //     $(this).parents("tr").find("#third-col").each(function(){
            //         valor3 += $(this).html();
            //         $('#nomcliente').val(valor3);
            //     });


            // });

 // });
function funccion1(row){
     var n1= row.getElementsByTagName('td');
     $('#noparaje').val(n1[0].innerHTML);
     $('#nocliente').val(n1[1].innerHTML);
     $('#nomcliente').val(n1[2].innerHTML);

};


       

