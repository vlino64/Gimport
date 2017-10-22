<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$page    = isset($_POST['page']) ? intval($_POST['page']) : 1;  
$rows    = isset($_POST['rows']) ? intval($_POST['rows']) : 20;  
$sort    = isset($_POST['sort']) ? strval($_POST['sort']) : '4';  
$order   = isset($_POST['order']) ? strval($_POST['order']) : 'desc';

$idgrups = isset($_REQUEST['idgrups']) ? $_REQUEST['idgrups'] : 0 ;

$offset = ($page-1)*$rows;
$result  = array();

$sql  = "SELECT COUNT(*) ";
$sql .= "FROM missatges_tutor ";
$sql .= "WHERE idgrup=$idgrups";
 
$rs = $db->query($sql);  
foreach($rs->fetchAll() as $row) { 
    $result["total"] = $row[0]; 
}


$sql  = "SELECT mt.*, ";
$sql .= "CONCAT(SUBSTR(mt.data,9,2),'-',SUBSTR(mt.data,6,2),'-',SUBSTR(mt.data,1,4)) AS data,ca.Valor ";
$sql .= "FROM missatges_tutor mt ";
$sql .= "INNER JOIN contacte_alumne ca ON ca.id_alumne=mt.idalumne ";
$sql .= "WHERE mt.idgrup=$idgrups AND ca.id_tipus_contacte=".TIPUS_nom_complet;
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