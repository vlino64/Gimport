<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$hora_inici = $_REQUEST['hora_inici'];
$hora_fi    = $_REQUEST['hora_fi'];
$idtorn     = $_REQUEST['idtorn'];
$activada   = $_REQUEST['activada'];
$esbarjo    = $_REQUEST['esbarjo'];

$sql = "insert into franges_horaries (idtorn,hora_inici,hora_fi,activada,esbarjo) values ('$idtorn','$hora_inici','$hora_fi','$activada','$esbarjo')";

$result = $db->query($sql);
if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>
