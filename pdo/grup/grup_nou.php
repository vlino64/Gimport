<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idtorn     = $_REQUEST['idtorn'];
$nom        = str_replace("'","\'",$_REQUEST['nom']);
$Descripcio = str_replace("'","\'",$_REQUEST['Descripcio']);

// Insertem la moduls_materies_ufs
$sql = "insert into grups (idtorn,nom,Descripcio) values ('$idtorn','$nom','$Descripcio')";
$result = $db->query($sql);

if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>
