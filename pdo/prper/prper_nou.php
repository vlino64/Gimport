<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idprofessors      = $_REQUEST['idprofessors'];
$id_dies_franges   = $_REQUEST['id_dies_franges'];
$idespais_centre   = $_REQUEST['idespais_centre'];

$sql = "INSERT INTO prof_permanencies (idprofessors,id_dies_franges,idespais_centre) VALUES ('$idprofessors','$id_dies_franges','$idespais_centre')";

$result = $db->query($sql);
if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>$sql));
}

//mysql_close();
?>
