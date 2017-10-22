<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idprofessors = intval($_REQUEST['idprofessors']);

if (! existLogProfessorData($db,$idprofessors,TIPUS_ACCIO_SURTODELCENTRE,date("Y-m-d"))) {
	insertaLogProfessor($db,$idprofessors,TIPUS_ACCIO_SURTODELCENTRE);
}

echo json_encode(array('success'=>true));
//mysql_close();
?>