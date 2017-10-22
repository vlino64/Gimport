<?php
/*---------------------------------------------------------------
* Aplicatiu: Softpack . Paquete integrado herramientas web
* Fitxer: access-denied.php
* Autor: Jatinder(phpsense)/ Modif: Victor Lino
* Descripci�: Fitxer: Acceso denegado
* Pre condi.:
* Post cond.:
----------------------------------------------------------------*/

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo _ACCES_NO ?></title>
<LINK href="../estilos/oceanis/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<br>
<br>
<table align="center" border="0" bgcolor="#ffbf6d" width=100%>
	<tr><td align="center">
<?php echo $_SESSION['SESS_MEMBER']."<br>";?>
	<p align="center">&nbsp;</p><h4 align="center" class="err">ACC&Egrave;S DENEGAT!<br />
	<br><a href="./login-form.php">TORNA LOGIN</a><br><br>

	</td></tr>
</table>
</body>
</html>
