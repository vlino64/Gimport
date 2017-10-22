<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$page    = isset($_POST['page']) ? intval($_POST['page']) : 1;  
$rows    = isset($_POST['rows']) ? intval($_POST['rows']) : 20;  
$sort    = isset($_POST['sort']) ? strval($_POST['sort']) : 'idperiodes_escolars';  
$order   = isset($_POST['order']) ? strval($_POST['order']) : 'asc';

$offset = ($page-1)*$rows; 

$sql = "select count(*) from periodes_escolars ";
$rs = $db->query($sql);  
foreach($rs->fetchAll() as $row) {  
    $result["total"] = $row[0]; 
}

$sql  = "select idperiodes_escolars,Nom,Descripcio,";
$sql .= "CONCAT(SUBSTR(data_inici,9,2),'-',SUBSTR(data_inici,6,2),'-',SUBSTR(data_inici,1,4)) AS data_inici,";
$sql .= "CONCAT(SUBSTR(data_fi,9,2),'-',SUBSTR(data_fi,6,2),'-',SUBSTR(data_fi,1,4)) AS data_fi,actual ";
$sql .= "from periodes_escolars order by $sort $order limit $offset,$rows";

$rs = $db->query($sql);

$items = array();  
foreach($rs->fetchAll() as $row) {  
    array_push($items, $row);  
}  
$result["rows"] = $items;

echo json_encode($result);

//$rs->closeCursor();
//mysql_close();
?>