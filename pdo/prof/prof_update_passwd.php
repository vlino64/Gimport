<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id_professor    = intval($_REQUEST['id']);
$contrasenya_1  = $_REQUEST['contrasenya_1'];
$contrasenya_2  = $_REQUEST['contrasenya_2'];

if ($contrasenya_1==$contrasenya_2) {
   $sql = "update contacte_professor set Valor=MD5('$contrasenya_1') where id_professor=$id_professor and id_tipus_contacte=".TIPUS_contrasenya;
   $result = $db->query($sql);
}

if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

?>