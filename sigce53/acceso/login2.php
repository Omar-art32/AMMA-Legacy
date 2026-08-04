<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIGCE</title>
  <link rel="icon" type="image/ico" href="../favicon.ico">
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>
<link rel="stylesheet" href="css/style.css?2">
<link rel="stylesheet" href="css/styles.css?4">

</head>
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

<body  style="background: url(images/fondo1.jpg);background-size: 100% 100%;background-attachment: fixed;background-position: 0 0px;height: 100%;">
	<!-- Main Content -->
	<div class="container-fluid">
		<div class="row main-content bg-success text-center">
			<div class="col-xs-12 col-md-4 text-center company__info">
				<span class="company__logo"><h2> <img src="images/sigce.png" width="98%" height="auto" style="margin-left:auto; margin-right:auto;"/></h2></span>
				<h4 class="company_title" style="color: #f7f5f5;">SIGCE</h4>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-8 login_form ">
				<div class="container-fluid">
					<div class="row">
						<h2>Iniciar Sesión</h2>
					</div>
					<div class="row">
						<form control="" class="form-group" id="formLogin">
							<div class="row">
								<input type="text" name="username" id="user" autocomplete="off" class="form__input" placeholder="Usuario">
							</div>
							<div class="row">
								<!-- <span class="fa fa-lock"></span> -->
								<input type="password" name="password" id="pswd" autocomplete="new-password" class="form__input" placeholder="Contraseña">
							</div>
							<!--div class="row">
								<input type="checkbox" name="remember_me" id="remember_me" class="">
								<label for="remember_me">Remember Me!</label>
							</div-->
							<div class="row">
								<input type="submit" value="Ingresar" class="btn">
							</div>
							<div class="form-group log-status">
								<span class="alert" id="alerta">Datos incorrectos</span>
							</div>
						</form>
					</div>
					<div class="row">
						<p>Si tienes alguna duda favor de comunicarse con el administrador</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Footer -->
	<script src='../js/jquery.min.js'></script>
    <script src="js/e.js"></script>
    <script src="js/index.js"></script>
<!-- partial -->
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
	 $("#formLogin").submit(function(event) {
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
