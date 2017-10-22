<?php
session_start();	 
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idprof_grup_materia  = intval($_REQUEST['id']);
$idprofessors       = $_REQUEST['idprofessors'];
$idagrups_materies  = $_REQUEST['idagrups_materies'];

$sql = "UPDATE prof_agrupament SET idprofessors='$idprofessors',idagrups_materies='$idagrups_materies' WHERE idprof_grup_materia=$idprof_grup_materia";

/*
$fp = fopen("log.txt","a");
fwrite($fp, $sql . PHP_EOL);
fclose($fp);
*/

$result = $db->query($sql);
if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>