<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$ccc_nom = isset($_REQUEST['ccc_nom']) ? str_replace("'","\'",$_REQUEST['ccc_nom']) : 0 ;

$sql    = "insert into ccc_tipus_mesura (ccc_nom) values ('$ccc_nom')";
$result = $db->query($sql);

if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>
