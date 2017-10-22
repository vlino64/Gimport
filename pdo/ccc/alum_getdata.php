<?php
session_start();
require_once('../bbdd/connect.php');
require_once('../func/constants.php');
require_once('../func/generic.php');
require_once('../func/seguretat.php');
$db->exec("set names utf8");

$sql  = "SELECT DISTINCT ca.id_alumne,ca.Valor as alumne FROM alumnes a ";
$sql .= "INNER JOIN contacte_alumne ca ON ca.id_alumne        =  a.idalumnes ";
$sql .= "INNER JOIN alumnes_grup_materia agm ON agm.idalumnes = ca.id_alumne ";
$sql .= "WHERE ca.id_tipus_contacte=".TIPUS_nom_complet." AND a.activat = 'S'" ;
$sql .= " ORDER BY ca.Valor";

$rs = $db->query($sql);
$result = array();
foreach($rs->fetchAll() as $row) {
	array_push($result, $row);
}
echo json_encode($result);

$rs->closeCursor();
//mysql_close();
?>