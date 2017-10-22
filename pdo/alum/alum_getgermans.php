<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");
 
$idalumnes  = isset($_REQUEST['idalumnes']) ? $_REQUEST['idalumnes'] : 0 ;
$idfamilies = getFamiliaAlumne($db,$idalumnes);

$sql  = "SELECT af.idalumnes,ca.Valor FROM alumnes_families af ";
$sql .= "INNER JOIN contacte_alumne ca ON af.idalumnes=ca.id_alumne ";
$sql .= "WHERE af.idfamilies='$idfamilies' AND ca.id_tipus_contacte=".TIPUS_nom_complet;
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