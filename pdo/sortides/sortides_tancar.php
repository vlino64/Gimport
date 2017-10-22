<?php
session_start();
include_once('../bbdd/connect.php');
include_once('../func/generic.php');
include_once('../func/constants.php');
$db->exec("set names utf8");

$idsortides  = isset($_REQUEST['idsortides']) ? $_REQUEST['idsortides'] : 0 ;

$sql  = "UPDATE sortides SET tancada='S' WHERE idsortides='$idsortides'";

$result = $db->query($sql);
if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>