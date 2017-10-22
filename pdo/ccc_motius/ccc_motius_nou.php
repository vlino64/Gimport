<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$nom_motiu = isset($_REQUEST['nom_motiu']) ? str_replace("'","\'",$_REQUEST['nom_motiu']) : 0 ;

$sql    = "insert into ccc_motius (nom_motiu) values ('$nom_motiu')";
$result = $db->query($sql);

if ($result){
	echo json_encode(array('success'=>true));
} else {
	echo json_encode(array('msg'=>'Algunos errores ocurrieron.'));
}

//mysql_close();
?>
