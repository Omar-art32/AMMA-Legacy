<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIGCE</title>
  <link rel="icon" type="image/x-icon" href="images/favicon.ico">
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
  <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>
  <link rel="stylesheet" href="css/style.css?v=5">
  <link rel="stylesheet" href="css/styles.css?v=10">
</head>

<body style="background: url(images/fondo2.jpg);background-size: 100% 100%;background-attachment: fixed;background-position: 0 0px;height: 100%;background-color: rgba(0,0,0,0.65);">
  <!-- Main Content -->
  <div class="container-fluid">
    <div class="row main-content bg-success text-center">
      <div class="col-md-4 text-center company__info">
        <span class="company__logo"><h2><img src="images/sigce.png" width="98%" height="auto" style="margin-left:auto; margin-right:auto;"/></h2></span>
        <h4 class="company_title" style="color: #f7f5f5;">SIGCE</h4>
      </div>
      <div class="col-md-8 col-xs-12 col-sm-12 login_form">
        <div class="container-fluid">
          <div class="row">
            <h2>Iniciar Sesión</h2>
          </div>
          <div class="row">
            <form control="" class="form-group" id="formLogin">
              <div class="row">
                <input type="text" autocomplete="on" name="username" id="user" class="form__input" placeholder="Usuario">
              </div>
              <div class="row">
                <input type="password" autocomplete="new-password" name="password" id="pswd" class="form__input" placeholder="Contraseña">
              </div>
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

  <!-- Scripts -->
  <!-- RUTA CORREGIDA: jQuery se cargaba desde ../js/jquery.min.js (un nivel
       arriba de acceso/, archivo que podía no existir y tumbaba todo el login).
       Ahora viene del mismo CDN que Bootstrap: sin dependencia de ruta local. -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="js/e.js"></script>
  <script src="js/index.js"></script>

  <script type="text/javascript">
    $(document).ready(function () {

      $('#pswd').on('keyup', function (event) {
        if (event.which === 13) {
          var val_u = $('#user').val();
          var val_p = $('#pswd').val();
          if (val_u !== '' && val_p !== '') {
            $('#formLogin').submit();
          } else {
            $('.alert').html('Datos incorrectos');
            $('.alert').fadeIn(500);
            setTimeout(function () { $('.alert').fadeOut(1500); }, 3000);
          }
        }
      });

      $('#formLogin').submit(function (event) {
        event.preventDefault();
        var username = $('#user').val();
        var password = $('#pswd').val();
        var passworde = CryptoJS.MD5(password);
        $.ajax({
          type: 'POST',
          url: 'entrar.php',
          contentType: 'application/x-www-form-urlencoded;charset=UTF-8',
          data: 'user=' + username + '&pswd=' + passworde + '&tipoCon=' + document.domain + '&protocol=' + location.protocol,
          dataType: 'json',
          // La respuesta ya llega como objeto JSON porque entrar.php envía la cabecera
          // application/json. Por ello no debe utilizarse JSON.parse(data).
          success: function (data) {
            // data = JSON.parse(data);
            if (data.status === 'OK') {
              // 'msj' contiene la URL de destino.
              $(location).attr('href', data.msj);
            } else {
              // 'msj' contiene el mensaje de error.
              $('.log-status').addClass('wrong-entry');
              $('.alert').html(data.msj);
              $('.alert').fadeIn(500);
              setTimeout(function () { $('.alert').fadeOut(1500); }, 3000);
              setTimeout(function () {
                $('.log-status').removeClass('wrong-entry');
                $('#pswd').val('');
                $('#pswd').focus();
              }, 3000);
            }
          }
        });
        return false;
      });

    });
  </script>
</body>
</html>
