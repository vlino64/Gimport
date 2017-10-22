<?php
/*---------------------------------------------------------------
* Aplicatiu: programa d'importació de dades a gassist
* Fitxer:backup.php
* Autor: Víctor Lino
* Descripció: Crea la cópia de la base de dades prèviament a la càrrega de nova informació
* Pre condi.:
* Post cond.:
* 
----------------------------------------------------------------*/
include("../config.php");

	$filename = "bk_".$_BD_GASSIST."_".date("d-m-Y_H-i-s").".sql";
	$mime = "application/x-gzip";

	header( "Content-Type: " . $mime );
	header( 'Content-Disposition: attachment; filename="' . $filename . '"' );

	$cmd = "mysqldump -u $_USR_GASSIST --password=$_PASS_GASSIST $_BD_GASSIST";   

	passthru( $cmd );
	//return;
	//$page = "../saga/menu.php";
	//$sec="0";
	//header("Refresh: $sec; url=$page");
	
	?>
