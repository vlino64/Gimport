<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");
 
$idmateria = isset($_REQUEST['idmateria']) ? $_REQUEST['idmateria'] : 0 ;

$sql  = "SELECT gm.id_grups,g.nom FROM grups_materies gm ";
$sql .= "INNER JOIN grups g ON gm.id_grups=g.idgrups ";
$sql .= "WHERE gm.id_mat_uf_pla=".$idmateria;
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