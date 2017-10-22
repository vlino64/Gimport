<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idincidencia_alumne = isset($_REQUEST['idincidencia_alumne']) ? $_REQUEST['idincidencia_alumne'] : 0 ;

foreach ($idincidencia_alumne as $id_in) {
    $sql = "DELETE FROM incidencia_alumne WHERE idincidencia_alumne='$id_in'";
	$result = $db->query($sql);	
}

echo json_encode(array('success'=>true));
//mysql_close();
?>