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
		
$content  = "Hi ha una nova absència de professorat enregistrada. A continuació els detalls.<br><br>";
$content .= "Professor:       ".getProfessor($db,$idprofessors,TIPUS_nom_complet)."<br>";
$content .= "Data :   ".$_REQUEST['data']."<br>";
$content .= "Motiu absència:   ".str_replace("'","\'",$comentari)."<br>";
						
$rsProfessorsCarrec = getProfessorsbyCargos($db,TIPUS_SUPERADMINISTRADOR);
foreach($rsProfessorsCarrec->fetchAll() as $row_p) {    
	$rol = "<br><br><i> Missatge rebut com a </i>".getLiteralCarrec($db,TIPUS_SUPERADMINISTRADOR)["nom_carrec"]."<br><br>";
	$to = getProfessor($db,$row_p['idprofessors'],TIPUS_email);
	
	mail($to,$subject,$content.$rol.$footer,$header);
}

if (isset($rsProfessorsCarrec)) {
	//mysql_free_result($rsProfessorsCarrec);
}
/* ********************************************************* */
/* ********************************************************* */
?>