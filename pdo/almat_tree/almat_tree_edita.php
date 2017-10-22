<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idgrups_materies = isset($_REQUEST['idgrups_materies']) ? $_REQUEST['idgrups_materies'] : 0 ;
$idalumnes        = isset($_REQUEST['idalumnes']) ? $_REQUEST['idalumnes'] : 0 ;

foreach ($idalumnes as $id_alumne) {
	$sql    = "DELETE FROM alumnes_grup_materia WHERE idgrups_materies='$idgrups_materies' AND idalumnes='$id_alumne'";
	$result = $db->query($sql);
}

echo json_encode(array('success'=>true));
//mysql_close();
?>