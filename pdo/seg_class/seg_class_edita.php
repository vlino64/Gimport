<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id_seguiment    = $_REQUEST['id'];
$id_dia_franja   = $_REQUEST['id_dia_franja'];
$id_grup_materia = $_REQUEST['id_grup_materia'];
$lectiva         = $_REQUEST['lectiva'];
$seguiment       = str_replace("'","\'",$_REQUEST['seguiment']);
$data            = substr($_REQUEST['data'],6,4)."-".substr($_REQUEST['data'],3,2)."-".substr($_REQUEST['data'],0,2);

$sql    = "update qp_seguiment set lectiva=$lectiva,seguiment='$seguiment' where id_seguiment=$id_seguiment";
$result = $db->query($sql);

if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>