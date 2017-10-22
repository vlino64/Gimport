<?php
session_start();	 
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$activat             = $_REQUEST['activat'];
$Nom_plan_estudis    = $_REQUEST['Nom_plan_estudis'];
$Acronim_pla_estudis = $_REQUEST['Acronim_pla_estudis'];

$sql = "insert into plans_estudis (activat,Nom_plan_estudis,Acronim_pla_estudis) values ('$activat','$Nom_plan_estudis','$Acronim_pla_estudis')";

$result = $db->query($sql);
if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>
