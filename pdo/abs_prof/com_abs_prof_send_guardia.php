<?php
/* ********************************************************* */
// Enviem notificació als professors de guàrdia 
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

$subject  = "[Geisoft] [".getDadesCentre($db)["nom"]."] Informació de guàrdia ";

$content  = "El professor ".getProfessor($db,$idprofessors,TIPUS_nom_complet)." ";
$content .= "ha comunicat que no podrà assistir a la seva classe del dia ";
$content .= $_REQUEST['data']." a les ".getLiteralFranjaHoraria($db,$id_fh).".<br><br>";
$content .= "A aquesta hora teniu una guàrdia assignada.<br><br>";
$content .= "Salutacions,";

$rol      = "<br><br><i> Missatge rebut com a professor de gu&agrave;rdia</i><br><br>";

$dia = date('w', strtotime($data));
					
$sql_c  = "SELECT g.idprofessors ";
$sql_c .= "FROM guardies g ";
$sql_c .= "INNER JOIN dies_franges       df ON g.id_dies_franges     = df.id_dies_franges "; 
$sql_c .= "INNER JOIN franges_horaries   fh ON df.idfranges_horaries = fh.idfranges_horaries  ";
$sql_c .= "WHERE df.iddies_setmana=$dia AND fh.esbarjo<>'S' AND df.idperiode_escolar=5 AND fh.idfranges_horaries=$id_fh ";

$rsGuardies = $db->query($sql_c);

foreach($rsGuardies->fetchAll() as $row_cl) {
	$id_professor  = $row_cl['idprofessors'];
	$to = getProfessor($db,$id_professor,TIPUS_email);
	
	mail($to,$subject,$content.$rol.$footer,$header);
}

if (isset($rsGuardies)) {
	//mysql_free_result($rsGuardies);
}
/* ********************************************************* */
/* ********************************************************* */		
?>