<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$nom         = str_replace("'","\'",$_REQUEST['nom']);
$descripcio  = str_replace("'","\'",$_REQUEST['descripcio']);

$sql         = "insert into textos_sms(nom,descripcio) values ('$nom','$descripcio')";
$result      = $db->query($sql);
	
if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>
