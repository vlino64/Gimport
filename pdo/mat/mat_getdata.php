<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$s_pe    = isset($_REQUEST['s_pe']) ? $_REQUEST['s_pe'] : '';

$page    = isset($_POST['page']) ? intval($_POST['page']) : 1;  
$rows    = isset($_POST['rows']) ? intval($_POST['rows']) : 20;  
$sort    = isset($_POST['sort']) ? strval($_POST['sort']) : '3';  
$order   = isset($_POST['order']) ? strval($_POST['order']) : 'asc';

$offset = ($page-1)*$rows;  
  
$result = array();

$sql  = "SELECT count(*) FROM materia mat ";
$sql .= "INNER JOIN moduls_materies_ufs mmuf ON mat.idmateria=mmuf.id_mat_uf_pla ";
$sql .= "WHERE mmuf.id_mat_uf_pla<>0 ";
if (isset($_REQUEST['s_pe'])) {
   $sql .= "AND mmuf.idplans_estudis=".$s_pe;
}
$rs  = $db->query($sql);
foreach($rs->fetchAll() as $row) {
    $result["total"] = $row[0]; 
}

$sql  = "SELECT mat.*,mmuf.*,pe.idplans_estudis,pe.Acronim_pla_estudis FROM materia mat ";
$sql .= "INNER JOIN moduls_materies_ufs mmuf ON mat.idmateria=mmuf.id_mat_uf_pla ";
$sql .= "INNER JOIN plans_estudis pe ON mmuf.idplans_estudis=pe.idplans_estudis ";
$sql .= "WHERE mmuf.id_mat_uf_pla<>0 ";
if (isset($_REQUEST['s_pe'])) {
   $sql .= "AND mmuf.idplans_estudis=".$s_pe;
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