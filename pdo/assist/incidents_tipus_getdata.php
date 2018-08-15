<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$sort    = isset($_POST['sort']) ? strval($_POST['sort']) : '1';  
$order   = isset($_POST['order']) ? strval($_POST['order']) : 'asc';

$offset = ($page-1)*$rows; 

$sql  = "select * from tipus_incidents ";
$sql .= " order by $sort $order ";

$rs = $db->query($sql);

$result = array();
foreach($rs->fetchAll() as $row) {
	array_push($result, $row);
}

echo json_encode($result);

$rs->closeCursor();
?>
