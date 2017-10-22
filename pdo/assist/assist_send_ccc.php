<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$rsCCC = getCCC($db,$_REQUEST['id_ccc']);

$idalumne          	= $rsCCC['idalumne'];
$idprofessors 		= $rsCCC['idprofessor'];
$idgrups     		= $rsCCC['idgrup'];
$idmateria    		= $rsCCC['idmateria'];
$data         		= date("Y-m-d");
$hora         		= date("H:i");
$idfranges_horaries     = $rsCCC['idfranges_horaries'];
$idespais_centre        = $rsCCC['idespais'];

$id_falta    		= $rsCCC['id_falta'];
$id_motius    		= $rsCCC['id_motius'];
$descripcio_detallada   = $rsCCC['descripcio_detallada'];
$expulsio    		= $rsCCC['expulsio'];

$id_tipus_sancio       = $rsCCC['id_tipus_sancio'];
$data_inici_sancio     = $rsCCC['data_inici_sancio'];
$data_fi_sancio        = $rsCCC['data_fi_sancio'];

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
		
		$content  = "Data incid&egrave;ncia: ".date("d-m-Y")."<br>";
		$content .= "Franja hor&agrave;ria: ".getLiteralFranjaHoraria($db,$idfranges_horaries)."<br><br>";	
		$content .= "Alumne:          ".getAlumne($db,$idalumne,TIPUS_nom_complet)."<br>";
		$content .= "Professor:       ".getProfessor($db,$idprofessors,TIPUS_nom_complet)."<br>";
		$content .= "Grup:            ".getGrup($db,$idgrups)["nom"]."<br>";
		$content .= "Materia:         ".getMateria($db,$idmateria)["nom_materia"]."<br>";
		$content .= "Espai:           ".getEspaiCentre($db,$idespais_centre)["descripcio"]."<br><br>";
		$content .= "Tipus CCC:       ".getLiteralTipusCCC($db,$id_falta)["nom_falta"]."<br>";
		$content .= "Expulsi&oacute;: ".$expulsio."<br><br>";
		$content .= "<b><u>Descripci&oacute; breu</u></b><br>";
		$content .= getLiteralMotiusCCC($db,$id_motius)["nom_motiu"]."<br><br>";
		$content .= "<b><u>Descripci&oacute; detallada</u></b><br>";
		$content .= $descripcio_detallada."<br><br>";
		//$content .= nl2br($descripcio_detallada)."<br><hr>";
		//$content .= "Sanci&oacute;:    ".getLiteralMesuresCCC($id_tipus_sancio)->ccc_nom."<br><hr>";
		
                if (countLastThreeMothsCCCAlumne($db,$idalumne) > 0){
                    $content .= "<b><u>CCC als darrers tres mesos :</u></b><br><ul>";
                }
                
                $rsHistoric = getLastThreeMothsCCCAlumne($db,$idalumne);
                foreach($rsHistoric->fetchAll() as $row_h) {
                    $data = new DateTime($row_h['data']);
                    $content .= "<li>".$data->format('d-m-Y')." ".$row_h['descripcio_detallada']."</li>";
                }
                
                if (countLastThreeMothsCCCAlumne($db,$idalumne) > 0){
                    $content .= "</ul>";
                }
                
		$rsCarrecs = getCarrecsComunicacioTipusCCC($db,$id_falta);
		foreach($rsCarrecs->fetchAll() as $row_c) {
			$rsProfessorsCarrec = getProfessorsbyCargos($db,$row_c['id_carrec']);
                        foreach($rsProfessorsCarrec->fetchAll() as $row_p) {
				$rol = "<br><br><i> Missatge rebut com a </i>".getLiteralCarrec($db,$row_c['id_carrec'])["nom_carrec"];
				if ($row_c['id_carrec']==TIPUS_SUPERADMINISTRADOR || $row_c['id_carrec']==TIPUS_ADMINISTRADOR) {
					$to = getProfessor($db,$row_p['idprofessors'],TIPUS_email);
					mail($to,$subject,$content.$rol.$footer,$header);
				}
				else if (isCarrecInGrup($db,$row_p['idprofessors'],$row_c['id_carrec'],$idgrups)) {
					if ($row_p['idprofessors'] == getCarrecPrincipalGrup($db,$row_c['id_carrec'],$idgrups)) {
					    $to = getProfessor($db,$row_p['idprofessors'],TIPUS_email);
					    mail($to,$subject,$content.$rol.$footer,$header);
					}
					
				}
			}
		}
				
/* ********************************************************* */
/* ********************************************************* */
if (isset($rsCarrecs)) {
	//mysql_free_result($rsCarrecs);
}
if (isset($rsProfessorsCarrec)) {
	//mysql_free_result($rsProfessorsCarrec);
}
if (isset($rsHistoric)) {
	//mysql_free_result($rsCarrecs);
}

echo json_encode(array('success'=>true));

?>       