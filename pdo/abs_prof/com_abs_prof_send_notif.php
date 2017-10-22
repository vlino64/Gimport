<?php
/* ********************************************************* */
// Enviem notificació als administradors 
/* ********************************************************* */
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');

$header  = 'MIME-Version: 1.0' . "\r\n";
$header .= 'Content-type: text/html; charset=utf-8' . "\r\n";
$header .= 'From: '.getDadesCentre($db)["nom"]."<no-reply@geisoft.cat>".'' . "\r\n";

$footer  = "<br><br> ==============<br>";
$footer .= "Nota: Aquest correu s'ha enviat des d'una adreça  de correu electrònic que no accepta correus entrants.\r\n";
$footer .= "Si us plau, no respongueu aquest missatge\r\n";

$subject  = "[Geisoft] [".getDadesCentre($db)["nom"]."] Comunicació absència ";
		
$content  = "Has comunicat una absència.<br><br>";
$content .= "Dades de l’absència: <br>".$_REQUEST['data']."<br> ".getLiteralFranjaHoraria($db,$id_fh)." h <br>";
$content .= getGrup($db,$id_grup)->nom."<br><br>";
$content .= "Recorda justificar adequadament aquesta/es absència/es<br><br>";
$content .= "Salutacions,";

$rol      = "<br><br><i> Missatge rebut com a professor que ha enregistrat la absència</i><br><br>";
$to  = getProfessor($db,$idprofessors,TIPUS_email);

mail($to,$subject,$content.$rol.$footer,$header);

if (isset($rsProfessorsCarrec)) {
	//mysql_free_result($rsProfessorsCarrec);
}
/* ********************************************************* */
/* ********************************************************* */
?>