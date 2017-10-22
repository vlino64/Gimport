<?php
/*---------------------------------------------------------------
* Aplicatiu: Softpack . Paquete integrado herramientas web
* Fitxer: logout.php
* Autor: Jatinder(phpsense)/ Modif: Victor Lino
* Descripció: Fitxer: Sortir de la sessió
* Pre condi.:
* Post cond.:
----------------------------------------------------------------*/


include("../lang/funciones_idioma.php");
$lang = check_lang();
include_once("../$lang");
//Start session
session_start();
//Unset the variables stored in session
unset($_SESSION['SESS_MEMBER_ID']);
unset($_SESSION['SESS_FIRST_NAME']);
unset($_SESSION['SESS_LAST_NAME']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo _OUT_SESSION ?></title>
<LINK href="../estilos/oceanis/style.css" rel="stylesheet" type="text/css">
</head>
<body>
	<br><br>
<table class="login" width=60%>
	<tr><td align="center">
	<fieldset>
	<legend><?php echo _ATENCIO;?></legend>
	<p align="center">&nbsp;</p><H3 align="center" class="err"><?php echo _OUT_SESSION ?>.</H3>
<p align="center"><?php echo _CLICK_TO ?> <a href="login-form.php"><br><?php echo _LOGIN ?></a></p>
	</fieldset>
	</td></tr>
</table>

</body>

</html>
