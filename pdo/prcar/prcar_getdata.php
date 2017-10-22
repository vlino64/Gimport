<?php
session_start();	 
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$page = isset($_POST['page']) ? intval($_POST['page']) : 0;  
$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 20;  
$sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'Valor';  
$order = isset($_POST['order']) ? strval($_POST['order']) : 'asc'; 
$cognoms = isset($_POST['cognoms']) ? $_POST['cognoms'] : '';
 
$offset = ($page-1)*$rows;  
  
$result = array(); 

$where = "cp.Valor like '%$cognoms%'";

$sql  = "SELECT count(*) FROM professor_carrec pc ";
$sql .= "INNER JOIN contacte_professor cp ON cp.id_professor=pc.idprofessors ";
$sql .= "WHERE $where AND cp.id_tipus_contacte=".TIPUS_nom_complet;

$rs = $db->query($sql); 
foreach($rs->fetchAll() as $row) {
    $result["total"] = $row[0];  
}

$sql  = "SELECT cp.*,pc.*,c.nom_carrec,g.nom,pc.principal FROM professor_carrec pc ";
$sql .= "INNER JOIN contacte_professor cp ON cp.id_professor=pc.idprofessors ";
$sql .= "INNER JOIN carrecs c ON c.idcarrecs=pc.idcarrecs ";
$sql .= "LEFT JOIN grups g ON g.idgrups=pc.idgrups ";
$sql .= "WHERE $where AND cp.id_tipus_contacte=".TIPUS_nom_complet;

$sql .= " ORDER BY $sort $order limit $offset,$rows";

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
