<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idsortides  = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0 ;
$data_inici  = substr($_REQUEST['data_inici'],6,4)."-".substr($_REQUEST['data_inici'],3,2)."-".substr($_REQUEST['data_inici'],0,2);
$data_fi     = substr($_REQUEST['data_fi'],6,4)."-".substr($_REQUEST['data_fi'],3,2)."-".substr($_REQUEST['data_fi'],0,2);
$hora_inici  = isset($_REQUEST['hora_inici'])   ? $_REQUEST['hora_inici'] : '00:00' ;
$hora_fi     = isset($_REQUEST['hora_fi']) ? $_REQUEST['hora_fi'] : '00:00' ;
$lloc 	     = isset($_REQUEST['lloc']) ? str_replace("'","\'",$_REQUEST['lloc']) : '' ;
$descripcio  = isset($_REQUEST['descripcio']) ? str_replace("'","\'",$_REQUEST['descripcio']) : '' ;

$sql  = "UPDATE sortides SET data_inici='$data_inici',data_fi='$data_fi',hora_inici='$hora_inici',hora_fi='$hora_fi',";
$sql .= "lloc='$lloc',descripcio='$descripcio' WHERE idsortides='$idsortides'";

$result = $db->query($sql);
if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>