<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idprofessors = isset($_SESSION['professor']) ? $_SESSION['professor'] : 0 ;

$idgrups      = isset($_REQUEST['idgrups'])   ? $_REQUEST['idgrups']    : 0 ;
$idmateria    = isset($_REQUEST['idmateria']) ? $_REQUEST['idmateria']  : 0 ;
$grup_materia = existGrupMateria($db,$idgrups,$idmateria);

$data         = date("Y-m-d");
$dia          = date("w");
$franja       = isset($_REQUEST['idfranges_horaries']) ? $_REQUEST['idfranges_horaries'] : 0 ;
$dia_franja   = existDiesFranges($db,$dia,$franja);

$modul_reg_prof = getModulsActius($db)["mod_reg_prof"];

if ($modul_reg_prof) {
  if (! existLogProfessorData($db,$idprofessors,TIPUS_ACCIO_ENTROALCENTRE,date("Y-m-d"))) {
    if (! existLogProfessorData($db,$idprofessors,TIPUS_ACCIO_SURTODELCENTRE,date("Y-m-d"))) {
	$log = insertaLogProfessor($db,$idprofessors,TIPUS_ACCIO_ENTROALCENTRE);
    }
  }
}

if (validEntryLogProfessor($db,$idprofessors,TIPUS_ACCIO_ENTRACLASSE)) {
	  $result = insertaLogProfessor($db,$idprofessors,TIPUS_ACCIO_ENTRACLASSE);
	  //$result = insertaLogProfessor($idprofessors,TIPUS_ACCIO_ENTROALCENTRE);
	  
	  $sql    = "INSERT INTO qp_seguiment (id_dia_franja,id_grup_materia,lectiva,seguiment,data) ";
	  $sql   .= "VALUES ('$dia_franja','$grup_materia','1','','$data')";
	  $result = $db->query($sql);
}

echo json_encode(array('success'=>true));

//mysql_close();
?>