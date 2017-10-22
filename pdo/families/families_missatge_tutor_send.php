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

		$subject  =	"[Geisoft] Nou missatge al tutor/a de ".getGrup($db,$idgrup)["nom"]." ";
			
		$content  = "Alumne:          ".getAlumne($db,$idalumne,TIPUS_nom_complet)."<br>";
		$content .= "Professor:       ".getProfessor($db,$idprofessor,TIPUS_nom_complet)."<br><hr>";
		$content .= "Tutor qui envia el missate: ".$nom_tutor."<br>";
		$content .= "Data:   ".date("d-m-Y")."<br>";
		$content .= "Hora:   ".date("H:i")."<br><hr>";
		$content .= "<b><u>Missatge</u></b><br>";
		$content .= $_REQUEST['missatge']."<br><hr>";
		
		// Enviem missatge al tutor principal si aquest existeix
		if ($idprofessor != 0) {
			$rol = "<br><br><i> Missatge rebut com a </i>".getLiteralCarrec($db,TIPUS_TUTOR)["nom_carrec"];
			$to = getProfessor($db,$idprofessor,TIPUS_email);	
			mail($to,$subject,$content.$rol.$footer,$header);
		}
		
		/*$rsProfessorsCarrec = getProfessorsbyCargos($db,TIPUS_TUTOR);
		while ($row_p = mysql_fetch_assoc($rsProfessorsCarrec)) {
				$rol = "<br><br><i> Missatge rebut com a </i>".getLiteralCarrec($db,TIPUS_TUTOR)->nom_carrec;
				if (isCarrecInGrup($row_p['idprofessors'],TIPUS_TUTOR,$idgrup)) {
					$to = getProfessor($db,$row_p['idprofessors'],TIPUS_nom_complet)."<".getProfessor($db,$row_p['idprofessors'],TIPUS_email).">";			
					mail($to,$subject,$content.$rol,$header);
				}
		}*/
		
		// Enviem missatge als administradors
		$rsProfessorsCarrec = getProfessorsbyCargos($db,TIPUS_ADMINISTRADOR);
                foreach($rsProfessorsCarrec->fetchAll() as $row_p) { 
				$rol = "<br><br><i> Missatge rebut com a </i>".getLiteralCarrec($db,TIPUS_ADMINISTRADOR)["nom_carrec"];
				$to = getProfessor($db,$row_p['idprofessors'],TIPUS_email);
				mail($to,$subject,$content.$rol.$footer,$header);
		}
   /* ********************************************************* */
   /* ********************************************************* */
   if (isset($rsProfessorsCarrec)) {
   		//mysql_free_result($rsProfessorsCarrec);
   }
?>