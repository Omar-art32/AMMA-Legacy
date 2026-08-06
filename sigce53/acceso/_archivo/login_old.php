<!DOCTYPE html>
<html >
<head>
  <link rel="icon" type="image/ico" href="../favicon.ico" />
  <meta charset="UTF-8">
  <title>SIGCE</title>
  <link rel='stylesheet prefetch' href='http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css'>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
<?php
  function get_real_ip()
  {
    if (isset($_SERVER["HTTP_CLIENT_IP"])) {
      return $_SERVER["HTTP_CLIENT_IP"];
    } elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
      return $_SERVER["HTTP_X_FORWARDED_FOR"];
    } elseif (isset($_SERVER["HTTP_X_FORWARDED"])) {
      return $_SERVER["HTTP_X_FORWARDED"];
    } elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])) {
      return $_SERVER["HTTP_FORWARDED_FOR"];
    } elseif (isset($_SERVER["HTTP_FORWARDED"])) {
      return $_SERVER["HTTP_FORWARDED"];
    } else {
      return $_SERVER["REMOTE_ADDR"];
    }
  }
  ?>
    <div class="logo">
	<?php //echo "IP: ". get_real_ip();?>
       <img src="images/sigce.png" width="98%" height="auto" style="margin-left:auto; margin-right:auto;"/>
    </div>
    <div class="login-form">
       <form role="form" action="">
         <h1>Iniciar Sesión</h1>
         <div class="form-group ">
           <input type="text" class="form-control" placeholder="Usuario " id="user">
           <i class="fa fa-user"></i>
         </div>
         <div class="form-group log-status">
           <input type="password" class="form-control" placeholder="Contraseña" id="pswd">
           <i class="fa fa-lock"></i>
         </div>
         <button type="button" name="login" id="login" class="log-btn">Ingresar</button>
         <div class="form-group log-status">
           <span class="alert" id="alerta">Datos incorrectos</span>
         </div>
       </form>
     </div>

    <script src='../js/jquery.min.js'></script>
    <script src="js/e.js"></script>
    <script src="js/index.js"></script>
    <script type="text/javascript">
    $(document).ready(function(){
	   $('#pswd').on('keyup', function (event) {
	   if(event.which === 13){
		 val_u= $('#user').val();
		 val_p= $('#pswd').val();
		 if(val_u!='' && val_p!='')
		 {
		   $( "#login" ).click();
		 }
		 else
		 {
		   $('.alert').html('Datos incorrectos');
		   $('.alert').fadeIn(500);
           setTimeout( "$('.alert').fadeOut(1500);",3000 );
		 }
	   }
	 });
	 /*function ocultar_msj()
	 {
		$("#add_err").html('');
     }*/
	  $("#login").click(function(event){
        event.preventDefault();
		username=$("#user").val();
        password=$("#pswd").val();
		passworde=CryptoJS.MD5(password);
         $.ajax({
            type: "POST",
            url: "entrar.php",
            contentType: "application/x-www-form-urlencoded;charset=UTF-8",
			data: "user="+username+"&pswd="+passworde+"&tipoCon="+document.domain+"&protocol="+location.protocol,
			datatype: 'json',
            success: function(data){
			  data=JSON.parse(data);
              if(data.status=='OK')
              {
				 var url=data.msj;
				 $(location).attr('href',url);
              }
              else
              {
				 $('.log-status').addClass('wrong-entry');
				 $('.alert').html(data.msj);
				 $('.alert').fadeIn(500);
				 setTimeout( "$('.alert').fadeOut(1500);",3000 );
				 setTimeout( "$('.log-status').removeClass('wrong-entry');$('#pswd').val('');$('#pswd').focus();",3000 );
              }
            },
            beforeSend:function()
			{
				//$("#add_err").html("Loading...")
            }
        });
		 return false;

	  });

	});
	function entrar()
	{

	}
</script>

</body>
</html>
