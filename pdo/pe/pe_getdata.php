<?php
session_start();	 
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$sql = "select count(*) from plans_estudis ";
$rs = $db->query($sql);  
foreach($rs->fetchAll() as $row) {  
    $result["total"] = $row[0]; 
}

$rs = $db->query('select * from plans_estudis');

$items = array();  
foreach($rs->fetchAll() as $row) {  
    array_push($items, $row);  
}  
$result["rows"] = $items;

echo json_encode($result);

$rs->closeCursor();
//mysql_close();
?>