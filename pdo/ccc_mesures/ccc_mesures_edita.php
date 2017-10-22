<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id      = isset($_REQUEST['idccc_tipus_mesura']) ? intval($_REQUEST['idccc_tipus_mesura']) : 0 ;
$ccc_nom = isset($_REQUEST['ccc_nom']) ? str_replace("'","\'",$_REQUEST['ccc_nom']) : 0 ;

$sql    = "update ccc_tipus_mesura set ccc_nom='$ccc_nom' where idccc_tipus_mesura=$id";
$result = $db->query($sql);

if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>