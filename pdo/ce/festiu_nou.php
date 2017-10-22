<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id_periode = $_REQUEST['id_periode'];
$festiu     = substr($_REQUEST['festiu'],6,4)."-".substr($_REQUEST['festiu'],3,2)."-".substr($_REQUEST['festiu'],0,2);

$sql = "insert into periodes_escolars_festius (id_periode,festiu) values ($id_periode,'$festiu')";
$result = $db->query($sql);

if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>