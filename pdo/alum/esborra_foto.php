<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');

$idalumnes = $_REQUEST['id'];
	
$sTempFileName = '../images/alumnes/' . $idalumnes . '.jpg';
@unlink($sTempFileName);
	
echo json_encode(array('success'=>true));
?>