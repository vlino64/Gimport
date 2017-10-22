<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0 ;

$sql  = "SELECT tcc.*,c.nom_carrec FROM ccc_tipus_comunicacio_carrec tcc ";
$sql .= "INNER JOIN carrecs c ON tcc.id_carrec=c.idcarrecs ";
$sql .= "WHERE tcc.id_tipus=".$id;

$rs = $db->query($sql);

$result = array();
foreach($rs->fetchAll() as $row) {
	array_push($result, $row);
}

echo json_encode($result);

$rs->closeCursor();
//mysql_close();
?>
