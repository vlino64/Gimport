<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");
 
$idgrups_materies   = isset($_REQUEST['idgrups_materies']) ? $_REQUEST['idgrups_materies'] : 0 ;

$sql  = "SELECT agm.idalumnes,ca.Valor FROM alumnes_grup_materia agm ";
$sql .= "INNER JOIN grups_materies gm ON agm.idgrups_materies=gm.idgrups_materies ";
$sql .= "INNER JOIN materia ma ON gm.id_mat_uf_pla=ma.idmateria ";
$sql .= "INNER JOIN contacte_alumne ca ON agm.idalumnes=ca.id_alumne ";
$sql .= "WHERE agm.idgrups_materies='".$idgrups_materies."' AND ca.id_tipus_contacte=".TIPUS_nom_complet;

$sql .= " UNION ";

$sql .= "SELECT agm.idalumnes,ca.Valor FROM alumnes_grup_materia agm ";
$sql .= "INNER JOIN grups_materies gm ON agm.idgrups_materies=gm.idgrups_materies ";
$sql .= "INNER JOIN unitats_formatives uf ON gm.id_mat_uf_pla     = uf.idunitats_formatives ";
$sql .= "INNER JOIN moduls_ufs         mu ON gm.id_mat_uf_pla     = mu.id_ufs ";
$sql .= "INNER JOIN moduls              m ON mu.id_moduls         = m.idmoduls ";
$sql .= "INNER JOIN contacte_alumne ca ON agm.idalumnes=ca.id_alumne ";
$sql .= "WHERE agm.idgrups_materies='".$idgrups_materies."' AND ca.id_tipus_contacte=".TIPUS_nom_complet;
$sql .= " ORDER BY 2";

/*$fp = fopen("log.txt","a");
fwrite($fp, $sql ."\n\n". PHP_EOL);
fclose($fp);*/

$rs = $db->query($sql);

$items = array();  
foreach($rs->fetchAll() as $row) {  
    array_push($items, $row);  
}  
echo json_encode($items); 

$rs->closeCursor();
//mysql_close();
?>