<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$sql  = "SELECT * ";
$sql .= "FROM dades_centre ";
$sql .= "WHERE iddades_centre = 1";

$rs = $db->query($sql);

$items = array('nom' => '','adreca' => '','cp' => '','poblacio' => '','tlf' => '','fax' => '','email' => '','prof_env_sms' => '');
  
foreach($rs->fetchAll() as $row) {  
	$items = $row;
}  
$result["rows"] = $items;  
  
echo json_encode($items);

$rs->closeCursor();
//mysql_close();
?>