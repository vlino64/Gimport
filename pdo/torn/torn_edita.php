<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idtorn  = intval($_REQUEST['id']);
$nom_torn = isset($_REQUEST['nom_torn']) ? $_REQUEST['nom_torn'] : '';

$sql = "UPDATE torn SET nom_torn='$nom_torn' WHERE idtorn=$idtorn";
$result = $db->query($sql);

if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>