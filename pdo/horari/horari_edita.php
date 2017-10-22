<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idalumnes_grup_materia = intval($_REQUEST['id']);
$idalumnes         = $_REQUEST['idalumnes'];
$idgrups_materies  = $_REQUEST['idgrups_materies'];

$sql = "UPDATE alumnes_grup_materia SET idalumnes='$idalumnes',idgrups_materies='$idgrups_materies' WHERE idalumnes_grup_materia=$idalumnes_grup_materia";

$result = $db->query($sql);
if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>