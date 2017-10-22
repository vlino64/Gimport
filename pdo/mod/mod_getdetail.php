<?php
session_start();	 
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id_moduls = isset($_REQUEST['id_moduls']) ? $_REQUEST['id_moduls'] : 0 ;

$sql  = "SELECT ufs.idunitats_formatives,ufs.nom_uf,ufs.hores,ufs.codi_uf, ";
$sql .= "CONCAT(SUBSTR(ufs.data_inici,9,2),'-',SUBSTR(ufs.data_inici,6,2),'-',SUBSTR(ufs.data_inici,1,4)) AS data_inici, ";
$sql .= "CONCAT(SUBSTR(ufs.data_fi,9,2),'-',SUBSTR(ufs.data_fi,6,2),'-',SUBSTR(ufs.data_fi,1,4)) AS data_fi,mmuf.activat ";
$sql .= "FROM unitats_formatives ufs ";
$sql .= "INNER JOIN moduls_ufs mu ON ufs.idunitats_formatives=mu.id_ufs ";
$sql .= "INNER JOIN moduls_materies_ufs mmuf ON ufs.idunitats_formatives=mmuf.id_mat_uf_pla ";
$sql .= "WHERE mu.id_moduls=".$id_moduls;

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