<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
//require_once('../func/seguretat.php');

// Continguts i destinataris
$contents     = str_replace("'","\'",$_REQUEST['contingut']);
//$contents	  = treureAccentsSms($contents);
$destinataris = $_SESSION['target_email'];

$header  = 'MIME-Version: 1.0' . "\r\n";
$header .= 'Content-type: text/html; charset=utf-8' . "\r\n";
$header .= 'From: '.getDadesCentre($db)["nom"]."<no-reply@geisoft.cat>".'' . "\r\n";

$footer  = "<br><br> ==============<br>";
$footer .= "Nota: Aquest correu s'ha enviat des d'una adreça  de correu electrònic que no accepta correus entrants.\r\n";
$footer .= "Si us plau, no respongueu aquest missatge\r\n";
                
//Recorregut de l'array i preparació de dades
//Enviem el correu pels tutors de cada alumne enregistrats
foreach ( $destinataris as $idalumne ) {
    
        $subject = "[Geisoft] Missatge a la familia de ".getAlumne($db,$idalumne,TIPUS_nom_complet);
        
        $email_tutor1 = getValorTipusContacteFamilies($db,$idalumne,TIPUS_email1);
	if (trim($email_tutor1 != '')) {
	  mail($email_tutor1,$subject,$contents.$footer,$header);
	}
	
	$email_tutor2 = getValorTipusContacteFamilies($db,$idalumne,TIPUS_email2);
	if (trim($email_tutor2 != '')) {
	  mail($email_tutor2,$subject,$contents.$footer,$header);
	}

}

$error="0";

if ($error=="0") {
    echo "Correu enviat correctament.<br>";
}
else {echo "S'ha produit un error amb codi".$error;}
 
?>