<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id        = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0 ;
$id_tipus  = isset($_REQUEST['id_tipus']) ? $_REQUEST['id_tipus'] : 0 ;
$id_carrec = isset($_REQUEST['id_carrec']) ? $_REQUEST['id_carrec'] : 0 ;

$sql = "update ccc_tipus_comunicacio_carrec set id_tipus='$id_tipus',id_carrec='$id_carrec' where idccc_tipus_comunicacio_carrec=$id";
$result = $db->query($sql);

if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>