<?php
//Incluimos librerias del phpmailer
include("class.phpmailer.php");
include("class.smtp.php");
//variable imagenes
global $headercorreo;
global $logoSistema;
//variable de correo
global $mail;
//variable general para el dominio del servidor
global $ruta_dom;
 //session_start(); 
class enviarCorreo//creacion de la clase
{
	
	function __construct($asunto)
	{ 
		global $mail,$ruta_dom,$headercorreo,$logoSistema;
		//ruta logo sistema
		$logoSistema='../../images/logo.png';
		//ruta del dominio
		$ruta_dom='localhost/wms/';
		//configuracion Servidor
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->SMTPAuth = true;
		$mail->SMTPSecure = "ssl";
		$mail->Host = "smtp.gmail.com";
		$mail->Port = 465;
		$mail->Username = "programacion@tubosyconexiones.mx";
		$mail->Password = "sistemasx1";
		$mail->From = "programacion@tubosyconexiones.mx";
		$mail->FromName = "programacion@tubosyconexiones.mx";
    }
	function enviarCorreo($correo,$usuario,$pass)
	{
		global $mail;
		$logoSistema='../../images/logo.png';
		//ruta del dominio
		$ruta_dom='http://45.33.4.84/wms/index.php';
		$tituloCorreo = "Datos de acceso";
		$mail->Subject = $tituloCorreo;
		$tituloSistema = "";
		$abreviatura = "(WMS)";
		$esloganSistema = "";
		$textoPieEmpresa = "TUBOS Y CONEXIONES";
		$textoPieCredito = "&#169; ".date('Y')." Derechos Reservados";
		//ESTILOS DEL CORREO
		$estiloTituloSistema ="color:#2584A6; font-family:Times New Roman, Times, serif; font-size:18px;";
		$estiloEslogan = "font-family: Verdana, Geneva, sans-serif; color:#898989; font-size:11px;";
		$estiloTitulo = "color:#253B6B; font-size:19px;";
		$colorPieTexto = "color:#FFFFFF; font-size:11px; font-family:Arial, Helvetica, sans-serif;";
		$colorFondoPie = "#80C242";//0286AE
		$fondoCuadroP = "#A4A8AA";
		$fondoDocumento ="#D0D0D0";
		$fondoHoja ="#FFFFFF";
		$estiloTexto = "background-color: #FCFCFC;font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#525252;";
		$estiloDatosAcceso = "color:#14436B; font-family:Arial, Helvetica, sans-serif; font-size:15px;";
		$estiloLink = "text-decoration:none; color:#14436B; font-family:Arial, Helvetica, sans-serif; font-size:15px;";
		$anchoLogo ="172";
		$altoLogo ="96";
		$estiloFondoPanelDerecho ="background-color: #E1E4E6;padding: 25px 10px;border-bottom: 1px solid #A5CAE4;border-top-left-radius: 4px;border-top-right-radius: 4px;line-height: 1.231;";
		//MENSAJE DEL CORREO
		$cuerpo ='<div style="width:100%;" align="center"><table width="100%" border="0" cellspacing="0" cellpadding="0" ><tr>';
		$cuerpo .='<td align="center" valign="top" style="background-color:'.$fondoDocumento.';" bgcolor="'.$fondoDocumento.'"><br><br><table width="693" border="0" cellspacing="0" cellpadding="0"><tr>';
		$cuerpo .='<td  style="'.$estiloFondoPanelDerecho.'">&nbsp;</td>';
		$cuerpo .='<td align="left" valign="top" bgcolor="'.$fondoHoja.'" style="background-color:'.$fondoHoja.';">';
		$cuerpo .='<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td width="35" align="left" valign="top">&nbsp;</td><td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><br>';
		
		$cuerpo .='</tr><tr height="35" "></tr><tr>';
		$cuerpo .='<td align="left" valign="top" width="193"><a href="'.$ruta_dom.'"><img src="'.$logoSistema.'" width="'.$anchoLogo.'" height="'.$altoLogo.'" vspace="3"></a>';
		$cuerpo .='<div align="center" style="'.$estiloTituloSistema.'">'.$tituloSistema.'</div>';
		$cuerpo .='<div style="'.$estiloEslogan.'"><i>'.$esloganSistema.'</i></div></td>';
		$cuerpo .='<td height="5" width="5" style="background-color: #F9F8F8;"></td>';
		$cuerpo .='<td height="5" width="10" style ="background-color: #FCFCFC;"></td>';
	
		$cuerpo .='<td align="left" valign="top" style="'.$estiloTexto.'">';
		$cuerpo .='<div align="right" style="'.$estiloTitulo.'">'.$tituloCorreo.'</div><br><hr>';
		
		//datos de acceso
		$cuerpo .='<br><div style="'.$estiloDatosAcceso.'"><b>Usuario :</b>  '.$usuario.'</div>';
		$cuerpo .='<div style="'.$estiloDatosAcceso.'"><b>Contrase&#241;a :</b>  '.$pass.'</div><br>';
		
		/*Atentamente*/
		$cuerpo .='<br><br><center><b> Atentamente</b><br>Administraci&#243;n del <b>'.$abreviatura.'</b></h4></center><br><table width="100%" border="0" cellspacing="0" cellpadding="0"></table></td></tr><tr>';
		$cuerpo .='<td align="left" valign="top" >&nbsp;</td></tr></table></td><td width="35" align="left" valign="top">&nbsp;</td></tr></table></td></tr><tr>';
		$cuerpo .='<td align="left" valign="top" bgcolor="'.$fondoCuadroP.'" style="background-color:'.$fondoCuadroP.';">';
		$cuerpo .='<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td width="10">&nbsp;</td><td height="10" valign="middle"><b></b><br>&nbsp;';
		$cuerpo .='</td><td width="35">&nbsp;</td></tr></table></td><td align="left" valign="top" bgcolor="'.$colorFondoPie.'" style="background-color:'.$colorFondoPie.';">';
		$cuerpo .='<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>	<td width="35">&nbsp;</td>';
		$cuerpo .='<td height="50" valign="middle" style="'.$colorPieTexto.'"><b>'.$textoPieEmpresa.'</b> '.$textoPieCredito;
		$cuerpo .='</td><td width="35">&nbsp;</td></tr></table></td></tr></table><br><br></td></tr></table></div>';
		$cuerpo .='';
		$mail->MsgHTML($cuerpo);
		$mail->AddAddress($correo, "Usuario WMS");
		$mail->IsHTML(true);
	//	$envio = $mail->Send();
		$envio;
		if(!$mail->Send()) 
		{
		 $envio = $mail->ErrorInfo;
		} else 
		{
		  $envio = 'Si';
		}
		return $envio;	
	}
		
}
?>