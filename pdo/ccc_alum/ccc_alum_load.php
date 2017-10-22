<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idccc_alumne_principal  = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0 ;

$sql  = "SELECT descripcio_detallada, ";
$sql .= "CONCAT(SUBSTR(data,9,2),'-',SUBSTR(data,6,2),'-',SUBSTR(data,1,4)) AS data_incident ";
$sql .= "FROM ccc_alumne_principal ";
$sql .= "WHERE idccc_alumne_principal='$idccc_alumne_principal'";

$rs = $db->query($sql);

$items = array('data_incident' => '', 'descripcio_detallada' => '');
  
foreach($rec->fetchAll() as $row) {  
	$items = $row;
}  
$result["rows"] = $items;  
  
echo json_encode($items);

$rs->closeCursor();
//mysql_close();
?>