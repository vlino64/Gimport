<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idtextos    = intval($_REQUEST['id']);
$nom         = str_replace("'","\'",$_REQUEST['nom']);
$descripcio  = str_replace("'","\'",$_REQUEST['descripcio']);

$sql = "update textos_sms set nom='$nom',descripcio='$descripcio' where idtextos=$idtextos";
$result = $db->query($sql);

if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>