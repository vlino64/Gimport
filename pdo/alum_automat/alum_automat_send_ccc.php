<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');

/* ********************************************************* */
// Enviem els correus pertinents, segons la configuració 
/* ********************************************************* */
		$header  = 'MIME-Version: 1.0' . "\r\n";
		$header .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$header .= 'From: '.getDadesCentre($db)["nom"]."<no-reply@geisoft.cat>".'' . "\r\n";
		
                $footer  = "<br><br> ==============<br>";
                $footer .= "Nota: Aquest correu s'ha enviat des d'una adreça  de correu electrònic que no accepta correus entrants.\r\n";
                $footer .= "Si us plau, no respongueu aquest missatge\r\n";

		$subject  =	"[Geisoft] Nova CCC enregistrada per un alumne";
		
		$content  = "Alumne:          ".getAlumne($db,$idalumne,TIPUS_nom_complet)."<br>";
		$content .= "Professor:       ".getProfessor($db,$idprofessor,TIPUS_nom_complet)."<br>";
		$content .= "Data incident:   ".$data_incident."<br>";
		if ($idgrup != 0) {
			$content .= "Grup:            ".getGrup($db,$idgrup)["nom"]."<br>";
			$content .= "Materia:         ".getMateria($db,$idmateria)["nom_materia"]."<br>";
			$content .= "Espai:           ".getEspaiCentre($db,$idespais)["descripcio"]."<br><hr>";
		}
		$content .= "<b><u>Fets que s'han produ&iuml;t</u></b><br>";
		$content .= $_REQUEST['descripcio_detallada']."<br><hr>";
		
		// Email cap al professor
		$to = getProfessor($db,$idprofessor,TIPUS_email);
		mail($to,$subject,$content.$footer,$header);
		
		// Email cap a l'alumne
		$to = getAlumne($db,$idalumne,TIPUS_email);
		mail($to,$subject,$content.$footer,$header);

/* ********************************************************* */
/* ********************************************************* */
?>