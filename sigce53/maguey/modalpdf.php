
<!DOCTYPE html>
<html>
<head>
    <title></title>
     <link href="css/modal.css" rel="stylesheet">
     <script type="text/javascript" src="js/funcionmodal.js"> </script>
     <script src="js/jquery.min.js"></script>
     <script src="js/bootstrap.min.js"></script>
     <script src="js/bootbox.min.js"></script>
</head>
<!-- script de los alert -->

<!-- estilo del modal -->
<style>
    #modalid{
      width: 43% !important;
    }
  </style>
  <script type="text/javascript">
    $(document).ready(function(){
      $("#btnmodal").click(function(){
     
        if($("#noparaje").val()!=""){

          if ($("#nocliente").val()!="") {
            if ($("#nomcliente").val()!="") {
              if($("#cant").val()!=""){
                var datos = $("#formmodal").serialize();

                $.ajax({
                  async: false,
                  type: "POST",
                  url: "php/operationmodal.php",
                  data:datos,
                  success:function(response) {
                    alert(response);
                  },
                  error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                  }
                });

              }else{
                  alert ("No ha ingresado la cantidad a generar!.");
                  return false;
              }

            }else{
              alert("los datos del campo Nombre Cliente no se extrageron correctamente.");
                return false;
            }

          }else{
            alert("los datos del campo Num. Cliente no se extrageron correctamente.");
            return false;
           
          }

        }else{
          alert("los datos del campo Num. Paraje no se extrageron correctamente.");
          return false;
        }
      });
    });
  </script>
<body>
    <!-- Modal -->
<div class="modal fade " id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered " id="modalid"role="document">
    <div class="modal-content ">
      <div class="modal-header modal-header-success">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h3 class="modal-title" id="exampleModalCenterTitle">Ingreso de Constancias</h3>   
      </div>
      <div class="modal-body ">
        
        <form class="form-horizontal" id="formmodal" action="" method="POST" name="formmodal" enctype="multipart/form-data" >
        <input type="hidden" name='usr' id='usr' value='<?php echo $_SESSION[$d_s]['s_username']?>'>
        <div class=" row">

            <div class="col-md-2" style="margin-left: 10px;">
               <label for="formmodal">No. Paraje</label>
                <input type="text" readonly class="form-control" id="noparaje" name="noparaje">
            </div>
            <div class="col-md-2">
               <label for="formmodal">No. Cliente</label>
              <input type="text" readonly class="form-control" id="nocliente" name="nocliente">
            </div>
            
            <div class="col-md-2" style="margin-left: 210px;">
              <label for="formmodal"  >Cantidad</label>
              <input type="text" class="form-control" align="right" id="cant" name="cant" required="true">
            </div>
          </div>
         
              <label for="formmodal">Nombre</label>
              <input type="text"  readonly class="form-control" id="nomcliente" name="nomcliente" aria-describedby="emailHelp"> 
        </div>
      <div class="modal-footer">
         <button type="submit"  name='btnmodal' id='btnmodal' class="btn btn-success"  data-dismiss="modal" onClick="" ><span class="glyphicon glyphicon-ok"></span> Guardar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove" value=""></span> Cancelar</button>
       
      </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>
