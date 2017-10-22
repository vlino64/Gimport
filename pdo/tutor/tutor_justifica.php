<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idincidencia_alumne = isset($_REQUEST['idincidencia_alumne']) ? $_REQUEST['idincidencia_alumne'] : 0 ;
$comentari           = isset($_REQUEST['comentari']) ? str_replace("'","\'",$_REQUEST['comentari']) : '';

foreach ($idincidencia_alumne as $id_in) {
    $sql = "UPDATE incidencia_alumne SET comentari='$comentari',id_tipus_incidencia='".TIPUS_FALTA_ALUMNE_JUSTIFICADA."' WHERE idincidencia_alumne='$id_in'";
	$result = $db->query($sql);	
}

echo json_encode(array('success'=>true));
//mysql_close();
?>