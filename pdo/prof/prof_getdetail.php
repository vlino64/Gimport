<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');

$id = $_REQUEST['id'];  

$db->exec("set names utf8");
  
$rs = $db->query("select * from alumnes where idalumnes='$id'");  
$items = array();  
foreach($rs->fetchAll() as $row) {  
    array_push($items, $row);  
}  
echo json_encode($items); 

$rs->closeCursor();
?>