<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0 ;

$sql  = "SELECT pe.idperiodes_escolars,pef.id_festiu, ";
$sql .= "CONCAT(SUBSTR(pef.festiu,9,2),'-',SUBSTR(pef.festiu,6,2),'-',SUBSTR(pef.festiu,1,4)) AS festiu ";
$sql .= "FROM periodes_escolars pe ";
$sql .= "INNER JOIN periodes_escolars_festius pef ON pe.idperiodes_escolars=pef.id_periode ";
$sql .= "WHERE pef.id_periode=".$id;

$rs = $db->query($sql);

$result = array();
foreach($rs->fetchAll() as $row) {
	array_push($result, $row);
}

echo json_encode($result);

$rs->closeCursor();
//mysql_close();
?>
