<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../mobi/seguretat.php');
$db->exec("set names utf8");

$sql  = "select * from ccc_motius where idccc_motius<>0 ";
$sql .= " order by 2 ";

$rs = $db->query($sql);
$result = array();
foreach($rs->fetchAll() as $row) {
	array_push($result, $row);
}

echo json_encode($result);

$rs->closeCursor();
//mysql_close();
?>