<?php
/*---------------------------------------------------------------
* Aplicatiu: Softpack . Paquete integrado herramientas web
* Fitxer: auth.php
* Autor: Jatinder(phpsense)/ Modif: Victor Lino
* Descripció: Fitxer: Línies a situar en cada fitxer que vulguem protegir
* Pre condi.:
* Post cond.:
----------------------------------------------------------------*/
?><?php
	//Start session
	session_start();
	
	//Check whether the session variable SESS_MEMBER_ID is present or not
	if(!isset($_SESSION['SESS_MEMBER_ID']) || (trim($_SESSION['SESS_MEMBER_ID']) == '')) {
		header("location: access-denied.php");
		exit();
	}
?>
