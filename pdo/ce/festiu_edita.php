<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id         = intval($_REQUEST['id']);
$id_periode = $_REQUEST['id_periode'];
$festiu     = substr($_REQUEST['festiu'],6,4)."-".substr($_REQUEST['festiu'],3,2)."-".substr($_REQUEST['festiu'],0,2);

$sql = "update periodes_escolars_festius set id_periode='$id_periode',festiu='$festiu' where id_festiu=$id";
$result = $db->query($sql);

if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>