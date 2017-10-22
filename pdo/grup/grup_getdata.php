<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$s_torn  = isset($_REQUEST['s_torn']) ? $_REQUEST['s_torn'] : '';

$page    = isset($_POST['page']) ? intval($_POST['page']) : 1;  
$rows    = isset($_POST['rows']) ? intval($_POST['rows']) : 20; 
$sort    = isset($_POST['sort']) ? strval($_POST['sort']) : 'idgrups';
$order   = isset($_POST['order']) ? strval($_POST['order']) : 'asc';

$offset = ($page-1)*$rows;  
  
$result = array();

$sql  = "SELECT count(*) FROM grups g ";
$sql .= "left join torn t on g.idtorn=t.idtorn WHERE g.idgrups<>0 ";
if (isset($_REQUEST['s_torn'])) {
   $sql .= "AND g.idtorn=".$s_torn;
}
$rs = $db->query($sql);
foreach($rs->fetchAll() as $row) {
    $result["total"] = $row[0];  
}

$sql  = "SELECT g.*,t.nom_torn from grups g ";
$sql .= "left join torn t on g.idtorn=t.idtorn WHERE g.idgrups<>0 ";
if (isset($_REQUEST['s_torn'])) {
   $sql .= "AND g.idtorn=".$s_torn;
}
$sql .= " ORDER BY $sort $order LIMIT $offset,$rows";
$rs = $db->query($sql);

$items = array();  
foreach($rs->fetchAll() as $row) {  
    array_push($items, $row);  
}  
$result["rows"] = $items;  
  
echo json_encode($result);

$rs->closeCursor();
//mysql_close();
?>