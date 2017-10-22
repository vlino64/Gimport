<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idgrups_materies = isset($_REQUEST['idgrups_materies']) ? $_REQUEST['idgrups_materies'] : 0 ;
$idalumnes        = isset($_REQUEST['idalumnes']) ? $_REQUEST['idalumnes'] : 0 ;

$sql = "INSERT INTO alumnes_grup_materia (idalumnes,idgrups_materies) VALUES ('$idalumnes','$idgrups_materies')";
$result = $db->query($sql);

echo json_encode(array('success'=>true));
//mysql_close();
?>