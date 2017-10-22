<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id_dies_franges   = $_REQUEST['id_dies_franges'];
$idespais_centre   = $_REQUEST['idespais_centre'];
$idgrups_materies  = $_REQUEST['idgrups_materies'];

$sql = "INSERT INTO unitats_classe (id_dies_franges,idespais_centre,idgrups_materies) VALUES ('$id_dies_franges','$idespais_centre','$idgrups_materies')";
$result = $db->query($sql);

if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>$sql));
}

//mysql_close();
?>
