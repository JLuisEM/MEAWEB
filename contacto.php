<?php include_once "cabecera.html"; ?>

<?php
	
	if(isset($_POST['Direccion_Email']) AND ($_POST['enviar'])) {
		session_start();
		$mensaje_error = "";
		include 'config-formulario.php';
	 	require_once('recaptchalib.php');




  		$resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

	
	
		if (!$resp->is_valid) {
    	 	$mensaje_error .= "Control Anti SPAM no es válido <br />";
  		}
	
	
		if(!isset($_POST['Nombre_Completo']) ||
			!isset($_POST['Direccion_Email']) ||
			!isset($_POST['Numero_Telefono']) ||
			!isset($_POST['Su_Mensaje'])		
			) {
			$mensaje_error .='Al Parecer tiene un problema con el Formulario <br />';		
		}
	
		
		$su_nombre = strip_tags($_POST['Nombre_Completo']);
		$_SESSION['su_nombre'] = $su_nombre;
		
		$email_de = strip_tags($_POST['Direccion_Email']);
		$_SESSION['email_de'] = $email_de;
		
		$telefono = strip_tags($_POST['Numero_Telefono']); 
		$_SESSION['telefono'] = $telefono;
		
		$su_comentario = strip_tags( $_POST['Su_Mensaje']);
		$_SESSION['su_comentario'] = $su_comentario;
		
		$email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
	  if(preg_match($email_exp,$email_de)==0) {
		$mensaje_error .= 'La dirección Email no es válida<br />';
	  }
	  if(strlen($su_nombre) < 2) {
		$mensaje_error .= 'Ingrese su Nombre y Apellido<br />';
	  }
	  if(strlen($su_comentario) < 5) {
		$mensaje_error .= 'Su Mensaje no es válido.<br />';
	  }
	  
	  if(strlen($mensaje_error) > 0) {
				echo '
					<div class="alerta"> <b>ERROR AL ENVIAR EL FORMULARIO !</b><br /><br /> '.$mensaje_error.'</div>		
				';
	   }
	  
	  // Si todo está bien, entonces enviamos el mensaje Email

	  if (strlen($mensaje_error) == 0){ 
			 
			  
			$mensaje_email = "MENSAJE DEL FORMULARIO DE CONTACTO. <br /><br />";
			
			function clean_string($string) {
			  $bad = array("content-type","bcc:","to:","cc:");
			  return str_replace($bad,"",$string);
			}
			$su_comentario= nl2br ($su_comentario);
			
			$mensaje_email .= "Nombre: ".clean_string($su_nombre)."<br />";
			$mensaje_email .= "Dirección Email: ".clean_string($email_de)."<br />";
			$mensaje_email .= "Teléfono: ".clean_string($telefono)."<br />";
			$mensaje_email .= "Mensaje: ".clean_string($su_comentario)."<br />";

		   $cabeceras = 'From:'.$email_de."\r\n".
				'Reply-To:'. $email_de. "\r\n".
				'X-Mailer: PHP/' . phpversion().
				'Return-Path:' .$email_de."\r\n".
				'MIME-Version: 1.0' . "\r\n".
				'Content-type: text/html; charset=iso-8859-1' . "\r\n";

		mail($enviar_a, $asunto, $mensaje_email, $cabeceras);
		header("Location: $pagina_confirmacion");
		echo "
		 <script>location.replace('".$pagina_confirmacion."')</script>
		";
	}
}
?>


	<form name="formulariocontacto" method="post" action="index.php" onSubmit="return validate.check(this)">

	<br>
	<table align="center" class="contactoform">
    
       
	<tr>
	 <td colspan="2">
	  
	 <div class="cabeceratitulo">Formulario de Contactos</div>
	  
	 <div class="mensajenota"><span class="estrella"> * </span>Campo Obligatorio</div>
	  
	 </td>
	</tr>
	<tr>
	 <td valign="top">
	  <label for="Nombre_Completo" class="labelcontacto">Nombre Completo<span class="estrella"> * </span></label>
	 </td>
	 <td valign="top">
	  <input name="Nombre_Completo" type="text" id="Nombre_Completo" style="width:300px; font-size:18px" value="<?php echo $_SESSION['su_nombre'] ?>" maxlength="50" >
	 </td>
	</tr>
	<tr>
	 <td valign="top">
	  <label for="Direccion_Email" class="labelcontacto">Dirección Email<span class="estrella"> * </span></label>
	 </td>
	 <td valign="top">
	  <input name="Direccion_Email" type="text" id="Direccion_Email" style="width:300px; font-size:18px" value="<?php echo $_SESSION['email_de'] ?>" maxlength="30" >
	 </td>
	</tr>
	<tr>
	 <td valign="top">
	  <label for="Numero_Telefono" class="labelcontacto">Número de Teléfono</label>
	 </td>
	 <td valign="top">
	  <input name="Numero_Telefono" type="text" id="Numero_Telefono" style="width:300px; font-size:18px" value="<?php echo $_SESSION['telefono'] ?>" maxlength="25">
	 </td>
	</tr>
	<tr>
	 <td valign="top">
	   <p>
	     <label for="Su_Mensaje" class="labelcontacto">Ingrese su mensaje<span class="estrella"> * </span></label>
      </p></td>
	 <td valign="top">
	  <textarea style="width:300px;height:160px;font-size:14px" name="Su_Mensaje" id="Su_Mensaje" maxlength="2000"><?php echo $_SESSION['su_comentario']?></textarea>
	 </td>
	</tr>
	<tr></tr>
    
    <tr>
	  <td colspan="2" style="text-align:center" ><p><span class="estrella">*</span> Por favor, introduzca los caracteres que ve en la imagen de abajo. Esto es requerido para evitar envíos automáticos. </p></td>
	  </tr>
	<tr>
	 <td colspan="2" style="text-align:center" >
  
	 <br />
	<div align="center">
     <?php
          include 'config-formulario.php';
		  require_once('recaptchalib.php');
          
          echo recaptcha_get_html($publickey);
        ?>
	</div>
	 <p><br />
	   <br />
	   <br />
	   
	   <input name="enviar" id="enviar" type="submit" value=" Enviar Formulario »" style="width:200px;height:40px;font-size:18px ">

	 <img src="spacer.gif" width="1" height="1"><img src="spacer.gif" width="1" height="1" alt="Neothek.com" longdesc="http://www.neothek.com/">
     <br />
     <br />
     <br />
     <br />
     <br />
     <br />
     <br />
	 <div class="tamanos"><a href="http://blog.neothek.com/blog-neothek/formulario-de-contacto-gratis-para-tu-sitio-web/" title="Formulario de contacto gratis">Formulario de contacto gratis</a> proporcionado por <a href="http://www.neothek.com/" title="Neothek.com">Neothek.com</a> | 
	   Servicios <a href="http://www.neothek.com/web-hosting/" title="web hosting">Web Hosting</a> </div>

     </td>
     
	</tr>
	
	</table>


    </form>
    <a href="http://www.neothek.com/web-hosting/" title="Web Hosting"></a>
    <div>
 

</div>
</body>
</html>