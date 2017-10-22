<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id_tipus  = isset($_REQUEST['id_tipus'])  ? $_REQUEST['id_tipus']  : 0 ;
$id_carrec = isset($_REQUEST['id_carrec']) ? $_REQUEST['id_carrec'] : 0 ;

$sql = "insert into ccc_tipus_comunicacio_carrec (id_tipus,id_carrec) values ($id_tipus,$id_carrec)";
$result = $db->query($sql);

if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>