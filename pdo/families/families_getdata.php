<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$sort    = isset($_POST['sort']) ? strval($_POST['sort']) : '3,4';  
$order   = isset($_POST['order']) ? strval($_POST['order']) : 'asc';

$idalumnes = $_SESSION['alumne'];

$sql  = " ";

/*$fp = fopen("log.txt","a");
fwrite($fp, $sql ."\n\n". PHP_EOL);
fclose($fp);*/
	 
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