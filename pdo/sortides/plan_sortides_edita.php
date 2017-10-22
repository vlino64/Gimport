<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");
		
$idprofessors = isset($_REQUEST['idprofessors']) ? $_REQUEST['idprofessors'] : 0 ;
$data_anada   = isset($_REQUEST['data_anada']) ? substr($_REQUEST['data_anada'],6,4)."-".substr($_REQUEST['data_anada'],3,2)."-".substr($_REQUEST['data_anada'],0,2) : date("Y-m-d");
$hora_anada   = isset($_REQUEST['hora_anada']) ? str_replace("'","\'",$_REQUEST['hora_anada']) : '' ;
$data_tornada = isset($_REQUEST['data_tornada']) ? substr($_REQUEST['data_tornada'],6,4)."-".substr($_REQUEST['data_tornada'],3,2)."-".substr($_REQUEST['data_tornada'],0,2) : date("Y-m-d");
$hora_tornada = isset($_REQUEST['hora_tornada']) ? str_replace("'","\'",$_REQUEST['hora_tornada']) : '' ;
$lloc         = isset($_REQUEST['lloc']) ? str_replace("'","\'",$_REQUEST['lloc']) : '' ;
$descripcio   = isset($_REQUEST['descripcio']) ? str_replace("'","\'",$_REQUEST['descripcio']) : '' ;
$tancada      = isset($_REQUEST['tancada']) ? str_replace("'","\'",$_REQUEST['tancada']) : '' ;
$afegir       = isset($_REQUEST['afegir']) ? $_REQUEST['afegir'] : 0 ;

if ($afegir == 1) {
		// Afegim sortida
		$sql  = "INSERT INTO sortides (data_inici,data_fi,hora_inici,hora_fi,lloc,descripcio,tancada) ";
		$sql .= "VALUES ('$data_anada','$data_tornada','$hora_anada','$hora_tornada','$lloc','$descripcio','$tancada')";
		$result     = $db->query($sql);
		$id_sortida = $db->lastInsertId();
		$_SESSION['sortida'] = $id_sortida;
		
		// Afegim professor responsable de la sortida
		$sql  = "INSERT INTO sortides_professor (id_sortida,id_professorat,responsable) ";
		$sql .= "VALUES ('$id_sortida','$idprofessors','S')";
		$result = $db->query($sql);		
}


/* ********************************************************* */
// Enviem notificació als administradors 
/* ********************************************************* */
$header  = 'MIME-Version: 1.0' . "\r\n";
$header .= 'Content-type: text/html; charset=utf-8' . "\r\n";
$header .= 'From: '.getDadesCentre($db)["nom"]."<".getDadesCentre($db)["email"].">".'' . "\r\n";
		
$subject  =	"[Geisoft] Nova sortida enregistrada ";
		
$content  =	"Hi ha una nova sortida enregistrada. A continuació els detalls.<br><br>";
$content .= "Professor que introdueix la sortida: ".getProfessor($db,$idprofessors,TIPUS_nom_complet)."<br>";
$content .= "Data anada: ".$_REQUEST['data_anada']." (".$hora_anada.") h <br>";
$content .= "Data tornada: ".$_REQUEST['data_tornada']." (".$hora_tornada.") h <br>";
$content .= "Lloc: ".$lloc."<br>";
						
$rsProfessorsCarrec = getProfessorsbyCargos($db,TIPUS_SUPERADMINISTRADOR);
foreach($rsProfessorsCarrec->fetchAll() as $row_p) {
	$rol = "<br><br><i> Missatge rebut com a </i>".getLiteralCarrec($db,TIPUS_SUPERADMINISTRADOR)["nom_carrec"];
	//$to  = getProfessor($db,$row_p['idprofessors'],TIPUS_nom_complet)."<".getProfessor($db,$row_p['idprofessors'],TIPUS_email).">";
	$to  = getProfessor($db,$row_p['idprofessors'],TIPUS_email);
	
	mail($to,$subject,$content.$rol,$header);
}
/* ********************************************************* */
/* ********************************************************* */
//mysql_free_result($rsProfessorsCarrec);


echo json_encode(array('success'=>true));

//mysql_close();
?>