<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$idplans_estudis   = isset($_REQUEST['idplans_estudis']) ? $_REQUEST['idplans_estudis'] : 0;

$sql  = "SELECT m.idmoduls,m.nom_modul FROM moduls m ";
$sql .= "INNER JOIN plans_estudis pe ON m.idplans_estudis=pe.idplans_estudis ";
$sql .= "WHERE m.idplans_estudis=".$idplans_estudis;

$rs = $db->query($sql);

$result = array();
foreach($rs->fetchAll() as $row) {
	array_push($result, $row);
}
echo json_encode($result);

$rs->closeCursor();
?>