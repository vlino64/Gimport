<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

/*$idunitats_classe = isset($_REQUEST['idunitats_classe']) ? $_REQUEST['idunitats_classe'] : 0 ;
$idgrups_materies = getUnitatsClasse($db,$idunitats_classe)->idgrups_materies;*/

$result  = array();

$sql  = "SELECT DISTINCT(agm.idalumnes),ca.Valor ";
$sql .= "FROM alumnes_grup_materia agm ";
$sql .= "INNER JOIN contacte_alumne   ca ON agm.idalumnes=ca.id_alumne ";
/*$sql .= "INNER JOIN grups_materies    gm ON agm.idgrups_materies  = gm.idgrups_materies ";	
$sql .= "INNER JOIN grups              g ON gm.id_grups            = g.idgrups ";
$sql .= "INNER JOIN materia            m ON gm.id_mat_uf_pla       = m.idmateria ";*/
$sql .= "WHERE ca.id_tipus_contacte=".TIPUS_nom_complet;
//$sql .= "WHERE agm.idgrups_materies=".$idgrups_materies." AND ca.id_tipus_contacte=".TIPUS_nom_complet;	

$sql .= " UNION ";
	 
$sql .= "SELECT DISTINCT(agm.idalumnes),ca.Valor ";
$sql .= "FROM alumnes_grup_materia agm ";
$sql .= "INNER JOIN contacte_alumne    ca ON agm.idalumnes=ca.id_alumne ";
/*$sql .= "INNER JOIN grups_materies     gm ON agm.idgrups_materies  = gm.idgrups_materies ";	 
$sql .= "INNER JOIN grups               g ON gm.id_grups            = g.idgrups ";
$sql .= "INNER JOIN unitats_formatives uf ON gm.id_mat_uf_pla     = uf.idunitats_formatives ";
$sql .= "INNER JOIN moduls_ufs         mu ON gm.id_mat_uf_pla     = mu.id_ufs ";
$sql .= "INNER JOIN moduls              m ON mu.id_moduls         = m.idmoduls ";*/
$sql .= "WHERE ca.id_tipus_contacte=".TIPUS_nom_complet;
//$sql .= "WHERE agm.idgrups_materies=".$idgrups_materies." AND ca.id_tipus_contacte=".TIPUS_nom_complet;

$sql .= " ORDER BY 2 ";

$rs = $db->query($sql);
$result = array();
foreach($rs->fetchAll() as $row) {
	array_push($result, $row);
}

echo json_encode($result);

$rs->closeCursor();
//mysql_close();
?>
