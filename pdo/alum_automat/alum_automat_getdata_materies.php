<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$sort      = isset($_POST['sort'])  ? strval($_POST['sort']) : '3,4';  
$order     = isset($_POST['order']) ? strval($_POST['order']) : 'asc';
$curs      = getCursActual($db)["idperiodes_escolars"];
$idalumnes = $_SESSION['alumne'];

// Obtenim totes les matèries que pertanyen a un determinat plà d'estudis

$sql  = "SELECT distinct(agm.idgrups_materies),gm.id_mat_uf_pla,g.nom as grup,m.nom_materia AS materia ";
$sql .= "FROM alumnes_grup_materia agm ";
$sql .= "INNER JOIN grups_materies     gm ON agm.idgrups_materies = gm.idgrups_materies ";
$sql .= "INNER JOIN grups               g ON gm.id_grups          = g.idgrups ";
$sql .= "INNER JOIN materia             m ON  gm.id_mat_uf_pla    = m.idmateria ";
$sql .= "INNER JOIN unitats_classe     uc ON gm.idgrups_materies  = uc.idgrups_materies ";
$sql .= "INNER JOIN dies_franges       df ON uc.id_dies_franges   = df.id_dies_franges ";
//$sql .= "INNER JOIN espais_centre      ec ON uc.idespais_centre   = ec.idespais_centre "; 
$sql .= "WHERE df.idperiode_escolar=$curs AND agm.idalumnes=$idalumnes ";
//$sql .= "GROUP BY 1 ";
	 
$sql .= " UNION ";
	 
$sql .= "SELECT distinct(agm.idgrups_materies),gm.id_mat_uf_pla,g.nom as grup,CONCAT(LEFT(m.nom_modul,20),'-',uf.nom_uf) AS materia ";
$sql .= "FROM alumnes_grup_materia agm ";
$sql .= "INNER JOIN grups_materies     gm ON agm.idgrups_materies = gm.idgrups_materies ";
$sql .= "INNER JOIN grups               g ON gm.id_grups          = g.idgrups ";
$sql .= "INNER JOIN unitats_formatives uf ON gm.id_mat_uf_pla     = uf.idunitats_formatives ";
$sql .= "INNER JOIN moduls_ufs         mu ON gm.id_mat_uf_pla     = mu.id_ufs ";
$sql .= "INNER JOIN moduls              m ON mu.id_moduls         = m.idmoduls ";
$sql .= "INNER JOIN unitats_classe     uc ON gm.idgrups_materies  = uc.idgrups_materies ";
$sql .= "INNER JOIN dies_franges       df ON uc.id_dies_franges   = df.id_dies_franges ";
//$sql .= "INNER JOIN espais_centre      ec ON uc.idespais_centre   = ec.idespais_centre "; 
$sql .= "WHERE df.idperiode_escolar=$curs AND agm.idalumnes=$idalumnes ";
//$sql .= "GROUP BY 1 ";
	 
$sql .= " ORDER BY $sort $order ";

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