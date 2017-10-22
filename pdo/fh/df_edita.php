<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id                 = intval($_REQUEST['id']);
$idfranges_horaries = $_REQUEST['idfranges_horaries'];
$iddies_setmana     = $_REQUEST['iddies_setmana'];
$curs_escolar       = $_SESSION['curs_escolar'];

$sql = "update dies_franges set iddies_setmana='$iddies_setmana',idfranges_horaries='$idfranges_horaries',idperiode_escolar='$curs_escolar' where id_dies_franges=$id";

$result = $db->query($sql);
if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>