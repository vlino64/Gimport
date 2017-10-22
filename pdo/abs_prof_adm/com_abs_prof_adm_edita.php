<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");
		
$idfranges_horaries   = isset($_REQUEST['idfranges_horaries'])  ? $_REQUEST['idfranges_horaries'] : 0 ;
$idgrups              = isset($_REQUEST['idgrups'])             ? $_REQUEST['idgrups'] : 0 ;
$idmateries           = isset($_REQUEST['idmateries'])          ? $_REQUEST['idmateries'] : 0 ;
$id_tipus_incidencia  = isset($_REQUEST['id_tipus_incidencia']) ? $_REQUEST['id_tipus_incidencia'] : 0 ;
$idprofessors 	      = isset($_REQUEST['idprofessors'])        ? $_REQUEST['idprofessors']        : 0 ;
$data                 = isset($_REQUEST['data'])                ? substr($_REQUEST['data'],6,4)."-".substr($_REQUEST['data'],3,2)."-".substr($_REQUEST['data'],0,2) : date("Y-m-d");
$comentari            = isset($_REQUEST['comentari'])           ? str_replace("'","\'",$_REQUEST['comentari']) : '' ;
$comentaris_tasca     = isset($_REQUEST['comentaris_tasca'])    ? $_REQUEST['comentaris_tasca'] : '' ;
$afegir               = isset($_REQUEST['afegir'])              ? $_REQUEST['afegir']                          : 0 ;

$pos = 0;
foreach ($idfranges_horaries as $id_fh) {
	
	$id_grup    = $idgrups[$pos];
	$id_materia = $idmateries[$pos];
	$comentari_tasca = str_replace("'","\'",$comentaris_tasca[$pos]);
	
	if ($id_fh != 0){
		//esborrar incidencia per aquell professor i dia
		$sql = "DELETE FROM incidencia_professor WHERE idprofessors='$idprofessors' AND data='$data' AND idfranges_horaries='$id_fh'";
		$result = $db->query($sql);
		
		if ($afegir == 1) {
			//insertar falta asistencia per aquell professor i dia
			$sql  = "INSERT INTO incidencia_professor (idprofessors,idgrups,id_mat_uf_pla,id_tipus_incidencia,data,comentari,comentari_tasca,idfranges_horaries) ";
			$sql .= "VALUES ('$idprofessors','$id_grup','$id_materia','$id_tipus_incidencia','$data','$comentari','$comentari_tasca','$id_fh')";
			$result = $db->query($sql);
		}
	}
	$pos++;
}

/* ********************************************************* */
// Enviem notificació als administradors 
/* ********************************************************* */
$header  = 'MIME-Version: 1.0' . "\r\n";
$header .= 'Content-type: text/html; charset=utf-8' . "\r\n";
$header .= 'From: '.getDadesCentre($db)["nom"]."<no-reply@geisoft.cat>".'' . "\r\n";
		
$subject  =	"[Geisoft] Professorat absent ";
		
$content  = "Hi ha una nova absència de professorat enregistrada. A continuació els detalls.<br><br>";
$content .= "Professor:       ".getProfessor($db,$idprofessors,TIPUS_nom_complet)."<br>";
$content .= "Data :   ".$_REQUEST['data']."<br>";
$content .= "Motiu absència:   ".$comentari."<br>";

$footer  = "<br><br> ==============<br>";
$footer .= "Nota: Aquest correu s'ha enviat des d'una adreça  de correu electrònic que no accepta correus entrants.\r\n";
$footer .= "Si us plau, no respongueu aquest missatge\r\n";

$rsProfessorsCarrec = getProfessorsbyCargos($db,TIPUS_SUPERADMINISTRADOR);
foreach($rsProfessorsCarrec->fetchAll() as $row_p) {
	$rol = "<br><br><i> Missatge rebut com a </i>".getLiteralCarrec($db,TIPUS_SUPERADMINISTRADOR)["nom_carrec"];
	//$to = getProfessor($db,$row_p['idprofessors'],TIPUS_nom_complet)."<".getProfessor($db,$row_p['idprofessors'],TIPUS_email).">";
	$to = getProfessor($db,$row_p['idprofessors'],TIPUS_email);
	
	mail($to,$subject,$content.$footer,$header);
}
/* ********************************************************* */
/* ********************************************************* */
//mysql_free_result($rsProfessorsCarrec);


echo json_encode(array('success'=>true));

//mysql_close();
?>