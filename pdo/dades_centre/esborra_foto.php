<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');

$sTempFileName = '../images/logo.jpg';
@unlink($sTempFileName);
	
echo json_encode(array('success'=>true));
?>