<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");
 
$id_sortida     = isset($_SESSION['sortida']) ? $_SESSION['sortida'] : 0 ;
if ($id_sortida == 0) {
	$id_sortida = isset($_REQUEST['sortida']) ? $_REQUEST['sortida'] : 0 ;
}

$sql  = "SELECT DISTINCT(sa.id_alumne),sa.idsortides_alumne,ca.Valor as alumne,gr.nom AS grup FROM sortides_alumne sa ";
$sql .= "INNER JOIN contacte_alumne       ca ON ca.id_alumne         = sa.id_alumne ";
$sql .= "INNER JOIN alumnes_grup_materia agm ON agm.idalumnes        = sa.id_alumne ";
$sql .= "INNER JOIN grups_materies        gm ON agm.idgrups_materies = gm.idgrups_materies ";
$sql .= "INNER JOIN grups                 gr ON gm.id_grups          = gr.idgrups ";
$sql .= "WHERE sa.id_sortida=".$id_sortida." AND ca.id_tipus_contacte=".TIPUS_nom_complet;
$sql .= " GROUP BY sa.id_alumne";
$sql .= " ORDER BY gr.nom,ca.Valor";

$rs = $db->query($sql);

$items = array();
foreach($rs->fetchAll() as $row) {  
    array_push($items, $row);  
}
echo json_encode($items); 

$rs->closeCursor();
//mysql_close();
?>