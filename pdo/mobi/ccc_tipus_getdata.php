<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../mobi/seguretat.php');
$db->exec("set names utf8");

$page    = isset($_POST['page']) ? intval($_POST['page']) : 1;  
$rows    = isset($_POST['rows']) ? intval($_POST['rows']) : 20;  
$sort    = isset($_POST['sort']) ? strval($_POST['sort']) : '1';  
$order   = isset($_POST['order']) ? strval($_POST['order']) : 'asc';

$offset = ($page-1)*$rows; 

$sql  = "select * from ccc_tipus where idccc_tipus<>0 ";
$sql .= " order by $sort $order limit $offset,$rows ";

$rs = $db->query($sql);

$result = array();
foreach($rs->fetchAll() as $row) {
	array_push($result, $row);
}

echo json_encode($result);

$rs->closeCursor();
//mysql_close();
?>
