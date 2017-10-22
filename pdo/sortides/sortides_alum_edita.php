<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");
 
$id_sortida        = isset($_SESSION['sortida']) ? $_SESSION['sortida'] : 0 ;
if ($id_sortida == 0) {
	$id_sortida = isset($_REQUEST['sortida']) ? $_REQUEST['sortida'] : 0 ;
}
$id_alumne         = isset($_REQUEST['id_alumne']) ? $_REQUEST['id_alumne'] : 0 ;
$idsortides_alumne = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0 ;

$sql = "UPDATE sortides_alumne SET id_sortida=$id_sortida,id_alumne=$id_alumne WHERE idsortides_alumne=$idsortides_alumne";

$result = $db->query($sql);
if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>