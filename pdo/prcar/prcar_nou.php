<?php
session_start();	 
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idprofessors = $_REQUEST['idprofessors'];
$idcarrecs    = $_REQUEST['idcarrecs'];
$idgrups      = $_REQUEST['idgrups'];
$principal    = $_REQUEST['principal'];

$sql = "INSERT INTO professor_carrec (idprofessors,idcarrecs,idgrups,principal) VALUES ('$idprofessors','$idcarrecs','$idgrups','$principal')";

$result = $db->query($sql);
if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>$sql));
}

//mysql_close();
?>
