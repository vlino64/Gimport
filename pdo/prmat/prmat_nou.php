<?php	 
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idprofessors       = $_REQUEST['idprofessors'];
$idagrups_materies  = $_REQUEST['idagrups_materies'];

$sql = "INSERT INTO prof_agrupament (idprofessors,idagrups_materies) VALUES ('$idprofessors','$idagrups_materies')";

$result = $db->query($sql);
if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>
