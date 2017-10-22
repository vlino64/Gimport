<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id = intval($_REQUEST['id']);

$sql = "delete from dies_franges where idfranges_horaries=$id";
$result = $db->query($sql);

$sql = "delete from franges_horaries where idfranges_horaries=$id";
$result = $db->query($sql);

if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>
