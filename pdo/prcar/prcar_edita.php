<?php
session_start();	 
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idprofessor_carrec = intval($_REQUEST['id']);
$idprofessors       = isset($_REQUEST['idprofessors']) ? $_REQUEST['idprofessors'] : 0 ;
$idcarrecs          = $_REQUEST['idcarrecs'];
$idgrups            = $_REQUEST['idgrups'];
$principal          = $_REQUEST['principal'];

$sql = "UPDATE professor_carrec SET idprofessors='$idprofessors',idcarrecs='$idcarrecs',idgrups='$idgrups',principal='$principal' WHERE idprofessor_carrec=$idprofessor_carrec";

$result = $db->query($sql);
if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>$sql));
}

//mysql_close();
?>