<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idsortides  = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0 ;

$sql  = "DELETE FROM sortides_alumne WHERE id_sortida='$idsortides'";
$result = $db->query($sql);

$sql  = "DELETE FROM sortides_professor WHERE id_sortida='$idsortides'";
$result = $db->query($sql);

$sql  = "DELETE FROM sortides WHERE idsortides='$idsortides'";
$result = $db->query($sql);

$result = $db->query($sql);
if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>