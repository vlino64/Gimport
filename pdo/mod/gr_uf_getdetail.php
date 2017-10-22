<?php
session_start();	 
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");
 
$idunitats_formatives = isset($_REQUEST['idunitats_formatives']) ? $_REQUEST['idunitats_formatives'] : 0 ;

$sql  = "SELECT gm.id_grups,g.nom FROM grups_materies gm ";
$sql .= "INNER JOIN grups g ON gm.id_grups=g.idgrups ";
$sql .= "WHERE gm.id_mat_uf_pla=".$idunitats_formatives;
$sql .= " ORDER BY 2";

$rs = $db->query($sql);

$items = array();  
foreach($rs->fetchAll() as $row) {  
    array_push($items, $row);  
}  
echo json_encode($items); 

$rs->closeCursor();
//mysql_close();
?>