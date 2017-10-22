<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id         = intval($_REQUEST['idespais_centre']);
$descripcio = $_REQUEST['descripcio'];
$activat    = $_REQUEST['activat'];

$sql = "update espais_centre set descripcio='$descripcio',activat='$activat' where idespais_centre=$id";

$result = $db->query($sql);
if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>