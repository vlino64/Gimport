<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$page    = isset($_POST['page']) ? intval($_POST['page']) : 1;  
$rows    = isset($_POST['rows']) ? intval($_POST['rows']) : 20;  
$sort    = isset($_POST['sort']) ? strval($_POST['sort']) : 'itemid';  
$order   = isset($_POST['order']) ? strval($_POST['order']) : 'asc';
$cognoms = isset($_POST['cognoms']) ? $_POST['cognoms'] : '';

$offset = ($page-1)*$rows;  
  
$result = array();

$where = "cognoms like '%$cognoms%'";  
$rs = $db->query("select count(*) from alumnes where " . $where);  
foreach($rs->fetchAll() as $row) { 
    $result["total"] = $row[0];
} 

$rs = $db->query("select * from alumnes where " . $where . " order by $sort $order limit $offset,$rows");
  
$items = array();  
foreach($rs->fetchAll() as $row) {  
    array_push($items, $row); 
}  
$result["rows"] = $items;  
  
echo json_encode($result);

$rs->closeCursor();
//mysql_free_result($result);
//mysql_close();
?>
