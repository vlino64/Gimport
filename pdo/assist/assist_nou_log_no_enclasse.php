<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idprofessors = isset($_SESSION['professor']) ? $_SESSION['professor'] : 0 ;

if (validEntryLogProfessor($db,$idprofessors,TIPUS_ACCIO_NOENTRACLASSE)) {
	  insertaLogProfessor($db,$idprofessors,TIPUS_ACCIO_NOENTRACLASSE);
}

echo json_encode(array('success'=>true));
?>