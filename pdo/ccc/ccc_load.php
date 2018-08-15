<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idccc_taula_principal  = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0 ;

$sql  = "SELECT id_falta,expulsio,id_motius,descripcio_detallada,id_tipus_sancio, ";
$sql .= "CONCAT(SUBSTR(data,9,2),'-',SUBSTR(data,6,2),'-',SUBSTR(data,1,4)) AS data_incident ";
//$sql .= "CONCAT(SUBSTR(data_fi_sancio,9,2),'-',SUBSTR(data_fi_sancio,6,2),'-',SUBSTR(data_fi_sancio,1,4)) AS data_fi_sancio ";
$sql .= "FROM ccc_taula_principal ";
$sql .= "WHERE idccc_taula_principal='$idccc_taula_principal'";

$rs = $db->query($sql);

$items = array('id_falta' => '','expulsio' => '','id_motius' => '','descripcio_detallada' => '','id_tipus_sancio' => '','data_inici_sancio' => '','data_fi_sancio' => '');
  
foreach($rs->fetchAll() as $row) {  
	$items = $row;
}  
$result["rows"] = $items;  
  
echo json_encode($items);

$rs->closeCursor();
?>
