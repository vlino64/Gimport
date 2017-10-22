<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$page       = isset($_POST['page']) ? intval($_POST['page']) : 1;  
$rows       = isset($_POST['rows']) ? intval($_POST['rows']) : 20;  
$sort       = isset($_POST['sort']) ? strval($_POST['sort']) : 'descripcio';  
$order      = isset($_POST['order']) ? strval($_POST['order']) : 'asc';
$descripcio = isset($_POST['descripcio']) ? $_POST['descripcio'] : '';

$offset = ($page-1)*$rows; 
$result = array();

$where = "descripcio like '%$descripcio%'";

$sql = "select count(*) from espais_centre where " . $where;
$rs = $db->query($sql);  
foreach($rs->fetchAll() as $row) {  
    $result["total"] = $row[0]; 
}

$sql = "select * from espais_centre where " . $where . " order by $sort $order limit $offset,$rows";
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
