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
                
		$subject  =	"[Geisoft] Nova CCC de tipus ".getLiteralTipusCCC($db,$id_falta)["nom_falta"]." enregistrada";
			
		$content  = "Alumne:          ".getAlumne($db,$idalumne,TIPUS_nom_complet)."<br>";
		$content .= "Professor:       ".getProfessor($db,$idprofessor,TIPUS_nom_complet)."<br>";
		if ($idunitats_classe!=0) {
			$content .= "Grup:            ".getGrup($db,$idgrup)["nom"]."<br>";
			$content .= "Materia:         ".getMateria($db,$idmateria)["nom_materia"]."<br>";
			$content .= "Espai:           ".getEspaiCentre($db,$idespais)["descripcio"]."<br><hr>";
		}

		$content .= "Data incident:   ".$data_incident."<br>";
		$content .= "Tipus CCC:       ".getLiteralTipusCCC($db,$id_falta)["nom_falta"]."<br>";
		$content .= "Expulsi&oacute;: ".$expulsio."<br><hr>";
		$content .= "<b><u>Descripci&oacute; breu</u></b><br>";
		$content .= getLiteralMotiusCCC($db,$id_motius)["nom_motiu"]."<br><br>";
		$content .= "<b><u>Descripci&oacute; detallada</u></b><br>";
		$content .= $_REQUEST['descripcio_detallada']."<br><hr>";
		//$content .= "Sanci&oacute;:    ".getLiteralMesuresCCC($id_tipus_sancio)->ccc_nom."<br><hr>";
				
		$rsCarrecs = getCarrecsComunicacioTipusCCC($db,$id_falta);
                foreach($rsCarrecs->fetchAll() as $row_c) {
			$rsProfessorsCarrec = getProfessorsbyCargos($db,$row_c['id_carrec']);
                        foreach($rsProfessorsCarrec->fetchAll() as $row_p) {
				$rol = "<br><br><i> Missatge rebut com a </i>".getLiteralCarrec($db,$row_c['id_carrec'])["nom_carrec"];
				if ($row_c['id_carrec']==TIPUS_SUPERADMINISTRADOR || $row_c['id_carrec']==TIPUS_ADMINISTRADOR) {
					$to = getProfessor($db,$row_p['idprofessors'],TIPUS_email);
					mail($to,$subject,$content.$rol.$footer,$header);
				}
				else if ($idgrup == 0) {
				}
				else if (isCarrecInGrup($db,$row_p['idprofessors'],$row_c['id_carrec'],$idgrup)) {
					if ($row_p['idprofessors'] == getCarrecPrincipalGrup($row_c['id_carrec'],$idgrup)) {
						$to = getProfessor($db,$row_p['idprofessors'],TIPUS_email);			
						mail($to,$subject,$content.$rol.$footer,$header);                                                
					}
				}
			}
		}
/* ********************************************************* */
/* ********************************************************* */
?>