<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$page    = isset($_POST['page']) ? intval($_POST['page']) : 1;  
$rows    = isset($_POST['rows']) ? intval($_POST['rows']) : 20;  
$sort    = isset($_POST['sort']) ? strval($_POST['sort']) : 'idgrups';  
$order   = isset($_POST['order']) ? strval($_POST['order']) : 'asc';

$g_pe    = isset($_REQUEST['g_pe']) ? $_REQUEST['g_pe'] : '';
$where = '';
if ($g_pe != '') {
   $where .= " AND idgrups=".$g_pe;
}

$offset = ($page-1)*$rows;  
  
$result = array();

$rs = $db->query("SELECT count(*) FROM grups WHERE idgrups<>0 ".$where);
foreach($rs->fetchAll() as $row) {
    $result["total"] = $row[0];  
}

$rs = $db->query("SELECT * from grups WHERE idgrups<>0 ".$where." ORDER BY $sort $order LIMIT $offset,$rows");
  
$items = array();  
foreach($rs->fetchAll() as $row) {  
    array_push($items, $row);  
}  
$result["rows"] = $items;  
  
echo json_encode($result);

$rs->closeCursor();
//mysql_close();
?>