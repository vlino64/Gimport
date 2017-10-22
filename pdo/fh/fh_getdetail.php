<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0 ;

$sql  = "SELECT df.*,ds.dies_setmana FROM dies_franges df ";
$sql .= "INNER JOIN dies_setmana ds ON df.iddies_setmana=ds.iddies_setmana ";
$sql .= "WHERE idfranges_horaries=".$id;

$rs = $db->query($sql);

$result = array();
foreach($rs->fetchAll() as $row) {
	array_push($result, $row);
}

echo json_encode($result);

$rs->closeCursor();
//mysql_close();
?>
