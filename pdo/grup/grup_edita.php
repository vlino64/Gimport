<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id         = intval($_REQUEST['id']);
$idtorn     = $_REQUEST['idtorn'];
$nom        = str_replace("'","\'",$_REQUEST['nom']);
$Descripcio = str_replace("'","\'",$_REQUEST['Descripcio']);

$sql = "update grups set idtorn='$idtorn',nom='$nom',Descripcio='$Descripcio' where idgrups=$id";
$result = $db->query($sql);

if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>