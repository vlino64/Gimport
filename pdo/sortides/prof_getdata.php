<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$sql  = "SELECT DISTINCT p.codi_professor,cp.id_professor,cp.Valor as professor FROM professors p ";
$sql .= "INNER JOIN contacte_professor cp ON cp.id_professor=p.idprofessors ";
$sql .= "WHERE p.activat='S' AND cp.id_tipus_contacte=".TIPUS_nom_complet;
$sql .= " ORDER BY cp.Valor";

$rs = $db->query($sql);
$result = array();
foreach($rs->fetchAll() as $row) {
	array_push($result, $row);
}
echo json_encode($result);

$rs->closeCursor();
//mysql_close();
?>