<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$page       = isset($_POST['page']) ? intval($_POST['page']) : 1;  
$rows       = isset($_POST['rows']) ? intval($_POST['rows']) : 20;  
$sort       = isset($_POST['sort']) ? strval($_POST['sort']) : 'data';  
$order      = isset($_POST['order']) ? strval($_POST['order']) : 'desc';

$data_inici = isset($_REQUEST['data_inici']) ? 
               substr($_REQUEST['data_inici'],6,4)."-".substr($_REQUEST['data_inici'],3,2)."-".substr($_REQUEST['data_inici'],0,2) : getCursActual($db)["data_inici"];
$data_fi    = isset($_REQUEST['data_fi'])    ? 
               substr($_REQUEST['data_fi'],6,4)."-".substr($_REQUEST['data_fi'],3,2)."-".substr($_REQUEST['data_fi'],0,2)          : getCursActual($db)["data_fi"];

$offset     = ($page-1)*$rows;
$result     = array();

$sql  = "SELECT cp.Valor,gs.idprofessors,COUNT(gs.idprofessors) AS Total ";
$sql .= "FROM guardies_signades gs ";
$sql .= "INNER JOIN contacte_professor cp ON gs.idprofessors = cp.id_professor ";
$sql .= "INNER JOIN professors p ON gs.idprofessors = p.idprofessors ";
$sql .= "WHERE p.activat='S' AND cp.id_tipus_contacte=".TIPUS_nom_complet." AND gs.data BETWEEN '".$data_inici."' AND '".$data_fi."' ";
$sql .= " GROUP BY gs.idprofessors ";
$sql .= " ORDER BY 3 DESC ";

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