<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idalumnes         = $_REQUEST['idalumnes'];
$idgrups_materies  = $_REQUEST['idgrups_materies'];

$sql = "INSERT INTO alumnes_grup_materia (idalumnes,idgrups_materies) VALUES ('$idalumnes','$idgrups_materies')";

$result = $db->query($sql);
if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>
